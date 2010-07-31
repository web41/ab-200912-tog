<?php

class ForgotPwd extends TPage
{
	public function onLoad($param) {
		parent::onLoad($param);
		$this->MainContent->DefaultButton = "btnSendPassword";
	}
	protected function btnSendPassword_Clicked($sender, $param)
	{
		$user = UserRecord::finder()->findByuser_email($this->txtEmail->Text);
		if ($user instanceof UserRecord)
		{
			// do reset password
			$new_pass = uniqid();
			$user->Password = md5($new_pass);
			$user->save();
			
			// send email notice
			$emailer = $this->Application->getModule('mailer');
			$email = $emailer->createNewEmail("SendNewPassword");
			$email->HtmlContent->findControl("FULL_NAME")->Text = $user->FirstName.' '.$user->LastName;
			$email->HtmlContent->findControl("NEW_PASSWORD")->Text = $new_pass;
			$receiver = new TEmailAddress;
			$receiver->Field = TEmailAddressField::Receiver;
			$receiver->Address = $this->txtEmail->SafeText;
			$receiver->Name = $this->txtEmail->SafeText;
			$email->getEmailAddresses()->add($receiver);
			try
			{
				$emailer->send($email);
				//$this->Application->getModule("auth")->login($this->txtEmail->SafeText,$this->txtPassword->SafeText);
				//$this->Response->redirect($this->Service->ConstructUrl("shop.Index"));
				$this->Notice->Type = UserNoticeType::Notice;
				$this->Notice->Text = $this->Application->getModule('message')->translate("NEW_PASSWORD_SENT");
				$this->MainContent->Visible = false;
			}
			catch(TException $e)
			{
				$this->Notice->Type = UserNoticeType::Error;
				$this->Notice->Text = $this->Application->getModule('message')->translate("UNKNOWN_ERROR");
			}
		}
		else
		{
			$this->Notice->Type = UserNoticeType::Error;
			$this->Notice->Text = $this->Application->getModule('message')->translate('ITEM_NOT_EXISTS',"email address");
		}
		$this->categoryMenu->populateData();
	}
}

?>