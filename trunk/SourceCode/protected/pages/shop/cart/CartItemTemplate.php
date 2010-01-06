<?php

Prado::using('System.I18N.core.NumberFormat');
class CartItemTemplate extends TRepeaterItemRenderer
{
	public function onDataBinding($param)
	{
		parent::onDataBinding($param);
		$this->lblSubtotal->Text = $this->getFormattedValue($this->Data->Subtotal);
	}
	protected function btnUpdate_Clicked($sender, $param)
	{
		$cartDetail = CartTempDetailRecord::finder()->withProperty()->withProduct()->findByPk($this->txtID->Value);
		if ($cartDetail instanceof CartTempDetailRecord)
		{
			$cartDetail->Quantity = TPropertyValue::ensureInteger($this->txtQty->SafeText);
			$cartDetail->Subtotal = $cartDetail->Quantity*$cartDetail->Product->getDiscountPrice($cartDetail->Property->Price);
			$cartDetail->save();
		}
		$this->Page->categoryMenu->populateData();
		$this->Page->populateData();
	}
	
	protected function btnDelete_Clicked($sender, $param)
	{
		$cartDetail = CartTempDetailRecord::finder()->withProperty()->findByPk($this->txtID->Value);
		$cartDetail->delete();
		$this->Page->categoryMenu->populateData();
		$this->Page->populateData();
	}
	
	public function getFormattedValue($value,$pattern="c",$currency="USD")
	{
		$formatter = new NumberFormat($this->Application->Globalization->Culture);
		return $formatter->format($value,$pattern,$currency,$this->Application->Globalization->Charset);
	}
}

?>