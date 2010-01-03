<?php

class PackageTypeTemplate extends TRepeaterItemRenderer
{
	public function onDataBinding($param)
	{
		parent::onDataBinding($param);
		$this->cboUOMSelector->DataSource = TPropertyValue::ensureArray($this->Application->Parameters["UNITS_OF_MEASURE"]);
		$this->cboUOMSelector->DataBind();
		if ($this->Data instanceof PackageTypeRecord)
		{
			$this->txtUnit->Text = $this->Data->Unit;
			$this->txtPrice->Text =$this->Data->Price;
			$this->txtInStock->Text =$this->Data->InStock;
			$this->cboUOMSelector->SelectedValue = $this->Data->UOM;
			$this->txtID->Value = $this->Data->ID;
		}
	}
}

?>