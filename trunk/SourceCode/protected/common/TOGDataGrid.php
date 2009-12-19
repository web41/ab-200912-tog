<?php

class TOGDataGrid extends TDataGrid
{
	public function onInit($param)
	{
		parent::onInit($param);
		$this->CellPadding="2";
		$this->CellSpacing="1";
		$this->HeaderStyle->CssClass="table_header";
		$this->FooterStyle->CssClass="hiding_footer";
		$this->AlternatingItemStyle->CssClass="table_field1";
		$this->ItemStyle->CssClass="table_field2";
		$this->AutoGenerateColumns="false";
		$this->AllowPaging="True";
		$this->AllowCustomPaging="True";
		$this->PagerStyle->Visible="False";
		$this->PageSize = $this->Application->Parameters['ROW_PER_PAGE'];
		$this->ShowFooter="True";
		$this->Width = "100%";
	}
}

class TOGPager extends TPager
{
	public function onInit($param)
	{
		parent::onInit($param);
		$this->Mode="Numeric";
		$this->ButtonType="LinkButton";
		$this->FirstPageText="First";
		$this->PrevPageText="Prev";
		$this->NextPageText="Next";
		$this->LastPageText="Last";
		$this->PageButtonCount=2;
	}
}

?>