<?php

class ProductExport extends TPage {
    private $_brandID = 0;
    private $_mfID = 0;
    private $_catID = 0;
    private $_sortable = array("product_name","brand_id","mf_id");
    private $_sortBy;
    private $_sortType;
    private $_queryParams = array("st","sb","b","mf","c");
    const AR = "ProductRecord";

    public function getSortBy()
    {
        return $this->_sortBy;
    }

    public function setSortBy($value)
    {
        $this->_sortBy = $value;
    }

    public function getSortable()
    {
        return $this->_sortable;
    }

    public function getSortType()
    {
        return $this->_sortType;
    }

    public function setSortType($value)
    {
        $this->_sortType = (strtolower($value)==="desc") ? "desc" : "asc";
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

    public function onLoad($param) {
        if (!$this->IsPostBack) {
            $this->cboCatSelector->DataSource = CategoryRecord::finder()->getCategoryTree(true);
            $this->cboCatSelector->DataBind();
            $criteria = new TActiveRecordCriteria;
            $criteria->Condition = "brand_id > 0";
            $criteria->OrdersBy["brand_name"] = "asc";
            $this->cboBrandSelector->DataSource = BrandRecord::finder()->findAll($criteria);
            $this->cboBrandSelector->DataBind();
            $criteria = new TActiveRecordCriteria;
            $criteria->Condition = "mf_id > 0";
            $criteria->OrdersBy["mf_name"] = "asc";
            $this->cboMfSelector->DataSource = ManufacturerRecord::finder()->findAll($criteria);
            $this->cboMfSelector->DataBind();
        }
    }

    protected function btnExport_Clicked($sender, $param) {
        Prado::using("Application.common.PHPExcel");
        Prado::using("Application.common.PHPExcel.Style");
        Prado::using("Application.common.PHPExcel.Style.Font");
        Prado::using("Application.common.PHPExcel.IOFactory");
        Prado::using("Application.common.PHPExcel.Writer.Excel5");

        $this->SortBy = $this->cboSortBySelector->SelectedValue;
        $this->SortType = $this->cboSortTypeSelector->SelectedValue;;
        $this->BrandID = $this->cboBrandSelector->SelectedValue;
        $this->MfID = $this->cboMfSelector->SelectedValue;
        $this->CatID = $this->cboCatSelector->SelectedValue;
        
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
        $criteria->Condition .= " where p.product_id > 0 ";
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
		$items = $activeRecord->finder()->withBrand()->withManufacturer()->withCategories()->withProperties()->findAll($criteria);
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
            $workSheet->setCellValue("B1","Supplier")->getStyle("B1")->getFont()->setBold(true);
            $workSheet->setCellValue("C1","Brand")->getStyle("C1")->getFont()->setBold(true);
			$workSheet->mergeCells("D1:E1");
			$workSheet->setCellValue("D1","Product")->getStyle("D1")->getFont()->setBold(true);
			$workSheet->setCellValue("F1","Price")->getStyle("F1")->getFont()->setBold(true);
			$workSheet->setCellValue("G1","Is Published")->getStyle("G1")->getFont()->setBold(true);
			
            $startRow = 2;
            if (count($items)>0)
            {
				for($i=0; $i<count($items); $i++)
                {
					$props = count($items[$i]->Properties);
					$workSheet->mergeCells('A'.($i+$startRow).':A'.($i+$startRow+$props-1));
					$workSheet->mergeCells('B'.($i+$startRow).':B'.($i+$startRow+$props-1));
					$workSheet->mergeCells('C'.($i+$startRow).':C'.($i+$startRow+$props-1));
					$workSheet->mergeCells('D'.($i+$startRow).':D'.($i+$startRow+$props-1));
					$workSheet->mergeCells('G'.($i+$startRow).':G'.($i+$startRow+$props-1));
                    
					$workSheet->setCellValue("A".($i+$startRow),$i+1);
                    $workSheet->setCellValue("B".($i+$startRow),$items[$i]?$items[$i]->Manufacturer->Name:"");
                    $workSheet->setCellValue("C".($i+$startRow),$items[$i]?$items[$i]->Brand->Name:"");
                    $workSheet->setCellValue("D".($i+$startRow),$items[$i]->Name);
					for($j=0; $j<$props; $j++) {
						$workSheet->setCellValue("E".($i+$startRow+$j),$items[$i]->Properties[$j]->Name);
						$workSheet->setCellValue("F".($i+$startRow+$j),number_format($items[$i]->Properties[$j]->Price,2));
					}
					$workSheet->setCellValue("G".($i+$startRow),$items[$i]->IsPublished==1?'Yes':'No');
					$startRow = $startRow + $props - 1;
                }
            }

			$workSheet->getColumnDimension("A")->setWidth(10);
			$workSheet->getColumnDimension("B")->setWidth(10);
			$workSheet->getColumnDimension("C")->setWidth(25);
			$workSheet->getColumnDimension("D")->setWidth(50);
			$workSheet->getColumnDimension("E")->setWidth(50);
			$workSheet->getColumnDimension("F")->setWidth(10);
			$workSheet->getColumnDimension("G")->setWidth(10);

            $phpExcelWriter = PHPExcel_IOFactory::createWriter($workBook, 'Excel5');
            $fileName = "TOG_ProductList_On_".date("Y.m.d_h.i",time()).".xls";
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