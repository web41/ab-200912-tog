<?php
Prado::using('System.I18N.core.NumberFormat');
Prado::using('Application.common.common');
class ShippingSchedule extends TPage
{
	public function getEstDeliveryDate()
	{
		return TPropertyValue::ensureArray(unserialize($this->getViewState("EstDeliveryDate","")));
	}

	public function setEstDeliveryDate($value)
	{
		$this->setViewState("EstDeliveryDate",serialize($value),"");
	}
	public function onLoad($param)
	{
		parent::onLoad($param);
		
		if (!$this->IsPostBack)
		{
			$cartRecord = CartTempRecord::finder()->findByPk($this->Session->SessionID);
			if (!$cartRecord) $this->Response->redirect($this->Service->ConstructUrl("shop.cart.Index"));
			$this->setEstDeliveryDate(OrderRecord::estimateDeliveryDate());
			$this->cboDeliveryDateSelector->Items->clear();
			$slots = TPropertyValue::ensureArray($this->Application->Parameters["DELIVERY_SLOTS"]);
			foreach($this->EstDeliveryDate as $est)
			{
				$item = new TListItem; $item->Text = $item->Value = date("l d/m/Y",$est['day']).' '.$slots[$est['time']];
				$this->cboDeliveryDateSelector->Items->add($item);
			}
		}
	}

	public function btnSubmit_Clicked($sender, $param)
	{
		if ($this->IsValid)
		{
			$cartRecord = CartTempRecord::finder()->findByPk($this->Session->SessionID);
			if ($cartRecord instanceof CartTempRecord)
			{
				$cartRecord->EstDeliveryDate = $this->cboDeliveryDateSelector->SelectedValue;
				try
				{
					$cartRecord->save();
					$this->Response->redirect($this->Service->ConstructUrl("shop.checkout.Review"));
				}
				catch(TException $e)
				{
					$this->Notice->Type = UserNoticeType::Error;
					$this->Notice->Text = $this->Application->getModule("message")->translate(($activeRecord->ID>0 ? "UPDATE_FAILED" : "ADD_FAILED"),"Shipping method","");
				}
			}
			else
			{
				$this->Notice->Type = UserNoticeType::Error;
				$this->Notice->Text = $this->Application->getModule("message")->translate("CART_EMPTY");
			}
			$this->populateData();
			$this->categoryMenu->populateData();
		}
	}

}

?>