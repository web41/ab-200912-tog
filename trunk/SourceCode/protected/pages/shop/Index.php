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
    private $_myFavourite = 0;
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

    public function getMyFavourite()
    {
        return $this->_myFavourite;
    }

    public function setMyFavourite($value)
    {
        $this->_myFavourite = TPropertyValue::ensureBoolean($value);
    }

	public function onLoad($param)
	{
		parent::onLoad($param);
		$this->CurrentPage = ($this->Request->contains('p')) ? TPropertyValue::ensureInteger($this->Request['p']) : 1;
		$this->SortBy = ($this->Request->contains('sb')) ? TPropertyValue::ensureInteger($this->Request['sb']) : 6;
		$this->SortType = ($this->Request->contains('st')) ? $this->Request['st'] : 'asc';
		$this->BrandID = ($this->Request->contains('b')) ? TPropertyValue::ensureInteger($this->Request['b']) : 0;
		$this->MfID = ($this->Request->contains('mf')) ? TPropertyValue::ensureInteger($this->Request['mf']) : 0;
		$this->CatID = ($this->Request->contains('c')) ? TPropertyValue::ensureInteger($this->Request['c']) : 0;
		$this->SubCatID = ($this->Request->contains('subc')) ? TPropertyValue::ensureInteger($this->Request['subc']) : 0;
		$this->IsBestSeller = ($this->Request->contains('best_seller')) ? TPropertyValue::ensureBoolean($this->Request['best_seller']) : false;
		$this->IsNew = ($this->Request->contains('new_arrival')) ? TPropertyValue::ensureBoolean($this->Request['new_arrival']) : false;
		$this->IsPromoted = ($this->Request->contains('promotion')) ? TPropertyValue::ensureBoolean($this->Request['promotion']) : false;
        $this->MyFavourite = ($this->Request->contains('my_favourite')) ? TPropertyValue::ensureBoolean($this->Request['my_favourite']) : false;
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
        $sqlmap = $this->Application->Modules['sqlmap']->Client;
        if ($this->MyFavourite) {
			$this->myFavouriteTitle->Visible = true;
			$this->continueShopping->Visible = true;
            $this->ItemList->VirtualItemCount = count($sqlmap->queryForList("FavouriteProduct", $this->Application->User->ID));
        }
        else {
            $sql = "";
            if (strlen($this->SearchText)>0 && $this->SearchText != $this->Master->DEFAULT_SEARCH_TEXT) {
			    $this->lblCatPath->Text = 'Search result for "'.$this->SearchText.'"';
			    $this->lblCatPath->Visible = true;
                $sql .= " AND (p.product_id LIKE '%".addslashes($this->SearchText)."%' or p.product_name LIKE '%".addslashes($this->SearchText)."%' or p.product_sku LIKE '%".addslashes($this->SearchText)."%')";
		    }
            if ($this->BrandID>0) {
                $sql .= "AND p.brand_id = $this->BrandID ";
            }
            if ($this->MfID>0) {
                $sql .= "AND p.mf_id = $this->MfID ";
            }
            if ($this->SubCatID>0) {
                $sql .= "AND c.cat_id = $this->SubCatID ";
            }
            else if ($this->CatID>0) {
                $sql .= "AND (c.parent_id = $this->CatID or c.cat_id = $this->CatID) ";
            }
            if ($this->IsBestSeller) {
				$this->lblCatPath->Text = 'Best Seller Products';
				$this->lblCatPath->Visible = true;
                $sql .= "AND p.product_best_seller = 1 ";
            }
            if ($this->IsNew) {
                $sql .= "AND p.product_new_arrival = 1 ";
            }
            if ($this->IsPromoted) {
                $sql .= "AND p.discount_id > 0 ";
            }
			// earthbound brand
			$toDay = time();
			if ((date('w',$toDay)==4 && date('G',$toDay)>=12) || (date('w',$toDay)==6 && date('G',$toDay)<=23)) {
				$sql .= "AND LOWER(b.brand_name) <> 'earthbound'";
			}
            $order = (isset($this->Sortable[$this->SortBy])?$this->Sortable[$this->SortBy]:$this->Sortable[1])." ".$this->SortType;
            $this->ItemList->VirtualItemCount = count($sqlmap->queryForList("BrowseProduct", array("ADDITIONAL_CONDITION"=>$sql,"ORDER_BY"=>$order)));
        }
		$this->MaxPage = ceil($this->ItemList->VirtualItemCount/$this->ItemList->PageSize);
        if ($this->CurrentPage > $this->MaxPage) $this->CurrentPage = $this->MaxPage;
        $limit = $this->ItemList->PageSize;
        $offset = ($this->CurrentPage-1) * $limit;

        if ($offset + $limit > $this->ItemList->VirtualItemCount)
            $limit = $this->ItemList->VirtualItemCount - $offset;
        if ($this->MyFavourite) {
            $items = $sqlmap->queryForList("FavouriteProduct", $this->Application->User->ID,null,$offset,$limit);
        }
        else {
            $order = (isset($this->Sortable[$this->SortBy])?$this->Sortable[$this->SortBy]:$this->Sortable[1])." ".$this->SortType;
            $items = $sqlmap->queryForList("BrowseProduct", array("ADDITIONAL_CONDITION"=>$sql,"ORDER_BY"=>$order),null,$offset,$limit);
        }
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