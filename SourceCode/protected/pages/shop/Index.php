<?php

class Index extends TPage
{	
	private $_maxPage = 1;
	private $_currentPage = 1;
	private $_sortBy = "";
	private $_sortType = "";
	private $_searchText = "";
	private $_brandID = 0;
	private $_mfID = 0;
	private $_catID = 0;
	private $_subcatID = 0;
	private $_isBestSeller = 0;
	private $_isNewArrival = 0;
	private $_isPromoted= 0;
	private $_sortable = array("product_id","product_name","product_sku","brand_id","mf_id","c_date","product_order");
	private $_queryParams = array("p","st","sb","b","balias","mf","q","c","calias","subc","subcalias","best_seller","new_arrival","promotion");

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
	
	public function getSubCatID()
	{
		return $this->_subcatID;
	}

	public function setSubCatID($value)
	{
		$this->_subcatID = TPropertyValue::ensureInteger($value);
	}
	
	public function getIsBestSeller()
	{
		return $this->_isBestSeller;
	}

	public function setIsBestSeller($value)
	{
		$this->_isBestSeller = TPropertyValue::ensureBoolean($value);
	}
	
	public function getIsNew()
	{
		return $this->_isNewArrival;
	}

	public function setIsNew($value)
	{
		$this->_isNewArrival = TPropertyValue::ensureBoolean($value);
	}
	
	public function getIsPromoted()
	{
		return $this->_isPromoted;
	}

	public function setIsPromoted($value)
	{
		$this->_isPromoted = TPropertyValue::ensureBoolean($value);
	}

	public function onLoad($param)
	{
		parent::onLoad($param);
		$this->CurrentPage = ($this->Request->contains('p')) ? TPropertyValue::ensureInteger($this->Request['p']) : 1;
		$this->SortBy = ($this->Request->contains('sb')) ? TPropertyValue::ensureInteger($this->Request['sb']) : 5;
		$this->SortType = ($this->Request->contains('st')) ? $this->Request['st'] : 'desc';
		$this->BrandID = ($this->Request->contains('b')) ? TPropertyValue::ensureInteger($this->Request['b']) : 0;
		$this->MfID = ($this->Request->contains('mf')) ? TPropertyValue::ensureInteger($this->Request['mf']) : 0;
		$this->CatID = ($this->Request->contains('c')) ? TPropertyValue::ensureInteger($this->Request['c']) : 0;
		$this->SubCatID = ($this->Request->contains('subc')) ? TPropertyValue::ensureInteger($this->Request['subc']) : 0;
		$this->IsBestSeller = ($this->Request->contains('best_seller')) ? TPropertyValue::ensureBoolean($this->Request['best_seller']) : false;
		$this->IsNew = ($this->Request->contains('new_arrival')) ? TPropertyValue::ensureBoolean($this->Request['new_arrival']) : false;
		$this->IsPromoted = ($this->Request->contains('promotion')) ? TPropertyValue::ensureBoolean($this->Request['promotion']) : false;
		$this->SearchText = ($this->Request->contains('q')) ? $this->Request['q'] : '';
		if (!$this->IsPostBack)
		{
			$this->populateData();
			//$this->renderCategoryPath();
			if ($this->CatID > 0) $this->renderCategoryPath();
		}
	}

