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
					$this->Application->getModule("auth")->updateSessionUser($this->Application->User->createUser($user->Email));
					$this->Response->redirect($this->Service->ConstructUrl("shop.account.profile.Index"));
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