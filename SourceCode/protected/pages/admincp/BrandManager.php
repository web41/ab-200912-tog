<?php

class BrandManager extends TPage
{
	public function onLoad($param)
	{
		if (!$this->IsPostBack)
		{
			$this->BrandList->DataSource = range(1,50);
			$this->BrandList->DataBind();
		}
	}
}

?>