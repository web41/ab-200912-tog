<?php
Prado::using('System.I18N.core.NumberFormat');
class MyFavourite extends TPage
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
		$criteria->Condition = "item_id in (SELECT item_id FROM tbl_order_item oi
								JOIN tbl_order o ON oi.order_id = o.order_id
								WHERE o.user_id = :user)";
		$criteria->Parameters[":user"] = $this->Application->User->ID;
		$criteria->OrdersBy["quantity"] = "desc";
		$criteria->OrdersBy["c_date"] = "desc";
		$criteria->Limit = 10;
		
		$this->rptMyFavourite->DataSource = OrderItemRecord::finder()->withProduct()->findAll($criteria);
		$this->rptMyFavourite->DataBind();
	}
	
	protected function rptMyFavourite_ItemCreated($sender, $param)
	{
		if ($param->Item->ItemType == "Item" || $param->Item->ItemType == "AlternatingItem")
		{
			if ($param->Item->DataItem)
			{
				$prop = $param->Item->DataItem->Property;
				$param->Item->lblPrice->Text = $this->getFormattedValue($prop->Price);
				$param->Item->lblPrice->Visible = $prop->Product->DiscountID > 0;
				$param->Item->lblDiscountPrice->Text = $this->getFormattedValue($prop->Product->getDiscountPrice($prop->Price));
			}
		}
	}
	
	public function getFormattedValue($value,$pattern="c",$currency="USD")
	{
		$formatter = new NumberFormat($this->Application->Globalization->Culture);
		return $formatter->format($value,$pattern,$currency,$this->Application->Globalization->Charset);
	}
}

?>