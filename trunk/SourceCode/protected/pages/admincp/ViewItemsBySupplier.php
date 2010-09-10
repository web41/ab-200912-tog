<?php

class ViewItemsBySupplier extends TPage
{
	private $_maxPage = 1;
	private $_currentPage = 1;
	private $_sortBy = "";
	private $_sortType = "";
	private $_fromDate = 0;
	private $_toDate = 0;
	private $_mfID = 0;
	private $_sortable = array("p.product_name","b.brand_name","oi.quantity","m.mf_name","oi.gen_counter");
	private $_queryParams = array("p","st","sb","mf","fd","td","type");
	const AR = "OrderItemRecord";

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

	public function getMfID()
	{
		return $this->_mfID;
	}

	public function setMfID($value)
	{
		$this->_mfID = TPropertyValue::ensureInteger($value);
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
		$this->SortType = ($this->Request->contains('st')) ? $this->Request['st'] : 'asc';
		$this->MfID = ($this->Request->contains('mf') && TPropertyValue::ensureInteger($this->Request['mf'])>0) ? TPropertyValue::ensureInteger($this->Request['mf']) : 0;
		$this->FromDate = ($this->Request->contains('fd') && TPropertyValue::ensureInteger($this->Request['fd'])>0) ? TPropertyValue::ensureInteger($this->Request['fd']) : mktime(0,0,0,date("n"),date("j"),date("Y"));
		$this->ToDate = ($this->Request->contains('td') && TPropertyValue::ensureInteger($this->Request['td'])>0) ? TPropertyValue::ensureInteger($this->Request['td']) : mktime(23,59,59,date("n"),date("j"),date("Y"));
		if (!$this->IsPostBack)
		{
			$this->cboMfSelector->DataSource = ManufacturerRecord::getAllItems();
			$this->cboMfSelector->DataBind();
			$this->cboMfSelector->SelectedValue = $this->MfID;
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
			$this->Notice->Text = $this->Application->getModule("message")->translate("ITEM_FOUND",0,"record");
		}
	}

	public function generateData()
	{
		$sql = "";
		if ($this->FromDate > 0)
			$sql .= " AND (o.c_date >= '".$this->FromDate."')";
		if ($this->ToDate > 0)
			$sql .= " AND (o.c_date <= '".$this->ToDate."')";
		if ($this->MfID>0)
			$sql .= " AND (m.mf_id = '".$this->MfID."')";

		if (!isset($this->Sortable[$this->SortBy])) $this->SortBy=3;
		$order = $this->Sortable[$this->SortBy]." ".$this->SortType;
		if ($this->SortBy==3) $order .= ", ".$this->Sortable[1]." ".$this->SortType;

		$sqlmap = $this->Application->Modules['sqlmap']->Client;
		return $sqlmap->queryForList("ViewItemsBySupplier", array("ADDITIONAL_CONDITION"=>$sql,"ORDER_BY"=>$order));
	}

