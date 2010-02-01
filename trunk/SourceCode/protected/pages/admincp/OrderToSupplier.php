<?php

Prado::using("Application.common.common");
class OrderToSupplier extends TPage
{
	public function onLoad($param)
	{
		parent::onLoad($param);
		if (!$this->IsPostBack)
		{
			$this->cboMfSelector->DataSource = ManufacturerRecord::finder()->getAllItems();
			$this->cboMfSelector->DataBind();
			$this->dpFromDate->Data = mktime(0,0,0,date("n"),date("j"),date("Y"));
			$this->dpToDate->Data = mktime(23,59,59,date("n"),date("j"),date("Y"));
		}
	}
	
	public function generateOrders()
	{
		$criteria = new TActiveRecordCriteria;
		$criteria->OrdersBy['c_date'] = 'desc';
		// addtional condition here
		// this part will be hard-code on each page
		$criteria->Condition = "order_id in (select distinct o.order_id from tbl_order o 
								left join tbl_order_item oi on o.order_id = oi.order_id
								left join tbl_product p on oi.product_id = p.product_id";
		
		$criteria->Condition .= " where o.order_id > 0 ";
		if ($this->dpFromDate->Data>0)
		{
			$criteria->Condition .= " and (o.c_date >= '".$this->dpFromDate->Data."') ";
		}
		if ($this->dpToDate->Data>0)
		{
			$criteria->Condition .= " and (o.c_date <= '".$this->dpToDate->Data."') ";
		}
		// -- 
		$criteria->Condition .= ")";
		return OrderRecord::finder()->withOrderItems()->withUser()->findAll($criteria);
	}
	
	public function generateOrderItemsByPublisher()
	{
		$criteria = new TActiveRecordCriteria;
		$criteria->OrdersBy['c_date'] = 'desc';
		// addtional condition here
		// this part will be hard-code on each page
		$criteria->Condition = "item_id in (select oi.item_id from tbl_order_item oi 
											left join tbl_order o on oi.order_id = o.order_id
											left join tbl_product p on oi.product_id = p.product_id
											where oi.order_id > 0 ";

		if ($this->dpFromDate->Data>0)
		{
			$criteria->Condition .= " and (o.c_date >= '".$this->dpFromDate->Data."') ";
		}
		if ($this->dpToDate->Data>0)
		{
			$criteria->Condition .= " and (o.c_date <= '".$this->dpToDate->Data."') ";
		}
		if (TPropertyValue::ensureInteger($this->cboMfSelector->SelectedValue) > 0)
		{
			$criteria->Condition .= " and (p.mf_id = '".TPropertyValue::ensureInteger($this->cboMfSelector->SelectedValue)."')";
		}
		// -- 
		$criteria->Condition .= ")";
		return OrderItemRecord::finder()->withOrder()->findAll($criteria);
	}
	
	protected function btnSubmit_Clicked($sender, $param)
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
			switch($this->radViewBy->SelectedValue)
			{
				default:
				case 0:
					$orders = $this->generateOrders();
					$totalOrders = count($orders);
					if ($totalOrders>0)
					{
						$startRow = 4;
						for($i=0;$i<count($orders);$i++)
						{
							$workSheet->setCellValue("A".($i+$startRow),date("d/m/Y",$orders[$i]->CreateDate))->getStyle("A".($i+$startRow))->getFont()->setBold(true);
							$workSheet->setCellValue("B".($i+$startRow),$orders[$i]->Num)->getStyle("B".($i+$startRow))->getFont()->setBold(true);
							$workSheet->setCellValue("C".($i+$startRow),$orders[$i]->User->FirstName." ".$orders[$i]->User->LastName)->getStyle("C".($i+$startRow))->getFont()->setBold(true);
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
					break;
				case 1:
					$supplier = null;
					if (TPropertyValue::ensureInteger($this->cboMfSelector->SelectedValue)>0)
						$supplier = ManufacturerRecord::finder()->findByPk(TPropertyValue::ensureInteger($this->cboMfSelector->SelectedValue));
					$orderItems = $this->generateOrderItemsByPublisher();
					if (count($orderItems) > 0)
					{
						$workSheet->setCellValue("A3","Supplier");
						$workSheet->setCellValue("B3",($supplier instanceof ManufacturerRecord ? $supplier->Name : "All suppliers"));
	
						$workSheet->setCellValue("A4","#")->getStyle("A4")->getFont()->setBold(true);
						$workSheet->setCellValue("B4","Item Description")->getStyle("B4")->getFont()->setBold(true);
						$workSheet->setCellValue("C4","Brand")->getStyle("C4")->getFont()->setBold(true);
						$workSheet->setCellValue("D4","Quantity")->getStyle("D4")->getFont()->setBold(true);
						if ($supplier == null) $workSheet->setCellValue("E4","Supplier")->getStyle("E4")->getFont()->setBold(true);
						
						$startRow = 5;
						for($i=0;$i<count($orderItems);$i++)
						{	
							$orderItems[$i]->Counter++;
							$orderItems[$i]->save();
							$product = ProductRecord::finder()->withManufacturer()->withBrand()->findByPk($orderItems[$i]->ProductID);
							$prop = PropertyRecord::finder()->findByPk($orderItems[$i]->PropertyID);
							$workSheet->setCellValue("A".($i+$startRow),$i+1);
							$workSheet->setCellValue("B".($i+$startRow),$product->Name." - ".($prop instanceof PropertyRecord ? $prop->Name : ""));
							$workSheet->setCellValue("C".($i+$startRow),$product->Brand->Name);
							$workSheet->setCellValue("D".($i+$startRow),$orderItems[$i]->Quantity);
							$workSheet->setCellValue("E".($i+$startRow),Common::showOrdinal($orderItems[$i]->Counter));
							if ($supplier == null)
							{
								$workSheet->setCellValue("F".($i+$startRow),$product->Manufacturer->Name);
							}
							
						}
	
						$workSheet->getColumnDimension("A")->setWidth(20);
						$workSheet->getColumnDimension("B")->setWidth(80);
						$workSheet->getColumnDimension("C")->setWidth(30);
					}
					break;
			}
			$phpExcelWriter = PHPExcel_IOFactory::createWriter($workBook, 'Excel5');
			$fileName = "Export Generated On ".date("Y.m.d_h.i.s",time()).".xls";
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