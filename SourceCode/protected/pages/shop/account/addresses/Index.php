<?php

class Index extends TPage
{
	public function onLoad($param)
	{
		parent::onLoad($param);
		if (!$this->IsPostBack)
		{
			$this->populateData();
			if (count($this->rptBilling->Items)>0) $this->lnkAddBilling->Visible = false;
			if ($this->Request->Contains("action") && $this->Request->Contains("msg"))
			{
				switch ($this->Request["action"])
				{
					case "add-success":
					case "update-success":
						$this->Notice->Type = UserNoticeType::Notice;
						$this->Notice->Text = $this->Request["msg"];
						break;
					case "add-failed":
					case "update-failed":
						$this->Notice->Type = UserNoticeType::Error;
						$this->Notice->Text = $this->Request["msg"];
						break;
					default:
						break;
				}
			}
		}
	}
	
	public function populateData()
	{
		$this->rptBilling->DataSource = UserAddressRecord::finder()->getAddressesByType("B");
		$this->rptBilling->DataBind();
		$this->rptShipping->DataSource = UserAddressRecord::finder()->getAddressesByType("S");
		$this->rptShipping->DataBind();
	}
	
	protected function RepeaterItemCommand($sender, $param)
	{
		switch($param->CommandName)
		{
			case "delete":
				$activeRecord = UserAddressRecord::finder()->findByPk($param->Item->txtID->Value);
				if ($activeRecord instanceof UserAddressRecord) $activeRecord->delete();
				break;
			case "set-default":
				$activeRecord = UserAddressRecord::finder()->findByPk($param->Item->txtID->Value);
				$activeRecord->IsDefault = true;
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
		}
		$this->populateData();
		$this->categoryMenu->populateData();
	}
}

?>