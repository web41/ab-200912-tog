<?php

class OrderDetail extends TPage
{
	private $_order;
	private $_payment;
	private $_orderItems;
	private $_shippingMethod;
	private $_hash;
	public function onLoad($param)
	{
		parent::onLoad($param);
		if (!$this->IsPostBack)
		{
			$this->setHash($this->Request['hash']);
			$this->setOrder();
			if ($this->Order)
			{
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
	}

	public function populateData()
	{
		$this->rptCart->DataSource = $this->OrderItems;
		$this->rptCart->DataBind();
	}
	
	public function setHash($value)
	{
		if(($value = $this->Application->SecurityManager->validateData($value))!==false)
		{
			$value=unserialize($value);
			if(is_array($value) && count($value)===2)
			{
				list($oid,$onum)=$value;
				$oid = TPropertyValue::ensureInteger($this->Application->SecurityManager->validateData(base64_decode($oid)));
				$onum = $this->Application->SecurityManager->validateData(base64_decode($onum));
				$this->_hash = array('oid'=>$oid,'onum'=>$onum);
			}
		}
	}

	public function getHash()
	{
		return $this->_hash;
	}

	public function setOrder()
	{
		if (isset($this->Hash["oid"]) && isset($this->Hash["onum"]))
		{
			// use Active Record to look for the specified post ID
			$activeRecord = OrderRecord::finder()->withShippingMethod()->withBCountry()->withSCountry()->findByorder_idAndorder_numAnduser_id(TPropertyValue::ensureInteger($this->Hash['oid']), $this->Hash['onum'],$this->Application->User->ID);
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
		if (isset($this->Hash["oid"]))
		{
			// use Active Record to look for the specified post ID
			$activeRecord = PaymentRecord::finder()->withPaymentMethod()->findAllByorder_id(TPropertyValue::ensureInteger($this->Hash['oid']));
			if (count($activeRecord))
			{
				$activeRecord = $activeRecord[0];
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
	
	public function test_Clicked($sender, $param)
	{
		var_dump($this->mainBox->getHtml());
	}
}

?>