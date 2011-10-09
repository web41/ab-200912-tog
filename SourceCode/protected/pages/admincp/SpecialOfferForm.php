<?php

class SpecialOfferForm extends TPage
{
	const AR = "SpecialOfferRecord";
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
				$this->lblHeader->Text = "Update special offer: ".$activeRecord->Title;
				$this->txtName->Text = $activeRecord->Title;
				$this->txtAlias->Text = $activeRecord->Alias;
				$this->radPublish->SelectedValue = $activeRecord->IsPublished;
				$this->txtBrief->Text = $activeRecord->Content;
				//$this->txtFreeText1->Text = $activeRecord->Link;				
				$this->imgFull->Visible = true;
				$this->imgFull->ImageUrl = $this->Request->UrlManagerModule->UrlPrefix."/useruploads/images/offer/".$activeRecord->ImagePath;
			}
			else
			{
				$this->lblHeader->Text = "Add new special offer";
			}
		}
	}

	protected function getItem()
	{
		if ($this->Request->Contains("id") && $this->Request->Contains("alias"))
		{
			// use Active Record to look for the specified post ID
			$activeRecord = Prado::createComponent(self::AR)->finder()->findByspecial_offer_idAndoffer_alias(TPropertyValue::ensureInteger($this->Request['id']), $this->Request['alias']);
			if($activeRecord === null)
			{
				$this->Notice->Type = AdminNoticeType::Error;
				$this->Notice->Text = $this->Application->getModule("message")->translate("ITEM_NOT_FOUND","product");
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

		$hashImage = "";
		if($this->fuImage->HasFile) 
		{
			$hashImage = md5(uniqid(time())).'.'.strtolower(array_pop(explode('.',$this->fuImage->FileName)));
			$filePath = dirname($this->Request->ApplicationFilePath).DIRECTORY_SEPARATOR."useruploads".DIRECTORY_SEPARATOR."images".DIRECTORY_SEPARATOR."offer".DIRECTORY_SEPARATOR;
			if ($activeRecord->ImagePath != '' && $activeRecord->ImagePath != self::NO_IMAGE) 
			{
				// Delete old thumbnail
				try
				{
					chmod($filePath.$activeRecord->ImagePath, 0777);
					unlink($filePath.$activeRecord->ImagePath);
				}
				catch(TException $e){}
			}

			$this->fuImage->saveAs($filePath.$hashImage, true);
		}
		
		$activeRecord->Title = $this->txtName->SafeText;
		$activeRecord->Alias = $this->txtAlias->SafeText;
		$activeRecord->IsPublished = $this->radPublish->SelectedValue;
		$activeRecord->Content = $this->txtBrief->SafeText;
		//$activeRecord->Link = $this->txtFreeText1->SafeText;		
		$activeRecord->ImagePath = (strlen($hashImage)>0)?$hashImage:($activeRecord->ID>0?$activeRecord->ImagePath:self::NO_IMAGE);
		
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
				$msg = $this->Application->getModule("message")->translate(($activeRecord->ID>0 ? "UPDATE_SUCCESS" : "ADD_SUCCESS"),"Special Offer",$activeRecord->Title);
				$activeRecord->save();
				if ($activeRecord->ID>0)
				{
					$criteria = new TActiveRecordCriteria;
					$criteria->Condition = "special_offer_id = :id";
					$criteria->Parameters[":id"] = $activeRecord->ID;
				}
				
				if (strlen($this->Request["refUrl"])>0)
					$url = urldecode($this->Request["refUrl"])."&action=$action&msg=$msg";
				else  $url = $this->Service->ConstructUrl("admincp.SpecialOfferManager",array("action"=>$action, "msg"=>$msg));
				$this->Response->redirect($url);
			}
			catch(TException $e)
			{
				$this->Notice->Type = AdminNoticeType::Error;
				$this->Notice->Text = $this->Application->getModule("message")->translate(($activeRecord->ID>0 ? "UPDATE_FAILED" : "ADD_FAILED"),"Product",$activeRecord->Name);
			}
		}
	}	

	protected function checkImageExtension_ServerValidated($sender, $param)
	{
		if ($param->Value != '') 
			$param->IsValid = in_array(strtolower(array_pop(explode('.', $param->Value))), TPropertyValue::ensureArray($this->Application->Parameters['AVAILABLE_IMAGES']));
	}
}

?>