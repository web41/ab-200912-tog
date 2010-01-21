<?php

class Index extends TPage
{
	public function onLoad($param)
	{
		$cartRecord = CartTempRecord::finder()->findByPk($this->Session->SessionID);
		if ($cartRecord instanceof CartTempRecord)
		{
			$cartRecord->Subtotal = $cartRecord->getSubtotalInSession();
			if ($cartRecord->Subtotal-$cartRecord->CouponAmount>=TPropertyValue::ensureFloat($this->Application->Parameters["MAXIMUM_ORDER_REQUIRED"]))
			{
				$cartRecord->Total = $cartRecord->Subtotal-$cartRecord->CouponAmount-$cartRecord->RewardPointsRebate+$cartRecord->ShippingAmount+$cartRecord->TaxAmount;
				try
				{
					$cartRecord->save();
				}
				catch(TException $ex)
				{ 
					$this->Response->redirect($this->Service->ConstructUrl("shop.cart.Index"));
				}
			}
			else
			{
				$this->Response->redirect($this->Service->ConstructUrl("shop.cart.Index"));
			}
			if ($cartRecord->BillingID == 0)
				$url = $this->Service->ConstructUrl("shop.checkout.Billing");
			else if ($cartRecord->ShippingID == 0)
				$url = $this->Service->ConstructUrl("shop.checkout.Shipping");
			else if ($cartRecord->ShippingMethodID == 0)
				$url = $this->Service->ConstructUrl("shop.checkout.ShippingSchedule");
			else $url = $this->Service->ConstructUrl("shop.checkout.Review");
		}
		else $url = $this->Service->ConstructUrl("shop.cart.Index");
		$this->Response->redirect($url);
	}
}

?>