<?php

class Index extends TPage
{
	public function onLoad($param)
	{
		parent::onLoad($param);
		if (!$this->IsPostBack)
		{
			$this->populateData();
		}
	}
	
	public function populateData()
	{
		$this->rptCart->DataSource = CartTempDetailRecord::finder()->withProduct()->withProperty()->findAllBysession_id($this->Session->SessionID);
		$this->rptCart->DataBind();
		$this->updateSubtotalInSession();
		if (count($this->rptCart->Items)<=0)
		{
			$this->Notice->Type = UserNoticeType::Notice;
			$this->Notice->Text = $this->Application->getModule("message")->translate("CART_EMPTY");
		}
	}
	
	public function updateSubtotalInSession()
	{
		$this->rptCart->Footer->lblSubtotal->Text = $this->getFormattedValue(CartTempRecord::finder()->getSubtotalInSession());
	}
	
	public function getFormattedValue($value,$pattern="c",$currency="USD")
	{
		$formatter = new NumberFormat($this->Application->Globalization->Culture);
		return $formatter->format($value,$pattern,$currency,$this->Application->Globalization->Charset);
	}
}

?>