<?php

class UserState extends TTemplateControl
{
	public function onLoad($param)
	{
		parent::onLoad($param);
		if ($this->Application->User->IsGuest)
		{
			$this->btnLogout->Visible = false;
		}
		else
		{
			$this->lblName->Text = $this->Application->User->FirstName." ".$this->Application->User->LastName;
			$this->btnLogout->Visible = true;
			$this->GuestPane->Visible = false;
			$this->userMenu->Visible = true;
		}
	}
	
	protected function btnLogout_Clicked($sender, $param)
	{
		$this->Application->getModule("auth")->logout();
		$this->Response->redirect($this->Service->ConstructUrl("shop.Index"));
	}
}

?>