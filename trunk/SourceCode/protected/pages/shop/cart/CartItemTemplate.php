<?php

Prado::using('System.I18N.core.NumberFormat');
Prado::using('Application.common.common');
class CartItemTemplate extends TRepeaterItemRenderer
{
	public function onDataBinding($param)
	{
		parent::onDataBinding($param);
		$this->lblSubtotal->Text = $this->getFormattedValue($this->Data->Subtotal);
		$this->lblUnitPrice->Text = $this->getFormattedValue($this->Data->Property->Price);
		$this->cboQtySelector->Items->clear();
		for($i=1;$i<=$this->Data->Property->InStock;$i++)
		{
			$item = new TListItem;
			$item->Text = $item->Value = $i;
			$this->cboQtySelector->Items->add($item);
		}
		
		$this->cboQtySelector->SelectedValue = $this->Data->Quantity;
	}
	protected function btnUpdate_Clicked($sender, $param)
	{
		$cartDetail = CartTempDetailRecord::finder()->withProduct()->findByPk($this->txtID->Value);
		if ($cartDetail instanceof CartTempDetailRecord)
		{
			$prop = PropertyRecord::finder()->findByPk($cartDetail->PropertyID);
			$cartDetail->Quantity = TPropertyValue::ensureInteger($this->cboQtySelector->SelectedValue);
			$cartDetail->Subtotal = $cartDetail->Quantity*Common::roundTo($cartDetail->Product->getDiscountPrice($prop->Price));
			$cartDetail->save();
		}
		try {
			if ($this->Page->categoryMenu) $this->Page->categoryMenu->populateData();
			if (method_exists($this->Page,"populateData")) $this->Page->populateData();
		}
		catch(TException $e) {}
	}
	
	protected function btnDelete_Clicked($sender, $param)
	{
		$cartDetail = CartTempDetailRecord::finder()->findByPk($this->txtID->Value);
		$cartDetail->delete();
		try {
			if ($this->Page->categoryMenu) $this->Page->categoryMenu->populateData();
			if (method_exists($this->Page,"populateData")) $this->Page->populateData();
		}
		catch(TException $e) {}
	}
	
	public function getFormattedValue($value,$pattern="c",$currency="USD")
	{
		$formatter = new NumberFormat($this->Application->Globalization->Culture);
		return $formatter->format($value,$pattern,$currency,$this->Application->Globalization->Charset);
	}
}

?>