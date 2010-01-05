<?php

class DiscountForm extends TPage
{
	const AR = "DiscountRecord";
	public function onLoad($param)
	{
		parent::onLoad($param);
		if (!$this->IsPostBack)
		{
			$activeRecord = $this->getItem();
			if ($activeRecord && $activeRecord->ID > 0)
			{
				// Populates the input controls with the existing post data
				$this->lblHeader->Text = "Update discount: ".$activeRecord->Name;
				$this->txtName->Text = $activeRecord->Name;
				$this->txtAlias->Text = $activeRecord->Alias;
				$this->txtAmount->Text = $activeRecord->IsPercent ? $activeRecord->Amount*100 : $activeRecord->Amount;
				$this->dpStartDate->Data = $activeRecord->StartDate;
				$this->dpEndDate->Data = $activeRecord->EndDate;
				$this->radIsPercent->SelectedValue = $activeRecord->IsPercent;
			}
			else
			{
				$this->lblHeader->Text = "Add new discount";
				$this->dpStartDate->Data = time();
				$this->dpEndDate->Data = time();
			}
		}
	}

	protected function getItem()
	{
		if ($this->Request->Contains("id") && $this->Request->Contains("alias"))
		{
			// use Active Record to look for the specified post ID
			$activeRecord = Prado::createComponent(self::AR)->finder()->findBydiscount_idAnddiscount_alias(TPropertyValue::ensureInteger($this->Request['id']), $this->Request['alias']);
			if($activeRecord === null)
			{
				$this->Notice->Type = AdminNoticeType::Error;
				$this->Notice->Text = $this->Application->getModule("message")->translate("ITEM_NOT_FOUND","discount");
				$this->mainBox->Visible = false;
			}
			return $activeRecord;
		}
		else
		{
			return Prado::createComponent(self::AR);
		}
	}
	
	private function bindItem()
	{
		$activeRecord = $this->getItem();

		$activeRecord->Name = $this->txtName->SafeText;
		$activeRecord->Alias = $this->txtAlias->SafeText;
		$activeRecord->StartDate = $this->dpStartDate->Data;
		$activeRecord->EndDate = $this->dpEndDate->Data;
		$activeRecord->IsPercent = $this->radIsPercent->SelectedValue;
		$activeRecord->Amount = $activeRecord->IsPercent ? TPropertyValue::ensureFloat($this->txtAmount->SafeText/100) : TPropertyValue::ensureFloat($this->txtAmount->SafeText);
		
		return $activeRecord;
	}

	protected function btnSubmit_Clicked($sender, $param)
	{
		if ($this->IsValid)
		{
			$activeRecord = $this->bindItem();
			try
			{
				$action = ($activeRecord->ID>0 ? "update-success" : "add-success");
				$msg = $this->Application->getModule("message")->translate(($activeRecord->ID>0 ? "UPDATE_SUCCESS" : "ADD_SUCCESS"),"Discount",$activeRecord->Name);
				$activeRecord->save();
				$this->Response->redirect($this->Service->ConstructUrl("admincp.DiscountManager",array("action"=>$action, "msg"=>$msg)));
			}
			catch(TException $e)
			{
				$this->Notice->Type = AdminNoticeType::Error;
				$this->Notice->Text = $this->Application->getModule("message")->translate(($activeRecord->ID>0 ? "UPDATE_FAILED" : "ADD_FAILED"),"Discount",$activeRecord->Name);
			}
		}
	}
	
	protected function btnAddMore_Clicked($sender, $param)
	{
		if ($this->IsValid)
		{
			$activeRecord = $this->bindItem();
			try
			{
				$activeRecord->save();
				$this->Response->redirect($this->Service->ConstructUrl("admincp.DiscountForm"));
			}
			catch(TException $e)
			{
				$this->Notice->Type = AdminNoticeType::Error;
				$this->Notice->Text = $this->Application->getModule("message")->translate(($activeRecord->ID>0 ? "UPDATE_FAILED" : "ADD_FAILED"),"Discount",$activeRecord->Name);
			}
		}
	}

	protected function uniqueCheck_ServerValidated($sender, $param)
	{
		if ($param->Value != '')
		{
			$criteria = new TActiveRecordCriteria;
			$criteria->Condition = "discount_name = :name";
			$criteria->Parameters[":name"] = $param->Value;
			$activeRecord = $this->getItem();
			if ($activeRecord && $activeRecord->ID > 0) $criteria->Condition .= " and discount_id <> '".$activeRecord->ID."'";
			$param->IsValid = count(Prado::createComponent(self::AR)->finder()->find($criteria)) == 0;
		}
	}
}

?>