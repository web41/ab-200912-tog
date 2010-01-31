<?php

class OrderHistoryForm extends TPage
{
	const AR = "OrderHistoryRecord";
	public function onLoad($param)
	{
		parent::onLoad($param);
		if (!$this->IsPostBack)
		{
			// bind data to combobox
			$this->cboStatusSelector->DataSource = OrderStatusRecord::finder()->getAllItems();
			$this->cboStatusSelector->DataBind();
			$activeRecord = $this->getItem();
			$order = $this->getOrder();
			if ($order)
			{
				if ($activeRecord && $activeRecord->ID > 0)
				{
					// Populates the input controls with the existing post data
					$this->lblHeader->Text = "Update history of order: ".$order->Num;
					$this->cboStatusSelector->SelectedValue = $activeRecord->StatusCode;
					$this->dpCreateDate->Data = $activeRecord->CreateDate;
					$this->txtDesc->Text = $activeRecord->Comments;
				}
				else
				{
					$this->lblHeader->Text = "Add new history of order: ".$order->Num;
					$this->dpCreateDate->Data = time();
				}
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
				$activeRecord = Prado::createComponent(self::AR)->finder()->findByhistory_idAndorder_id(TPropertyValue::ensureInteger($this->Request['id']), TPropertyValue::ensureInteger($this->Request['oid']));
				if($activeRecord === null)
				{
					$this->Notice->Type = AdminNoticeType::Error;
					$this->Notice->Text = $this->Application->getModule("message")->translate("ITEM_NOT_FOUND","history");
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
		$activeRecord->OrderID = $this->Order->ID;
		$activeRecord->StatusCode = $this->cboStatusSelector->SelectedValue;
		$activeRecord->CreateDate = $this->dpCreateDate->Data;
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
				$msg = $this->Application->getModule("message")->translate(($activeRecord->ID>0 ? "UPDATE_SUCCESS" : "ADD_SUCCESS"),"History","");
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
				$this->Notice->Text = $this->Application->getModule("message")->translate(($activeRecord->ID>0 ? "UPDATE_FAILED" : "ADD_FAILED"),"History","");
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
				$this->Response->redirect($this->Service->ConstructUrl("admincp.OrderHistoryForm",array("oid"=>$this->Order->ID)));
			}
			catch(TException $e)
			{
				$this->Notice->Type = AdminNoticeType::Error;
				$this->Notice->Text = $this->Application->getModule("message")->translate(($activeRecord->ID>0 ? "UPDATE_FAILED" : "ADD_FAILED"),"History","");
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