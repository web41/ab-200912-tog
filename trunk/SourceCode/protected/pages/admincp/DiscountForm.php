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
				$this->lblHeader->Text = "Update category: ".$activeRecord->Name;
				$this->txtName->Text = $activeRecord->Name;
				$this->txtAlias->Text = $activeRecord->Alias;
				$this->txtAmount->Text = $activeRecord->Amount;
				$this->dpStartDate->TimeStamp = $activeRecord->StartDate;
				$this->dpEndDate->TimeStamp = $activeRecord->EndDate;
				$this->radIsPercent->SelectedValue = $activeRecord->IsPercent;
			}
			else
			{
				$this->lblHeader->Text = "Add new category";
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

	protected function btnSubmit_Clicked($sender, $param)
	{
		if ($this->IsValid)
		{
			$activeRecord = $this->getItem();

			$activeRecord->Name = $this->txtName->SafeText;
			$activeRecord->Alias = $this->txtAlias->SafeText;
			$activeRecord->Amount = TPropertyValue::ensureFloat($this->txtAmount->SafeText);
			$activeRecord->StartDate = $this->dpStartDate->TimeStamp;
			$activeRecord->EndDate = $this->dpEndDate->TimeStamp;
			$activeRecord->IsPercent = $this->radIsPercent->SelectedValue;

			try
			{
				$action = ($activeRecord->ID>0 ? "update-success" : "add-success");
				$msg = $this->Application->getModule("message")->translate(($activeRecord->ID>0 ? "UPDATE_SUCCESS" : "ADD_SUCCESS"),"Discount",$this->txtName->SafeText);
				$activeRecord->save();
				$this->Response->redirect($this->Service->ConstructUrl("admincp.DiscountManager",array("action"=>$action, "msg"=>$msg)));
			}
			catch(TException $e)
			{
				$this->Notice->Type = AdminNoticeType::Error;
				$this->Notice->Text = $this->Application->getModule("message")->translate(($activeRecord->ID>0 ? "UPDATE_FAILED" : "ADD_FAILED"),"Discount",$this->txtName->SafeText);
			}
		}
	}

	protected function uniqueCheck_ServerValidated($sender, $param)
	{
		if ($param->Value != '')
		{
			$criteria = new TActiveRecordCriteria;
			$criteria->Condition = "discount_name = '".preg_replace("/'/", "/''/", $param->Value)."' ";
			$activeRecord = $this->getItem();
			if ($activeRecord && $activeRecord->ID > 0) $criteria->Condition .= " and discount_id <> '".$activeRecord->ID."'";
			$param->IsValid = count(Prado::createComponent(self::AR)->finder()->find($criteria)) == 0;
		}
	}
}

?>