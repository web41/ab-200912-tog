<?php

class Index extends TPage
{
	public function onLoad($param)
	{
		if (!$this->IsPostBack)
		{
			$this->datalist->DataSource = array(1,2,3,4,5,6,7);
			$this->datalist->DataBind();
		}
	}
	
}

?>