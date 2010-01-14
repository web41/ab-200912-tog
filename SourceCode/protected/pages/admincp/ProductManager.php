<?php

class ProductManager extends TPage
{
	private $_maxPage = 1;
	private $_currentPage = 1;
	private $_sortBy = "";
	private $_sortType = "";
	private $_searchText = "";
	private $_brandID = 0;
	private $_mfID = 0;
	private $_catID = 0;
	private $_sortable = array("product_id","product_name","product_sku","brand_id","mf_id","c_date","product_order");
	private $_queryParams = array("p","st","sb","b","mf","c","q");
	const AR = "ProductRecord";

	public function getSortBy()
	{
		return $this->_sortBy;
	}

	public function setSortBy($value)
	{
		$this->_sortBy = $value;
	}

	public function getSortType()
	{
		return $this->_sortType;
	}

	public function setSortType($value)
	{
		$this->_sortType = (strtolower($value)==="desc") ? "desc" : "asc";
	}

	public function getSortable()
	{
		return $this->_sortable;
	}

	public function setMaxPage($value)
	{
		$this->_maxPage = TPropertyValue::ensureInteger($value);
	}

	public function getMaxPage()
	{
		return $this->_maxPage;
	}

	public function setCurrentPage($value)
	{
		$this->_currentPage = TPropertyValue::ensureInteger($value);
	}

	public function getCurrentPage()
	{
		return $this->_currentPage;
	}

	public function getQueryParameters()
	{
		return $this->_queryParams;
	}

	public function getSearchText()
	{
		return $this->_searchText;
	}

	public function setSearchText($value)
	{
		$this->_searchText = $value;
	}

	public function getBrandID()
	{
		return $this->_brandID;
	}

	public function setBrandID($value)
	{
		$this->_brandID = TPropertyValue::ensureInteger($value);
	}
	
	public function getMfID()
	{
		return $this->_mfID;
	}

	public function setMfID($value)
	{
		$this->_mfID = TPropertyValue::ensureInteger($value);
	}
	
	public function getCatID()
	{
		return $this->_catID;
	}

	public function setCatID($value)
	{
		$this->_catID = TPropertyValue::ensureInteger($value);
	}

	public function onLoad($param)
	{
		parent::onLoad($param);
		// register search button
		$this->ClientScript->registerDefaultButton($this->txtSearchText,$this->btnSearch);
		$this->CurrentPage = ($this->Request->contains('p')) ? TPropertyValue::ensureInteger($this->Request['p']) : 1;
		$this->SortBy = ($this->Request->contains('sb')) ? TPropertyValue::ensureInteger($this->Request['sb']) : 1;
		$this->SortType = ($this->Request->contains('st')) ? $this->Request['st'] : 'asc';
		$this->BrandID = ($this->Request->contains('b')) ? TPropertyValue::ensureInteger($this->Request['b']) : 0;
		$this->MfID = ($this->Request->contains('mf')) ? TPropertyValue::ensureInteger($this->Request['mf']) : 0;
		$this->CatID = ($this->Request->contains('c')) ? TPropertyValue::ensureInteger($this->Request['c']) : 0;
		$this->SearchText = ($this->Request->contains('q')) ? $this->Request['q'] : '';
		if (!$this->IsPostBack)
		{
			// fill parent selector combobox
			$this->cboCatSelector->DataSource = CategoryRecord::finder()->getCategoryTree(true);
			$this->cboCatSelector->DataBind();
			$this->cboCatSelector->SelectedValue = $this->CatID;
			$criteria = new TActiveRecordCriteria;
			$criteria->Condition = "brand_id > 0";
			$criteria->OrdersBy["brand_name"] = "asc";
			$this->cboBrandSelector->DataSource = BrandRecord::finder()->findAll($criteria);
			$this->cboBrandSelector->DataBind();
			$this->cboBrandSelector->SelectedValue = $this->BrandID;
			$criteria = new TActiveRecordCriteria;
			$criteria->Condition = "mf_id > 0";
			$criteria->OrdersBy["mf_name"] = "asc";
			$this->cboMfSelector->DataSource = ManufacturerRecord::finder()->findAll($criteria);
			$this->cboMfSelector->DataBind();
			$this->cboMfSelector->SelectedValue = $this->MfID;
			$this->populateData();
			if ($this->Request->Contains("action") && $this->Request->Contains("msg"))
			{
				switch ($this->Request["action"])
				{
					case "add-success":
					case "update-success":
						$this->Notice->Type = AdminNoticeType::Information;
						$this->Notice->Text = $this->Request["msg"];
						break;
					case "add-failed":
					case "update-failed":
						$this->Notice->Type = AdminNoticeType::Error;
						$this->Notice->Text = $this->Request["msg"];
						break;
					default:
						break;
				}
			}
		}
	}

