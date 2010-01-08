<?php

class Shipping extends TPage
{
	public function onLoad($param)
	{
		parent::onLoad($param);
		if (!$this->IsPostBack)
		{
			$cartRecord = CartTempRecord::finder()->findByPk($this->Session->SessionID);
			if (!$cartRecord)
				$this->Response->redirect($this->Service->ConstructUrl("shop.cart.Index"));
			// populate drop down lists
			$this->cboTitleSelector->DataSource = TPropertyValue::ensureArray($this->Application->Parameters["USER_TITLE"]);
			$this->cboTitleSelector->DataBind();
			$this->cboCountrySelector->DataSource = CountryRecord::finder()->getAllItems();
			$this->cboCountrySelector->DataBind();
			$this->populateShippingAddress();
			$this->cboShippingSelector_CallBack($this->cboShippingSelector,null);
		}
	}
	
	protected function populateShippingAddress()
	{
		$this->cboShippingSelector->Items->clear();
		$shippingAddresses = UserAddressRecord::finder()->getAddressesByType("S");
		foreach($shippingAddresses as $address)
		{
			$item = new TListItem;
			$item->Text = $address->Title." ".$address->FirstName." ".$address->LastName;
			$item->Value = $address->ID;
			if ($address->IsDefault) $item->Selected = true;
			$this->cboShippingSelector->Items->add($item);
		}
	}

	protected function getItem($id)
	{
		// use Active Record to look for the specified post ID
		$activeRecord = UserAddressRecord::finder()->findByaddress_idAnduser_idAndaddress_type($id,$this->Application->User->ID,"S");
		if($activeRecord === null)
		{
			//$this->Notice->Type = UserNoticeType::Error;
			//$this->Notice->Text = $this->Application->getModule("message")->translate("ITEM_NOT_FOUND","address");
			//$this->mainBox->Visible = false;
			$activeRecord = new UserAddressRecord;
			$activeRecord->IsDefault = true;
			$activeRecord->Type = "S";
		}
		return $activeRecord;
	}

	private function bindItem()
	{
		$activeRecord = $this->getItem($this->cboShippingSelector->SelectedValue);
		$activeRecord->UserID = $this->Application->User->ID;
		$activeRecord->Title = $this->cboTitleSelector->SelectedValue;
		$activeRecord->Type = "S";
		if ($activeRecord->ID<=0) $activeRecord->IsDefault = true;
		$activeRecord->FirstName = $this->txtFirstName->SafeText;
		$activeRecord->LastName = $this->txtLastName->SafeText;
		$activeRecord->CompanyName = $this->txtCompanyName->SafeText;
		$activeRecord->Address1 = $this->txtAddress1->SafeText;
		$activeRecord->Address2 = $this->txtAddress2->SafeText;
		$activeRecord->City =  $this->txtCity->SafeText;
		$activeRecord->State = $this->txtState->SafeText;
		$activeRecord->ZipCode = $this->txtZip->SafeText;
		$activeRecord->CountryCode = $this->cboCountrySelector->SelectedValue;
		$activeRecord->Phone1 = $this->txtPhone1->SafeText;
		$activeRecord->Phone2 = $this->txtPhone2->SafeText;
		$activeRecord->Fax = $this->txtFax->SafeText;

		return $activeRecord;
	}

	protected function btnSubmit_Clicked($sender, $param)
	{
		if ($this->IsValid)
		{
			$activeRecord = $this->bindItem();
			try
			{
				$activeRecord->save();
				$shippingAddresses = UserAddressRecord::finder()->getAddressesByType("S");
				foreach($shippingAddresses as $address)
				{
					if ($address->ID != $activeRecord->ID)
					{
						$address->IsDefault = false;
						$address->save();
					}
				}
				$cartRecord = CartTempRecord::finder()->withCartTempDetails()->findByPk($this->Session->SessionID);
				if ($cartRecord instanceof CartTempRecord)
				{
					$cartRecord->UserID = $this->Application->User->ID;
					$cartRecord->ShippingID = $activeRecord->ID;
					$cartRecord->save();
					foreach($cartRecord->CartTempDetails as $cartDetail)
					{
						$cartDetail->UserID = $this->Application->User->ID;
						$cartDetail->save();
					}
					$this->Response->redirect($this->Service->ConstructUrl("shop.checkout.ShippingMethod"));
				}
				else
				{
					$this->Notice->Type = UserNoticeType::Error;
					$this->Notice->Text = $this->Application->getModule("message")->translate("ITEM_NOT_FOUND","cart");
				}
			}
			catch(TException $e)
			{
				$this->Notice->Type = UserNoticeType::Error;
				$this->Notice->Text = $this->Application->getModule("message")->translate(($activeRecord->ID>0 ? "UPDATE_FAILED" : "ADD_FAILED"),"Address","");
			}
		}
	}
	
	protected function cboShippingSelector_CallBack($sender, $param)
	{
		
		$activeRecord = $this->getItem($sender->SelectedValue);
		if ($activeRecord && $activeRecord->ID > 0)
		{
			// Populates the input controls with the existing post data
			$this->cboTitleSelector->SelectedValue = $activeRecord->Title;
			$this->txtFirstName->Text = $activeRecord->FirstName;
			$this->txtLastName->Text = $activeRecord->LastName;
			$this->txtCompanyName->Text = $activeRecord->CompanyName;
			$this->txtAddress1->Text = $activeRecord->Address1;
			$this->txtAddress2->Text = $activeRecord->Address2;
			$this->txtCity->Text = $activeRecord->City;
			$this->txtState->Text = $activeRecord->State;
			$this->txtZip->Text = $activeRecord->ZipCode;
			$this->cboCountrySelector->SelectedValue = $activeRecord->CountryCode;
			$this->txtPhone1->Text = $activeRecord->Phone1;
			$this->txtPhone2->Text = $activeRecord->Phone2;
			$this->txtFax->Text = $activeRecord->Fax;
		}
		else
		{
			// Populates the input controls with the existing post data
			$this->cboTitleSelector->SelectedValue = "Mr.";
			$this->txtFirstName->Text = "";
			$this->txtLastName->Text = "";
			$this->txtCompanyName->Text = "";
			$this->txtAddress1->Text = "";
			$this->txtAddress2->Text = "";
			$this->txtCity->Text = "";
			$this->txtState->Text = "";
			$this->txtZip->Text = "";
			$this->cboCountrySelector->SelectedValue = "SG";
			$this->txtPhone1->Text = "";
			$this->txtPhone2->Text = "";
			$this->txtFax->Text = "";
		}
	}
}

?>