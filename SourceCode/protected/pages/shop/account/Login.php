<?php

class Login extends TPage
{
	public function onLoad($param)
	{
		if (!$this->Application->User->IsGuest)
			$this->Response->redirect($this->Service->ConstructUrl("shop.Welcome"));
		$this->ClientScript->registerDefaultButton($this->mainBox,$this->btnLogin);
		parent::onLoad($param);
	}
	public function btnLogin_Clicked($sender, $param)
	{
		if ($this->IsValid)
		{
			$auth = $this->Application->getModule('auth');
			$mail = $this->txtEmail->Text;
			$pass = $this->txtPassword->Text;
			if ($auth->login($mail, $pass, TPropertyValue::ensureInteger($this->Application->Parameters["USER_TIMEOUT"])))
			{
				$url = $auth->ReturnUrl;
				if (empty($url))
					$url = $this->Service->ConstructUrl("shop.Welcome");
				$this->Response->redirect($url);
			}
			else
			{
				$this->Notice->Type = UserNoticeType::Error;
				$this->Notice->Text = $this->Application->getModule('message')->translate('USER_LOGIN_FAILED');
			}
		}
		$this->categoryMenu->populateData();
	}
}

?>