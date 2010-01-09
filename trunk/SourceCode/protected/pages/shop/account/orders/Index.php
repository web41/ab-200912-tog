<?php

class Index extends TPage
{
	public function onLoad($param)
	{
		parent::onLoad($param);
		if (!$this->IsPostBack)
		{
			$this->populateData();
		}
	}
	
	public function populateData()
	{
		$criteria = new TActiveRecordCriteria;
		$criteria->Condition = "order_id > 0 and user_id = :id";
		$criteria->Parameters[":id"] = $this->Application->User->ID;
		$criteria->OrdersBy["c_date"] = "desc";
		$this->rptOrder->DataSource = OrderRecord::finder()->findAll($criteria);
		$this->rptOrder->DataBind();
	}
}

?>