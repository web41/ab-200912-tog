<?php

class StatisticsPane extends TTemplateControl
{
	public function onLoad($param)
	{
		parent::onLoad($param);
		if (!$this->Page->IsPostBack)
		{
			$this->lblTotalUser->Text = $this->getTotalUser();
			$this->lblTotalInStock->Text = $this->getTotalInStock();
			$this->lblTotalProduct->Text = $this->getTotalProduct();
			$this->lblTodayPurchases->Text = $this->getTodayPurchases();
			$this->lblOnlineUser->Text = $this->getCurrentUser();
			$this->lblTotalOrder->Text = $this->getTotalOrder();
			$this->lblTotalPendingOrder->Text = $this->getPendingOrder();
		}
	}
	
	public function getTotalUser()
	{
		$criteria = new TActiveRecordCriteria;
		$criteria->Condition = "user_id > 0";
		return UserRecord::finder()->count($criteria);
	}
	
	public function getTotalInStock($publishedOnly=false)
	{
		$counter = 0;
		$criteria = new TActiveRecordCriteria;
		$criteria->Condition = "product_id > 0";
		if ($publishedOnly) $criteria->Condition .= " and product_publish = 1";
		$products = ProductRecord::finder()->findAll($criteria);
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
	
	public function getTotalProduct($publishedOnly=false)
	{
		$criteria = new TActiveRecordCriteria;
		$criteria->Condition = "product_id > 0";
		if ($publishedOnly) $criteria->Condition .= " and product_publish = 1";
		return ProductRecord::finder()->count($criteria);
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
		$past_time = time()-12*60*60;
		$criteria = new TActiveRecordCriteria;
		$criteria->Condition = "last_visit_date >= {$past_time} and last_visit_date <= ".time();
		$lastVisitUserCount = UserRecord::finder()->count($criteria);
		$currentUserCounter = TPropertyValue::ensureInteger(Prado::getApplication()->getGlobalState("UserOnlineCounter",0));
		if ($currentUserCounter > $lastVisitUserCount)
		{
			$currentUserCounter = $lastVisitUserCount;
			Prado::getApplication()->setGlobalState("UserOnlineCounter",$currentUserCounter);
		}
		return $currentUserCounter;
	}
	
	public function getTotalOrder()
	{
		$criteria = new TActiveRecordCriteria;
		$criteria->Condition = "order_id > 0";
		return OrderRecord::finder()->count($criteria);
	}
	
	public function getPendingOrder()
	{
		$criteria = new TActiveRecordCriteria;
		$criteria->Condition = "order_id in (select o.order_id
								from tbl_order o 
								left join tbl_order_history oh on o.order_id = oh.order_id
								where oh.order_status_code = 'W'
								and o.order_id not in (select order_id from tbl_order_history
								where order_status_code <> 'W'))";
		return OrderRecord::finder()->count($criteria);
	}
}

?>