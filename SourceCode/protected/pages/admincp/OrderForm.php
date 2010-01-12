<?php

class OrderForm extends TPage
{
	public function onLoad($param)
	{
		parent::onLoad($param);
		if (!$this->IsPostBack)
		{
			$activeRecord = $this->getItem();
			$this->lblHeader->Text = "Order detail: ".$activeRecord->Num;
			$this->lblOrderNum->Text = $activeRecord->Num;
			$this->lblOrderDate->Text = date('m/d/Y h:i:s A',$activeRecord->CreateDate);
			$this->lblLatestStatus->Text = $activeRecord->LatestHistory->OrderStatus->Name;
			$this->lnkUser->Text = $activeRecord->User->FirstName." ".$activeRecord->User->LastName;
			$this->lnkUser->NavigateUrl = $this->Service->ConstructUrl("admincp.UserForm",array("id"=>$activeRecord->User->ID,"alias"=>$activeRecord->User->Email));
			
			$this->lblBilling->Text = $activeRecord->BFirstName." ".$activeRecord->BLastName."<br />";
			$this->lblBilling->Text .= $activeRecord->BAddress1;
			if (strlen($activeRecord->BAddress2)>0) $this->lblBilling->Text .= ", ".$activeRecord->BAddress2;
			$this->lblBilling->Text .= ", ".$activeRecord->BCity.", ".$activeRecord->BState." ".$activeRecord->BZipCode.", ".$activeRecord->BCountry->Name."<br />";
			$this->lblBilling->Text .= "Tel 1: ".$activeRecord->BPhone1;
			if (strlen($activeRecord->BPhone2)>0) $this->lblBilling->Text .= ", Tel 2: ".$activeRecord->BPhone2;
			if (strlen($activeRecord->BFax)>0) $this->lblBilling->Text .= ", Fax: ".$activeRecord->BFax;
			
			$this->lblShipping->Text = $activeRecord->SFirstName." ".$activeRecord->SLastName."<br />";
			$this->lblShipping->Text .= $activeRecord->SAddress1;
			if (strlen($activeRecord->SAddress2)>0) $this->lblShipping->Text .= ", ".$activeRecord->SAddress2;
			$this->lblShipping->Text .= ", ".$activeRecord->SCity.", ".$activeRecord->SState." ".$activeRecord->SZipCode.", ".$activeRecord->SCountry->Name."<br />";
			$this->lblShipping->Text .= "Tel 1: ".$activeRecord->SPhone1;
			if (strlen($activeRecord->SPhone2)>0) $this->lblShipping->Text .= ", Tel 2: ".$activeRecord->SPhone2;
			if (strlen($activeRecord->SFax)>0) $this->lblShipping->Text .= ", Fax: ".$activeRecord->SFax;
			
			$this->rptOrderItem->DataSource = OrderItemRecord::finder()->withProduct()->findAllByorder_id($activeRecord->ID);
			$this->rptOrderItem->DataBind();
			
			$this->rptPayment->DataSource = PaymentRecord::finder()->withPaymentMethod()->findAllByorder_id($activeRecord->ID);
			$this->rptPayment->DataBind();
			
			$this->rptHistory->DataSource = OrderHistoryRecord::finder()->withOrderStatus()->findAllByorder_id($activeRecord->ID);
			$this->rptHistory->DataBind();
		}
	}
	
	protected function getItem()
	{
		if ($this->Request->Contains("id") && $this->Request->Contains("num"))
		{
			// use Active Record to look for the specified post ID
			$activeRecord = OrderRecord::finder()->withUser()->withShippingMethod()->withBCountry()->withSCountry()->findByorder_idAndorder_num(TPropertyValue::ensureInteger($this->Request['id']), $this->Request['num']);
			if($activeRecord === null)
			{
				$this->Notice->Type = AdminNoticeType::Error;
				$this->Notice->Text = $this->Application->getModule("message")->translate("ITEM_NOT_FOUND","order");
				$this->mainBox->Visible = false;
			}
			return $activeRecord;
		}
		else
		{
			$this->Notice->Type = AdminNoticeType::Error;
			$this->Notice->Text = $this->Application->getModule("message")->translate("ITEM_NOT_FOUND","order");
			$this->mainBox->Visible = false;
		}
	}
	
	public function getPaymentStatus()
	{
		return TPropertyValue::ensureArray($this->Application->Parameters["PAYMENT_STATUS"]);
	}
}

?>