<?php
Prado::using('Application.common.common');
class OrderForm extends TPage
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
		$activeRecord = $this->getItem();
		$deliverers = TPropertyValue::ensureArray($this->Application->Parameters["DELIVERER"]);
		$this->cboDelivererSelector->Items->clear();
		foreach($deliverers as $id=>$value)
		{
			$item = new TListItem; $item->Text = $item->Value = $value;
			$this->cboDelivererSelector->Items->add($item);
		}
		//$this->cboTotalPacksSelector->Items->clear();
		foreach(range(1,10) as $value)
		{
			$item = new TListItem; $item->Text = $item->Value = $value;
			//$this->cboTotalPacksSelector->Items->add($item);
		}
		if ($activeRecord instanceof OrderRecord)
		{
			$this->lblHeader->Text = "Order detail: ".$activeRecord->Num;
			$this->lblOrderNum->Text = $activeRecord->Num;
			$this->lblOrderDate->Text = date('d/m/Y h:i:s A',$activeRecord->CreateDate);
			$this->lblLatestStatus->Text = $activeRecord->LatestHistory?$activeRecord->LatestHistory->OrderStatus->Name:"";
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
			
			$sqlmap = $this->Application->Modules['sqlmap']->Client;
			$this->rptOrderItem->DataSource = $sqlmap->queryForList("GetOrderItemByOrderID", $activeRecord->ID);
			$this->rptOrderItem->DataBind();

			$this->rptPayment->DataSource = PaymentRecord::finder()->withPaymentMethod()->findAllByorder_id($activeRecord->ID);
			$this->rptPayment->DataBind();

			$this->rptHistory->DataSource = OrderHistoryRecord::finder()->withOrderStatus()->findAllByorder_id($activeRecord->ID);
			$this->rptHistory->DataBind();

			$this->nfSubtotal->Value = Common::roundTo($activeRecord->Subtotal);
			$this->nfShippingAmount->Value = Common::roundTo($activeRecord->ShippingMethod->Price);
			$this->nfShippingDiscount->Value = Common::roundTo($this->nfShippingAmount->Value - $activeRecord->ShippingAmount);
			$this->nfCouponDiscount->Value = Common::roundTo($activeRecord->CouponAmount);
			$this->nfPointRebate->Value = Common::roundTo($activeRecord->RewardPointsRebate);
			$this->nfTotal->Value = Common::roundTo($activeRecord->Total);

			if (strlen($activeRecord->Deliverer)>0) $this->cboDelivererSelector->SelectedValue = $activeRecord->Deliverer;
			//if ($activeRecord->TotalPacks>0) $this->cboTotalPacksSelector->SelectedValue = $activeRecord->TotalPacks;
			
			if (strlen($activeRecord->EstDeliveryDate) > 0) $this->txtEstDeliveryDate->Text = $activeRecord->EstDeliveryDate;
			$this->txtComments->Text = $activeRecord->Comments;
			
			// Added by Tom on Jun-2011
			if ($activeRecord->GoGreen == 0) $this->lblGoGreen->Text = "Customer wants plastic bag.";
			else $this->lblGoGreen->Text = "Customer wants to go-green, <b>no plastic bag please</b>.";
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
	
	protected function btnGenerateInvoice_Clicked($sender, $param)
	{
		if ($this->IsValid)
		{
			$activeRecord = $this->getItem();
			$activeRecord->Deliverer = $this->cboDelivererSelector->SelectedValue;
			$activeRecord->TotalPacks = 10;// Customer want to remove total pack 10/10/2010 $this->cboTotalPacksSelector->SelectedValue;
			$activeRecord->EstDeliveryDate = $this->txtEstDeliveryDate->SafeText;
			$activeRecord->Comments = $this->txtComments->Text;
			try
			{
				$activeRecord->save();
				if ($activeRecord->LatestHistory->StatusCode == "W")
				{
					$historyRecord = new OrderHistoryRecord;
					$historyRecord->OrderID = $activeRecord->ID;
					$historyRecord->StatusCode = "P"; // P = processing
					$historyRecord->Comments = "Generate invoice and process pending order";
					$historyRecord->save();
				}
				$this->ClientScript->registerEndScript("popup","popup2('OrderInvoice','".$this->Service->ConstructUrl("admincp.OrderInvoice",array("id"=>$activeRecord->ID,"num"=>$activeRecord->Num))."',true)");
				//$this->Response->redirect($this->Service->ConstructUrl("admincp.OrderInvoice",array("id"=>$activeRecord->ID,"num"=>$activeRecord->Num)));
				$this->populateData();
			}
			catch(TException $ex)
			{
				$this->Notice->Type = AdminNoticeType::Error;
				$this->Notice->Text = $this->Application->getModule("message")->translate("UPDATE_FAILED","order", "");
			}
		}
	}
	
	protected function btnGenerateInvoiceAdmin_Clicked($sender, $param)
	{
		if ($this->IsValid)
		{
			$activeRecord = $this->getItem();
			$activeRecord->Deliverer = $this->cboDelivererSelector->SelectedValue;
			$activeRecord->TotalPacks = 10;// Customer want to remove total pack $this->cboTotalPacksSelector->SelectedValue;
			$activeRecord->EstDeliveryDate = $this->txtEstDeliveryDate->SafeText;
			$activeRecord->Comments = $this->txtComments->Text;
			try
			{
				$activeRecord->save();
				if ($activeRecord->LatestHistory->StatusCode == "W")
				{
					$historyRecord = new OrderHistoryRecord;
					$historyRecord->OrderID = $activeRecord->ID;
					$historyRecord->StatusCode = "P"; // P = processing
					$historyRecord->Comments = "Generate invoice and process pending order";
					$historyRecord->save();
				}
				$this->ClientScript->registerEndScript("popup","popup2('OrderInvoice','".$this->Service->ConstructUrl("admincp.OrderInvoice",array("id"=>$activeRecord->ID,"num"=>$activeRecord->Num,"mode"=>"admin"))."',true)");
				//$this->Response->redirect($this->Service->ConstructUrl("admincp.OrderInvoice",array("id"=>$activeRecord->ID,"num"=>$activeRecord->Num)));
				$this->populateData();
			}
			catch(TException $ex)
			{
				$this->Notice->Type = AdminNoticeType::Error;
				$this->Notice->Text = $this->Application->getModule("message")->translate("UPDATE_FAILED","order", "");
			}
		}
	}
}

?>