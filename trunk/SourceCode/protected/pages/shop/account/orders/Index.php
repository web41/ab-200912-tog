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
	
	public function generateHash($oid,$onum)
	{
		$oid = base64_encode($this->Application->SecurityManager->hashData($oid));
		$onum = base64_encode($this->Application->SecurityManager->hashData($onum));
		$hash = array($oid,$onum);
		return $this->Application->SecurityManager->hashData(serialize($hash));
	}
}

?>