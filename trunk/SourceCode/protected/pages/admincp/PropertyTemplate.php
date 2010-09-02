<?php

class PropertyTemplate extends TRepeaterItemRenderer
{
	public function onDataBinding($param)
	{
		parent::onDataBinding($param);
		if ($this->Data instanceof PropertyRecord)
		{
			$this->txtName->Text = $this->Data->Name;
			$this->txtPrice->Text = number_format($this->Data->Price,2);
			$this->txtCostPrice->Text = number_format($this->Data->CostPrice,2);
			$this->txtInStock->Text =$this->Data->InStock;
			$this->txtID->Value = $this->Data->ID;
		}
	}
	
	protected function btnDelete_Clicked($sender, $param)
	{
		$prop = PropertyRecord::finder()->findByPk($this->txtID->Value);
		if ($prop instanceof PropertyRecord)
			$prop->delete();
		$this->Page->populateProperties();
	}
}

?>