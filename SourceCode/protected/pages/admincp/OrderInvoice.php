<?php

class OrderInvoice extends TPage
{
	public function onLoad($param)
	{
		parent::onLoad($param);
		if (!$this->IsPostBack)
		{
			$activeRecord = $this->getItem();
			if ($activeRecord instanceof OrderRecord)
			{
				$this->lblOrderNum->Text = $activeRecord->Num;
				$this->lblOrderDate->Text = date('d/m/Y',$activeRecord->CreateDate);
				$this->lblLatestStatus->Text = $activeRecord->LatestHistory->OrderStatus->Name;
				
				$this->lblBilling->Text = $activeRecord->BFirstName." ".$activeRecord->BLastName."<br />";
				$this->lblBilling->Text .= $activeRecord->BAddress1;
				if (strlen($activeRecord->BAddress2)>0) $this->lblBilling->Text .= ", ".$activeRecord->BAddress2;
				if (strlen($activeRecord->BCity)>0) $this->lblBilling->Text .= ", ".$activeRecord->BCity;
				if (strlen($activeRecord->BState)>0) $this->lblBilling->Text .= ", ".$activeRecord->BState;
				$this->lblBilling->Text .= ", ".$activeRecord->BZipCode.", ".$activeRecord->BCountry->Name."<br />";
				$this->lblBilling->Text .= "Tel 1: ".$activeRecord->BPhone1;
				if (strlen($activeRecord->BPhone2)>0) $this->lblBilling->Text .= ", Tel 2: ".$activeRecord->BPhone2;
				if (strlen($activeRecord->BFax)>0) $this->lblBilling->Text .= ", Fax: ".$activeRecord->BFax;
	
				$this->lblShipping->Text = $activeRecord->SFirstName." ".$activeRecord->SLastName."<br />";
				$this->lblShipping->Text .= $activeRecord->SAddress1;
				if (strlen($activeRecord->SAddress2)>0) $this->lblShipping->Text .= ", ".$activeRecord->SAddress2;
				if (strlen($activeRecord->SCity)>0) $this->lblShipping->Text .= ", ".$activeRecord->SCity;
				if (strlen($activeRecord->SState)>0) $this->lblShipping->Text .= ", ".$activeRecord->SState;
				$this->lblShipping->Text .= ", ".$activeRecord->SZipCode.", ".$activeRecord->SCountry->Name."<br />";
				$this->lblShipping->Text .= "Tel 1: ".$activeRecord->SPhone1;
				if (strlen($activeRecord->SPhone2)>0) $this->lblShipping->Text .= ", Tel 2: ".$activeRecord->SPhone2;
				if (strlen($activeRecord->SFax)>0) $this->lblShipping->Text .= ", Fax: ".$activeRecord->SFax;
	
				$sqlmap = $this->Application->Modules['sqlmap']->Client;
				$this->rptOrderItem->DataSource = $sqlmap->queryForList("GetOrderItemByOrderID", $activeRecord->ID);
				$this->rptOrderItem->DataBind();
				
				$this->lblDeliverer->Text = $activeRecord->Deliverer;
				//$this->lblTotalPacks->Text = $activeRecord->TotalPacks;
				$this->lblPaymentTerm->Text = PaymentRecord::finder()->withPaymentMethod()->findByorder_id($activeRecord->ID)->PaymentMethod->Name;
				$this->lblDeliveryDate->Text = $activeRecord->EstDeliveryDate;
				$this->lblComments->Text = $activeRecord->Comments;
	
				$this->nfSubtotal->Value = $activeRecord->Subtotal;
				$this->nfShippingAmount->Value = $activeRecord->ShippingMethod->Price;
				$this->nfShippingDiscount->Value = $this->nfShippingAmount->Value - $activeRecord->ShippingAmount;
				$this->nfCouponDiscount->Value = $activeRecord->CouponAmount;
				$this->nfPointRebate->Value = $activeRecord->RewardPointsRebate;
				$this->nfTotal->Value = $activeRecord->Total;
				
				// Added by Tom on Jun-2011
				if ($activeRecord->GoGreen == 1) $this->lblGoGreen->Text = "Customer wants to go-green, <b>no plastic bags please</b>.<br />";
			}
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
}

?>