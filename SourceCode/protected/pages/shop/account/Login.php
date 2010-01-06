<?php

class Login extends TPage
{
	public function onLoad($param)
	{
		if (!$this->Application->User->IsGuest)
			$this->Response->redirect($this->Service->ConstructUrl("shop.Index"));
		$this->ClientScript->registerDefaultButton($this->txtEmail,$this->btnLogin);
		$this->ClientScript->registerDefaultButton($this->txtPassword,$this->btnLogin);
		parent::onLoad($param);
	}
	public function btnLogin_Clicked($sender, $param)
	{
		if ($this->IsValid)
		{
			$auth = $this->Application->getModule('auth');
			$mail = $this->txtEmail->Text;
			$pass = $this->txtPassword->Text;
			if ($auth->login($mail, $pass))
			{
				$url = $auth->ReturnUrl;
				if (empty($url))
					$url = $this->Service->ConstructUrl("shop.Index");
				$this->Response->redirect($url);
			}
			else
			{
				$this->Notice->Type = UserNoticeType::Error;
				$this->Notice->Text = $this->Application->getModule('message')->translate('USER_LOGIN_FAILED');
			}
		}
	}
}

?>