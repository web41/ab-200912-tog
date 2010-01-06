<?php

class StatisticsPane extends TTemplateControl
{
	public function onLoad($param)
	{
		parent::onLoad($param);
		if (!$this->Page->IsPostBack)
		{
			$this->lblTotalUser->Text = $this->getTotalUser();
			$this->lblTotalInStock->Text = $this->getTotalProduct();
			$this->lblTodayPurchases->Text = $this->getTodayPurchases();
			$this->lblOnlineUser->Text = $this->getCurrentUser();
		}
	}
	
	public function getTotalUser()
	{
		$criteria = new TActiveRecordCriteria;
		$criteria->Condition = "user_id > 0";
		return UserRecord::finder()->count($criteria);
	}
	
	public function getTotalProduct($publishedOnly=false)
	{
		$counter = 0;
		$criteria = new TActiveRecordCriteria;
		$criteria->Condition = "product_id > 0";
		if ($publishedOnly) $criteria->Condition .= " and product_publish = 1";
		$products = ProductRecord::finder()->withProperties()->findAll($criteria);
		foreach($products as $product)
		{
			if (count($product->Properties)>0)
			{
				foreach($product->Properties as $prop)
				{
					$counter += $prop->InStock;
				}
			}
		}
		return $counter;
	}
	
	public function getTodayPurchases()
	{
		$first_date = mktime(0, 0, 0, date("m") , date("d"), date("Y")); 
		$last_date = mktime(23, 59, 59, date("m") , date("d"), date("Y"));
		$criteria = new TActiveRecordCriteria;
		$criteria->Condition = "c_date >= {$first_date} and c_date <= {$last_date}";
		return OrderRecord::finder()->count($criteria);
	}
	
	public function getCurrentUser()
	{
		$past_time = mktime(2, 0, 0, date("m") , date("d"), date("Y"));
		$criteria = new TActiveRecordCriteria;
		$criteria->Condition = "last_visit_date >= {$past_time} and last_visit_date <= ".time();
		return UserRecord::finder()->count($criteria);
	}
}

?>