<?php

class UserAddressForm extends TPage
{
	const AR = "UserAddressRecord";
	public function onLoad($param)
	{
		parent::onLoad($param);
		if (!$this->IsPostBack)
		{
			$this->cboUserSelector->DataSource = UserRecord::finder()->getAllItems();
			$this->cboUserSelector->DataBind();
			$this->cboTitleSelector->DataSource = TPropertyValue::ensureArray($this->Application->Parameters["USER_TITLE"]);
			$this->cboTitleSelector->DataBind();
			$this->cboCountrySelector->DataSource = CountryRecord::finder()->getAllItems();
			$this->cboCountrySelector->DataBind();
			$activeRecord = $this->getItem();
			if ($activeRecord && $activeRecord->ID > 0)
			{
				// Populates the input controls with the existing post data
				$this->lblHeader->Text = "Update user address: ".$activeRecord->FirstName." ".$activeRecord->LastName;
				$this->cboUserSelector->SelectedValue = $activeRecord->UserID;
				$this->txtAlias->Text = $activeRecord->Alias;
				$this->cboTypeSelector->SelectedValue = $activeRecord->Type;
				$this->cboDefaultSelector->SelectedValue = $activeRecord->IsDefault;
				$this->cboTitleSelector->SelectedValue = $activeRecord->Title;
				$this->txtCompanyName->Text = $activeRecord->CompanyName;
				$this->txtFirstName->Text = $activeRecord->FirstName;
				$this->txtLastName->Text = $activeRecord->LastName;
				$this->txtAddress1->Text = $activeRecord->Address1;
				$this->txtAddress2->Text = $activeRecord->Address2;
				$this->txtCity->Text = $activeRecord->City;
				$this->txtState->Text = $activeRecord->State;
				$this->txtZipCode->Text = $activeRecord->ZipCode;
				$this->cboCountrySelector->SelectedValue = $activeRecord->CountryCode;
				$this->txtPhone1->Text = $activeRecord->Phone1;
				$this->txtPhone2->Text = $activeRecord->Phone2;
				$this->txtFax->Text = $activeRecord->Fax;
			}
			else
			{
				$this->cboUserSelector->SelectedValue = TPropertyValue::ensureInteger($this->Request['u']);
				$this->lblHeader->Text = "Add new user address";
			}
			$this->cboUserSelector_CallBack($this->cboUserSelector,null);
		}
	}

	protected function getItem()
	{
		if ($this->Request->Contains("id") && $this->Request->Contains("alias"))
		{
			// use Active Record to look for the specified post ID
			$activeRecord = Prado::createComponent(self::AR)->finder()->findByaddress_idAndaddress_alias(TPropertyValue::ensureInteger($this->Request['id']), $this->Request['alias']);
			if($activeRecord === null)
			{
				$this->Notice->Type = AdminNoticeType::Error;
				$this->Notice->Text = $this->Application->getModule("message")->translate("ITEM_NOT_FOUND","address");
				$this->mainBox->Visible = false;
			}
			return $activeRecord;
		}
		else
		{
			return Prado::createComponent(self::AR);
		}
	}

	private function bindItem()
	{
		$activeRecord = $this->getItem();
		
		$activeRecord->UserID = $this->cboUserSelector->SelectedValue;
		$activeRecord->Alias = $this->txtAlias->SafeText;
		$activeRecord->Title = $this->cboTitleSelector->SelectedValue;
		$activeRecord->Type = $this->cboTypeSelector->SelectedValue;
		$activeRecord->IsDefault = $this->cboDefaultSelector->SelectedValue;
		if ($activeRecord->Type =="B") $activeRecord->IsDefault = true;
		$activeRecord->FirstName = $this->txtFirstName->SafeText;
		$activeRecord->LastName = $this->txtLastName->SafeText;
		$activeRecord->CompanyName = $this->txtCompanyName->SafeText;
		$activeRecord->Address1 = $this->txtAddress1->SafeText;
		$activeRecord->Address2 = $this->txtAddress2->SafeText;
		$activeRecord->City =  $this->txtCity->SafeText;
		$activeRecord->State = $this->txtState->SafeText;
		$activeRecord->ZipCode = $this->txtZipCode->SafeText;
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
				$action = ($activeRecord->ID>0 ? "update-success" : "add-success");
				$msg = $this->Application->getModule("message")->translate(($activeRecord->ID>0 ? "UPDATE_SUCCESS" : "ADD_SUCCESS"),"Address",$activeRecord->FirstName.' '.$activeRecord->LastName);
				$activeRecord->save();

				// if set default = true, reset all others items
				if($activeRecord->Type=="S"&&$activeRecord->IsDefault)
				{
					$shippingAddresses = Prado::createComponent(self::AR)->finder()->getAddressesByType("S",$activeRecord->UserID);
					foreach($shippingAddresses as $address)
					{
						if ($address->ID != $activeRecord->ID)
						{
							$address->IsDefault = false;
							$address->save();
						}
					}
				}
				$this->Response->redirect($this->Service->ConstructUrl("admincp.UserAddressManager",array("u"=>$activeRecord->UserID,"action"=>$action, "msg"=>$msg)));
			}
			catch(TException $e)
			{
				$this->Notice->Type = AdminNoticeType::Error;
				$this->Notice->Text = $this->Application->getModule("message")->translate(($activeRecord->ID>0 ? "UPDATE_FAILED" : "ADD_FAILED"),"Address",$activeRecord->FirstName.' '.$activeRecord->LastName);
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
				// if set default = true, reset all others items
				if($activeRecord->Type=="S"&&$activeRecord->IsDefault)
				{
					$shippingAddresses = Prado::createComponent(self::AR)->finder()->getAddressesByType("S",$activeRecord->UserID);
					foreach($shippingAddresses as $address)
					{
						if ($address->ID != $activeRecord->ID)
						{
							$address->IsDefault = false;
							$address->save();
						}
					}
				}
				$this->Response->redirect($this->Service->ConstructUrl("admincp.UserAddressForm",array("u"=>$activeRecord->UserID)));
			}
			catch(TException $e)
			{
				$this->Notice->Type = AdminNoticeType::Error;
				$this->Notice->Text = $this->Application->getModule("message")->translate(($activeRecord->ID>0 ? "UPDATE_FAILED" : "ADD_FAILED"),"User",$activeRecord->FirstName.' '.$activeRecord->LastName);
			}
		}
	}
	
	protected function cboUserSelector_CallBack($sender, $param)
	{
		$this->cboTypeSelector->Items->clear();
		if (count(UserAddressRecord::finder()->getAddressesByType("B",$sender->SelectedValue,array($this->Item->ID)))<=0)
		{
			//$this->cboTypeSelector->Items->remove($this->cboTypeSelector->Items->findItemByValue("B"));
			$item = new TListItem; $item->Text = "Billing Address"; $item->Value = "B";
			$this->cboTypeSelector->Items->add($item);
		}
		$item = new TListItem; $item->Text = "Shipping Address"; $item->Value = "S";
		$this->cboTypeSelector->Items->add($item);
		
	}
}

?>