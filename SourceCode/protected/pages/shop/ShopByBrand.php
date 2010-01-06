<?php

class ShopByBrand extends TPage
{
	public function onLoad($param)
	{
		parent::onLoad($param);
		$this->populateData();
	}
	
	public function populateData()
	{
		$this->rptBrandList->DataSource = BrandRecord::finder()->getAllItems();
		$this->rptBrandList->DataBind();
		
		if (count($this->rptBrandList->Items)<=0)
		{
			$this->Notice->Type = UserNoticeType::Notice;
			$this->Notice->Text = $this->Application->getModule("message")->translate("ITEM_FOUND",0,"brand");
		}
	}
}

?>