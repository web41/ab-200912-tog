<?php

Prado::using('Application.common.common');
class Review extends TPage
{
	private $_cart;
	private $_billing;
	private $_shipping;
	private $_shippingMethod;
	private $_user;
	
	public function getEstDeliveryDate()
	{
		return TPropertyValue::ensureArray(unserialize($this->getViewState("EstDeliveryDate","")));
	}
	
	public function setEstDeliveryDate($value)
	{
		$this->setViewState("EstDeliveryDate",serialize($value),"");
	}
	
	public function onLoad($param)
	{
		parent::onLoad($param);
		$this->setEstDeliveryDate(OrderRecord::estimateDeliveryDate());
		if (!$this->IsPostBack)
		{
			$this->setCart();
			$cartRecord = $this->getCart();
			if (!$cartRecord)
				$this->Response->redirect($this->Service->ConstructUrl("shop.cart.Index"));
			else
			{
				$this->setBillingAddress();
				$this->setShippingAddress();
				$this->setShippingMethod();
				$this->populateData();
				$this->cboPaymentSelector->DataSource = PaymentMethodRecord::finder()->getAllItems(true);
				$this->cboPaymentSelector->DataBind();
				
				$this->cboCreditsSelector->Items->clear();
				$organicPoints = TPropertyValue::ensureArray($this->Application->Parameters["ORGANIC_POINTS"]);
				foreach($organicPoints as $money=>$point)
				{
					if ($this->User->Credits >= $point)
					{
						$item = new TListItem; $item->Text = $point; $item->Value = $money;
						$this->cboCreditsSelector->Items->add($item);
					}
					else break;
				}
				$this->cboDeliveryDateSelector->Items->clear();
				$slots = TPropertyValue::ensureArray($this->Application->Parameters["DELIVERY_SLOTS"]);
				foreach($this->EstDeliveryDate as $est)
				{
					$item = new TListItem; $item->Text = $item->Value = date("l m/d/Y",$est['day']).' '.$slots[$est['time']];
					$this->cboDeliveryDateSelector->Items->add($item);
				}
			}
		}
	}

	public function populateData()
	{
		$this->rptCart->DataSource = CartTempDetailRecord::finder()->withProduct()->findAllBysession_id($this->Session->SessionID);
		$this->rptCart->DataBind();
		if (count($this->rptCart->Items)<=0)
		{
			$this->Notice->Type = UserNoticeType::Notice;
			$this->Notice->Text = $this->Application->getModule("message")->translate("CART_EMPTY");
		}
	}
	
	public function setBillingAddress()
	{
		$this->_billing = $this->getCart()->BillingAddress;
	}
	
	public function getBillingAddress()
	{
		if (!$this->_billing) $this->setBillingAddress();
		return $this->_billing;
	}
	
	public function setShippingAddress()
	{
		$this->_shipping = $this->getCart()->ShippingAddress;
	}

	public function getShippingAddress()
	{
		if (!$this->_shipping) $this->setShippingAddress();
		return $this->_shipping;
	}
	
	public function setShippingMethod()
	{
		$this->_shippingMethod = $this->getCart()->ShippingMethod;
	}
	
	public function getShippingMethod()
	{
		if (!$this->_shippingMethod) $this->setShippingMethod();
		return $this->_shippingMethod;
	}
	
	public function setCart()
	{
		$this->_cart = CartTempRecord::finder()->withBillingAddress()->withShippingAddress()->withShippingMethod()->withCartTempDetails()->findBysession_idAnduser_id($this->Session->SessionID,$this->Application->User->ID);
	}
	
	public function getCart()
	{
		if (!$this->_cart) $this->setCart();
		return $this->_cart;
	}
	
	public function setUser()
	{
		$this->_user = UserRecord::finder()->findByPk($this->Application->User->ID);
	}

	public function getUser()
	{
		if (!$this->_user) $this->setUser();
		return $this->_user;
	}
	
