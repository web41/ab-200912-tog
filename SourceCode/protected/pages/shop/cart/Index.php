<?php

Prado::using('System.I18N.core.NumberFormat');
class Index extends TPage
{
	public function onLoad($param)
	{
		parent::onLoad($param);
		if (!$this->IsPostBack)
		{
			$this->populateData();
			$cartRecord = CartTempRecord::finder()->withCoupon()->findByPk($this->Session->SessionID);
			if ($cartRecord instanceof CartTempRecord)
			{
				if ($cartRecord->Coupon instanceof CouponRecord && $cartRecord->Coupon->ID>0)
				{
					$this->couponForm->Visible = false;
					$this->couponDiscount->Visible = true;
					$this->lblCouponDiscount->Text = $this->getFormattedValue($cartRecord->CouponAmount);
				}
				else
				{
					$this->couponForm->Visible = true;
					$this->couponDiscount->Visible = false;
				}
			}
			else
			{
				$this->Notice->Type = UserNoticeType::Error;
				$this->Notice->Text = $this->Application->getModule("message")->translate("ITEM_NOT_FOUND","cart");
			}
		}
	}
	
	public function populateData()
	{
		$this->rptCart->DataSource = CartTempDetailRecord::finder()->withProduct()->findAllBysession_id($this->Session->SessionID);
		$this->rptCart->DataBind();
		$this->updateSubtotalInSession();
		if (count($this->rptCart->Items)<=0)
		{
			$this->Notice->Type = UserNoticeType::Notice;
			$this->Notice->Text = $this->Application->getModule("message")->translate("CART_EMPTY");
		}
	}
	
	public function updateSubtotalInSession()
	{
		$cartRecord = CartTempRecord::finder()->findByPk($this->Session->SessionID);
		$this->lblSubtotal->Text = $this->getFormattedValue($cartRecord->getSubtotalInSession());
		$this->lblTotal->Text = $this->getFormattedValue($cartRecord->getSubtotalInSession()-$cartRecord->CouponAmount);
	}
	
	public function getFormattedValue($value,$pattern="c",$currency="USD")
	{
		$formatter = new NumberFormat($this->Application->Globalization->Culture);
		return $formatter->format($value,$pattern,$currency,$this->Application->Globalization->Charset);
	}
	
	public function btnSubmit_Clicked($sender, $param)
	{
		$cartRecord = CartTempRecord::finder()->findByPk($this->Session->SessionID);
		if ($cartRecord instanceof CartTempRecord)
		{
			$cartRecord->Subtotal = $cartRecord->getSubtotalInSession();
			$cartRecord->Total = $cartRecord->Subtotal-$cartRecord->CouponAmount+$cartRecord->ShippingAmount+$cartRecord->TaxAmount;
			try
			{
				$cartRecord->save();
				$this->Response->redirect($this->Service->ConstructUrl("shop.checkout.Index"));
			}
			catch(TException $ex)
			{
				$this->Notice->Type = UserNoticeType::Error;
				$this->Notice->Text = $this->Application->getModule("message")->translate("UNKNOWN_ERROR");
			}
			
		}
		else
		{
			$this->Notice->Type = UserNoticeType::Error;
			$this->Notice->Text = $this->Application->getModule("message")->translate("ITEM_NOT_FOUND","cart");
		}
		$this->populateData();
		$this->categoryMenu->populateData();
	}
	
	protected function btnApplyCoupon_Clicked($sender, $param)
	{
		if ($this->IsValid)
		{
			$coupon = CouponRecord::finder()->findBycoupon_code($this->txtCouponCode->SafeText);
			if ($coupon instanceof CouponRecord)
			{
				$cartRecord = CartTempRecord::finder()->findByPk($this->Session->SessionID);
				$cartRecord->CouponCode = $coupon->Code;
				$cartRecord->CouponAmount = ($coupon->IsPercent) ? $cartRecord->getSubtotalInSession()*$coupon->Amount : $coupon->Amount;
				$cartRecord->save();
				$this->couponForm->Visible = false;
				$this->couponDiscount->Visible = true;
				$this->lblCouponDiscount->Text = $this->getFormattedValue($cartRecord->CouponAmount);
			}
			else
			{
				$this->Notice->Type = UserNoticeType::Error;
				$this->Notice->Text = $this->Application->getModule("message")->translate("ITEM_NOT_FOUND","coupon");
			}
		}
		$this->populateData();
		$this->categoryMenu->populateData();
	}
	
	protected function validCheck_ServerValidated($sender, $param)
	{
		if ($param->Value != '')
		{
			$param->IsValid = count(CouponRecord::finder()->findBycoupon_code($param->Value)) == 1;
		}
	}
}

?>