<?php

Prado::using('System.I18N.core.NumberFormat');
class OrderItemForm extends TPage {
	
	const AR = "OrderItemRecord";
	public function onLoad($param) {
		parent::onLoad($param);
		
		if (!$this->IsPostBack) {
			$criteria = new TActiveRecordCriteria;
			$criteria->OrdersBy['brand_id'] = 'asc';
			$criteria->OrdersBy['product_name'] = 'asc';
			$criteria->Condition = 'product_id > 0 AND product_publish = 1';
			$this->cboProductSelector->DataSource = ProductRecord::finder()->findAll($criteria);
			$this->cboProductSelector->DataBind();
			$activeRecord = $this->getItem();
			if ($activeRecord && $activeRecord->ID > 0) {
				// Populates the input controls with the existing post data
				$this->lblHeader->Text = "Update order item";
				$this->cboProductSelector->SelectedValue = $activeRecord->ProductID;
				$this->cboProductSelector_CallBack($this->cboProductSelector,null);
				$this->cboPropertySelector->SelectedValue = $activeRecord->PropertyID;
				$this->txtBrand->Text = $activeRecord->Product->Brand->Name;
				$this->txtSupplier->Text = $activeRecord->Product->Manufacturer->Name;
				$this->txtQuantity->Text = $activeRecord->Quantity;
			}
			else {
				$this->lblHeader->Text = "Add new order item";
			}
		}
	}
	
	protected function getOrder() {
		$order = null;
		if ($this->Request->Contains("ordernum")) {
			$order = OrderRecord::finder()->findByorder_num($this->Request['ordernum']);
			if($order === null)
			{
				$this->Notice->Type = AdminNoticeType::Error;
				$this->Notice->Text = $this->Application->getModule("message")->translate("ITEM_NOT_FOUND","order");
				$this->mainBox->Visible = false;
			}
		}
		else {
			$this->Notice->Type = AdminNoticeType::Error;
			$this->Notice->Text = $this->Application->getModule("message")->translate("ITEM_NOT_FOUND","order");
			$this->mainBox->Visible = false;
		}
		return $order;
	}
	
	protected function getItem()
	{
		if ($this->Request->Contains("id") && $this->Request->Contains("ordernum"))
		{
			// use Active Record to look for the specified post ID
			$activeRecord = Prado::createComponent(self::AR)->finder()->findByPk(TPropertyValue::ensureInteger($this->Request['id']));
			if($activeRecord === null)
			{
				$this->Notice->Type = AdminNoticeType::Error;
				$this->Notice->Text = $this->Application->getModule("message")->translate("ITEM_NOT_FOUND","order item");
				$this->mainBox->Visible = false;
			}
			return $activeRecord;
		}
		else
		{
			return Prado::createComponent(self::AR);
		}
	}
	
	private function bindItem()
	{
		$order = $this->getOrder();
		$activeRecord = $this->getItem();
		$activeRecord->OrderID = $order->ID;
		$activeRecord->ProductID = $this->cboProductSelector->SelectedValue;
		$activeRecord->PropertyID = $this->cboPropertySelector->SelectedValue;
		$activeRecord->Quantity = TPropertyValue::ensureInteger($this->txtQuantity->Text);
		$product = ProductRecord::finder()->withDiscount()->findByPk($activeRecord->ProductID);
		if ($product->Discount instanceof DiscountRecord)
		{
			$activeRecord->DiscountName = $product->Discount->Name;
			$activeRecord->DiscountAmount = $product->Discount->Amount;
			$activeRecord->DiscountIsPercent = $product->Discount->IsPercent;
		}
		$prop = PropertyRecord::finder()->findByPk($activeRecord->PropertyID);
		if ($prop instanceof PropertyRecord)
		{
			$activeRecord->UnitPrice = $prop->Price;
		}
		$activeRecord->Subtotal = $activeRecord->UnitPrice * $activeRecord->Quantity;
		return $activeRecord;
	}
	
	protected function btnSubmit_Clicked($sender, $param) {
		if ($this->IsValid) {
			$activeRecord = $this->bindItem();
			try {
				$activeRecord->save();
				$orderItems = OrderItemRecord::finder()->findAllByorder_id($activeRecord->OrderID);
				$order = $this->getOrder();
				$subtotal = 0;
				foreach($orderItems as $orderItem) {
					$subtotal += $orderItem->Subtotal;
				}
				$order->Subtotal = $subtotal;
				if ($order->Subtotal < 100) {
					$shippingMethod = ShippingMethodRecord::finder()->findByPk(6);
					if ($shippingMethod instanceof ShippingMethodRecord) {
						$order->ShippingMethodID = $shippingMethod->ID;
						$order->ShippingAmount = $shippingMethod->Price; 
					}
				}
				else {
					$order->ShippingMethodID = 0;
					$order->ShippingAmount = 0;
				}
				$order->Total = $order->Subtotal-$order->CouponAmount-$order->RewardPointsRebate+$order->ShippingAmount+$order->TaxAmount;
				$order->save();
				$url = $this->Service->ConstructUrl("admincp.OrderForm",array("id"=>$order->ID, "num"=>$order->Num));
				$this->Response->redirect($url);
			}
			catch(TException $e) {
				$this->Notice->Type = AdminNoticeType::Error;
				$this->Notice->Text = $this->Application->getModule("message")->translate(($activeRecord->ID>0 ? "UPDATE_FAILED" : "ADD_FAILED"),"Brand",$activeRecord->Name);
			}
		}
	}
	
	protected function cboProductSelector_CallBack($sender, $param) {
		$product = ProductRecord::finder()->withBrand()->withManufacturer()->findByPk($sender->SelectedValue);
		$this->cboPropertySelector->Items->clear();
		foreach($product->Properties as $prop)
		{
			$item = new TListItem;
			$item->Text = $prop->Name." ".$this->getFormattedValue(Common::roundTo($prop->Price));
			$item->Value = $prop->ID;
			$this->cboPropertySelector->Items->add($item);
		}
		$this->txtBrand->Text = $product->Brand->Name;
		$this->txtSupplier->Text = $product->Manufacturer->Name;
	}
	
	public function getFormattedValue($value,$pattern="c",$currency="USD")
	{
		$formatter = new NumberFormat($this->Application->Globalization->Culture);
		return $formatter->format($value,$pattern,$currency,$this->Application->Globalization->Charset);
	}
}