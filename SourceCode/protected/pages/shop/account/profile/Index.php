<?php

class Index extends TPage
{
	public function onLoad($param) {
		parent::onLoad($param);
		if (!$this->IsPostBack) {
			if ($this->Request->contains('success')&&$this->Request->contains('success')==1) {
				$this->Notice->Type = UserNoticeType::Notice;
				$this->Notice->Text = $this->Application->getModule('message')->translate('UPDATE_SUCCESS','Your account','');
			}
		}
	}
}

?>