<?php

class RightColumn extends TTemplateControl
{
	protected function btnLogout_Clicked($sender, $param)
	{
		$this->Application->getModule("auth")->logout();
		$this->Response->redirect($this->Service->ConstructUrl("shop.Index"));
	}
}

?>