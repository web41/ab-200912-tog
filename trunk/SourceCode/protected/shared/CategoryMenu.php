<?php

class CategoryMenu extends TTemplateControl
{
	public function onLoad($param)
	{
		parent::onLoad($param);
		$criteria = new TActiveRecordCriteria;
		$criteria->Condition = "cat_id > 0 and parent_id = 0 and cat_publish = 1";
		$criteria->OrdersBy["cat_order"] = "asc";
		$this->rptCategoryMenu->DataSource = CategoryRecord::finder()->findAll($criteria);
		$this->rptCategoryMenu->DataBind();
	}
}

?>