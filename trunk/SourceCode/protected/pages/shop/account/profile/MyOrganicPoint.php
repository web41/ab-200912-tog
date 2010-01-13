<?php

class MyOrganicPoint extends TPage
{
	public function onLoad($param)
	{
		parent::onLoad($param);
		if (!$this->IsPostBack)
		{
			$activeRecord = $this->getUser();
			$this->lblCredits->Text = $activeRecord->Credits;
			$this->lblCreditsUsed->Text = $activeRecord->CreditsUsed;
			$this->lblCreditsBalance->Text = $activeRecord->Credits - $activeRecord->CreditsUsed;
		}
	}
	
	public function getUser()
	{
		return UserRecord::finder()->findByPk($this->Application->User->ID);
	}
}

?>