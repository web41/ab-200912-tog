<?php

class ViewItemsByOrder extends TPage
{
	private $_maxPage = 1;
	private $_currentPage = 1;
	private $_sortBy = "";
	private $_sortType = "";
	private $_fromDate = 0;
	private $_toDate = 0;
	private $_sortable = array("order_num","total","c_date");
	private $_queryParams = array("p","st","sb","fd","td","type");
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
		$this->FromDate = ($this->Request->contains('fd')) ? TPropertyValue::ensureInteger($this->Request['fd']) : mktime(0,0,0,date("n"),date("j"),date("Y"));
		$this->ToDate = ($this->Request->contains('td')) ? TPropertyValue::ensureInteger($this->Request['td']) : mktime(23,59,59,date("n"),date("j"),date("Y"));
		if (!$this->IsPostBack)
		{
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
		$criteria = new TActiveRecordCriteria;
		$criteria->OrdersBy['c_date'] = 'desc';
		// addtional condition here
		// this part will be hard-code on each page
		$criteria->Condition = "order_id in (select distinct o.order_id from tbl_order o 
								left join tbl_order_item oi on o.order_id = oi.order_id
								left join tbl_product p on oi.product_id = p.product_id";

		$criteria->Condition .= " where o.order_id > 0 ";
		if ($this->FromDate>0)
		{
			$criteria->Condition .= " and (o.c_date >= '".$this->FromDate."') ";
		}
		if ($this->ToDate>0)
		{
			$criteria->Condition .= " and (o.c_date <= '".$this->ToDate."') ";
		}
		// -- 
		$criteria->Condition .= ")";
		return OrderRecord::finder()->withOrderItems()->withUser()->findAll($criteria);
	}

	public function populateSortUrl($sortBy, $sortType, $fromDate=0, $toDate=0, $resetPage=true)
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
		return $this->Service->ConstructUrl($serviceParameter,$params);
	}

	protected function btnSearch_Clicked($sender, $param)
	{
		if ($this->IsValid)
		{
			//$this->SearchText = THttpUtility::htmlEncode($this->txtSearchText->SafeText);
			//$this->populateData();
			$this->Response->redirect($this->populateSortUrl($this->SortBy,$this->SortType,$this->dpFromDate->Data,$this->dpToDate->Data));
		}
	}

	protected function btnSearchReset_Clicked($sender, $param)
	{
		//$this->SearchText = "";
		//$this->txtSearchText->Text = "Filter by ID or Name";
		//$this->populateData();
		$this->Response->redirect($this->populateSortUrl($this->SortBy,$this->SortType,0,0));
	}

	protected function ItemList_ItemCreated($sender, $param)
	{
		if ($param->Item->ItemChildList instanceof TRepeater && $param->Item->Data)
		{
			$param->Item->ItemChildList->DataSource = $param->Item->Data->OrderItems;
			$param->ITem->ItemChildList->DataBind();
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
			$workSheet->setCellValue("B1",date("d/m/Y",$this->dpFromDate->Data));
			$workSheet->setCellValue("A2","To Date");
			$workSheet->setCellValue("B2",date("d/m/Y",$this->dpToDate->Data));
			$orders = $this->generateData();
			$totalOrders = count($orders);
			if ($totalOrders>0)
			{
				$startRow = 4;
				for($i=0;$i<count($orders);$i++)
				{
					$workSheet->setCellValue("A".($i+$startRow),date("d/m/Y",$orders[$i]->CreateDate))->getStyle("A".($i+$startRow))->getFont()->setBold(true);
					$workSheet->setCellValue("B".($i+$startRow),$orders[$i]->Num." (".$orders[$i]->User->FirstName." ".$orders[$i]->User->LastName.")")->getStyle("B".($i+$startRow))->getFont()->setBold(true);
					//$workSheet->setCellValue("C".($i+$startRow),$orders[$i]->User->FirstName." ".$orders[$i]->User->LastName)->getStyle("C".($i+$startRow))->getFont()->setBold(true);
					$workSheet->setCellValue("F".($i+$startRow),$orders[$i]->Total)->getStyle("F".($i+$startRow))->getFont()->setBold(true);
					$orderItems = $orders[$i]->OrderItems;
					if (count($orderItems)>0)
					{	
						$startRow++;
						for($j=0;$j<count($orderItems);$j++)
						{
							$orderItems[$j]->Counter++;
							$orderItems[$j]->save();
							$product = ProductRecord::finder()->withManufacturer()->withBrand()->findByPk($orderItems[$j]->ProductID);
							$prop = PropertyRecord::finder()->findByPk($orderItems[$j]->PropertyID);
							if ($product instanceof ProductRecord)
							{
								$workSheet->setCellValue("A".($j+$i+$startRow),$j+1);
								$workSheet->setCellValue("B".($j+$i+$startRow),$product->Name." - ".($prop instanceof PropertyRecord ? $prop->Name : ""));
								$workSheet->setCellValue("C".($j+$i+$startRow),$product->Brand->Name);
								$workSheet->setCellValue("D".($j+$i+$startRow),$orderItems[$j]->UnitPrice);
								$workSheet->setCellValue("E".($j+$i+$startRow),$orderItems[$j]->Quantity);
								$workSheet->setCellValue("F".($j+$i+$startRow),$orderItems[$j]->Subtotal);
								$workSheet->setCellValue("G".($j+$i+$startRow),$product->Manufacturer->Name);
								$workSheet->setCellValue("H".($j+$i+$startRow),Common::showOrdinal($orderItems[$j]->Counter));	
							}
						}
					}
					$startRow=$startRow+count($orderItems);
				}
				$workSheet->getColumnDimension("A")->setWidth(20);
				$workSheet->getColumnDimension("B")->setWidth(80);
				$workSheet->getColumnDimension("C")->setWidth(30);
			}
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