	public function getFormattedValue($value,$pattern="c",$currency="USD")
	{
		$formatter = new NumberFormat($this->Application->Globalization->Culture);
		return $formatter->format($value,$pattern,$currency,$this->Application->Globalization->Charset);
	}
	
	protected function cboPaymentSelector_CallBack($sender, $param)
	{
		$payment = PaymentMethodRecord::finder()->findByPk($sender->SelectedValue);
		if ($payment instanceof PaymentMethodRecord)
		{
			$this->imgPayment->Visible = true;
			$this->imgPayment->AlternateText = $payment->Name;
			$this->imgPayment->ImageUrl = $this->Request->UrlManagerModule->UrlPrefix."/useruploads/images/payment_method/".$payment->ImagePath;
		}
		else $this->imgPayment->Visible = false;
	}
	
	protected function btnCancel_Clicked($sender, $param)
	{
		if ($this->IsValid)
		{
			$criteria = new TActiveRecordCriteria;
			$criteria->Condition = "session_id = :id and user_id = :user";
			$criteria->Parameters[':id'] = $this->Application->Session->SessionID;
			$criteria->Parameters[':user'] = $this->Application->User->ID;
			CartTempRecord::finder()->deleteAll($criteria);
			$this->Response->redirect($this->Service->ConstructUrl("shop.Index"));
		}
	}
	
