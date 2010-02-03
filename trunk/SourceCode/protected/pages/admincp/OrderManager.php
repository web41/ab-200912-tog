<?php

class OrderManager extends TPage
{
	private $_maxPage = 1;
	private $_currentPage = 1;
	private $_sortBy = "";
	private $_sortType = "";
	private $_searchText = "";
	private $_userID = "";
	private $_sortable = array("order_id","order_num","total","c_date");
	private $_queryParams = array("p","st","sb","q");
	const AR = "OrderRecord";

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
	
	public function getUserID()
	{
		return $this->_userID;
	}
	
	public function setUserID($value)
	{
		$this->_userID = TPropertyValue::ensureInteger($value);
	}

	public function onLoad($param)
	{
		parent::onLoad($param);
		// register search button
		$this->ClientScript->registerDefaultButton($this->txtSearchText,$this->btnSearch);
		$this->CurrentPage = ($this->Request->contains('p')) ? intval($this->Request['p']) : 1;
		$this->SortBy = ($this->Request->contains('sb')) ? TPropertyValue::ensureInteger($this->Request['sb']) : 3;
		$this->SortType = ($this->Request->contains('st')) ? $this->Request['st'] : 'desc';
		$this->SearchText = ($this->Request->contains('q')) ? $this->Request['q'] : '';
		$this->UserID = ($this->Request->contains('u')) ? TPropertyValue::ensureInteger($this->Request['u']) : 0;
		if (!$this->IsPostBack)
		{
			// set user selector 
			$users = UserRecord::finder()->getAllItems();
			$this->cboUserSelector->Items->clear();
			foreach($users as $user)
			{
				$item = new TListItem; $item->Text = $user->FirstName.' '.$user->LastName; $item->Value = $user->ID;
				$this->cboUserSelector->Items->add($item);
			}
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
		$criteria->Condition = "order_id in (select distinct order_id from tbl_order where order_id > 0 ";
		if (strlen($this->SearchText)>0)
		{
			$searchArray = explode(" ",THttpUtility::htmlDecode($this->SearchText));
			$searchQuery = "";
			foreach($searchArray as $index=>$value)
			{
				$searchQuery .= ($index>0 ? " or " : "")." order_id like '%".addslashes($searchArray[$index])."%' or order_num like '%".addslashes($searchArray[$index])."%'";
			}
			$criteria->Condition .= " and (".$searchQuery.")";
		}
		if ($this->UserID > 0)
		{
			$criteria->Condition .= " and user_id = {$this->UserID}";
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
		$items = $activeRecord->finder()->withUser()->findAll($criteria);
		$this->ItemList->DataSource = $items;
		$this->ItemList->dataBind();
		if (count($items) <= 0)
		{
			$this->Notice->Type = AdminNoticeType::Information;
			$this->Notice->Text = $this->Application->getModule("message")->translate("ITEM_FOUND",0,"order");
		}
	}

	public function populateSortUrl($sortBy, $sortType, $search="", $user=0, $resetPage=true)
	{
		$params = $this->Request->toArray();
		foreach($params as $key=>$value)
		{
			if (!in_array($key,$this->QueryParameters))
				unset($params[$key]);
		}
		$serviceParameter = $this->Request->ServiceParameter;
		if ($resetPage)	$params['p'] = 1;
		$params['sb'] = $sortBy;
		$params['st'] = $sortType;
		if (strlen($search)>0) $params['q'] = $search;
		else if (isset($params['q'])) unset($params['q']);
		if ($user>0) $params['u'] = $user;
		else if (isset($params['u'])) unset($params['u']);
		return $this->Service->ConstructUrl($serviceParameter,$params);
	}

	protected function list_ItemCreated($sender, $param)
	{
		if ($param->Item->ItemType == "Item" || $param->Item->ItemType == "AlternatingItem")
		{
			if ($param->Item->Data)
			{
				$param->Item->colDeleteButton->Button->Attributes->onclick = 'if(!confirm("'.$this->Application->getModule("message")->translate("DELETE_CONFIRM","order",$param->Item->Data->Num).'")) return false;';
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
						$this->Notice->Text = $this->Application->getModule("message")->translate("DELETE_SUCCESS","Order",$activeRecord->Num);
						$this->populateData();
					}
					catch(TException $e)
					{
						$this->Notice->Type = AdminNoticeType::Error;
						$this->Notice->Text = $this->Application->getModule("message")->translate("DELETE_FAILED","Order",$activeRecord->Num);
					}
				}
				else
				{
					$this->Notice->Type = AdminNoticeType::Error;
					$this->Notice->Text = $this->Application->getModule("message")->translate("ITEM_NOT_FOUND","order");
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
				$this->Notice->Text = $this->Application->getModule("message")->translate("DELETE_ALL_SUCCESS","order");
			}
			catch (TException $e)
			{
				$this->Notice->Type = AdminNoticeType::Error;
				$this->Notice->Text = $this->Application->getModule("message")->translate("DELETE_ALL_FAILED","order");
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
					$activeRecord = Prado::createComponent(self::AR)->finder()->findByPk(TPropertyValue::ensureInteger($item->colID->lblBrandID->Text));
					if ($activeRecord)
					{	
						$this->Response->redirect($this->Service->ConstructUrl("admincp.OrderForm",array("id"=>$activeRecord->ID,"alias"=>$activeRecord->Num)));
						return;
					}
					else
					{
						$this->Notice->Type = AdminNoticeType::Error;
						$this->Notice->Text = $this->Application->getModule("message")->translate("ITEM_NOT_FOUND","order");
					}
				}
			}
		}
	}

	protected function btnSearch_Clicked($sender, $param)
	{
		if ($this->IsValid)
		{
			//$this->SearchText = THttpUtility::htmlEncode($this->txtSearchText->SafeText);
			//$this->populateData();
			$this->Response->redirect($this->populateSortUrl($this->SortBy,$this->SortType,THttpUtility::htmlEncode($this->txtSearchText->SafeText),$this->UserID));
		}
	}

	protected function btnSearchReset_Clicked($sender, $param)
	{
		//$this->SearchText = "";
		//$this->txtSearchText->Text = "Filter by ID or Name";
		//$this->populateData();
		$this->Response->redirect($this->populateSortUrl($this->SortBy,$this->SortType,"",0));
	}
	
	protected function cboUserSelector_SelectedIndexChanged($sender, $param)
	{
		$this->Response->redirect($this->populateSortUrl($this->SortBy,$this->SortType,"",(strlen($sender->SelectedValue)>0?$sender->SelectedValue:0)));
	}
}

?>