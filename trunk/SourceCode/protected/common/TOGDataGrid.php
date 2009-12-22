<?php

class TOGDataGrid extends TDataGrid
{
	public function onInit($param)
	{
		parent::onInit($param);
		$this->CellPadding="2";
		$this->CellSpacing="1";
		$this->HeaderStyle->CssClass="table_header";
		$this->AlternatingItemStyle->CssClass="table_field1";
		$this->ItemStyle->CssClass="table_field2";
		$this->AutoGenerateColumns="false";
		$this->AllowPaging="true";
		$this->AllowCustomPaging="true";
		$this->PagerStyle->Visible="False";
		$this->PageSize = $this->Application->Parameters['ROW_PER_PAGE'];
		$this->ShowFooter="false";
		$this->Width = "100%";
	}
}

?>