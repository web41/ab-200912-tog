<?php

class CategoryMenu extends TTemplateControl
{
	public function onLoad($param)
	{
		parent::onLoad($param);
		$criteria = new TActiveRecordCriteria;
		$criteria->Condition = "cat_id > 0 and parent_id = 0 and cat_publish = 1";
		$criteria->OrdersBy["cat_order"] = "asc";
		$this->rptCategoryMenu->DataSource = CategoryRecord::finder()->withParent()->findAll($criteria);
		$this->rptCategoryMenu->DataBind();
	}
	
	protected function rptCategoryMenu_ItemCreated($sender, $param)
	{
		if ($param->Item->ItemType == "Item" || $param->Item->ItemType == "AlternatingItem")
		{
			$criteria = new TActiveRecordCriteria;
			$criteria->Condition = "cat_id > 0 and parent_id = :id and cat_publish = 1";
			$criteria->Parameters[":id"] = $param->Item->Data->ID;
			$criteria->OrdersBy["cat_name"] = "asc";
			$param->Item->ChildCategory->DataSource = CategoryRecord::finder()->findAll($criteria);
			$param->Item->ChildCategory->DataBind();
		}
	}
}

?>