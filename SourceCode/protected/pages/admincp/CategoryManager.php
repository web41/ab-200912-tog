<?php

class CategoryManager extends TPage
{
	private $_maxPage = 1;
	private $_currentPage = 1;
	private $_sortBy = "";
	private $_sortType = "";
	private $_searchText = "";
	private $_parent = 0;
	private $_sortable = array("cat_id","cat_name");
	private $_queryParams = array("p","st","sb","q","parent");
	const AR = "CategoryRecord";

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
	
	public function getParent()
	{
		return $this->_parent;
	}
	
	public function setParent($value)
	{
		$this->_parent = TPropertyValue::ensureInteger($value);
	}

	public function onLoad($param)
	{
		parent::onLoad($param);
		// register search button
		$this->ClientScript->registerDefaultButton($this->txtSearchText,$this->btnSearch);
		$this->CurrentPage = ($this->Request->contains('p')) ? intval($this->Request['p']) : 1;
		$this->SortBy = ($this->Request->contains('sb')) ? TPropertyValue::ensureInteger($this->Request['sb']) : 1;
		$this->SortType = ($this->Request->contains('st')) ? $this->Request['st'] : 'asc';
		$this->SearchText = ($this->Request->contains('q')) ? $this->Request['q'] : '';
		$this->Parent = ($this->Request->contains('parent')) ? TPropertyValue::ensureInteger($this->Request['parent']) : 0;
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

	protected function populateData()
	{
		$criteria = new TActiveRecordCriteria;
		$criteria->OrdersBy[$this->Sortable[$this->SortBy]] = $this->SortType;
		// addtional condition here
		// this part will be hard-code on each page
		$criteria->Condition = "cat_id in (select distinct cat_id from tbl_category where cat_id > 0 ";
		if (strlen($this->SearchText)>0)
		{
			$searchArray = explode(" ",THttpUtility::htmlDecode($this->SearchText));
			$searchQuery = "";
			foreach($searchArray as $index=>$value)
			{
				$searchQuery .= ($index>0 ? " or " : "")." cat_id like '%".$searchArray[$index]."%' or cat_name like '%".$searchArray[$index]."%'";
			}
			$criteria->Condition .= " and (".$searchQuery.") ";
		}
		if ($this->Parent>0)
		{
			$criteria->Condition .= " and (parent_id = '".$this->Parent."') ";
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
		$items = $activeRecord->finder()->findAll($criteria);
		$this->ItemList->DataSource = $items;
		$this->ItemList->dataBind();
		if (count($items) <= 0)
		{
			$this->Notice->Type = AdminNoticeType::Information;
			$this->Notice->Text = $this->Application->getModule("message")->translate("ITEM_FOUND",0,"category");
		}
	}

	public function populateSortUrl($sortBy, $sortType, $search="", $parent=0, $resetPage=true)
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
		if ($parent>0) $params['parent'] = $parent;
		else if (isset($params['parent'])) unset($params['parent']);
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
				$param->Item->colDeleteButton->Button->Attributes->onclick = 'if(!confirm(\''.$this->Application->getModule("message")->translate("DELETE_CONFIRM","category",$param->Item->Data->Name).'\')) return false;';
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
						$this->Notice->Text = $this->Application->getModule("message")->translate("DELETE_SUCCESS","Category",$activeRecord->Name);
						$this->populateData();
					}
					catch(TException $e)
					{
						$this->Notice->Type = AdminNoticeType::Error;
						$this->Notice->Text = $this->Application->getModule("message")->translate("DELETE_FAILED","Category",$activeRecord->Name);
					}
				}
				else
				{
					$this->Notice->Type = AdminNoticeType::Error;
					$this->Notice->Text = $this->Application->getModule("message")->translate("ITEM_NOT_FOUND","category");
				}
				break;
		}
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
				$this->Notice->Text = $this->Application->getModule("message")->translate("DELETE_ALL_SUCCESS","catagory");
				$this->populateData();
			}
			catch (TException $e)
			{
				$this->Notice->Type = AdminNoticeType::Error;
				$this->Notice->Text = $this->Application->getModule("message")->translate("DELETE_ALL_FAILED","catagory");
			}
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
						$this->Response->redirect($this->Service->ConstructUrl("admincp.CategoryForm",array("id"=>$activeRecord->ID,"alias"=>$activeRecord->Alias)));
						return;
					}
					else
					{
						$this->Notice->Type = AdminNoticeType::Error;
						$this->Notice->Text = $this->Application->getModule("message")->translate("ITEM_NOT_FOUND","category");
					}
				}
			}
		}
	}

	protected function btnSearch_Clicked($sender, $param)
	{
		if ($this->IsValid)
		{
			$this->Response->redirect($this->populateSortUrl($this->SortBy,$this->SortType,THttpUtility::htmlEncode($this->txtSearchText->SafeText),$this->Parent));
		}
	}

	protected function btnSearchReset_Clicked($sender, $param)
	{
		$this->Response->redirect($this->populateSortUrl($this->SortBy,$this->SortType,'',$this->Parent));
	}
}

?>