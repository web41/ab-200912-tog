<?php

class ProductForm extends TPage
{
	const AR = "ProductRecord";
	const NO_IMAGE = "noimage.png";
	public function onLoad($param)
	{
		parent::onLoad($param);
		if (!$this->IsPostBack)
		{
			// fill parent selector combobox
			$criteria = new TActiveRecordCriteria;
			$criteria->Condition = "brand_id > 0";
			$criteria->OrdersBy["brand_name"] = "asc";
			$this->cboBrandSelector->DataSource = BrandRecord::finder()->findAll($criteria);
			$this->cboBrandSelector->DataBind();
			$criteria = new TActiveRecordCriteria;
			$criteria->Condition = "mf_id > 0";
			$criteria->OrdersBy["mf_name"] = "asc";
			$this->cboMfSelector->DataSource = ManufacturerRecord::finder()->findAll($criteria);
			$this->cboMfSelector->DataBind();
			//$this->cboUOMSelector->DataSource = TPropertyValue::ensureArray($this->Application->Parameters["UNITS_OF_MEASURE"]);
			//$this->cboUOMSelector->DataBind();
			$this->cboCatSelector->DataSource = CategoryRecord::finder()->getCategoryTree();
			$this->cboCatSelector->DataBind();
			$this->populateProperties();
			$activeRecord = $this->getItem();
			if ($activeRecord && $activeRecord->ID > 0)
			{
				// Populates the input controls with the existing post data
				$this->lblHeader->Text = "Update product: ".$activeRecord->Name;
				$this->txtSKU->Text = $activeRecord->SKU;
				$this->txtName->Text = $activeRecord->Name;
				$this->txtAlias->Text = $activeRecord->Alias;
				//$this->txtInStock->Text = $activeRecord->InStock;
				//$this->cboUOMSelector->SelectedValue = $activeRecord->UOM;
				$this->cboBrandSelector->SelectedValue = $activeRecord->BrandID;
				$this->cboMfSelector->SelectedValue = $activeRecord->MfID;
				$this->cboCatSelector->SelectedValues = $activeRecord->CategoryIDs;
				//$this->txtPrice->Text = $activeRecord->Price;
				$this->cboDiscountSelector->SelectedValue = $activeRecord->DiscountID;
				$this->radPublish->SelectedValue = $activeRecord->IsPublished;
				$this->radNewArrival->SelectedValue = $activeRecord->IsNewArrival;
				$this->radBestSeller->SelectedValue = $activeRecord->IsBestSeller;
				$this->radHotDeal->SelectedValue = $activeRecord->IsHotDeal;
				$this->txtBrief->Text = $activeRecord->Brief;
				$this->txtDesc->Text = $activeRecord->Description;
			}
			else
			{
				$this->lblHeader->Text = "Add new product";
			}
		}
	}

