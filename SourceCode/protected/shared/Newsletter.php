<?php

class Newsletter extends TTemplateControl
{
	protected function btnSubmit_Clicked($sender, $param)
	{
		if ($this->Page->IsValid)
		{
			// insert the email into mailing list table
			$activeRecord = new MailingListRecord;
			$activeRecord->Address = $this->txtEmail->SafeText;
			$activeRecord->UserID = $this->Application->User->IsGuest ? 0 : $this->Application->User->ID;
			try
			{	
				$activeRecord->save();
				$this->txtEmail->Text = "Enter your email";
				if ($this->Page->Notice)
				{
					$this->Page->Notice->Type = UserNoticeType::Notice;
					$this->Page->Notice->Text = $this->Application->getModule('message')->translate('NEWSLETTER_REG_SUCCESS');
				}
			}
			catch(TException $e)
			{
				if ($this->Page->Notice)
				{
					$this->Page->Notice->Type = UserNoticeType::Error;
					$this->Page->Notice->Text = $this->Application->getModule('message')->translate('NEWSLETTER_REG_FAILED');
				}
			}
			$this->Page->categoryMenu->populateData();
			$this->Page->populateData();
		}
	}
	protected function uniqueCheck_ServerValidated($sender, $param)
	{
		if ($param->Value != '')
		{
			$criteria = new TActiveRecordCriteria;
			$criteria->Condition = "mailing_address = :name";
			$criteria->Parameters[":name"] = $param->Value;
			$param->IsValid = count(MailingListRecord::finder()->find($criteria)) == 0;
		}
		$this->Page->categoryMenu->populateData();
		$this->Page->populateData();
	}
}

?>