<?php

class Index extends TPage
{
	public function onLoad($param)
	{
		$cartRecord = CartTempRecord::finder()->findByPk($this->Session->SessionID);
		if ($cartRecord->BillingID == 0)
			$url = $this->Service->ConstructUrl("shop.checkout.Billing");
		else if ($cartRecord->ShippingID == 0)
			$url = $this->Service->ConstructUrl("shop.checkout.Shipping");
		else if ($cartRecord->ShippingMethodID == 0)
			$url = $this->Service->ConstructUrl("shop.checkout.ShippingMethod");
		else $url = $this->Service->ConstructUrl("shop.checkout.Review");
		$this->Response->redirect($url);
	}
}

?>