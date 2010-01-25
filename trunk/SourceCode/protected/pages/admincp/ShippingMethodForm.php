<?php

class ShippingMethodForm extends TPage
{
	const AR = "ShippingMethodRecord";
	const NO_IMAGE = "noimage.png";
	public function onLoad($param)
	{
		parent::onLoad($param);
		if (!$this->IsPostBack)
		{
			$activeRecord = $this->getItem();
			if ($activeRecord && $activeRecord->ID > 0)
			{
				// Populates the input controls with the existing post data
				$this->lblHeader->Text = "Update shipping method: ".$activeRecord->Name;
				$this->txtName->Text = $activeRecord->Name;
				$this->txtAlias->Text = $activeRecord->Alias;
				$this->txtPrice->Text = TPropertyValue::ensureFloat($activeRecord->Price);
				$this->radPublish->SelectedValue = $activeRecord->IsPublished;
			}
			else
			{
				$this->lblHeader->Text = "Add new shipping method";
			}
		}
	}

	protected function getItem()
	{
		if ($this->Request->Contains("id") && $this->Request->Contains("alias"))
		{
			// use Active Record to look for the specified post ID
			$activeRecord = Prado::createComponent(self::AR)->finder()->findBymethod_idAndmethod_alias(TPropertyValue::ensureInteger($this->Request['id']), $this->Request['alias']);
			if($activeRecord === null)
			{
				$this->Notice->Type = AdminNoticeType::Error;
				$this->Notice->Text = $this->Application->getModule("message")->translate("ITEM_NOT_FOUND","shipping method");
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

		$activeRecord->Name = $this->txtName->SafeText;
		$activeRecord->Alias = $this->txtAlias->SafeText;
		$activeRecord->Price = TPropertyValue::ensureFloat($this->txtPrice->Text);
		$activeRecord->IsPublished = $this->radPublish->SelectedValue;

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
				$msg = $this->Application->getModule("message")->translate(($activeRecord->ID>0 ? "UPDATE_SUCCESS" : "ADD_SUCCESS"),"Shipping method",$activeRecord->Name);
				$activeRecord->save();
				//$this->Response->redirect($this->Service->ConstructUrl("admincp.ShippingMethodManager",array("action"=>$action, "msg"=>$msg)));
				if (strlen($this->Request["refUrl"])>0)
					$url = urldecode($this->Request["refUrl"])."&action=$action&msg=$msg";
				else  $url = $this->Service->ConstructUrl("admincp.ShippingMethodManager",array("action"=>$action, "msg"=>$msg));
				$this->Response->redirect($url);
			}
			catch(TException $e)
			{
				$this->Notice->Type = AdminNoticeType::Error;
				$this->Notice->Text = $this->Application->getModule("message")->translate(($activeRecord->ID>0 ? "UPDATE_FAILED" : "ADD_FAILED"),"Shipping method",$activeRecord->Name);
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
				$this->Response->redirect($this->Service->ConstructUrl("admincp.ShippingMethodForm"));
			}
			catch(TException $e)
			{
				$this->Notice->Type = AdminNoticeType::Error;
				$this->Notice->Text = $this->Application->getModule("message")->translate(($activeRecord->ID>0 ? "UPDATE_FAILED" : "ADD_FAILED"),"Shipping method",$activeRecord->Name);
			}
		}
	}

	protected function uniqueCheck_ServerValidated($sender, $param)
	{
		if ($param->Value != '')
		{
			$criteria = new TActiveRecordCriteria;
			$criteria->Condition = "method_name = :name";
			$criteria->Parameters[":name"] = $param->Value;
			$activeRecord = $this->getItem();
			if ($activeRecord && $activeRecord->ID > 0) $criteria->Condition .= " and method_id <> '".$activeRecord->ID."'";
			$param->IsValid = count(Prado::createComponent(self::AR)->finder()->find($criteria)) == 0;
		}
	}

	protected function checkImageExtension_ServerValidate($sender, $param)
	{
		if ($param->Value != '') 
			$param->IsValid = in_array(strtolower(array_pop(explode('.', $param->Value))), TPropertyValue::ensureArray($this->Application->Parameters['AVAILABLE_IMAGES']));
	}
}

?>