	public function populateData()
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
		$criteria->Condition .= " where p.product_id > 0 and p.product_publish = 1 ";
		if (strlen($this->SearchText)>0 && $this->SearchText != $this->Master->DEFAULT_SEARCH_TEXT)
		{
			/* search by any term **
			$searchArray = explode(" ",THttpUtility::htmlDecode($this->SearchText));
			$searchQuery = "";
			foreach($searchArray as $index=>$value)
			{
				$searchQuery .= ($index>0 ? " or " : "")." p.product_id like '%".$searchArray[$index]."%' or p.product_name like '%".$searchArray[$index]."%' or p.product_sku like '%".$searchArray[$index]."%'";
			}
			$criteria->Condition .= " and (".$searchQuery.") ";
			*/
			
			/* search by all term **
			
			*/
			$this->lblCatPath->Text = 'Search result for "'.$this->SearchText.'"';
			$this->lblCatPath->Visible = true;
			$criteria->Condition .= " and (p.product_id like '%".addslashes($this->SearchText)."%' or p.product_name like '%".addslashes($this->SearchText)."%' or p.product_sku like '%".addslashes($this->SearchText)."%')";
		}
		if ($this->BrandID>0)
		{
			$criteria->Condition .= " and p.brand_id = '".$this->BrandID."' ";
		}
		if ($this->MfID>0)
		{
			$criteria->Condition .= " and p.mf_id = '".$this->MfID."' ";
		}
		if ($this->SubCatID>0)
		{
			$criteria->Condition .= " and c.cat_id = '".$this->SubCatID."' ";
		}
		else if ($this->CatID>0)
		{
			$criteria->Condition .= " and (c.parent_id = '".$this->CatID."' or c.cat_id = '".$this->CatID."')";
		}
		
		if ($this->IsBestSeller)
		{
			$criteria->Condition .= " and p.product_best_seller = 1 ";
		}
		if ($this->IsNew)
		{
			$criteria->Condition .= " and p.product_new_arrival = 1";
		}
		if ($this->IsPromoted)
		{
			$criteria->Condition .= " and p.discount_id > 0";
		}
		// -- 
		$criteria->Condition .= ")";
		$this->ItemList->VirtualItemCount = count(ProductRecord::finder()->findAll($criteria));
		$this->MaxPage = ceil($this->ItemList->VirtualItemCount/$this->ItemList->PageSize);
		if ($this->CurrentPage > $this->MaxPage) $this->CurrentPage = $this->MaxPage;
		$limit = $this->ItemList->PageSize;
		$offset = ($this->CurrentPage-1) * $limit;

		if ($offset + $limit > $this->ItemList->VirtualItemCount)
			$limit = $this->ItemList->VirtualItemCount - $offset;

		$criteria->Limit = $limit;
		$criteria->Offset = $offset;
		$items = ProductRecord::finder()->withBrand()->withManufacturer()->withProperties()->findAll($criteria);
		$this->ItemList->DataSource = $items;
		$this->ItemList->dataBind();
		if (count($items) <= 0)
		{
			$this->Notice->Type = UserNoticeType::Notice;
			$this->Notice->Text = $this->Application->getModule("message")->translate("ITEM_FOUND",0,"product");
			$this->mainBox->Visible = false;
		}
	}
	
	public function renderCategoryPath()
	{
		if ($this->CatID>0)
		{
			$cat=null; $subcat=null;
			$this->lblCatPath->Text .= "<a href='".$this->Service->ConstructUrl("shop.Index")."'>All categories</a>";
			$this->lblCatPath->Text .= $this->breadCrumbSeparator();
			$cat = CategoryRecord::finder()->findByPk($this->CatID);
			$this->lblCatPath->Text .= "<a href='".$this->Service->ConstructUrl('shop.Index',array('c'=>$cat->ID,'calias'=>$cat->Alias))."'>".$cat->Name."</a>";
			if ($this->SubCatID>0)
			{
				$this->lblCatPath->Text .= $this->breadCrumbSeparator();
				$subcat = CategoryRecord::finder()->findByPk($this->SubCatID);
				$this->lblCatPath->Text .= "<a href='".$this->Service->ConstructUrl('shop.Index',array('c'=>$cat->ID,'calias'=>$cat->Alias,'subc'=>$subcat->ID,'subcalias'=>$subcat->Alias))."'>".$subcat->Name."</a>";
			}
			$this->lblCatPath->Visible = true;
		}
	}
	
	public function breadCrumbSeparator()
	{
		return "<img src='".$this->Page->Theme->BaseUrl."/images/arrow_green.png'/>";
	}
}

?>