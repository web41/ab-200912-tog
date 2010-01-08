<?php

class CouponForm extends TPage
{
	const AR = "CouponRecord";
	public function onLoad($param)
	{
		parent::onLoad($param);
		if (!$this->IsPostBack)
		{
			$activeRecord = $this->getItem();
			if ($activeRecord && $activeRecord->ID > 0)
			{
				// Populates the input controls with the existing post data
				$this->lblHeader->Text = "Update coupon: ".$activeRecord->Code;
				$this->txtCode->Text = $activeRecord->Code;
				$this->txtAmount->Text = $activeRecord->IsPercent ? $activeRecord->Amount*100 : $activeRecord->Amount;
				$this->radPublish->SelectedValue = $activeRecord->IsPublished;
				$this->radIsPercent->SelectedValue = $activeRecord->IsPercent;
			}
			else
			{
				$this->lblHeader->Text = "Add new coupon";
			}
		}
	}

	protected function getItem()
	{
		if ($this->Request->Contains("id") && $this->Request->Contains("alias"))
		{
			// use Active Record to look for the specified post ID
			$activeRecord = Prado::createComponent(self::AR)->finder()->findBycoupon_idAndcoupon_code(TPropertyValue::ensureInteger($this->Request['id']), $this->Request['alias']);
			if($activeRecord === null)
			{
				$this->Notice->Type = AdminNoticeType::Error;
				$this->Notice->Text = $this->Application->getModule("message")->translate("ITEM_NOT_FOUND","coupon");
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

		$activeRecord->Code = $this->txtCode->SafeText;
		$activeRecord->Amount = $activeRecord->IsPercent ? TPropertyValue::ensureFloat($this->txtAmount->SafeText/100) : TPropertyValue::ensureFloat($this->txtAmount->SafeText);
		$activeRecord->IsPercent = $this->radIsPercent->SelectedValue;
		$activeRecord->IsPublished = $this->radPublish->SelectedValue;
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
				$msg = $this->Application->getModule("message")->translate(($activeRecord->ID>0 ? "UPDATE_SUCCESS" : "ADD_SUCCESS"),"Coupon",$activeRecord->Code);
				$activeRecord->save();
				$this->Response->redirect($this->Service->ConstructUrl("admincp.CouponManager",array("action"=>$action, "msg"=>$msg)));
			}
			catch(TException $e)
			{
				$this->Notice->Type = AdminNoticeType::Error;
				$this->Notice->Text = $this->Application->getModule("message")->translate(($activeRecord->ID>0 ? "UPDATE_FAILED" : "ADD_FAILED"),"Coupon",$activeRecord->Code);
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
				$this->Response->redirect($this->Service->ConstructUrl("admincp.CouponForm"));
			}
			catch(TException $e)
			{
				$this->Notice->Type = AdminNoticeType::Error;
				$this->Notice->Text = $this->Application->getModule("message")->translate(($activeRecord->ID>0 ? "UPDATE_FAILED" : "ADD_FAILED"),"Coupon",$activeRecord->Code);
			}
		}
	}

	protected function uniqueCheck_ServerValidated($sender, $param)
	{
		if ($param->Value != '')
		{
			$criteria = new TActiveRecordCriteria;
			$criteria->Condition = "coupon_code = :name";
			$criteria->Parameters[":name"] = $param->Value;
			$activeRecord = $this->getItem();
			if ($activeRecord && $activeRecord->ID > 0) $criteria->Condition .= " and coupon_id <> '".$activeRecord->ID."'";
			$param->IsValid = count(Prado::createComponent(self::AR)->finder()->find($criteria)) == 0;
		}
	}
}

?>