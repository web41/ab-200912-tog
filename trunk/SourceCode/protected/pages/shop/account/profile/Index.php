<?php

class Index extends TPage
{
	public function onLoad($param) {
		param::onLoad($param);
		if (!$this->IsPostBack) {
			if ($this->Request->contains('success')&&$this->Request->contains('sucess')==1) {
				$this->Notice->Type = UserNoticeType::Notice;
				$this->Notice->Text = $this->Application->getModule('message')->translate('UPDATE_SUCCESS','Your account','');
			}
		}
	}
}

?>