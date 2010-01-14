<?php

class OrderStatusForm extends TPage
{
	const AR = "OrderStatusRecord";
	public function onLoad($param)
	{
		parent::onLoad($param);
		if (!$this->IsPostBack)
		{
			$activeRecord = $this->getItem();
			if ($activeRecord && $activeRecord->ID > 0)
			{
				// Populates the input controls with the existing post data
				$this->lblHeader->Text = "Update order status: ".$activeRecord->Name;
				$this->txtName->Text = $activeRecord->Name;
				$this->txtAlias->Text = $activeRecord->Alias;
				$this->txtCode->Text = $activeRecord->Code;
				$this->txtDesc->Text = $activeRecord->Description;
			}
			else
			{
				$this->lblHeader->Text = "Add new order status";
			}
		}
	}

	protected function getItem()
	{
		if ($this->Request->Contains("id") && $this->Request->Contains("alias"))
		{
			// use Active Record to look for the specified post ID
			$activeRecord = Prado::createComponent(self::AR)->finder()->findBystatus_idAndstatus_alias(TPropertyValue::ensureInteger($this->Request['id']), $this->Request['alias']);
			if($activeRecord === null)
			{
				$this->Notice->Type = AdminNoticeType::Error;
				$this->Notice->Text = $this->Application->getModule("message")->translate("ITEM_NOT_FOUND","order status");
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
		$activeRecord->Code = $this->txtCode->SafeText;
		$activeRecord->Description = $this->txtDesc->Text;

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
				$msg = $this->Application->getModule("message")->translate(($activeRecord->ID>0 ? "UPDATE_SUCCESS" : "ADD_SUCCESS"),"Order status",$activeRecord->Name);
				$activeRecord->save();
				$this->Response->redirect($this->Service->ConstructUrl("admincp.OrderStatusManager",array("action"=>$action, "msg"=>$msg)));
			}
			catch(TException $e)
			{
				$this->Notice->Type = AdminNoticeType::Error;
				$this->Notice->Text = $this->Application->getModule("message")->translate(($activeRecord->ID>0 ? "UPDATE_FAILED" : "ADD_FAILED"),"Order status",$activeRecord->Name);
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
				$this->Response->redirect($this->Service->ConstructUrl("admincp.OrderStatusForm"));
			}
			catch(TException $e)
			{
				$this->Notice->Type = AdminNoticeType::Error;
				$this->Notice->Text = $this->Application->getModule("message")->translate(($activeRecord->ID>0 ? "UPDATE_FAILED" : "ADD_FAILED"),"Order status",$activeRecord->Name);
			}
		}
	}

	protected function uniqueCheck_ServerValidated($sender, $param)
	{
		if ($param->Value != '')
		{
			$criteria = new TActiveRecordCriteria;
			$criteria->Condition = "status_name = :name";
			$criteria->Parameters[":name"] = $param->Value;
			$activeRecord = $this->getItem();
			if ($activeRecord && $activeRecord->ID > 0) $criteria->Condition .= " and status_id <> '".$activeRecord->ID."'";
			$param->IsValid = count(Prado::createComponent(self::AR)->finder()->find($criteria)) == 0;
		}
	}
	
	protected function uniqueCheck2_ServerValidated($sender, $param)
	{
		if ($param->Value != '')
		{
			$criteria = new TActiveRecordCriteria;
			$criteria->Condition = "status_code = :name";
			$criteria->Parameters[":name"] = $param->Value;
			$activeRecord = $this->getItem();
			if ($activeRecord && $activeRecord->ID > 0) $criteria->Condition .= " and status_id <> '".$activeRecord->ID."'";
			$param->IsValid = count(Prado::createComponent(self::AR)->finder()->find($criteria)) == 0;
		}
	}
}

?>