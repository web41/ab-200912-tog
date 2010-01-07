<?php

class Review extends TPage
{
	public function onLoad($param)
	{
		parent::onLoad($param);
		if (!$this->IsPostBack)
		{
			$this->populateData();
			$this->cboPaymentSelector->DataSource = PaymentMethodRecord::finder()->getAllItems(true);
			$this->cboPaymentSelector->DataBind();
			$this->cboPaymentSelector->SelectedIndex = 0;
			$this->cboPaymentSelector_CallBack($this->cboPaymentSelector,null);
		}
	}

	public function populateData()
	{
		$this->rptCart->DataSource = CartTempDetailRecord::finder()->withProduct()->withProperty()->findAllBysession_id($this->Session->SessionID);
		$this->rptCart->DataBind();
		if (count($this->rptCart->Items)<=0)
		{
			$this->Notice->Type = UserNoticeType::Notice;
			$this->Notice->Text = $this->Application->getModule("message")->translate("CART_EMPTY");
		}
	}
	
	public function getBillingAddress()
	{
		return $this->getCart()->BillingAddress;
	}
	
	public function getShippingAddress()
	{
		return $this->getCart()->ShippingAddress;
	}
	
	public function getShippingMethod()
	{
		return $this->getCart()->ShippingMethod;
	}
	
	public function getCoupon()
	{
		return $this->getCart()->Coupon;
	}
	
	public function getCart()
	{
		return CartTempRecord::finder()->withBillingAddress()->withShippingAddress()->withShippingMethod()->withCoupon()->findByPk($this->Session->SessionID);
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
}

?>