<?php

class AdminMaster extends TTemplateControl
{
	protected function getUrlPrefix()
	{
		return $this->Request->UrlManagerModule->UrlPrefix;
	}
	
	protected function btnLogout_Clicked($sender, $param)
	{
		$this->Application->getModule("auth")->logout();
		$this->Response->redirect($this->Service->ConstructUrl("shop.account.Login"));
	}
}

?>