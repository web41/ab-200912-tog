<?php

class Billing extends TPage
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
			$activeRecord = $this->getItem();
			if ($activeRecord && $activeRecord->ID > 0)
			{
				// Populates the input controls with the existing post data
				$this->cboTitleSelector->SelectedValue = $activeRecord->Title;
				$this->txtFirstName->Text = $activeRecord->FirstName;
				$this->txtLastName->Text = $activeRecord->LastName;
				$this->txtCompanyName->Text = $activeRecord->CompanyName;
				$this->txtAddress1->Text = $activeRecord->Address1;
				$this->txtAddress2->Text = $activeRecord->Address2;
				//$this->txtCity->Text = $activeRecord->City;
				//$this->txtState->Text = $activeRecord->State;
				$this->txtZip->Text = $activeRecord->ZipCode;
				$this->cboCountrySelector->SelectedValue = $activeRecord->CountryCode;
				$this->txtPhone1->Text = $activeRecord->Phone1;
				$this->txtPhone2->Text = $activeRecord->Phone2;
				$this->txtFax->Text = $activeRecord->Fax;
			}
            else
            {
                $this->txtFirstName->Text = $this->Application->User->FirstName;
                $this->txtLastName->Text = $this->Application->User->LastName;
                $this->txtPhone1->Text = $this->Application->User->Phone;
                $this->cboCountrySelector->SelectedValue = 'SG';
            }
		}
	}
	
	protected function getItem()
	{
		// use Active Record to look for the specified post ID
		$activeRecord = UserAddressRecord::finder()->findByuser_idAndaddress_typeAndis_default($this->Application->User->ID,"B",true);
		if($activeRecord === null)
		{
			//$this->Notice->Type = UserNoticeType::Error;
			//$this->Notice->Text = $this->Application->getModule("message")->translate("ITEM_NOT_FOUND","address");
			//$this->mainBox->Visible = false;
			$activeRecord = new UserAddressRecord;
			$activeRecord->IsDefault = true;
			$activeRecord->Type = "B";
		}
		return $activeRecord;
	}
	
	private function bindItem()
	{
		$activeRecord = $this->getItem();
		$activeRecord->UserID = $this->Application->User->ID;
		$activeRecord->Title = $this->cboTitleSelector->SelectedValue;
		if ($activeRecord->Type =="B") $activeRecord->IsDefault = true;
		$activeRecord->FirstName = $this->txtFirstName->SafeText;
		$activeRecord->LastName = $this->txtLastName->SafeText;
		$activeRecord->CompanyName = $this->txtCompanyName->SafeText;
		$activeRecord->Address1 = $this->txtAddress1->SafeText;
		$activeRecord->Address2 = $this->txtAddress2->SafeText;
		$activeRecord->City =  '';//$this->txtCity->SafeText;
		$activeRecord->State = '';//$this->txtState->SafeText;
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
				$cartRecord = CartTempRecord::finder()->withCartTempDetails()->findByPk($this->Session->SessionID);
				if ($cartRecord instanceof CartTempRecord)
				{
					$cartRecord->UserID = $this->Application->User->ID;
					$cartRecord->BillingID = $activeRecord->ID;
					if ($this->chkBillShip->Checked) $cartRecord->ShippingID = $activeRecord->ID;
					$cartRecord->save();
					foreach($cartRecord->CartTempDetails as $cartDetail)
					{
						$cartDetail->UserID = $this->Application->User->ID;
						$cartDetail->save();
					}
					if ($this->chkBillShip->Checked)
						$this->Response->redirect($this->Service->ConstructUrl("shop.checkout.ShippingSchedule"));
					else $this->Response->redirect($this->Service->ConstructUrl("shop.checkout.Shipping"));
				}
				else
				{
					$this->Notice->Type = UserNoticeType::Notice;
					$this->Notice->Text = $this->Application->getModule("message")->translate("CART_EMPTY");
				}
			}
			catch(TException $e)
			{
				$this->Notice->Type = UserNoticeType::Error;
				$this->Notice->Text = $this->Application->getModule("message")->translate(($activeRecord->ID>0 ? "UPDATE_FAILED" : "ADD_FAILED"),"Address","");
			}
			$this->populateData();
			$this->categoryMenu->populateData();
		}
	}
}

?>