<?php

class BrandManager extends TPage
{
	private $_maxPage = 1;
	private $_currentPage = 1;
	private $_sortBy = "";
	private $_sortType = "";
	private $_sortable = array("brand_id","brand_name");
	const AR = "BrandRecord";
	
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
	
	public function onLoad($param)
	{
		if (!$this->IsPostBack)
		{
			$this->CurrentPage = ($this->Request->contains('p')) ? intval($this->Request['p']) : 1;
			$this->SortBy = ($this->Request->contains('sb')) ? TPropertyValue::ensureInteger($this->Request['sb']) : 1;
			$this->SortType = ($this->Request->contains('st')) ? $this->Request['st'] : 'asc';
			$this->populateData();
		}
	}
	
	protected function populateData()
	{
		$criteria = new TActiveRecordCriteria;
		$criteria->OrdersBy[$this->Sortable[$this->SortBy]] = $this->SortType;
		// addtional condition here
		$criteria->Condition = "brand_id > 0";
		// -- 
		$activeRecord = Prado::createComponent(self::AR);
		$this->BrandList->VirtualItemCount = count($activeRecord->finder()->findAll($criteria));
		$this->MaxPage = ceil($this->BrandList->VirtualItemCount/$this->BrandList->PageSize);
		if ($this->CurrentPage > $this->MaxPage) $this->CurrentPage = $this->MaxPage;
		$limit = $this->BrandList->PageSize;
		$offset = ($this->CurrentPage-1) * $limit;

		if ($offset + $limit > $this->BrandList->VirtualItemCount)
			$limit = $this->BrandList->VirtualItemCount - $offset;

		$criteria->Limit = $limit;
		$criteria->Offset = $offset;
		$items = $activeRecord->finder()->findAll($criteria);
		if (count($items) > 0)
		{
			$this->BrandList->DataSource = $items;
			$this->BrandList->dataBind();
		}
		else
		{
			$this->Notice->Type = AdminNoticeType::Information;
			$this->Notice->Text = $this->Application->getModule("message")->translate("ITEM_FOUND",0,"brand");
		}
	}
	
	public function populateSortUrl($sortBy, $sortType, $resetPage=true)
	{
		$params = $this->Request->toArray();
		$serviceParameter = '';
		if (isset($params[$this->Request->ServiceID]))
		{
			$serviceParameter = $params[$this->Request->ServiceID];
			unset($params[$this->Request->ServiceID]);
		}
		if ($resetPage)	$params['p'] = 1;
		$params['sb'] = $sortBy;
		$params['st'] = $sortType;
		return $this->Service->ConstructUrl($serviceParameter,$params);
	}
}

?>