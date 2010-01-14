<?php

class PayPalRedirector extends TPage
{
	private $_hash;
	public function onLoad($param)
	{
		parent::onLoad($param);
		if (!$this->IsPostBack)
		{
			$this->setHash($this->Request['hash']);
			$orderRecord = $this->getOrder();
			$paymentRecord = $this->getPayment();
			$this->PPayPal1->Amount = 1;// round(TPropertyValue::ensureFloat($orderRecord->Total),2);
			$this->PPayPal1->Title = "The Organic Grocer - Purchase Order";
			$this->PPayPal1->CancelUrl = $this->PPayPal1->ReturnUrl = $this->Request->getBaseUrl($this->Request->IsSecureConnection).$this->Service->ConstructUrl("shop.checkout.Confirmation",array("hash"=>$this->generateHash($this->Order->ID,$this->Order->Num,$this->Payment->ID)));
		}
	}
	
	public function setHash($value)
	{
		if(($value = $this->Application->SecurityManager->validateData($value))!==false)
		{
			$value=unserialize($value);
			if(is_array($value) && count($value)===3)
			{
				list($oid,$onum,$pid)=$value;
				$oid = TPropertyValue::ensureInteger($this->Application->SecurityManager->validateData(base64_decode($oid)));
				$onum = $this->Application->SecurityManager->validateData(base64_decode($onum));
				$pid = TPropertyValue::ensureInteger($this->Application->SecurityManager->validateData(base64_decode($pid)));
				$this->_hash = array('oid'=>$oid,'onum'=>$onum,'pid'=>$pid);
			}
		}
	}

	public function getHash()
	{
		return $this->_hash;
	}
	
	protected function getOrder()
	{
		if (isset($this->Hash["oid"]) && isset($this->Hash["onum"]))
		{
			// use Active Record to look for the specified post ID
			$activeRecord = OrderRecord::finder()->findByorder_idAndorder_num(TPropertyValue::ensureInteger($this->Hash['oid']), $this->Hash['onum']);
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
		if (isset($this->Hash["oid"]) && isset($this->Hash["pid"]))
		{
			// use Active Record to look for the specified post ID
			$activeRecord = PaymentRecord::finder()->findBypayment_idAndorder_id(TPropertyValue::ensureInteger($this->Hash['pid']), TPropertyValue::ensureInteger($this->Hash['oid']));
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
	
	public function generateHash($oid,$onum,$pid)
	{
		$oid = base64_encode($this->Application->SecurityManager->hashData($oid));
		$onum = base64_encode($this->Application->SecurityManager->hashData($onum));
		$pid = base64_encode($this->Application->SecurityManager->hashData($pid));
		$hash = array($oid,$onum,$pid);
		return $this->Application->SecurityManager->hashData(serialize($hash));
	}
}

?>