	protected function populateData()
	{
		$criteria = new TActiveRecordCriteria;
		$criteria->OrdersBy[$this->Sortable[$this->SortBy]] = $this->SortType;
		// addtional condition here
		// this part will be hard-code on each page
		$criteria->Condition = "product_id in (select distinct p.product_id from tbl_product p ";
		if ($this->CatID>0)
		{
			$criteria->Condition .= " left join tbl_product_cat_xref pcx on p.product_id = pcx.product_id 
									 left join tbl_category c on pcx.cat_id = c.cat_id ";
		}
		$criteria->Condition .= " where p.product_id > 0 ";
		if (strlen($this->SearchText)>0)
		{
			$searchArray = explode(" ",THttpUtility::htmlDecode($this->SearchText));
			$searchQuery = "";
			foreach($searchArray as $index=>$value)
			{
				$searchQuery .= ($index>0 ? " or " : "")." p.product_id like '%".addslashes($searchArray[$index])."%' or p.product_name like '%".addslashes($searchArray[$index])."%' or p.product_sku like '%".addslashes($searchArray[$index])."%'";
			}
			$criteria->Condition .= " and (".$searchQuery.") ";
		}
		if ($this->BrandID>0)
		{
			$criteria->Condition .= " and (p.brand_id = '".$this->BrandID."') ";
		}
		if ($this->MfID>0)
		{
			$criteria->Condition .= " and (p.mf_id = '".$this->MfID."') ";
		}
		if ($this->CatID>0)
		{
			$criteria->Condition .= " and (c.cat_id = '".$this->CatID."' or c.parent_id = '".$this->CatID."') ";
		}
		// -- 
		$criteria->Condition .= ")";
		$activeRecord = Prado::createComponent(self::AR);
		$this->ItemList->VirtualItemCount = count($activeRecord->finder()->findAll($criteria));
		$this->MaxPage = ceil($this->ItemList->VirtualItemCount/$this->ItemList->PageSize);
		if ($this->CurrentPage > $this->MaxPage) $this->CurrentPage = $this->MaxPage;
		$limit = $this->ItemList->PageSize;
		$offset = ($this->CurrentPage-1) * $limit;

		if ($offset + $limit > $this->ItemList->VirtualItemCount)
			$limit = $this->ItemList->VirtualItemCount - $offset;

		$criteria->Limit = $limit;
		$criteria->Offset = $offset;
		$items = $activeRecord->finder()->withBrand()->withManufacturer()->withCategories()->findAll($criteria);
		$this->ItemList->DataSource = $items;
		$this->ItemList->dataBind();
		if (count($items) <= 0)
		{
			$this->Notice->Type = AdminNoticeType::Information;
			$this->Notice->Text = $this->Application->getModule("message")->translate("ITEM_FOUND",0,"product");
		}
	}

	public function populateSortUrl($sortBy, $sortType, $search="", $brand=0, $mf=0, $cat=0, $resetPage=true)
	{
		$params = $this->Request->toArray();
		foreach($params as $key=>$value)
		{
			if (!in_array($key,$this->QueryParameters))
				unset($params[$key]);
		}
		$serviceParameter = $this->Request->ServiceParameter;
		if (strlen($search)>0) $params['q'] = $search;
		else if (isset($params['q'])) unset($params['q']);
		if ($brand>0) $params['b'] = $brand;
		else if (isset($params['b'])) unset($params['b']);
		if ($mf>0) $params['mf'] = $mf;
		else if (isset($params['mf'])) unset($params['mf']);
		if ($cat>0) $params['c'] = $cat;
		else if (isset($params['c'])) unset($params['c']);
		if ($resetPage)	$params['p'] = 1;
		$params['sb'] = $sortBy;
		$params['st'] = $sortType;

		return $this->Service->ConstructUrl($serviceParameter,$params);
	}

	protected function list_ItemCreated($sender, $param)
	{
		if ($param->Item->ItemType == "Item" || $param->Item->ItemType == "AlternatingItem")
		{
			if ($param->Item->Data)
			{
				$param->Item->colDeleteButton->Button->Attributes->onclick = 'if(!confirm("'.$this->Application->getModule("message")->translate("DELETE_CONFIRM","product",$param->Item->Data->Name).'")) return false;';
			}
		}
	}

