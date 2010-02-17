<?php
Prado::using('System.I18N.core.NumberFormat');
Prado::using('Application.common.common');
class ProductBoxTemplate extends TRepeaterItemRenderer
{
	public function onDataBinding($param)
	{
		parent::onDataBinding($param);
		if (count($this->Data)>0)
		{
			foreach($this->Data->getProperties() as $prop)
			{
				$item = new TListItem;
				$item->Text = $prop->Name." ".$this->getFormattedValue(Common::roundTo($prop->Price));
				$item->Value = $prop->ID;
				$this->cboPropertySelector->Items->add($item);
			}
		}
		//$this->lblPrice->Text = count($this->Data->Properties)>0?$this->getFormattedValue(($this->Data->Properties[0]->Price)):"$0.00";
		if (count($this->cboPropertySelector->Items)>0) 
		{
			$this->cboPropertySelector->SelectedIndex = 0;
			$this->cboPropertySelector_CallBack($this->cboPropertySelector,null);
		}
		if ($this->Data)
		{
			if (strlen($this->Data->Brief)>185)
			{
				$this->lblDesc->Text = $this->Data->Brief . "<a style='color:brown;' href='".$this->Service->ConstructUrl("shop.ProductDetail",array("id"=>$this->Data->ID,"alias"=>$this->Data->Alias))."'>...more</a>";
			}
			else if (strlen($this->Data->Description)>0)
			{
				if (strlen($this->Data->Description)>185)
					$this->lblDesc->Text = Common::neatTrim($this->Data->Description,185,"") . "<a style='color:brown;' href='".$this->Service->ConstructUrl("shop.ProductDetail",array("id"=>$this->Data->ID,"alias"=>$this->Data->Alias))."'>...more</a>";
				else $this->lblDesc->Text = $this->Data->Description;
			}
		}
	}
	public function getFormattedValue($value,$pattern="c",$currency="USD")
	{
		$formatter = new NumberFormat($this->Application->Globalization->Culture);
		return $formatter->format($value,$pattern,$currency,$this->Application->Globalization->Charset);
	}
	
	protected function cboPropertySelector_CallBack($sender, $param)
	{
		$prop = PropertyRecord::finder()->withProduct()->findByPk($sender->SelectedValue);
		if ($prop instanceof PropertyRecord)
		{
			if (strlen($prop->Name)>0) $this->lblProperty->Text = "&nbsp;".$prop->Name;
			$this->lblPrice->Text = $this->getFormattedValue(Common::roundTo($prop->Price));
			$this->lblPrice->Visible = $prop->Product->DiscountID > 0;
			$this->lblDiscountPrice->Text = $this->getFormattedValue(Common::roundTo($prop->Product->getDiscountPrice($prop->Price)));
			$this->cboQuantitySelector->Items->clear();
			for($i=1;$i<=$prop->InStock;$i++)
			{
				$item = new TListItem;
				$item->Text = $item->Value = $i;
				$this->cboQuantitySelector->Items->add($item);
			}
		}
	}
	
	protected function btnAddToCart_Clicked($sender, $param)
	{
		if (!$this->cboPropertySelector->SelectedValue)
		{
			$this->Page->Notice->Type = UserNoticeType::Error;
			$this->Page->Notice->Text = $this->Application->getModule("message")->translate("COMBOBOX_REQUIRED","property of product");
			$this->Page->categoryMenu->populateData();
			$this->Page->populateData();
		}
		else
		{
			// do add cart here
			try
			{
				$cartTemp = CartTempRecord::finder()->findByPk($this->Session->SessionID);
				if (!($cartTemp instanceof CartTempRecord))
				{
					$cartTemp = new CartTempRecord;
					$cartTemp->SessionID = $this->Session->SessionID;
					if (!$this->User->IsGuest) $cartTemp->UserID = $this->User->ID;
					$cartTemp->save();
				}
				// we should check if user select the same product
				$cartDetail = CartTempDetailRecord::finder()->findBysession_idAndproduct_idAndprop_id($this->Session->SessionID,$this->txtID->Value,$this->cboPropertySelector->SelectedValue);
				$prop = PropertyRecord::finder()->withProduct()->findByPk($this->cboPropertySelector->SelectedValue);
				if ($cartDetail instanceof CartTempDetailRecord)
				{
					$cartDetail->Quantity = $cartDetail->Quantity + $this->cboQuantitySelector->SelectedValue;
				}
				else
				{
					$cartDetail = new CartTempDetailRecord;
					$cartDetail->HashID = md5(uniqid(time()));
					$cartDetail->SessionID = $cartTemp->SessionID;
					if (!$this->User->IsGuest) $cartDetail->UserID = $this->User->ID;
					$cartDetail->ProductID = $this->txtID->Value;
					$cartDetail->Quantity = $this->cboQuantitySelector->SelectedValue;
					if ($prop instanceof PropertyRecord)
					{
						$cartDetail->PropertyID = $prop->ID;
					}
					$cartDetail->CreateDate = time();
				}
				$cartDetail->Subtotal = $cartDetail->Quantity*Common::roundTo($prop->Product->getDiscountPrice($prop->Price));
				
				$cartDetail->save();
				//$this->Response->redirect($this->Service->ConstructUrl("shop.cart.Index"));
				$this->Page->ajaxCart->refreshCart();
			}
			catch(TException $e)
			{
				$this->Page->Notice->Type = UserNoticeType::Error;
				$this->Page->Notice->Text = $this->Application->getModule("message")->translate("UNKNOWN_ERROR");
				$this->Page->categoryMenu->populateData();
				$this->Page->populateData();
			}
		}
	}
}

?>