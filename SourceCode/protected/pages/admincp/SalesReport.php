<?php

class SalesReport extends TPage
{
	private $_maxPage = 1;
	private $_currentPage = 1;
	private $_sortBy = "";
	private $_sortType = "";
	private $_status = "";
	private $_fromDate = 0;
	private $_toDate = 0;
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

	public function getStatus()
	{
		return $this->_status;
	}

	public function setStatus($value)
	{
		$this->_status = $value;
	}
	
	public function getFromDate()
	{
		return $this->_fromDate;
	}

	public function setFromDate($value)
	{
		$this->_fromDate = TPropertyValue::ensureInteger($value);
	}
	
	public function getToDate()
	{
		return $this->_toDate;
	}

	public function setToDate($value)
	{
		$this->_toDate = TPropertyValue::ensureInteger($value);
	}

	public function onLoad($param)
	{
		parent::onLoad($param);
		// register search button
		$this->CurrentPage = ($this->Request->contains('p')) ? intval($this->Request['p']) : 1;
		$this->SortBy = ($this->Request->contains('sb')) ? TPropertyValue::ensureInteger($this->Request['sb']) : 3;
		$this->SortType = ($this->Request->contains('st')) ? $this->Request['st'] : 'desc';
		$this->Status = ($this->Request->contains('stt')) ? $this->Request['stt'] : "";
		$this->FromDate = ($this->Request->contains('fd')) ? TPropertyValue::ensureInteger($this->Request['fd']) : mktime(0,0,0,date("n"),date("j"),date("Y"));
		$this->ToDate = ($this->Request->contains('td')) ? TPropertyValue::ensureInteger($this->Request['td']) : mktime(23,59,59,date("n"),date("j"),date("Y"));
		if (!$this->IsPostBack)
		{
			$this->cboStatusSelector->DataSource = OrderStatusRecord::getAllItems();
			$this->cboStatusSelector->DataBind();
			$this->cboStatusSelector->SelectedValue = $this->Status;
			$this->populateData();
			$this->dpFromDate->Data = $this->FromDate;
			$this->dpToDate->Data = $this->ToDate;
		}
	}

	protected function populateData()
	{
		$items = $this->generateData();
		$this->ItemList->DataSource = $items;
		$this->ItemList->dataBind();
		if (count($items) <= 0)
		{
			$this->Notice->Type = AdminNoticeType::Information;
			$this->Notice->Text = $this->Application->getModule("message")->translate("ITEM_FOUND",0,"order");
		}
	}
	
	public function generateData()
	{
		$sql = "";
		if ($this->FromDate > 0)
			$sql .= " AND (o.c_date >= '".$this->FromDate."')";
		if ($this->ToDate > 0)
			$sql .= " AND (o.c_date <= '".$this->ToDate."')";
		if (strlen($this->Status)>0)
			$sql .= " AND (oh.order_status_code = '".$this->Status."' AND oh.c_date = (SELECT MAX(c_date) FROM tbl_order_history WHERE order_id = o.order_id))";
		
		$order = $this->Sortable[$this->SortBy]." ".$this->SortType;
		
		$sqlmap = $this->Application->Modules['sqlmap']->Client;
		return $sqlmap->queryForList("SalesReport", array("ADDITIONAL_CONDITION"=>$sql,"ORDER_BY"=>$order));
	}

	public function populateSortUrl($sortBy, $sortType, $fromDate=0, $toDate=0, $status="", $resetPage=true)
	{
		if ($fromDate == 0) $fromDate = mktime(0,0,0,date("n"),date("j"),date("Y"));
		if ($toDate == 0) $toDate = mktime(23,59,59,date("n"),date("j"),date("Y"));
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
		if ($fromDate>0) $params['fd'] = $fromDate;
		else if (isset($params['fd'])) unset($params['fd']);
		if ($toDate>0) $params['td'] = $toDate;
		else if (isset($params['td'])) unset($params['td']);
		if (strlen($status)>0) $params['stt'] = $status;
		else if (isset($params['stt'])) unset($params['stt']);
		return $this->Service->ConstructUrl($serviceParameter,$params);
	}

	protected function btnSearch_Clicked($sender, $param)
	{
		if ($this->IsValid)
		{
			//$this->SearchText = THttpUtility::htmlEncode($this->txtSearchText->SafeText);
			//$this->populateData();
			$this->Response->redirect($this->populateSortUrl($this->SortBy,$this->SortType,$this->dpFromDate->Data,$this->dpToDate->Data,$this->cboStatusSelector->SelectedValue));
		}
	}

	protected function btnSearchReset_Clicked($sender, $param)
	{
		//$this->SearchText = "";
		//$this->txtSearchText->Text = "Filter by ID or Name";
		//$this->populateData();
		$this->Response->redirect($this->populateSortUrl($this->SortBy,$this->SortType,0,0,""));
	}
	