	protected function list_ItemCommand($sender, $param)
	{
		switch($param->CommandName)
		{
			case "delete":
				$activeRecord = Prado::createComponent(self::AR)->finder()->findByPk(TPropertyValue::ensureInteger($param->Item->colID->lblItemID->Text));
				if ($activeRecord)
				{
					try
					{
						$activeRecord->delete();
						//var_dump();
						$this->Notice->Type = AdminNoticeType::Information;
						$this->Notice->Text = $this->Application->getModule("message")->translate("DELETE_SUCCESS","Product",$activeRecord->Name);
					}
					catch(TException $e)
					{
						$this->Notice->Type = AdminNoticeType::Error;
						$this->Notice->Text = $this->Application->getModule("message")->translate("DELETE_FAILED","Product",$activeRecord->Name);
					}
				}
				else
				{
					$this->Notice->Type = AdminNoticeType::Error;
					$this->Notice->Text = $this->Application->getModule("message")->translate("ITEM_NOT_FOUND","product");
				}
				break;
			case "publish":
				$activeRecord = Prado::createComponent(self::AR)->finder()->findByPk(TPropertyValue::ensureInteger($param->Item->colID->lblItemID->Text));
				if ($activeRecord)
				{
					try
					{
						$activeRecord->IsPublished = !$activeRecord->IsPublished;
						$activeRecord->save();
						$this->Notice->Type = AdminNoticeType::Information;
						$this->Notice->Text = $this->Application->getModule("message")->translate("ITEM_ACTION_SUCCESS",$activeRecord->Name,($activeRecord->IsPublished ? "published" : "unpublished"));
					}
					catch(TException $e)
					{
						$this->Notice->Type = AdminNoticeType::Error;
						$this->Notice->Text = $this->Application->getModule("message")->translate("ITEM_ACTION_FAILED",$activeRecord->Name,($activeRecord->IsPublished ? "published" : "unpublished"));
					}	
				}
				else
				{
					$this->Notice->Type = AdminNoticeType::Error;
					$this->Notice->Text = $this->Application->getModule("message")->translate("ITEM_NOT_FOUND","product");
				}
				break;
			case "order_up":
			case "order_down":
				$activeRecord = Prado::createComponent(self::AR)->finder()->findByPk(TPropertyValue::ensureInteger($param->Item->colID->lblItemID->Text));
				if ($activeRecord)
				{
					try
					{
						$effectedRecord = Prado::createComponent(self::AR)->finder()->findByproduct_order(($param->CommandName=="order_up"?$activeRecord->Ordering-1:$activeRecord->Ordering+1));
						if ($effectedRecord)
						{
							$temp = $effectedRecord->Ordering;
							$effectedRecord->Ordering = $activeRecord->Ordering;
							$effectedRecord->save();
							$activeRecord->Ordering = $temp;
							$activeRecord->save();
						}
						$this->Notice->Type = AdminNoticeType::Information;
						$this->Notice->Text = $this->Application->getModule("message")->translate("ITEM_ACTION_SUCCESS",$activeRecord->Name,"ordered");
					}
					catch(TException $e)
					{
						//throw new TException($e);
						$this->Notice->Type = AdminNoticeType::Error;
						$this->Notice->Text = $this->Application->getModule("message")->translate("ITEM_ACTION_FAILED",$activeRecord->Name,"ordered");
					}
				}
				break;
			case "order_save":
				foreach($this->ItemList->Items as $item) 
				{
					$activeRecord = Prado::createComponent(self::AR)->finder()->findByPk(TPropertyValue::ensureInteger($item->colID->lblItemID->Text));
					$order = TPropertyValue::ensureInteger($item->colOrder->findControl("txtOrder")->Text);
					if ($order < 0) $order = 0;
					$criteria = new TActiveRecordCriteria;
					$criteria->Condition = "product_id <> '".$activeRecord->ID."' and product_order = '".$order."'";
					$is_existed = (Prado::createComponent(self::AR)->finder()->find($criteria));
					$activeRecord->Ordering = ($is_existed) ? 0 : $order;
					$activeRecord->save();
				}
				break;
			default:
				break;
		}
		$this->populateData();
	}

	protected function btnDelete_Clicked($sender, $param)
	{
		if($this->IsValid)  // when all validations succeed
		{
			$items = array();
			foreach($this->ItemList->Items as $item) {
				if ($item->colCheckBox->chkItem->Checked) {                                   
					$items[] = $item->colID->lblItemID->Text;
				}
			}
			try
			{
				Prado::createComponent(self::AR)->finder()->deleteAllByPks($items);
				//var_dump(implode(",",$items));
				$this->Notice->Type = AdminNoticeType::Information;
				$this->Notice->Text = $this->Application->getModule("message")->translate("DELETE_ALL_SUCCESS","product");
			}
			catch (TException $e)
			{
				$this->Notice->Type = AdminNoticeType::Error;
				$this->Notice->Text = $this->Application->getModule("message")->translate("DELETE_ALL_FAILED","product");
			}
			$this->populateData();
		}
	}