	protected function btnSubmit_Clicked($sender, $param)
	{
		if ($this->IsValid)
		{
			// update payment method & create order here
			$cartRecord = $this->getCart();
			if ($cartRecord instanceof CartTempRecord)
			{
				$order = new OrderRecord;
				$order->BFirstName = $cartRecord->BillingAddress->FirstName;
				$order->BLastName = $cartRecord->BillingAddress->LastName;
				$order->BAddress1 = $cartRecord->BillingAddress->Address1;
				$order->BAddress2 = $cartRecord->BillingAddress->Address2;
				$order->BCity = $cartRecord->BillingAddress->City;
				$order->BState = $cartRecord->BillingAddress->State;
				$order->BCountryCode = $cartRecord->BillingAddress->CountryCode;
				$order->BZipCode = $cartRecord->BillingAddress->ZipCode;
				$order->BPhone1 = $cartRecord->BillingAddress->Phone1;
				$order->BPhone2 = $cartRecord->BillingAddress->Phone2;
				$order->BFax = $cartRecord->BillingAddress->Fax;
	
				$order->SFirstName = $cartRecord->ShippingAddress->FirstName;
				$order->SLastName = $cartRecord->ShippingAddress->LastName;
				$order->SAddress1 = $cartRecord->ShippingAddress->Address1;
				$order->SAddress2 = $cartRecord->ShippingAddress->Address2;
				$order->SCity = $cartRecord->ShippingAddress->City;
				$order->SState = $cartRecord->ShippingAddress->State;
				$order->SCountryCode = $cartRecord->ShippingAddress->CountryCode;
				$order->SZipCode = $cartRecord->ShippingAddress->ZipCode;
				$order->SPhone1 = $cartRecord->ShippingAddress->Phone1;
				$order->SPhone2 = $cartRecord->ShippingAddress->Phone2;
				$order->SFax = $cartRecord->ShippingAddress->Fax;
	
				$order->Subtotal = $cartRecord->Subtotal;
				$order->TaxAmount = $cartRecord->TaxAmount;
				$order->ShippingMethodID = $cartRecord->ShippingMethodID;
				$order->ShippingAmount = $cartRecord->ShippingAmount;
				$order->CouponCode = $cartRecord->CouponCode;
				$order->CouponAmount = $cartRecord->CouponAmount;
				$order->RewardPointsRebate = $this->cboCreditsSelector->SelectedValue;//$cartRecord->RewardPointsRebate;
				$order->Total = $order->Subtotal-$order->CouponAmount-$order->RewardPointsRebate+$order->ShippingAmount+$order->TaxAmount;//$cartRecord->Total;
				$order->Currency = "USD";
				$order->IPAddress = $this->Request->UserHostAddress;
				$order->EstDeliveryDate = $this->cboDeliveryDateSelector->SelectedValue;
				$order->Comments = $this->txtComments->SafeText;
				
				try
				{
					$order->save();
					// insert order item
					foreach($cartRecord->CartTempDetails as $cartDetail)
					{
						$orderItem = new OrderItemRecord;
						$orderItem->OrderID = $order->ID;
						$orderItem->ProductID = $cartDetail->ProductID;
						$product = ProductRecord::finder()->withDiscount()->findByPk($orderItem->ProductID);
						if ($product->Discount instanceof DiscountRecord)
						{
							$orderItem->DiscountName = $product->Discount->Name;
							$orderItem->DiscountAmount = $product->Discount->Amount;
							$orderItem->DiscountIsPercent = $product->Discount->IsPercent;
						}
						$orderItem->PropertyID = $cartDetail->PropertyID;
						$prop = PropertyRecord::finder()->findByPk($orderItem->PropertyID);
						if ($prop instanceof PropertyRecord)
						{
							$orderItem->UnitPrice = $prop->Price;
						}
						$orderItem->Quantity = $cartDetail->Quantity;
						$orderItem->Subtotal = $cartDetail->Subtotal;
						$orderItem->save();
					}
					// insert order history
					$orderHistory = new OrderHistoryRecord;
					$orderHistory->OrderID = $order->ID;
					$orderHistory->StatusCode = "W"; // 'W' means Pending
					$orderHistory->save();
					
					$payment = new PaymentRecord;
					$payment->OrderID = $order->ID;
					$payment->PaymentMethodID = $this->cboPaymentSelector->SelectedValue;
					$payment->Status = 0; // pending 
					$payment->Amount = 0;
					$payment->save();
					
					// credit user organic points
					$user = UserRecord::finder()->findByPk($this->Application->User->ID);
					if ($user instanceof UserRecord)
					{
						if ($this->cboCreditsSelector->SelectedItem)
						{
							$user->Credits = $user->Credits - TPropertyValue::ensureInteger($this->cboCreditsSelector->SelectedItem->Text);
							$user->CreditsUsed = $user->CreditsUsed + TPropertyValue::ensureInteger($this->cboCreditsSelector->SelectedItem->Text);
						}
						$user->save();
						$this->Application->getModule("auth")->updateSessionUser($this->Application->User->createUser($user->Email));
					}
					
					if ($payment->PaymentMethodID == 1) // paypal
					{
						$this->Response->redirect($this->Service->ConstructUrl("shop.checkout.PayPalRedirector",array("hash"=>$this->generateHash($order->ID,$order->Num,$payment->ID))));
					}
					else if ($payment->PaymentMethodID == 2) // cash on delivery
					{
						$this->Response->redirect($this->Service->ConstructUrl("shop.checkout.Confirmation",array("hash"=>$this->generateHash($order->ID,$order->Num,$payment->ID))));
					}
				}
				catch(TException $ex)
				{
					$this->Notice->Type = UserNoticeType::Error;
					$this->Notice->Text = $ex;//$this->Application->getModule("message")->translate("UNKNOWN_ERROR");
				}
			}
			else
			{
				$this->Notice->Type = UserNoticeType::Error;
				$this->Notice->Text = $this->Application->getModule("message")->translate("CART_EMPTY");
			}
		}
		$this->populateData();
		$this->categoryMenu->populateData();
	}
	
	public function generateHash($oid,$onum,$pid)
	{
		$oid = base64_encode($this->Application->SecurityManager->hashData($oid));
		$onum = base64_encode($this->Application->SecurityManager->hashData($onum));
		$pid = base64_encode($this->Application->SecurityManager->hashData($pid));
		$hash = array($oid,$onum,$pid);
		return $this->Application->SecurityManager->hashData(serialize($hash));
	}
}

?>