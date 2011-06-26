<?php

class MailingListManager extends TPage
{
	private $_maxPage = 1;
	private $_currentPage = 1;
	private $_sortBy = "";
	private $_sortType = "";
	private $_searchText = "";
	private $_sortable = array("mailing_id","mailing_address","mailing_name");
	private $_queryParams = array("p","st","sb","q");
	private $_mailType = 'mailing';
	const AR = "MailingListRecord";

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
	
	public function getMailType() {
		return $this->_mailType;
	}
	
	public function setMailType($value) {
		$this->_mailType = $value;
	}

	public function onLoad($param)
	{
		parent::onLoad($param);
		// register search button
		$this->ClientScript->registerDefaultButton($this->txtSearchText,$this->btnSearch);
		$this->CurrentPage = ($this->Request->contains('p')) ? TPropertyValue::ensureInteger($this->Request['p']) : 1;
		$this->SortBy = ($this->Request->contains('sb')) ? TPropertyValue::ensureInteger($this->Request['sb']) : 1;
		$this->SortType = ($this->Request->contains('st')) ? $this->Request['st'] : 'asc';
		$this->SearchText = ($this->Request->contains('q')) ? $this->Request['q'] : '';
		$this->MailType = ($this->Request->contains('mt')) ? $this->Request['mt'] : 'mailing';
		if (!$this->IsPostBack)
		{
			if ($this->SearchText) $this->txtSearchText->Text = $this->SearchText;
			$this->cboTypeSelector->SelectedValue = $this->MailType;
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
		if ($this->MailType=='mailing') {
			$this->ItemList->Visible = true; $this->ItemList2->Visible = false;
			$criteria = new TActiveRecordCriteria;
			$criteria->OrdersBy[$this->Sortable[$this->SortBy]] = $this->SortType;
			// addtional condition here
			// this part will be hard-code on each page
			$criteria->Condition = "mailing_id in (select distinct mailing_id from tbl_mailing_list where mailing_id > 0 ";
			if (strlen($this->SearchText)>0)
			{
				$searchArray = explode(" ",THttpUtility::htmlDecode($this->SearchText));
				$searchQuery = "";
				foreach($searchArray as $index=>$value)
				{
					$searchQuery .= ($index>0 ? " or " : "")." mailing_id like '%".addslashes($searchArray[$index])."%' or mailing_address like '%".addslashes($searchArray[$index])."%'";
				}
				$criteria->Condition .= " and (".$searchQuery.") ";
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
				$this->Notice->Text = $this->Application->getModule("message")->translate("ITEM_FOUND",0,"record");
			}
		}
		else if ($this->MailType=='user') {
			$this->ItemList->Visible = false; $this->ItemList2->Visible = true;
			$this->PPager1->ControlToPaginate = 'ItemList2';
			// re-define sortable
			$this->_sortable = array('user_id','user_email','first_name','last_name');
			$criteria = new TActiveRecordCriteria;
			$criteria->OrdersBy[$this->Sortable[$this->SortBy]] = $this->SortType;
			if ($this->SortBy==2) {
				$criteria->OrdersBy[$this->Sortable[3]] = $this->SortType;
			}
			// addtional condition here
			// this part will be hard-code on each page
			$criteria->Condition = "user_id in (select distinct user_id from tbl_user where user_id > 0 ";
			if (strlen($this->SearchText)>0)
			{
				$searchArray = explode(" ",THttpUtility::htmlDecode($this->SearchText));
				$searchQuery = "";
				foreach($searchArray as $index=>$value)
				{
					$searchQuery .= ($index>0 ? " or " : "")." user_id like '%".addslashes($searchArray[$index])."%' or user_email like '%".addslashes($searchArray[$index])."%'";
				}
				$criteria->Condition .= " and (".$searchQuery.") ";
			}
			// -- 
			$criteria->Condition .= ")";
			$this->ItemList2->VirtualItemCount = UserRecord::finder()->count($criteria);
			$this->MaxPage = ceil($this->ItemList2->VirtualItemCount/$this->ItemList2->PageSize);
			if ($this->CurrentPage > $this->MaxPage) $this->CurrentPage = $this->MaxPage;
			$limit = $this->ItemList2->PageSize;
			$offset = ($this->CurrentPage-1) * $limit;

			if ($offset + $limit > $this->ItemList2->VirtualItemCount)
				$limit = $this->ItemList2->VirtualItemCount - $offset;

			$criteria->Limit = $limit;
			$criteria->Offset = $offset;
			$items = UserRecord::finder()->findAll($criteria);
			$this->ItemList2->DataSource = $items;
			$this->ItemList2->dataBind();
			if (count($items) <= 0)
			{
				$this->Notice->Type = AdminNoticeType::Information;
				$this->Notice->Text = $this->Application->getModule("message")->translate("ITEM_FOUND",0,"record");
			}
		}
	}

	public function populateSortUrl($sortBy, $sortType, $search="", $mailType='mailing', $resetPage=true)
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
		if (strlen($mailType)>0) $params['mt'] = $mailType;
		else if (isset($params['mt'])) unset($params['mt']);
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
				$param->Item->colDeleteButton->Button->Attributes->onclick = 'if(!confirm("'.$this->Application->getModule("message")->translate("DELETE_CONFIRM","email",$param->Item->Data->Address).'")) return false;';
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
						$this->Notice->Text = $this->Application->getModule("message")->translate("DELETE_SUCCESS","Email",$activeRecord->Address);
					}
					catch(TException $e)
					{
						$this->Notice->Type = AdminNoticeType::Error;
						$this->Notice->Text = $this->Application->getModule("message")->translate("DELETE_FAILED","Email",$activeRecord->Address);
					}
				}
				else
				{
					$this->Notice->Type = AdminNoticeType::Error;
					$this->Notice->Text = $this->Application->getModule("message")->translate("ITEM_NOT_FOUND","email");
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
				$this->Notice->Text = $this->Application->getModule("message")->translate("DELETE_ALL_SUCCESS","email");
			}
			catch (TException $e)
			{
				$this->Notice->Type = AdminNoticeType::Error;
				$this->Notice->Text = $this->Application->getModule("message")->translate("DELETE_ALL_FAILED","email");
			}
			$this->populateData();
		}
	}

	protected function btnSearch_Clicked($sender, $param)
	{
		if ($this->IsValid)
		{
			$this->Response->redirect($this->populateSortUrl($this->SortBy,$this->SortType,THttpUtility::htmlEncode($this->txtSearchText->SafeText)));
		}
	}

	protected function btnSearchReset_Clicked($sender, $param)
	{
		$this->Response->redirect($this->populateSortUrl($this->SortBy,$this->SortType,''));
	}
	
	protected function btnExport_Clicked($sender, $param)
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
									->setTitle("The Organic Grocer Mailing List generated on ".date("m.D.Y",time()))
									->setSubject("The Organic Grocer Mailing List")
									->setDescription("The Organic Grocer Mailing List generated on ".date("m.D.Y",time()));
			$workBook->setActiveSheetIndex(0);
			$workSheet = $workBook->getActiveSheet();
			$workSheet->setCellValue("A1","#")->setCellValue("B1","ID")->setCellValue("C1","Name")->setCellValue("D1","Email Address");
			$workSheet->getStyle('A1')->getFont()->setBold(true);
			$workSheet->getStyle('B1')->getFont()->setBold(true);
			$workSheet->getStyle('C1')->getFont()->setBold(true);
			$workSheet->getStyle('D1')->getFont()->setBold(true);
			$mailingLists = MailingListRecord::finder()->findAll();
			for($i=0;$i<count($mailingLists);$i++)
			{
				$workBook->setActiveSheetIndex(0)->setCellValue("A".($i+2),$i+1)->setCellValue("B".($i+2),$mailingLists[$i]->ID)->setCellValue("C".($i+2),$mailingLists[$i]->Name)->setCellValue("D".($i+2),$mailingLists[$i]->Address);
			}
			$workBook->setActiveSheetIndex(0)->getColumnDimension("D")->setWidth(50);
			$phpExcelWriter = PHPExcel_IOFactory::createWriter($workBook, 'Excel5');
			//$filePath = dirname($this->Request->ApplicationFilePath).DIRECTORY_SEPARATOR."useruploads".DIRECTORY_SEPARATOR."docs".DIRECTORY_SEPARATOR;
			//$fileName = md5(uniqid(time())).".xls";
			$fileName = "Mailing_List_Generated_On_".date("Y.m.d_h.i.s",time()).".xls";
			//$phpExcelWriter->save($filePath.$fileName);
			//$this->Response->writeFile($filePath.$fileName,null,"application/vnd.ms-excel",array("Content-Type: application/vnd.ms-excel","Content-Disposition: attachment;filename='Mailing List Generated On ".date("m.d.Y.h.i",time()).".xls'","Cache-Control: max-age=0"));
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
		$this->populateData();
	}
	
	protected function btnGetEmail_Clicked($sender, $param)
	{
		try
		{
			$filecontent="TOG Email List: ";
			$mailingLists = MailingListRecord::finder()->findAll();
			for($i=0;$i<count($mailingLists);$i++)
			{
				$filecontent .= $mailingLists[$i]->Address . ", ";
			}
			
			$filecontent = substr($filecontent, 0, strlen($filecontent) - 2);
			
			$fileName = "TOG_Email_List_".date("d-m-Y",time()).".txt";
			$this->Response->appendHeader("Content-Type:plain/text");
			$this->Response->appendHeader("Content-Disposition:attachment;filename=$fileName");
			$this->Response->appendHeader("Cache-Control:max-age=0");
			$this->Response->appendHeader("Content-Transfer-Encoding: binary");			
			echo"$filecontent"; 
			exit();
		}
		catch(Exception $ex)
		{
			$this->Notice->Type = AdminNoticeType::Error;
			$this->Notice->Text = $this->Application->getModule("message")->translate("UNKNOWN_ERROR");
		}
		$this->populateData();
	}
	
	protected function cboTypeSelector_SelectedIndexChanged($sender, $param) {
		$this->Response->redirect($this->populateSortUrl($this->SortBy,$this->SortType,'',$sender->SelectedValue));
	}
}

?>