	public function populateSortUrl($sortBy, $sortType, $fromDate=0, $toDate=0, $mfID=0, $resetPage=true)
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
		if ($mfID>0) $params['mf'] = $mfID;
		else if (isset($params['mf'])) unset($params['mf']);
		return $this->Service->ConstructUrl($serviceParameter,$params);
	}

	protected function btnSearch_Clicked($sender, $param)
	{
		if ($this->IsValid)
		{
			//$this->SearchText = THttpUtility::htmlEncode($this->txtSearchText->SafeText);
			//$this->populateData();
			$this->Response->redirect($this->populateSortUrl($this->SortBy,$this->SortType,$this->dpFromDate->Data,$this->dpToDate->Data,$this->cboMfSelector->SelectedValue));
		}
	}

	protected function btnSearchReset_Clicked($sender, $param)
	{
		//$this->SearchText = "";
		//$this->txtSearchText->Text = "Filter by ID or Name";
		//$this->populateData();
		$this->Response->redirect($this->populateSortUrl($this->SortBy,$this->SortType,0,0,0));
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
			$workSheet->setCellValue("B1",date("d/m/Y",$this->dpFromDate->Data));
			$workSheet->setCellValue("A2","To Date");
			$workSheet->setCellValue("B2",date("d/m/Y",$this->dpToDate->Data));
			
			$supplier = null;
			if (TPropertyValue::ensureInteger($this->MfID)>0)
				$supplier = ManufacturerRecord::finder()->findByPk(TPropertyValue::ensureInteger($this->MfID));
			$orderItems = $this->generateData();
			if (count($orderItems) > 0)
			{
				$workSheet->setCellValue("A3","Supplier");
				$workSheet->setCellValue("B3",($supplier instanceof ManufacturerRecord ? $supplier->Name : "All suppliers"));

				$workSheet->setCellValue("A4","No.")->getStyle("A4")->getFont()->setBold(true);
				$workSheet->setCellValue("B4","Item Description")->getStyle("B4")->getFont()->setBold(true);
				$workSheet->setCellValue("C4","Brand")->getStyle("C4")->getFont()->setBold(true);
				$workSheet->setCellValue("D4","Cost")->getStyle("D4")->getFont()->setBold(true);
				$workSheet->setCellValue("E4","Quantity")->getStyle("E4")->getFont()->setBold(true);
				$workSheet->setCellValue("F4","Amount")->getStyle("F4")->getFont()->setBold(true);
				if ($supplier == null) $workSheet->setCellValue("H4","Supplier")->getStyle("H4")->getFont()->setBold(true);

				$startRow = 5;
				$totalCost = 0;
				for($i=0;$i<count($orderItems);$i++)
				{	
					$orderItems[$i]->Counter++;
					$orderItems[$i]->save();
					$product = ProductRecord::finder()->withManufacturer()->withBrand()->findByPk($orderItems[$i]->ProductID);
					$prop = PropertyRecord::finder()->findByPk($orderItems[$i]->PropertyID);
					$workSheet->setCellValue("A".($i+$startRow),$i+1);
					$workSheet->setCellValue("B".($i+$startRow),$product->Name." - ".($prop instanceof PropertyRecord ? $prop->Name : ""));
					$workSheet->setCellValue("C".($i+$startRow),$product->Brand->Name);
					$workSheet->setCellValue("D".($i+$startRow),($prop instanceof PropertyRecord ? number_format($prop->CostPrice,2) : 0));
					$workSheet->setCellValue("E".($i+$startRow),$orderItems[$i]->Quantity);
					$workSheet->setCellValue("F".($i+$startRow),($prop instanceof PropertyRecord ? number_format($prop->CostPrice*$orderItems[$i]->Quantity,2) : 0));
					$workSheet->setCellValue("G".($i+$startRow),Common::showOrdinal($orderItems[$i]->Counter));
					if ($supplier == null)
					{
						$workSheet->setCellValue("H".($i+$startRow),$product->Manufacturer->Name);
					}
					$totalCost += ($prop instanceof PropertyRecord ? number_format($prop->CostPrice*$orderItems[$i]->Quantity,2) : 0);
				}
				$workSheet->setCellValue("E".(count($orderItems)+$startRow),'Total')->getStyle("E".(count($orderItems)+$startRow))->getFont()->setBold(true);
				$workSheet->setCellValue("F".(count($orderItems)+$startRow),$totalCost)->getStyle("F".(count($orderItems)+$startRow))->getFont()->setBold(true);

				$workSheet->getColumnDimension("A")->setWidth(20);
				$workSheet->getColumnDimension("B")->setWidth(80);
				$workSheet->getColumnDimension("C")->setWidth(30);
			}
			
			$phpExcelWriter = PHPExcel_IOFactory::createWriter($workBook, 'Excel5');
			$fileName = "Export_Generated_On_".date("Y-m-d_h-i",time()).".xls";
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
			$this->Notice->Text = $ex;//$this->Application->getModule("message")->translate("UNKNOWN_ERROR");
		}
	}
}

?>