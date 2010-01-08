<?php

class Confirmation extends TPage
{
	private $_order;
	private $_payment;
	private $_orderItems;
	private $_shippingMethod;
	public function onLoad($param)
	{
		parent::onLoad($param);
		if (!$this->IsPostBack)
		{
			$this->setOrder();
			$this->setOrderItems();
			$this->setPayment();
			$this->setShippingMethod();
			$this->populateData();
			
			// delete temp data
			$criteria = new TActiveRecordCriteria;
			$criteria->Condition = "session_id = :id and user_id = :user";
			$criteria->Parameters[':id'] = $this->Application->Session->SessionID;
			$criteria->Parameters[':user'] = $this->Application->User->ID;
			CartTempRecord::finder()->deleteAll($criteria);
		}
	}
	
	public function populateData()
	{
		$this->rptCart->DataSource = $this->OrderItems;
		$this->rptCart->DataBind();
	}
	
	public function setOrder()
	{
		if ($this->Request->Contains("oid") && $this->Request->Contains("onum"))
		{
			// use Active Record to look for the specified post ID
			$activeRecord = OrderRecord::finder()->withShippingMethod()->withBCountry()->withSCountry()->findByorder_idAndorder_num(TPropertyValue::ensureInteger($this->Request['oid']), $this->Request['onum']);
			if($activeRecord === null)
			{
				$this->Notice->Type = UserNoticeType::Error;
				$this->Notice->Text = $this->Application->getModule("message")->translate("ITEM_NOT_FOUND","order");
				$this->mainBox->Visible = false;
			}
			$this->_order = $activeRecord;
		}
		else
		{
			$this->Notice->Type = UserNoticeType::Error;
			$this->Notice->Text = $this->Application->getModule("message")->translate("ITEM_NOT_FOUND","order");
			$this->mainBox->Visible = false;
		}
	}
	
	public function getOrder()
	{
		return $this->_order;
	}

	public function setPayment()
	{
		if ($this->Request->Contains("oid") && $this->Request->Contains("pid"))
		{
			// use Active Record to look for the specified post ID
			$activeRecord = PaymentRecord::finder()->withPaymentMethod()->findBypayment_idAndorder_id(TPropertyValue::ensureInteger($this->Request['pid']), TPropertyValue::ensureInteger($this->Request['oid']));
			if($activeRecord === null)
			{
				$this->Notice->Type = UserNoticeType::Error;
				$this->Notice->Text = $this->Application->getModule("message")->translate("ITEM_NOT_FOUND","payment");
				$this->mainBox->Visible = false;
			}
			$this->_payment = $activeRecord;
		}
		else
		{
			$this->Notice->Type = UserNoticeType::Error;
			$this->Notice->Text = $this->Application->getModule("message")->translate("ITEM_NOT_FOUND","payment");
			$this->mainBox->Visible = false;
		}
	}
	
	public function getPayment()
	{
		return $this->_payment;
	}
	
	public function setOrderItems()
	{
		$this->_orderItems = OrderItemRecord::finder()->withProduct()->findAllByorder_id($this->Order->ID);
	}
	
	public function getOrderItems()
	{
		return $this->_orderItems;
	}
	
	public function setShippingMethod()
	{
		$this->_shippingMethod = $this->Order->ShippingMethod;
	}
	
	public function getShippingMethod()
	{
		return $this->_shippingMethod;
	}
	
	public function getFormattedValue($value,$pattern="c",$currency="USD")
	{
		$formatter = new NumberFormat($this->Application->Globalization->Culture);
		return $formatter->format($value,$pattern,$currency,$this->Application->Globalization->Charset);
	}
}

?>