	protected function getItem()
	{
		if ($this->Request->Contains("id") && $this->Request->Contains("alias"))
		{
			// use Active Record to look for the specified post ID
			$activeRecord = Prado::createComponent(self::AR)->withProperties()->finder()->findByproduct_idAndproduct_alias(TPropertyValue::ensureInteger($this->Request['id']), $this->Request['alias']);
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
	
	private function populateProperties()
	{
		$properties = $this->getItem()->Properties;
		if (count($properties)<=0)
		{
			$properties[] = new PropertyRecord;
			$this->setViewState("NewPropertyCount",1,0);
		}
		$this->rptProperty->DataSource = $properties;
		$this->rptProperty->DataBind();
	}
	
	private function bindItem()
	{
		$activeRecord = $this->getItem();

		$hashImage = "";
		if($this->fuImage->HasFile) 
		{
			$hashImage = md5(uniqid(time())).'.'.strtolower(array_pop(explode('.',$this->fuImage->FileName)));
			$filePath = dirname($this->Request->ApplicationFilePath).DIRECTORY_SEPARATOR."useruploads".DIRECTORY_SEPARATOR."images".DIRECTORY_SEPARATOR."product".DIRECTORY_SEPARATOR;
			if ($activeRecord->ImagePath != '') 
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

		$hashThumb = "";
		if($this->fuThumb->HasFile) 
		{
			$hashThumb = md5(uniqid(time())).'.'.strtolower(array_pop(explode('.',$this->fuThumb->FileName)));
			$filePath = dirname($this->Request->ApplicationFilePath).DIRECTORY_SEPARATOR."useruploads".DIRECTORY_SEPARATOR."images".DIRECTORY_SEPARATOR."product".DIRECTORY_SEPARATOR."thumbs".DIRECTORY_SEPARATOR;
			if ($activeRecord->ThumbnailPath != '') 
			{
				// Delete old thumbnail
				try
				{
					chmod($filePath.$activeRecord->ThumbnailPath, 0777);
					unlink($filePath.$activeRecord->ThumbnailPath);
				}
				catch(TException $e){}
			}

			$this->fuThumb->saveAs($filePath.$hashThumb, true);
		}

		$activeRecord->SKU = $this->txtSKU->SafeText;
		$activeRecord->Name = $this->txtName->SafeText;
		$activeRecord->Alias = $this->txtAlias->SafeText;
		//$activeRecord->InStock = TPropertyValue::ensureInteger($this->txtInStock->SafeText);
		//$activeRecord->UOM = $this->cboUOMSelector->SelectedValue;
		$activeRecord->BrandID = $this->cboBrandSelector->SelectedValue;
		$activeRecord->MfID = $this->cboMfSelector->SelectedValue;
		
		//$activeRecord->Price = TPropertyValue::ensureFloat($this->txtPrice->Text);
		$activeRecord->DiscountID = $this->cboDiscountSelector->SelectedValue;
		$activeRecord->IsPublished = $this->radPublish->SelectedValue;
		$activeRecord->IsNewArrival = $this->radNewArrival->SelectedValue;
		$activeRecord->IsBestSeller = $this->radBestSeller->SelectedValue;
		$activeRecord->IsHotDeal = $this->radHotDeal->SelectedValue;
		$activeRecord->Brief = $this->txtBrief->SafeText;
		$activeRecord->Description = $this->txtDesc->Text;
		$activeRecord->ImagePath = (strlen($hashImage)>0)?$hashImage:($activeRecord->ID>0?$activeRecord->ImagePath:self::NO_IMAGE);
		$activeRecord->ThumbnailPath = (strlen($hashThumb)>0)?$hashThumb:($activeRecord->ID>0?$activeRecord->ThumbnailPath:self::NO_IMAGE);
		
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
				$msg = $this->Application->getModule("message")->translate(($activeRecord->ID>0 ? "UPDATE_SUCCESS" : "ADD_SUCCESS"),"Product",$activeRecord->Name);
				$activeRecord->save();
				if ($activeRecord->ID>0)
				{
					$criteria = new TActiveRecordCriteria;
					$criteria->Condition = "product_id = :id";
					$criteria->Parameters[":id"] = $activeRecord->ID;
					ProductCatRecord::finder()->deleteAll($criteria);
					PropertyRecord::finder()->deleteAll($criteria);
				}
				foreach($this->cboCatSelector->SelectedValues as $catID)
				{
					$productCat = new ProductCatRecord(array("ProductID"=>$activeRecord->ID,"CatID"=>$catID));
					$productCat->save();
				}
				foreach($this->rptProperty->Items as $item)
				{
					$price = TPropertyValue::ensureFloat($item->txtPrice->Text);
					$name = $item->txtName->SafeText;
					$stock = TPropertyValue::ensureInteger($item->txtInStock->Text);
					$packageType = new PropertyRecord;
					if ($price>0&&$stock>0&&strlen($name)>0)
					{
						$packageType->ProductID = $activeRecord->ID;
						$packageType->Price = $price;
						$packageType->Name = $name;
						$packageType->InStock = $stock;
						$packageType->save();
					}
					//var_dump($uom);
				}
				$this->Response->redirect($this->Service->ConstructUrl("admincp.ProductManager",array("action"=>$action, "msg"=>$msg)));
			}
			catch(TException $e)
			{
				$this->Notice->Type = AdminNoticeType::Error;
				$this->Notice->Text = $e;//$this->Application->getModule("message")->translate(($activeRecord->ID>0 ? "UPDATE_FAILED" : "ADD_FAILED"),"Product",$activeRecord->Name);
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
				if ($activeRecord->ID>0)
				{
					$criteria = new TActiveRecordCriteria;
					$criteria->Condition = "product_id = :id";
					$criteria->Parameters[":id"] = $activeRecord->ID;
					ProductCatRecord::finder()->deleteAll($criteria);
					PackageTypeRecord::finder()->deleteAll($criteria);
				}
				foreach($this->cboCatSelector->SelectedValues as $catID)
				{
					$record = new ProductCatRecord(array("ProductID"=>$activeRecord->ID,"CatID"=>$catID));
					$record->save();
				}
				$this->Response->redirect($this->Service->ConstructUrl("admincp.ProductForm"));
			}
			catch(TException $e)
			{
				$this->Notice->Type = AdminNoticeType::Error;
				$this->Notice->Text = $this->Application->getModule("message")->translate(($activeRecord->ID>0 ? "UPDATE_FAILED" : "ADD_FAILED"),"Product",$activeRecord->Name);
			}
		}
	}
	
	protected function btnAddProperty_Clicked($sender, $param)
	{
		$properties = array();
		$count = $this->getViewState('NewPropertyCount',0)+1;
		$this->setViewState('NewPropertyCount',$count,0);
		if ($this->Item->ID>0)
		{
			$properties = $this->Item->Properties;
		}
		for($i=1;$i<=$count;$i++)
		{
			$properties[] = new PropertyRecord;
		}
		$this->rptProperty->DataSource = $properties;
		$this->rptProperty->DataBind();
	}

	protected function uniqueCheck_ServerValidated($sender, $param)
	{
		if ($param->Value != '')
		{
			$criteria = new TActiveRecordCriteria;
			$criteria->Condition = "product_name = :name";
			$criteria->Parameters[":name"] = $param->Value;
			$activeRecord = $this->getItem();
			if ($activeRecord && $activeRecord->ID > 0) $criteria->Condition .= " and product_id <> '".$activeRecord->ID."'";
			$param->IsValid = count(Prado::createComponent(self::AR)->finder()->find($criteria)) == 0;
		}
	}
	
	protected function uniqueCheck2_ServerValidated($sender, $param)
	{
		if ($param->Value != '')
		{
			$criteria = new TActiveRecordCriteria;
			$criteria->Condition = "product_sku = :name";
			$criteria->Parameters[":name"] = $param->Value;
			$activeRecord = $this->getItem();
			if ($activeRecord && $activeRecord->ID > 0) $criteria->Condition .= " and product_id <> '".$activeRecord->ID."'";
			$param->IsValid = count(Prado::createComponent(self::AR)->finder()->find($criteria)) == 0;
		}
	}

	protected function checkImageExtension_ServerValidated($sender, $param)
	{
		if ($param->Value != '') 
			$param->IsValid = in_array(strtolower(array_pop(explode('.', $param->Value))), TPropertyValue::ensureArray($this->Application->Parameters['AVAILABLE_IMAGES']));
	}
}

?>