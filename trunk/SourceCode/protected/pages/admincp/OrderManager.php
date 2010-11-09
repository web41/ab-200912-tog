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
	private $_orderStatuses = array();
	private $_latestStatus = "";
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
	
	public function getLatestStatus()
	{
		return $this->_latestStatus;
	}

	public function setLatestStatus($value)
	{
		$this->_latestStatus = $value;
	}

	public function onLoad($param)
	{
		parent::onLoad($param);
		$this->_orderStatuses = OrderStatusRecord::finder()->findAll();
		// register search button
		$this->ClientScript->registerDefaultButton($this->txtSearchText,$this->btnSearch);
		$this->CurrentPage = ($this->Request->contains('p')) ? intval($this->Request['p']) : 1;
		$this->SortBy = ($this->Request->contains('sb')) ? TPropertyValue::ensureInteger($this->Request['sb']) : 3;
		$this->SortType = ($this->Request->contains('st')) ? $this->Request['st'] : 'desc';
		$this->SearchText = ($this->Request->contains('q')) ? $this->Request['q'] : '';
		$this->UserID = ($this->Request->contains('u')) ? TPropertyValue::ensureInteger($this->Request['u']) : 0;
		$this->LatestStatus = ($this->Request->contains('ls')) ? $this->Request['ls'] : '';
		if (!$this->IsPostBack)
		{
			if ($this->SearchText) $this->txtSearchText->Text = $this->SearchText;
			// set user selector 
			$users = UserRecord::finder()->getAllItems();
			$this->cboUserSelector->Items->clear();
			foreach($users as $user)
			{
				$item = new TListItem; $item->Text = $user->FirstName.' '.$user->LastName; $item->Value = $user->ID;
				$this->cboUserSelector->Items->add($item);
			}
			$this->cboUserSelector->SelectedValue = $this->UserID;
			$this->cboStatusSelector->DataSource = $this->_orderStatuses;
			$this->cboStatusSelector->DataBind();
			$this->cboStatusSelector->SelectedValue = $this->LatestStatus;
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
		$criteria->Condition = "order_id in (select distinct o.order_id from tbl_order o
												left join tbl_order_history oh ON o.order_id = oh.order_id
												where o.order_id > 0 ";
		if (strlen($this->SearchText)>0)
		{
			$searchArray = explode(" ",THttpUtility::htmlDecode($this->SearchText));
			$searchQuery = "";
			foreach($searchArray as $index=>$value)
			{
				$searchQuery .= ($index>0 ? " or " : "")." o.order_id like '%".addslashes($searchArray[$index])."%' or o.order_num like '%".addslashes($searchArray[$index])."%'";
			}
			$criteria->Condition .= " and (".$searchQuery.")";
		}
		if ($this->UserID > 0)
		{
			$criteria->Condition .= " and o.user_id = {$this->UserID}";
		}
		if (strlen($this->LatestStatus)>0) {
			$criteria->Condition .= " and oh.order_status_code = :status and oh.c_date = (SELECT max(c_date) FROM tbl_order_history WHERE order_id = oh.order_id)";
			$criteria->Parameters[':status'] = $this->LatestStatus;
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

	public function populateSortUrl($sortBy, $sortType, $search="", $user=0, $latestStatus='', $resetPage=true)
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
		if (strlen($latestStatus)>0) $params['ls'] = $latestStatus;
		else if (isset($params['ls'])) unset($params['ls']);
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
				$param->Item->colLatestHistory->cboLatestHistory->DataSource = $this->_orderStatuses;
				$param->Item->colLatestHistory->cboLatestHistory->DataBind();
				if ($param->Item->Data->LatestHistory) 
					$param->Item->colLatestHistory->cboLatestHistory->SelectedValue = $param->Item->Data->LatestHistory->StatusCode;
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
			case 'history_save':
				foreach($this->ItemList->Items as $item) 
				{
					$activeRecord = Prado::createComponent(self::AR)->finder()->findByPk(TPropertyValue::ensureInteger($item->colID->lblItemID->Text));
					$newStatus = $item->colLatestHistory->cboLatestHistory->SelectedValue;
					if ($activeRecord && $activeRecord->LatestHistory && $activeRecord->LatestHistory->StatusCode!=$newStatus)
					{
						$history = new OrderHistoryRecord;
						$history->OrderID = $activeRecord->ID;
						$history->StatusCode = $item->colLatestHistory->cboLatestHistory->SelectedValue;
						$history->Comments = "Change status directly from Order Manager page";
						$history->save();
					}
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
					$activeRecord = Prado::createComponent(self::AR)->finder()->findByPk(TPropertyValue::ensureInteger($item->colID->lblItemID->Text));
					if ($activeRecord)
					{	
						$this->Response->redirect($this->Service->ConstructUrl("admincp.OrderForm",array("id"=>$activeRecord->ID,"num"=>$activeRecord->Num,"refUrl"=>urlencode($this->populateSortUrl($this->SortBy,$this->SortType,"",$this->UserID)))));
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
		$this->Response->redirect($this->populateSortUrl($this->SortBy,$this->SortType,"",$sender->SelectedValue,$this->LatestStatus));
	}
	
	protected function cboStatusSelector_SelectedIndexChanged($sender, $param)
	{
		$this->Response->redirect($this->populateSortUrl($this->SortBy,$this->SortType,"",$this->UserID,$sender->SelectedValue));
	}

	protected function btnExport_Clicked($sender, $param) {
		
		Prado::using("Application.common.PHPExcel");
		Prado::using("Application.common.PHPExcel.Style");
		Prado::using("Application.common.PHPExcel.Style.Font");
		Prado::using("Application.common.PHPExcel.IOFactory");
		Prado::using("Application.common.PHPExcel.Writer.Excel5");
		
		// get data
		$criteria = new TActiveRecordCriteria;
		$criteria->OrdersBy[$this->Sortable[$this->SortBy]] = $this->SortType;
		// addtional condition here
		// this part will be hard-code on each page
		$criteria->Condition = "order_id in (select distinct o.order_id from tbl_order o
												left join tbl_order_history oh ON o.order_id = oh.order_id
												where o.order_id > 0 ";
		if (strlen($this->SearchText)>0)
		{
			$searchArray = explode(" ",THttpUtility::htmlDecode($this->SearchText));
			$searchQuery = "";
			foreach($searchArray as $index=>$value)
			{
				$searchQuery .= ($index>0 ? " or " : "")." o.order_id like '%".addslashes($searchArray[$index])."%' or o.order_num like '%".addslashes($searchArray[$index])."%'";
			}
			$criteria->Condition .= " and (".$searchQuery.")";
		}
		if ($this->UserID > 0)
		{
			$criteria->Condition .= " and o.user_id = {$this->UserID}";
		}
		if (strlen($this->LatestStatus)>0) {
			$criteria->Condition .= " and oh.order_status_code = :status and oh.c_date = (SELECT max(c_date) FROM tbl_order_history WHERE order_id = oh.order_id)";
			$criteria->Parameters[':status'] = $this->LatestStatus;
		}
		// -- 
		$criteria->Condition .= ")";
		$activeRecord = Prado::createComponent(self::AR);
		$items = $activeRecord->finder()->withUser()->findAll($criteria);

		try
		{
			$workBook = new PHPExcel();
            $workBook->getProperties()->setCreator("Alex Do")
                ->setLastModifiedBy("Alex Do")
                ->setTitle("The Organic Grocer Product List generated on ".date("m.D.Y",time()))
                ->setSubject("The Organic Grocer Product List")
                ->setDescription("The Organic Grocer Product List generated on ".date("m.D.Y",time()));
            $workBook->setActiveSheetIndex(0);
            $workSheet = $workBook->getActiveSheet();
            $workSheet->setCellValue("A1","No")->getStyle("A1")->getFont()->setBold(true);;
            $workSheet->setCellValue("B1","Order Number")->getStyle("B1")->getFont()->setBold(true);
            $workSheet->setCellValue("C1","Customer")->getStyle("C1")->getFont()->setBold(true);
			$workSheet->setCellValue("D1","Total")->getStyle("C1")->getFont()->setBold(true);
			$workSheet->setCellValue("E1","Order Date")->getStyle("D1")->getFont()->setBold(true);
			$workSheet->setCellValue("F1","Last Status")->getStyle("E1")->getFont()->setBold(true);
			
            $startRow = 2;
            if (count($items)>0)
            {
				for($i=0; $i<count($items); $i++)
                {
					$workSheet->setCellValue("A".($i+$startRow),$i+1);
                    $workSheet->setCellValue("B".($i+$startRow),$items[$i]?$items[$i]->Num:"");
                    $workSheet->setCellValue("C".($i+$startRow),$items[$i]?$items[$i]->User->FirstName.' '.$items[$i]->User->LastName:"");
                    $workSheet->setCellValue("D".($i+$startRow),$items[$i]?number_format($items[$i]->Total,2):0);
					$workSheet->setCellValue("E".($i+$startRow),$items[$i]?date('d/m/Y h:i:s a',$items[$i]->CreateDate):'');
					$workSheet->setCellValue("F".($i+$startRow),$items[$i]&&$items[$i]->LatestHistory&&$items[$i]->LatestHistory->OrderStatus?$items[$i]->LatestHistory->OrderStatus->Name:'');
                }
            }

			$workSheet->getColumnDimension("A")->setWidth(10);
			$workSheet->getColumnDimension("B")->setWidth(25);
			$workSheet->getColumnDimension("C")->setWidth(20);
			$workSheet->getColumnDimension("D")->setWidth(15);
			$workSheet->getColumnDimension("E")->setWidth(25);
			$workSheet->getColumnDimension("E")->setWidth(20);

            $phpExcelWriter = PHPExcel_IOFactory::createWriter($workBook, 'Excel5');
            $fileName = "TOG_OrderList_On_".date("Y-m-d_h-i",time()).".xls";
            $this->Response->appendHeader("Content-Type:application/vnd.ms-excel");
            $this->Response->appendHeader("Content-Disposition:attachment;filename=$fileName");
            $this->Response->appendHeader("Cache-Control:max-age=0");
            $phpExcelWriter->save('php://output'); 
            //$this->Response->flush();
            exit();
		}
		catch(Exception $ex)
		{
			$this->Notice->Type = AdminNoticeType::Error;
			$this->Notice->Text = $this->Application->getModule("message")->translate("UNKNOWN_ERROR");
		}
	}
}

?>