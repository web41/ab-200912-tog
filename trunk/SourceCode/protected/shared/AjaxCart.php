<?php

Prado::using('System.I18N.core.NumberFormat');
Prado::using('Application.common.common');
class AjaxCart extends TTemplateControl
{
	public function refreshCart()
	{
		$cartTempDetails = CartTempDetailRecord::finder()->withProduct()->findAllBysession_id($this->Session->SessionID);
		$writer = "";
		$writer .= "\n<div class=\"shopping_bag\">";
		foreach($cartTempDetails as $cartItem)
		{
			$writer .= $this->renderCartItem($cartItem);
		}
		$writer .= "\n</div>";
		$this->Page->CallBackClient->hide($this->imgLoading);
		$this->Page->CallBackClient->show($this->ajaxCartPanel);
		//if ($this->Page->imgLoading) $this->Page->CallBackClient->hide($this->Page->imgLoading);
		$this->Page->CallBackClient->callClientFunction("ajaxCart_loaded");
		$this->Page->CallBackClient->update($this->ajaxCartPanel,$writer);
	}
	
	public function renderCartItem($cartItem)
	{
		if ($cartItem instanceof CartTempDetailRecord)
		{
			$product = $cartItem->Product;
			$prop = $cartItem->Property;
			$writer = "";
			$writer .= "\n\t<div class=\"product\">";
			$writer .= "\n\t\t<b>".$product->Name."</b><br />";
			$writer .= "\n\t\tQuantity:&nbsp;".$cartItem->Quantity." &nbsp;-&nbsp; Total: <span>".$this->getFormattedValue($cartItem->Quantity*Common::roundTo($product->getDiscountPrice($prop->Price)))."</span>";
			$writer .= "\n\t</div>";
			return $writer;
		}
	}
	
	public function getFormattedValue($value,$pattern="c",$currency="USD")
	{
		$formatter = new NumberFormat($this->Application->Globalization->Culture);
		return $formatter->format($value,$pattern,$currency,$this->Application->Globalization->Charset);
	}
	
	protected function load_TriggerCallBack($sender, $param)
	{
		$sender->stopTimer();
		$this->refreshCart();
	}
}

?>