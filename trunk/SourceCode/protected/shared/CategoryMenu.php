<?php

class CategoryMenu extends TTemplateControl
{
	public function onLoad($param)
	{
		parent::onLoad($param);
		$this->rptCategoryMenu->DataSource = CategoryRecord::getAllParent();
		$this->rptCategoryMenu->DataBind();
	}
}

?>