	protected function btnEdit_Clicked($sender, $param)
	{
		if($this->IsValid)  // when all validations succeed
		{
			foreach($this->ItemList->Items as $item) {
				if ($item->colCheckBox->chkItem->Checked) {                                   
					$activeRecord = Prado::createComponent(self::AR)->finder()->findByPk(TPropertyValue::ensureInteger($item->colID->lblItemID->Text));
					if ($activeRecord)
					{	
						$this->Response->redirect($this->Service->ConstructUrl("admincp.ProductForm",array("id"=>$activeRecord->ID,"alias"=>$activeRecord->Alias)));
						return;
					}
					else
					{
						$this->Notice->Type = AdminNoticeType::Error;
						$this->Notice->Text = $this->Application->getModule("message")->translate("ITEM_NOT_FOUND","product");
					}
				}
			}
		}
	}

	protected function btnPublish_Clicked($sender, $param)
	{
		if ($this->IsValid)
		{
			$items = array();
			foreach($this->ItemList->Items as $item) 
			{
				if ($item->colCheckBox->chkItem->Checked) 
				{
					$activeRecord = Prado::createComponent(self::AR)->finder()->findByPk(TPropertyValue::ensureInteger($item->colID->lblItemID->Text));
					try
					{
						$activeRecord->IsPublished = true;
						$activeRecord->save();
					}
					catch(TException $e)
					{
						$this->Notice->Type = AdminNoticeType::Error;
						$this->Notice->Text = $this->Application->getModule("message")->translate("ITEM_ACTION_SUCCESS",$activeRecord->Name,($activeRecord->IsPublished ? "published" : "unpublished"));
						break;
					}
				}
			}
			$this->Notice->Type = AdminNoticeType::Information;
			$this->Notice->Text = $this->Application->getModule("message")->translate("ITEM_ACTION_SUCCESS","Selected items","published");
			$this->populateData();
		}
	}

	protected function btnUnpublish_Clicked($sender, $param)
	{
		if ($this->IsValid)
		{
			$items = array();
			foreach($this->ItemList->Items as $item) {
				if ($item->colCheckBox->chkItem->Checked) 
				{
					$activeRecord = Prado::createComponent(self::AR)->finder()->findByPk(TPropertyValue::ensureInteger($item->colID->lblItemID->Text));
					try
					{
						$activeRecord->IsPublished = false;
						$activeRecord->save();
					}
					catch(TException $e)
					{
						$this->Notice->Type = AdminNoticeType::Error;
						$this->Notice->Text = $this->Application->getModule("message")->translate("ITEM_ACTION_FAILED",$activeRecord->Name,($activeRecord->IsPublished ? "published" : "unpublished"));
						break;
					}
				}
			}
			$this->Notice->Type = AdminNoticeType::Information;
			$this->Notice->Text = $this->Application->getModule("message")->translate("ITEM_ACTION_SUCCESS","Selected items","unpublished");
			$this->populateData();
		}
	}

	protected function btnSearch_Clicked($sender, $param)
	{
		if ($this->IsValid)
		{
			$this->Response->redirect($this->populateSortUrl($this->SortBy,$this->SortType,THttpUtility::htmlEncode($this->txtSearchText->SafeText),$this->BrandID,$this->MfID,$this->CatID));
		}
	}

	protected function btnSearchReset_Clicked($sender, $param)
	{
		$this->Response->redirect($this->populateSortUrl($this->SortBy,$this->SortType,'',$this->BrandID,$this->MfID,$this->CatID));
	}
	
	protected function cboMfSelector_SelectedIndexChanged($sender, $param)
	{
		$this->Response->redirect($this->populateSortUrl($this->SortBy,$this->SortType,'',$this->BrandID,$sender->SelectedValue,$this->CatID));
	}
	
	protected function cboBrandSelector_SelectedIndexChanged($sender, $param)
	{
		$this->Response->redirect($this->populateSortUrl($this->SortBy,$this->SortType,'',$sender->SelectedValue,$this->MfID,$this->CatID));
	}
	
	protected function cboCatSelector_SelectedIndexChanged($sender, $param)
	{
		$this->Response->redirect($this->populateSortUrl($this->SortBy,$this->SortType,'',$this->BrandID,$this->MfID,$sender->SelectedValue));
	}
}

?>