	public $MasterTotal=0;
	protected function ItemList_ItemCreated($sender, $param)
	{
		if ($param->Item->Data && ($param->Item->ItemType == "Item" || $param->Item->ItemType == "AlternatingItem"))
		{
			$this->MasterTotal += $param->Item->Data->Total;
		}
	}
	
	public function btnExport_Clicked($sender, $param)
	{
		Prado::using("Application.common.PHPExcel");
		Prado::using("Application.common.PHPExcel.Style");
		Prado::using("Application.common.PHPExcel.Style.Font");
		Prado::using("Application.common.PHPExcel.IOFactory");
		Prado::using("Application.common.PHPExcel.Writer.Excel5");
		try
		{
			$workBook = new PHPExcel();
			$workBook->getProperties()->setCreator("Alex Do")
									->setLastModifiedBy("Alex Do")
									->setTitle("The Organic Grocer Export generated on ".date("m.D.Y",time()))
									->setSubject("The Organic Grocer Export")
									->setDescription("The Organic Grocer Export generated on ".date("m.D.Y",time()));
			$workBook->setActiveSheetIndex(0);
			$workSheet = $workBook->getActiveSheet();
			$workSheet->setCellValue("A1","From Date");
			$workSheet->setCellValue("B1",date("d/m/Y",$this->FromDate));
			$workSheet->setCellValue("C1","To Date");
			$workSheet->setCellValue("D1",date("d/m/Y",$this->ToDate));
			$workSheet->setCellValue("A2","Order Status");
			$status = OrderStatusRecord::finder()->findBystatus_code($this->Status);
			$workSheet->setCellValue("B2",($status?$status->Name:"All"));
			
			$workSheet->setCellValue("A4","No")->getStyle("A4")->getFont()->setBold(true);
			$workSheet->setCellValue("B4","Order Date")->getStyle("B4")->getFont()->setBold(true);
			$workSheet->setCellValue("C4","Order Number")->getStyle("C4")->getFont()->setBold(true);
			$workSheet->setCellValue("D4","Customer Name")->getStyle("D4")->getFont()->setBold(true);
			$workSheet->setCellValue("E4","Total Amount")->getStyle("E4")->getFont()->setBold(true);
			$workSheet->setCellValue("F4","Order Status")->getStyle("F4")->getFont()->setBold(true);
			
			$items = $this->generateData();
			$startRow = 5;
			$masterTotal = 0;
			if (count($items)>0)
			{
				for($i=0;$i<count($items);$i++)
				{
					$workSheet->setCellValue("A".($i+$startRow),$i+1);
					$workSheet->setCellValue("B".($i+$startRow),date('d/m/Y h:i:s A',$items[$i]->CreateDate));
					$workSheet->setCellValue("C".($i+$startRow),$items[$i]->Num);
					$user = UserRecord::finder()->findByPk($items[$i]->UserID);
					$workSheet->setCellValue("D".($i+$startRow),$user->FirstName." ".$user->LastName);
					$workSheet->setCellValue("E".($i+$startRow),$items[$i]->Total);
					$workSheet->setCellValue("F".($i+$startRow),($items[$i]->LatestHistory ? $items[$i]->LatestHistory->OrderStatus->Name : ""));
					$masterTotal += $items[$i]->Total;
				}
				$workSheet->setCellValue("D".($startRow+count($items)+1),"Total Amount");
				$workSheet->setCellValue("E".($startRow+count($items)+1),$masterTotal);
			}
			
			$workSheet->getColumnDimension("A")->setWidth(15);
			$workSheet->getColumnDimension("B")->setWidth(30);
			$workSheet->getColumnDimension("C")->setWidth(30);
			$workSheet->getColumnDimension("D")->setWidth(30);
			$workSheet->getColumnDimension("E")->setWidth(20);
			$workSheet->getColumnDimension("F")->setWidth(20);
			
			$phpExcelWriter = PHPExcel_IOFactory::createWriter($workBook, 'Excel5');
			$fileName = "Export_Generated_On_".date("Y.m.d_h.i.s",time()).".xls";
			$this->Response->appendHeader("Content-Type:application/vnd.ms-excel");
			$this->Response->appendHeader("Content-Disposition:attachment;filename=$fileName");
			$this->Response->appendHeader("Cache-Control:max-age=0");
			$phpExcelWriter->save('php://output'); 
			$this->Response->flush();
			exit();
		}
		catch(Exception $ex)
		{
			$this->Notice->Type = AdminNoticeType::Error;
			$this->Notice->Text = $ex;//$this->Application->getModule("message")->translate("UNKNOWN_ERROR");
		}
	}
}

?>