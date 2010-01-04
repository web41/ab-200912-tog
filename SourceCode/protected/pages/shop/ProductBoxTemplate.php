<?php
Prado::using('System.I18N.core.NumberFormat');
class ProductBoxTemplate extends TRepeaterItemRenderer
{
	public function onDataBinding($param)
	{
		parent::onDataBinding($param);
		if (count($this->Data)>0)
		{
			foreach($this->Data->Properties as $prop)
			{
				$item = new TListItem;
				$item->Text = $prop->Name." - ".$this->getFormattedValue($prop->Price);
				$item->Value = $prop->ID;
				$this->cboPropertySelector->Items->add($item);
			}
		}
		$this->lblPrice->Text = count($this->Data->Properties)>0?$this->getFormattedValue($this->Data->Properties[0]->Price):"$0.00";
		if (count($this->cboPropertySelector->Items)>0) 
		{
			$this->cboPropertySelector->SelectedIndex = 0;
			$this->cboPropertySelector_CallBack($this->cboPropertySelector,null);
		}
	}
	public function getFormattedValue($value,$pattern="c",$currency="USD")
	{
		$formatter = new NumberFormat($this->Application->Globalization->Culture);
		return $formatter->format($value,$pattern,$currency,$this->Application->Globalization->Charset);
	}
	
	protected function cboPropertySelector_CallBack($sender, $param)
	{
		$prop = PropertyRecord::finder()->findByPk($sender->SelectedValue);
		if ($prop instanceof PropertyRecord)
		{
			$this->lblPrice->Text = $this->getFormattedValue($prop->Price);
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
		}
	}
}

?>