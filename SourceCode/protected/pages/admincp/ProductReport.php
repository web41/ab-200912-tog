<?php

class ProductReport extends TPage
{
	private $_maxPage = 1;
	private $_currentPage = 1;
	private $_sortBy = "";
	private $_sortType = "";
	private $_brandID = 0;
	private $_catID = 0;
	private $_fromDate = 0;
	private $_toDate = 0;
	private $_sortable = array("b.brand_name","p.product_name","total_quantity");
	private $_queryParams = array("p","st","sb","c","b","fd","td");
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
	
	public function getBrandID()
	{
		return $this->_brandID;
	}

	public function setBrandID($value)
	{
		$this->_brandID = TPropertyValue::ensureInteger($value);
	}

	public function getCatID()
	{
		return $this->_catID;
	}

	public function setCatID($value)
	{
		$this->_catID = TPropertyValue::ensureInteger($value);
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
		$this->BrandID = ($this->Request->contains('b')) ? TPropertyValue::ensureInteger($this->Request['b']) : 0;
		$this->CatID = ($this->Request->contains('c')) ? TPropertyValue::ensureInteger($this->Request['c']) : 0;
		if (!$this->IsPostBack)
		{
			// fill parent selector combobox
			$this->cboCatSelector->DataSource = CategoryRecord::finder()->getCategoryTree(true);
			$this->cboCatSelector->DataBind();
			$this->cboCatSelector->SelectedValue = $this->CatID;
			$this->cboBrandSelector->DataSource = BrandRecord::getAllItems();
			$this->cboBrandSelector->DataBind();
			$this->cboBrandSelector->SelectedValue = $this->BrandID;
			$this->dpFromDate->Data = $this->FromDate;
			$this->dpToDate->Data = $this->ToDate;
			$this->populateData();
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
		if ($this->BrandID>0)
			$sql .= " AND (b.brand_id = '".$this->BrandID."')";
		if ($this->CatID>0)
			$sql .= " AND (pcx.cat_id = '".$this->CatID."')";

		$order = (isset($this->Sortable[$this->SortBy])?$this->Sortable[$this->SortBy]:$this->Sortable[1])." ".$this->SortType;

		$sqlmap = $this->Application->Modules['sqlmap']->Client;
		return $sqlmap->queryForList("ProductReport", array("ADDITIONAL_CONDITION"=>$sql,"ORDER_BY"=>$order));
	}

	public function populateSortUrl($sortBy, $sortType, $fromDate=0, $toDate=0, $brandID=0, $catID=0, $resetPage=true)
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
		if ($brandID>0) $params['b'] = $brandID;
		else if (isset($params['b'])) unset($params['b']);
		if ($catID>0) $params['c'] = $catID;
		else if (isset($params['c'])) unset($params['c']);
		return $this->Service->ConstructUrl($serviceParameter,$params);
	}

	protected function btnSearch_Clicked($sender, $param)
	{
		if ($this->IsValid)
		{
			//$this->SearchText = THttpUtility::htmlEncode($this->txtSearchText->SafeText);
			//$this->populateData();
			$this->Response->redirect($this->populateSortUrl($this->SortBy,$this->SortType,$this->dpFromDate->Data,$this->dpToDate->Data,$this->cboBrandSelector->SelectedValue,$this->cboCatSelector->SelectedValue));
		}
	}

	protected function btnSearchReset_Clicked($sender, $param)
	{
		//$this->SearchText = "";
		//$this->txtSearchText->Text = "Filter by ID or Name";
		//$this->populateData();
		$this->Response->redirect($this->populateSortUrl($this->SortBy,$this->SortType,0,0,0,0));
	}

	public $MasterQuantity=0;
	protected function ItemList_ItemCreated($sender, $param)
	{
		if ($param->Item->Data && ($param->Item->ItemType == "Item" || $param->Item->ItemType == "AlternatingItem"))
		{
			$this->MasterQuantity += $param->Item->Data->TotalQuantity;
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
			$workSheet->setCellValue("A2","Category");
			$cat = CategoryRecord::finder()->findByPk($this->CatID);
			$workSheet->setCellValue("B2",$cat&&$cat->ID>0?$cat->Name:"All");
			$workSheet->setCellValue("C2","Brand");
			$brand = BrandRecord::finder()->findByPk($this->BrandID);
			$workSheet->setCellValue("D2",$brand&&$brand->ID>0?$brand->Name:"All");

			$workSheet->setCellValue("A4","No")->getStyle("A4")->getFont()->setBold(true);
			$workSheet->setCellValue("B4","Category")->getStyle("B4")->getFont()->setBold(true);
			$workSheet->setCellValue("C4","Brand")->getStyle("C4")->getFont()->setBold(true);
			$workSheet->setCellValue("D4","Product Name")->getStyle("D4")->getFont()->setBold(true);
			$workSheet->setCellValue("E4","Qty")->getStyle("E4")->getFont()->setBold(true);

			$items = $this->generateData();
			$startRow = 5;
			$masterQuantity = 0;
			if (count($items)>0)
			{
				for($i=0;$i<count($items);$i++)
				{
					$workSheet->setCellValue("A".($i+$startRow),$i+1);
					$workSheet->setCellValue("B".($i+$startRow),$items[$i]->Product?$items[$i]->Product->CategoryNames:"");
					$workSheet->setCellValue("C".($i+$startRow),$items[$i]->Product?$items[$i]->Product->Brand->Name:"");
					$workSheet->setCellValue("D".($i+$startRow),$items[$i]->Product->Name.($items[$i]->Property?" - ".$items[$i]->Property->Name:""));
					$workSheet->setCellValue("E".($i+$startRow),$items[$i]->Quantity);
					$masterQuantity += $items[$i]->Quantity;
				}
				$workSheet->setCellValue("D".($startRow+count($items)+1),"Total");
				$workSheet->setCellValue("E".($startRow+count($items)+1),$masterQuantity);
			}

			$workSheet->getColumnDimension("A")->setWidth(15);
			$workSheet->getColumnDimension("B")->setWidth(50);
			$workSheet->getColumnDimension("C")->setWidth(20);
			$workSheet->getColumnDimension("D")->setWidth(75);
			$workSheet->getColumnDimension("E")->setWidth(15);

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