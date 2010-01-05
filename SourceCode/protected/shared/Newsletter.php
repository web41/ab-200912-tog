<?php

class Newsletter extends TTemplateControl
{
	protected function btnSubmit_Clicked($sender, $param)
	{
		if ($this->Page->IsValid)
		{
			// insert the email into mailing list table
			
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
	}
}

?>