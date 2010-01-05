<?php
Prado::using('System.I18N.core.NumberFormat');
class ProductDetail extends TPage
{
	public function onLoad($param)
	{
		parent::onLoad($param);
		if (!$this->IsPostBack)
		{
			$activeRecord = $this->getItem();
			if ($activeRecord instanceof ProductRecord)
			{
				foreach($activeRecord->Properties as $prop)
				{
					$item = new TListItem;
					$item->Text = $prop->Name." - ".$this->getFormattedValue($prop->Price);
					$item->Value = $prop->ID;
					$this->cboPropertySelector->Items->add($item);
				}
				$this->lblPrice->Text = count($activeRecord->Properties)>0?$this->getFormattedValue($activeRecord->Properties[0]->Price):"$0.00";
				if (count($this->cboPropertySelector->Items)>0) 
				{
					$this->cboPropertySelector->SelectedIndex = 0;
					$this->cboPropertySelector_CallBack($this->cboPropertySelector,null);
				}
			}
		}
	}
	
	public function getFormattedValue($value,$pattern="c",$currency="USD")
	{
		$formatter = new NumberFormat($this->Application->Globalization->Culture);
		return $formatter->format($value,$pattern,$currency,$this->Application->Globalization->Charset);
	}
	
	public function getItem()
	{
		if ($this->Request->Contains("id") && $this->Request->Contains("alias"))
		{
			// use Active Record to look for the specified post ID
			$activeRecord = ProductRecord::finder()->withBrand()->withManufacturer()->withProperties()->finder()->findByproduct_idAndproduct_alias(TPropertyValue::ensureInteger($this->Request['id']), $this->Request['alias']);
			if($activeRecord === null)
			{
				$this->Notice->Type = UserNoticeType::Error;
				$this->Notice->Text = $this->Application->getModule("message")->translate("ITEM_NOT_FOUND","product");
				$this->mainContent->Visible = false;
			}
			return $activeRecord;
		}
		else
		{
			$this->Notice->Type = UserNoticeType::Error;
			$this->Notice->Text = $this->Application->getModule("message")->translate("ITEM_NOT_FOUND","product");
			$this->mainContent->Visible = false;
		}
	}
	
	protected function cboPropertySelector_CallBack($sender, $param)
	{
		$prop = PropertyRecord::finder()->findByPk($sender->SelectedValue);
		if ($prop instanceof PropertyRecord)
		{
			$this->lblPrice->Text = $this->getFormattedValue($prop->Price);
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
			$this->Notice->Type = UserNoticeType::Error;
			$this->Notice->Text = $this->Application->getModule("message")->translate("COMBOBOX_REQUIRED","property of product");
			$this->categoryMenu->populateData();
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
				$cartDetail = new CartTempDetailRecord;
				$cartDetail->HashID = md5(uniqid(time()));
				$cartDetail->SessionID = $cartTemp->SessionID;
				if (!$this->User->IsGuest) $cartDetail->UserID = $this->User->ID;
				$cartDetail->ProductID = $this->Item->ID;
				$cartDetail->Quantity = $this->cboQuantitySelector->SelectedValue;
				$prop = PropertyRecord::finder()->findByPk($this->cboPropertySelector->SelectedValue);
				if ($prop instanceof PropertyRecord)
				{
					$cartDetail->PropertyID = $prop->ID;
				}
				$cartDetail->Subtotal = $cartDetail->Quantity*$prop->Price;
				$cartDetail->CreateDate = time();
				$cartDetail->save();
				$this->Response->redirect($this->Service->ConstructUrl("shop.cart.Index"));
			}
			catch(TException $e)
			{
				$this->Notice->Type = UserNoticeType::Error;
				$this->Notice->Text = $this->Application->getModule("message")->translate("UNKNOWN_ERROR");
				$this->categoryMenu->populateData();
			}
		}
	}
}

?>