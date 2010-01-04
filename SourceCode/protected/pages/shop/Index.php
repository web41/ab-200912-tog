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
		$this->CurrentPage = ($this->Request->contains('p')) ? TPropertyValue::ensureInteger($this->Request['p']) : 1;
		$this->SortBy = ($this->Request->contains('sb')) ? TPropertyValue::ensureInteger($this->Request['sb']) : 1;
		$this->SortType = ($this->Request->contains('st')) ? $this->Request['st'] : 'asc';
		$this->BrandID = ($this->Request->contains('b')) ? TPropertyValue::ensureInteger($this->Request['b']) : 0;
		$this->MfID = ($this->Request->contains('mf')) ? TPropertyValue::ensureInteger($this->Request['mf']) : 0;
		$this->CatID = ($this->Request->contains('c')) ? TPropertyValue::ensureInteger($this->Request['c']) : 0;
		$this->SearchText = ($this->Request->contains('q')) ? $this->Request['q'] : '';
		if (!$this->IsPostBack)
		{
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
		if (strlen($this->SearchText)>0)
		{
			$searchArray = explode(" ",THttpUtility::htmlDecode($this->SearchText));
			$searchQuery = "";
			foreach($searchArray as $index=>$value)
			{
				$searchQuery .= ($index>0 ? " or " : "")." p.product_id like '%".$searchArray[$index]."%' or p.product_name like '%".$searchArray[$index]."%' or p.product_sku like '%".$searchArray[$index]."%'";
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
		$items = $activeRecord->finder()->withBrand()->withManufacturer()->withProperties()->findAll($criteria);
		$this->ItemList->DataSource = $items;
		$this->ItemList->dataBind();
		if (count($items) <= 0)
		{
			$this->Notice->Type = AdminNoticeType::Information;
			$this->Notice->Text = $this->Application->getModule("message")->translate("ITEM_FOUND",0,"product");
		}
	}
}

?>