<?php

class UserForm extends TPage
{
	const AR = "UserRecord";
	public function onLoad($param)
	{
		parent::onLoad($param);
		if (!$this->IsPostBack)
		{
			$criteria = new TActiveRecordCriteria;
			$criteria->Condition = "user_type_id > 0";
			$criteria->OrdersBy["user_type_name"] = "asc";
			$this->cboTypeSelector->DataSource = UserTypeRecord::finder()->findAll($criteria);
			$this->cboTypeSelector->DataBind();
			$this->cboStatusSelector->DataSource = TPropertyValue::ensureArray($this->Application->Parameters["USER_STATUS"]);
			$this->cboStatusSelector->DataBind();
			$activeRecord = $this->getItem();
			if ($activeRecord && $activeRecord->ID > 0)
			{
				// Populates the input controls with the existing post data
				$this->lblHeader->Text = "Update user: ".$activeRecord->Email;
				$this->txtEmail->Text = $activeRecord->Email;
				$this->txtPassword->Text = $activeRecord->Password;
				$this->cboTypeSelector->SelectedValue = $activeRecord->UserTypeID;
				$this->cboStatusSelector->SelectedValue = $activeRecord->Status;
				$this->txtCredits->Text = $activeRecord->Credits;
				$this->txtIPAddress->Text = $activeRecord->IPAddress;
				$this->lblLastVisitDate->Text = ($activeRecord->LastVisitDate>0) ? date('m/d/Y',$activeRecord->LastVisitDate) : '--';
				$this->lblLastVisitIP->Text = $activeRecord->LastVisitIP;
			}
			else
			{
				$this->lblHeader->Text = "Add new user";
			}
		}
	}

	protected function getItem()
	{
		if ($this->Request->Contains("id") && $this->Request->Contains("alias"))
		{
			// use Active Record to look for the specified post ID
			$activeRecord = Prado::createComponent(self::AR)->finder()->findByuser_idAnduser_email(TPropertyValue::ensureInteger($this->Request['id']), $this->Request['alias']);
			if($activeRecord === null)
			{
				$this->Notice->Type = AdminNoticeType::Error;
				$this->Notice->Text = $this->Application->getModule("message")->translate("ITEM_NOT_FOUND","user");
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
		$activeRecord->Email = $activeRecord->Username = $this->txtEmail->SafeText;
		$activeRecord->Password = ($activeRecord->Password != $this->txtPassword->SafeText) ? md5($this->txtPassword->SafeText) : $activeRecord->Password;
		$activeRecord->UserTypeID = $this->cboTypeSelector->SelectedValue;
		$activeRecord->Status = $this->cboStatusSelector->SelectedValue;
		$activeRecord->Credits = TPropertyValue::ensureInteger($this->txtCredits->SafeText);
		$activeRecord->IPAddress = $this->txtIPAddress->SafeText;

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
				$msg = $this->Application->getModule("message")->translate(($activeRecord->ID>0 ? "UPDATE_SUCCESS" : "ADD_SUCCESS"),"User",$activeRecord->Email);
				$activeRecord->save();
				$this->Response->redirect($this->Service->ConstructUrl("admincp.UserManager",array("action"=>$action, "msg"=>$msg)));
			}
			catch(TException $e)
			{
				$this->Notice->Type = AdminNoticeType::Error;
				$this->Notice->Text = $this->Application->getModule("message")->translate(($activeRecord->ID>0 ? "UPDATE_FAILED" : "ADD_FAILED"),"User",$activeRecord->Email);
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
				$this->Response->redirect($this->Service->ConstructUrl("admincp.UserForm"));
			}
			catch(TException $e)
			{
				$this->Notice->Type = AdminNoticeType::Error;
				$this->Notice->Text = $this->Application->getModule("message")->translate(($activeRecord->ID>0 ? "UPDATE_FAILED" : "ADD_FAILED"),"User",$activeRecord->Email);
			}
		}
	}

	protected function uniqueCheck_ServerValidated($sender, $param)
	{
		if ($param->Value != '')
		{
			$criteria = new TActiveRecordCriteria;
			$criteria->Condition = "user_email = :name";
			$criteria->Parameters[":name"] = $param->Value;
			$activeRecord = $this->getItem();
			if ($activeRecord && $activeRecord->ID > 0) $criteria->Condition .= " and user_id <> '".$activeRecord->ID."'";
			$param->IsValid = count(Prado::createComponent(self::AR)->finder()->find($criteria)) == 0;
		}
	}
}

?>