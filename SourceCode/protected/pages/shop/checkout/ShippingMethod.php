<?php
Prado::using('System.I18N.core.NumberFormat');
class ShippingMethod extends TPage
{
	public function onLoad($param)
	{
		parent::onLoad($param);
		if (!$this->IsPostBack)
		{
			//$this->cboMethodSelector->DataSource = ShippingMethod::finder()->getAllItems();
			//$this->cboMethodSelector->DataBind();
			$this->populateData();
			$cartRecord = CartTempRecord::finder()->findByPk($this->Session->SessionID);
			if ($cartRecord instanceof CartTempRecord && $cartRecord->ShippingMethodID > 0)
				$this->cboMethodSelector->SelectedValue = $cartRecord->ShippingMethodID;
		}
	}
	
	public function populateData()
	{
		$this->cboMethodSelector->Items->clear();
		foreach(ShippingMethodRecord::finder()->getAllItems() as $method)
		{
			$item = new TListItem;
			$item->Text = $method->Name." (".$this->getFormattedValue($method->Price).")";
			$item->Value = $method->ID;
			$this->cboMethodSelector->Items->add($item);
		}
	}
	
	public function getFormattedValue($value,$pattern="c",$currency="USD")
	{
		$formatter = new NumberFormat($this->Application->Globalization->Culture);
		return $formatter->format($value,$pattern,$currency,$this->Application->Globalization->Charset);
	}
	
	public function btnSubmit_Clicked($sender, $param)
	{
		if ($this->IsValid)
		{
			$cartRecord = CartTempRecord::finder()->findByPk($this->Session->SessionID);
			$method = ShippingMethodRecord::finder()->findByPk($this->cboMethodSelector->SelectedValue);
			if ($cartRecord instanceof CartTempRecord)
			{
				$cartRecord->ShippingMethodID = $method->ID;
				$cartRecord->ShippingAmount = $method->Price;
				$cartRecord->Total = $cartRecord->Subtotal-$cartRecord->CouponAmount+$cartRecord->ShippingAmount+$cartRecord->TaxAmount;
				try
				{
					$cartRecord->save();
					$this->Response->redirect($this->Service->ConstructUrl("shop.checkout.Review"));
				}
				catch(TException $e)
				{
					$this->Notice->Type = UserNoticeType::Error;
					$this->Notice->Text = $this->Application->getModule("message")->translate(($activeRecord->ID>0 ? "UPDATE_FAILED" : "ADD_FAILED"),"Shipping method","");
				}
			}
			else
			{
				$this->Notice->Type = UserNoticeType::Error;
				$this->Notice->Text = $this->Application->getModule("message")->translate("ITEM_NOT_FOUND","cart");
			}
		}
	}
	
}

?>