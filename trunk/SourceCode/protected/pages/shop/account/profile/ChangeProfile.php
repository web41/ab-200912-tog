<?php

class ChangeProfile extends TPage
{
	public function onLoad($param)
	{
		parent::onLoad($param);
		$this->ClientScript->registerDefaultButton($this->mainBox,$this->btnSubmit);
		if (!$this->IsPostBack)
		{
			$this->lblEmail->Text = $this->Application->User->Email;
			$this->txtFirstName->Text = $this->Application->User->FirstName;
			$this->txtLastName->Text = $this->Application->User->LastName;
		}
	}
	
	protected function btnSubmit_Clicked($sender, $param)
	{
		if ($this->IsValid)
		{
			if (md5($this->txtPassword->SafeText) == $this->Application->User->Password)
			{
				$user = UserRecord::finder()->findByPk($this->Application->User->ID);
				if ($user instanceof UserRecord)
				{
					$user->FirstName = $this->txtFirstName->SafeText;
					$user->LastName = $this->txtLastName->SafeText;
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
				$this->Notice->Text = $this->Application->getModule('message')->translate('ITEM_INVALID','Password');
			}
			$this->categoryMenu->populateData();
		}
	}
}

?>