<?php

class PaymentForm extends TPage
{
	const AR = "PaymentRecord";
	public function onLoad($param)
	{
		parent::onLoad($param);
		if (!$this->IsPostBack)
		{
			$this->cboMethodSelector->DataSource = PaymentMethodRecord::finder()->getAllItems();
			$this->cboMethodSelector->DataBind();
			$this->cboStatusSelector->DataSource = TPropertyValue::ensureArray($this->Application->Parameters["PAYMENT_STATUS"]);
			$this->cboStatusSelector->DataBind();
			$activeRecord = $this->getItem();
			if ($activeRecord && $activeRecord->ID > 0)
			{
				// Populates the input controls with the existing post data
				$this->lblHeader->Text = "Update payment of order: ".$this->Order->Num;
				$this->cboMethodSelector->SelectedValue = $activeRecord->PaymentMethodID;
				$this->cboStatusSelector->SelectedValue = $activeRecord->Status;
				$this->txtAmount->Text = $activeRecord->Amount;
				$this->txtDesc->Text = $activeRecord->Comments;
			}
			else
			{
				$this->lblHeader->Text = "Add new payment of order:".$this->Order->Num;
			}
		}
	}

	protected function getItem()
	{
		if ($this->Request->Contains("oid"))
		{
			if ($this->Request->Contains("id"))
			{
				// use Active Record to look for the specified post ID
				$activeRecord = Prado::createComponent(self::AR)->finder()->findBypayment_idAndorder_id(TPropertyValue::ensureInteger($this->Request['id']), TPropertyValue::ensureInteger($this->Request['oid']));
				if($activeRecord === null)
				{
					$this->Notice->Type = AdminNoticeType::Error;
					$this->Notice->Text = $this->Application->getModule("message")->translate("ITEM_NOT_FOUND","payment");
					$this->mainBox->Visible = false;
				}
				return $activeRecord;
			}
			else
			{
				return Prado::createComponent(self::AR);
			}
		}
		else
		{
			$this->Notice->Type = AdminNoticeType::Error;
			$this->Notice->Text = $this->Application->getModule("message")->translate("ITEM_NOT_FOUND","order");
			$this->mainBox->Visible = false;
		}
	}

	private function bindItem()
	{
		$activeRecord = $this->getItem();

		$activeRecord->PaymentMethodID = $this->cboMethodSelector->SelectedValue;
		$activeRecord->Status = $this->cboStatusSelector->SelectedValue;
		$activeRecord->Amount = TPropertyValue::ensureFloat($this->txtAmount->Text);
		$activeRecord->Comments = $this->txtDesc->Text;
		
		return $activeRecord;
	}

	protected function btnSubmit_Clicked($sender, $param)
	{
		if ($this->IsValid)
		{
			$activeRecord = $this->bindItem();
			try
			{
				$action = ($activeRecord->ID>0 ? "update-success" : "add-success");
				$msg = $this->Application->getModule("message")->translate(($activeRecord->ID>0 ? "UPDATE_SUCCESS" : "ADD_SUCCESS"),"Payment","");
				$activeRecord->save();
				//$this->Response->redirect($this->Service->ConstructUrl("admincp.OrderForm",array("id"=>$this->Order->ID,"num"=>$this->Order->Num,"action"=>$action, "msg"=>$msg)));
				if (strlen($this->Request["refUrl"])>0)
					$url = urldecode($this->Request["refUrl"])."&id={$this->Order->ID}&num={$this->Order->Num}&action=$action&msg=$msg";
				else  $url = $this->Service->ConstructUrl("admincp.OrderForm",array("id"=>$this->Order->ID,"num"=>$this->Order->Num,"action"=>$action, "msg"=>$msg));
				$this->Response->redirect($url);
			}
			catch(TException $e)
			{
				$this->Notice->Type = AdminNoticeType::Error;
				$this->Notice->Text = $this->Application->getModule("message")->translate(($activeRecord->ID>0 ? "UPDATE_FAILED" : "ADD_FAILED"),"Payment","");
			}
		}
	}

	protected function btnAddMore_Clicked($sender, $param)
	{
		if ($this->IsValid)
		{
			$activeRecord = $this->bindItem();
			try
			{
				$activeRecord->save();
				$this->Response->redirect($this->Service->ConstructUrl("admincp.PaymentForm",array("oid"=>$this->Order->ID)));
			}
			catch(TException $e)
			{
				$this->Notice->Type = AdminNoticeType::Error;
				$this->Notice->Text = $this->Application->getModule("message")->translate(($activeRecord->ID>0 ? "UPDATE_FAILED" : "ADD_FAILED"),"Coupon",$activeRecord->Code);
			}
		}
	}
	
	public function getOrder()
	{
		if ($this->Request->Contains("oid"))
		{
			$activeRecord = OrderRecord::finder()->findByPk(TPropertyValue::ensureInteger($this->Request['oid']));
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