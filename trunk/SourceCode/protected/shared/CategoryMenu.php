<?php

class CategoryMenu extends TTemplateControl
{
	public function onLoad($param)
	{
		parent::onLoad($param);
		if (!$this->Page->IsPostBack)
		{
			$this->populateData();
		}
	}
	
	protected function rptCategoryMenu_ItemCreated($sender, $param)
	{
		if (($param->Item->ItemType == "Item" || $param->Item->ItemType == "AlternatingItem") && $param->Item->Data instanceof CategoryRecord)
		{
			$criteria = new TActiveRecordCriteria;
			$criteria->Condition = "cat_id > 0 and parent_id = :id and cat_publish = 1";
			$criteria->Parameters[":id"] = $param->Item->Data->ID;
			$criteria->OrdersBy["cat_order"] = "asc";
			$param->Item->ChildCategory->DataSource = CategoryRecord::finder()->findAll($criteria);
			$param->Item->ChildCategory->DataBind();
		}
	}
	
	public function populateData()
	{
		$criteria = new TActiveRecordCriteria;
		$criteria->Condition = "cat_id > 0 and parent_id = 0 and cat_publish = 1";
		$criteria->OrdersBy["cat_order"] = "asc";
		$this->rptCategoryMenu->DataSource = CategoryRecord::finder()->findAll($criteria);
		$this->rptCategoryMenu->DataBind();
	}
}

?>