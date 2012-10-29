<?php

class ChangePassword extends TPage
{
	public function onLoad($param)
	{
		parent::onLoad($param);
		$this->ClientScript->registerDefaultButton($this->mainBox,$this->btnSubmit);
	}
	
	protected function btnSubmit_Clicked($sender, $param)
	{
		if ($this->IsValid)
		{
			if (md5($this->txtCurrentPassword->SafeText) == $this->Application->User->Password)
			{
				$user = UserRecord::finder()->findByPk($this->Application->User->ID);
				if ($user instanceof UserRecord)
				{
					$user->Password = md5($this->txtNewPassword->SafeText);
					$user->save();
					
					// send email notice
					$emailer = $this->Application->getModule('mailer');
					$email = $emailer->createNewEmail("ChangePassword");
					$email->HtmlContent->findControl("FULL_NAME")->Text = $user->FirstName.' '.$user->LastName;
					$email->HtmlContent->findControl("NEW_PASSWORD")->Text = $this->txtNewPassword->SafeText;
					$receiver = new TEmailAddress;
					$receiver->Field = TEmailAddressField::Receiver;
					$receiver->Address = $user->Email;
					$receiver->Name = $user->FirstName.' '.$user->LastName;
					$email->getEmailAddresses()->add($receiver);
					try
					{
						$emailer->send($email);
						$this->Application->getModule("auth")->updateSessionUser($this->Application->User->createUser($user->Email));
						$this->Notice->Type = UserNoticeType::Notice;
						$this->Notice->Text = $this->Application->getModule('message')->translate('UPDATE_SUCCESS','Your password','');
						$this->mainBox->Visible=false;
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
					$this->Notice->Text = $this->Application->getModule('message')->translate('ITEM_NOT_FOUND','user');
				}
			}
			else
			{
				$this->Notice->Type = UserNoticeType::Error;
				$this->Notice->Text = $this->Application->getModule('message')->translate('PASSWORD_INCORRECT');
			}
			$this->categoryMenu->populateData();
		}
	}
}

?>