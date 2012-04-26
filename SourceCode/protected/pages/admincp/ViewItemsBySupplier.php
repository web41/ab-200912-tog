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
	private $_orderStatusCode = '0'; // Added by Tom -26/04/2012
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
	
	public function getOrderStatusCode()
	{
		return $this->_orderStatusCode;
	}

	public function setOrderStatusCode($value)
	{
		$this->_orderStatusCode = $value;
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
		$this->OrderStatusCode = ($this->Request->contains('os')) ? $this->Request['os'] : '0'; // Tom 26/04/2012
		$tempFromDate = ($this->Request->contains('fd') && TPropertyValue::ensureInteger($this->Request['fd'])>0) ? TPropertyValue::ensureInteger($this->Request['fd']) : time();
		$tempToDate = ($this->Request->contains('td') && TPropertyValue::ensureInteger($this->Request['td'])>0) ? TPropertyValue::ensureInteger($this->Request['td']) : time();
		$this->FromDate = mktime(0,0,0,date("n",$tempFromDate),date("j",$tempFromDate),date("Y",$tempFromDate));
		$this->ToDate = mktime(23,59,59,date("n",$tempToDate),date("j",$tempToDate),date("Y",$tempToDate));
		if (!$this->IsPostBack)
		{
			$this->cboMfSelector->DataSource = ManufacturerRecord::getAllItems();
			$this->cboMfSelector->DataBind();
			$this->cboMfSelector->SelectedValue = $this->MfID;
			// Added by Tom 26-04-2012
			$this->cboOrderStatusSelector->DataSource = OrderStatusRecord::getAllItems();
			$this->cboOrderStatusSelector->DataBind();
			$this->cboOrderStatusSelector->SelectedValue = $this->OrderStatusCode;
			// End
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
		if ($this->OrderStatusCode != '0') {
			$sql .= " AND (oh.order_status_code = '". $this->OrderStatusCode ."')";
			$sql .=	" AND (oh.c_date = (SELECT max(c_date) FROM tbl_order_history WHERE order_id = oh.order_id))";
		}
		else {		
			//$sql .= " AND (oh.order_status_code in ('C','W','P', 'D'))";
			$sql .=	" AND (oh.c_date = (SELECT max(c_date) FROM tbl_order_history WHERE order_id = oh.order_id))";			
		}
		if (!isset($this->Sortable[$this->SortBy])) $this->SortBy=3;
		$order = $this->Sortable[$this->SortBy]." ".$this->SortType;
		if ($this->SortBy==3) $order .= ", ".$this->Sortable[1]." ".$this->SortType;

		$sqlmap = $this->Application->Modules['sqlmap']->Client;
		return $sqlmap->queryForList("ViewItemsBySupplier", array("ADDITIONAL_CONDITION"=>$sql,"ORDER_BY"=>$order));
	}

	//public function populateSortUrl($sortBy, $sortType, $fromDate=0, $toDate=0, $mfID=0, $resetPage=true)
	public function populateSortUrl($sortBy, $sortType, $fromDate=0, $toDate=0, $mfID=0, $orderStatus='0', $resetPage=true)
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
		if ($orderStatus != '' && $orderStatus != '0') $params['os'] = $orderStatus;
		else if (isset($params['os'])) unset($params['os']);
		return $this->Service->ConstructUrl($serviceParameter,$params);
	}

	protected function btnSearch_Clicked($sender, $param)
	{
		if ($this->IsValid)
		{
			//$this->SearchText = THttpUtility::htmlEncode($this->txtSearchText->SafeText);
			//$this->populateData();
				//$this->populateSortUrl($this->SortBy,$this->SortType,$this->dpFromDate->Data,$this->dpToDate->Data,$this->cboMfSelector->SelectedValue,$this->cboOrderStatusSelector->SelectedValue);
			$this->Response->redirect($this->populateSortUrl($this->SortBy,$this->SortType,$this->dpFromDate->Data,$this->dpToDate->Data,$this->cboMfSelector->SelectedValue,$this->cboOrderStatusSelector->SelectedValue));
		}
	}

	protected function btnSearchReset_Clicked($sender, $param)
	{
		//$this->SearchText = "";
		//$this->txtSearchText->Text = "Filter by ID or Name";
		//$this->populateData();
		$this->Response->redirect($this->populateSortUrl($this->SortBy,$this->SortType,0,0,0,0));
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
				$workSheet->setCellValue("B4","Supplier")->getStyle("B4")->getFont()->setBold(true);
				$workSheet->setCellValue("C4","Brand")->getStyle("C4")->getFont()->setBold(true);
				$workSheet->setCellValue("D4","Item Description")->getStyle("D4")->getFont()->setBold(true);
				$workSheet->setCellValue("E4","Quantity")->getStyle("E4")->getFont()->setBold(true);
				$workSheet->setCellValue("F4","Cost")->getStyle("F4")->getFont()->setBold(true);
				$workSheet->setCellValue("G4","Selling Price")->getStyle("G4")->getFont()->setBold(true);
				$workSheet->setCellValue("H4","Customer Name")->getStyle("H4")->getFont()->setBold(true);
				$workSheet->setCellValue("I4","Invoice")->getStyle("I4")->getFont()->setBold(true);
				//$workSheet->setCellValue("J4","No of Exports")->getStyle("J4")->getFont()->setBold(true);

				/*$startRow = 5;
				$totalCost = 0;
				$totalSell = 0;
				$i=0;
				foreach($orderItems as $orderItem)
				{	
					//$orderItem->Counter++;
					//$orderItem->save();
					$product = ProductRecord::finder()->withManufacturer()->withBrand()->findByPk($orderItem->ProductID);
					$prop = PropertyRecord::finder()->findByPk($orderItem->PropertyID);
					$workSheet->setCellValue("A".($i+$startRow),$i+1);
					$workSheet->setCellValue("B".($i+$startRow),$product->Manufacturer->Name);
					$workSheet->setCellValue("C".($i+$startRow),$product->Brand->Name);
					$workSheet->setCellValue("D".($i+$startRow),$product->Name." - ".($prop instanceof PropertyRecord ? $prop->Name : ""));
					$workSheet->setCellValue("E".($i+$startRow),$orderItem->Quantity);
					$workSheet->setCellValue("F".($i+$startRow),($prop instanceof PropertyRecord ? number_format($prop->CostPrice*$orderItem->Quantity,2) : 0));
					$workSheet->setCellValue("G".($i+$startRow),($prop instanceof PropertyRecord ? number_format($prop->Price*$orderItem->Quantity,2) : 0));
					$workSheet->setCellValue("H".($i+$startRow),$orderItem->Order->BFirstName.' '.$orderItem->Order->BLastName);
					$workSheet->setCellValue("I".($i+$startRow),$orderItem->Order->Num);
					//$workSheet->setCellValue("J".($i+$startRow),Common::showOrdinal($orderItems[$i]->Counter));

					$totalCost += ($prop instanceof PropertyRecord ? number_format($prop->CostPrice*$orderItem->Quantity,2) : 0);
					$totalSell += ($prop instanceof PropertyRecord ? number_format($prop->Price*$orderItem->Quantity,2) : 0);
					
					$i++;
				}
				$workSheet->setCellValue("E".(count($orderItems)+$startRow),'Total')->getStyle("E".(count($orderItems)+$startRow))->getFont()->setBold(true);
				$workSheet->setCellValue("F".(count($orderItems)+$startRow),$totalCost)->getStyle("F".(count($orderItems)+$startRow))->getFont()->setBold(true);
				$workSheet->setCellValue("G".(count($orderItems)+$startRow),$totalSell)->getStyle("G".(count($orderItems)+$startRow))->getFont()->setBold(true);*/
				
				// re-order items
				$orderedItems = array();
				foreach($orderItems as $orderItem) 
				{
					$product = ProductRecord::finder()->withManufacturer()->withBrand()->findByPk($orderItem->ProductID);
					$orderedItems[$product->Manufacturer->Name][] = $orderItem;
				}
				
				// release memory
				unset($orderItems);
				
				// write to excel
				$startRow = 5;
				$i=$startRow;
				$counter=1;
				$totalCost = 0;
				$totalSell = 0;
				foreach($orderedItems as $supplierName=>$orderItems) 
				{
					$workSheet->mergeCells("B".$i.":B".(count($orderItems)-1+$i))->setCellValue("B".$i,$supplierName)->getStyle("B$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
					$totalCostBySupplier = 0;
					$totalSellBySupplier = 0;
					foreach($orderItems as $orderItem)
					{
						$product = ProductRecord::finder()->withManufacturer()->withBrand()->findByPk($orderItem->ProductID);
						$prop = PropertyRecord::finder()->findByPk($orderItem->PropertyID);
						
						$workSheet->setCellValue("A".$i,$counter);
						
						$workSheet->setCellValue("C".$i,$product->Brand->Name);
						$workSheet->setCellValue("D".$i,$product->Name." - ".($prop instanceof PropertyRecord ? $prop->Name : ""));
						$workSheet->setCellValue("E".$i,$orderItem->Quantity);
						$workSheet->setCellValue("F".$i,($prop instanceof PropertyRecord ? number_format($prop->CostPrice*$orderItem->Quantity,2) : 0));
						$workSheet->setCellValue("G".$i,($prop instanceof PropertyRecord ? number_format($prop->Price*$orderItem->Quantity,2) : 0));
						$workSheet->setCellValue("H".$i,$orderItem->Order->BFirstName.' '.$orderItem->Order->BLastName);
						$workSheet->setCellValue("I".$i,$orderItem->Order->Num);
						
						$totalCostBySupplier += ($prop instanceof PropertyRecord ? number_format($prop->CostPrice*$orderItem->Quantity,2) : 0);
						$totalSellBySupplier += ($prop instanceof PropertyRecord ? number_format($prop->Price*$orderItem->Quantity,2) : 0);
						
						$i++;
						$counter++;
					}
					
					$workSheet->setCellValue("E$i",'Total by Supplier')->getStyle("E$i")->getFont()->setBold(true);
					$workSheet->setCellValue("F$i",$totalCostBySupplier)->getStyle("F$i")->getFont()->setBold(true);
					$workSheet->setCellValue("G$i",$totalSellBySupplier)->getStyle("G$i")->getFont()->setBold(true);
					$i++;
					
					$totalCost += $totalCostBySupplier;
					$totalSell += $totalSellBySupplier;
				}
				
				if ($supplier==null)
				{
					$i++;
					$workSheet->setCellValue("E$i",'Total')->getStyle("E$i")->getFont()->setBold(true);
					$workSheet->setCellValue("F$i",$totalCost)->getStyle("F$i")->getFont()->setBold(true);
					$workSheet->setCellValue("G$i",$totalSell)->getStyle("G$i")->getFont()->setBold(true);
				}

				$workSheet->getColumnDimension("A")->setWidth(20);
				$workSheet->getColumnDimension("C")->setWidth(50);
				$workSheet->getColumnDimension("D")->setWidth(80);
				$workSheet->getColumnDimension("H")->setWidth(20);
				$workSheet->getColumnDimension("I")->setWidth(20);
			}
			
			$phpExcelWriter = PHPExcel_IOFactory::createWriter($workBook, 'Excel5');
			$fileName = "Items_By_Supplier_".date("Y-m-d_h-i",time()).".xls";
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