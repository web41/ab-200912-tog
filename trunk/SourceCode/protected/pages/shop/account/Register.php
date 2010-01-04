<?php

class Register extends TPage
{
	public function onLoad($param)
	{
		if (!$this->Application->User->IsGuest)
				$this->Response->redirect($this->Service->ConstructUrl("shop.Index"));
			parent::onLoad($param);
	}
	
	protected function btnSubmit_Clicked($sender, $param)
	{
		if ($this->IsValid)
		{
			$activeRecord = new UserRecord;
			$activeRecord->Username = $activeRecord->Email = $this->txtEmail->SafeText;
			$activeRecord->Password = md5($this->txtPassword->SafeText);
			$activeRecord->UserTypeID = 3; // Type = 3 Registered User - this must be changed manually whenever updating user type data
			$activeRecord->Credits = 0; // default credits
			$activeRecord->Status = 1; // status: -1 inactivated, 0 waiting for activation, 1 activated
			$activeRecord->IPAddress = $this->Request->UserHostAddress;
			$activeRecord->LastVisitIP = '';
			$activeRecord->LastVisitDate = 0;
			$activeRecord->CreateDate = $activeRecord->ModifyDate = time();
			
			try
			{
				$activeRecord->save();
				// if register successsful, send email
				$emailer = $this->Application->getModule('mailer');
				$email = $emailer->createNewEmail("RegisterSuccess");
				$email->HtmlContent->findControl("SENDER_USERNAME")->Text = $email->HtmlContent->findControl("SENDER_USERNAME2")->Text = $this->txtEmail->SafeText;
				$email->HtmlContent->findControl("SENDER_NAME")->Text = $this->txtPassword->SafeText;
				$receiver = new TEmailAddress;
				$receiver->Field = TEmailAddressField::Receiver;
				$receiver->Address = $this->txtEmail->SafeText;
				$receiver->Name = $this->txtEmail->SafeText;
				$email->getEmailAddresses()->add($receiver);
				$emailer->send($email);
				$this->Application->getModule("auth")->login($this->txtEmail->SafeText,$this->txtPassword->SafeText);
				$this->Response->redirect($this->Service->ConstructUrl("shop.Index"));
			}
			catch(TException $e)
			{
				$this->Notice->Type = UserNoticeType::Error;
				$this->Notice->Text = $this->Application->getModule('message')->translate('REGISTER_FAILED');
			}
		}
	}
	
	protected function uniqueCheck_ServerValidated($sender, $param)
	{
		if ($param->Value != '')
		{
			$criteria = new TActiveRecordCriteria;
			$criteria->Condition = "user_email = :name or user_name = :name";
			$criteria->Parameters[":name"] = $param->Value;
			$param->IsValid = !(UserRecord::finder()->find($criteria) instanceof UserRecord);
		}
	}
}

?>