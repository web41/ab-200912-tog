<?php

class PayPalRedirector extends TPage
{
	public function onLoad($param)
	{
		parent::onLoad($param);
		if (!$this->IsPostBack)
		{
			$orderRecord = $this->getOrder();
			$paymentRecord = $this->getPayment();
			$this->PPayPal1->Amount = round(TPropertyValue::ensureFloat($orderRecord->Total),2);
			$this->PPayPal1->Title = "The Organic Grocer - Purchase Order";
		}
	}
	
	protected function getOrder()
	{
		if ($this->Request->Contains("oid") && $this->Request->Contains("onum"))
		{
			// use Active Record to look for the specified post ID
			$activeRecord = OrderRecord::finder()->findByorder_idAndorder_num(TPropertyValue::ensureInteger($this->Request['oid']), $this->Request['onum']);
			if($activeRecord === null)
			{
				$this->Notice->Type = UserNoticeType::Error;
				$this->Notice->Text = $this->Application->getModule("message")->translate("ITEM_NOT_FOUND","order");
				$this->mainBox->Visible = false;
			}
			return $activeRecord;
		}
		else
		{
			$this->Notice->Type = UserNoticeType::Error;
			$this->Notice->Text = $this->Application->getModule("message")->translate("ITEM_NOT_FOUND","order");
			$this->mainBox->Visible = false;
		}
	}
	
	protected function getPayment()
	{
		if ($this->Request->Contains("oid") && $this->Request->Contains("pid"))
		{
			// use Active Record to look for the specified post ID
			$activeRecord = PaymentRecord::finder()->findBypayment_idAndorder_id(TPropertyValue::ensureInteger($this->Request['pid']), TPropertyValue::ensureInteger($this->Request['oid']));
			if($activeRecord === null)
			{
				$this->Notice->Type = UserNoticeType::Error;
				$this->Notice->Text = $this->Application->getModule("message")->translate("ITEM_NOT_FOUND","payment");
				$this->mainBox->Visible = false;
			}
			return $activeRecord;
		}
		else
		{
			$this->Notice->Type = UserNoticeType::Error;
			$this->Notice->Text = $this->Application->getModule("message")->translate("ITEM_NOT_FOUND","payment");
			$this->mainBox->Visible = false;
		}
	}
}

?>