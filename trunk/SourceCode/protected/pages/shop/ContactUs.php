<?php

class ContactUs extends TPage
{
	public function onLoad($param)
	{
		parent::onLoad($param);
	}
	protected function submitButton_Clicked($sender, $param)
	{
		$emailer = $this->Application->getModule('mailer');
		$email = $emailer->createNewEmail("ContactForm");
		$email->HtmlContent->findControl("SENDER_EMAIL")->Text = $email->HtmlContent->findControl("SENDER_EMAIL2")->Text = $this->txtEmail->SafeText;
		$email->HtmlContent->findControl("SENDER_NAME")->Text = $this->txtName->SafeText;
		$email->HtmlContent->findControl("SENDER_PHONE")->Text = $this->txtPhone->SafeText;
		$email->HtmlContent->findControl("INQUIRY_CONTENT")->Text = $this->txtMessage->SafeText;
		try
		{
			$emailer->send($email);
			//$this->Response->redirect($this->Service->ConstructUrl("home.ThankYou",array("ref"=>"contact-us")));
			$this->mainContent->Visible = false;
			$this->sentSuccess->Visible = true;
		}
		catch(TException $ex)
		{
			//throw new THttpException(500, $ex->ErrorMessage);
			$this->Notice->Type = UserNoticeType::Error;
			$this->Notice->Text = $this->Application->getModule('message')->translate('UNKNOWN_ERROR');
			$this->mainBox->Visible = false;
		}
	}
}

?>