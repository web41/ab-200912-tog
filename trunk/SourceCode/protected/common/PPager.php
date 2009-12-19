<?php

class PPager extends TWebControl
{
	private $_pageID = 'p';
	private $_pageCount = 1;
	private $_pageButtonCount = 5;
	private $_currentPage = 1;
	private $_controlToPaginate = '';
	
	public function getControlToPaginate()
	{
		return $this->_controlToPaginate;
	}
	
	public function setControlToPaginate($value)
	{
		$this->_controlToPaginate = $value;
	}
	
	public function getPageButtonCount()
	{
		return $this->_pageButtonCount;
	}

	public function setPageButtonCount($value)
	{
		if(($value=TPropertyValue::ensureInteger($value))<1)
			throw new TInvalidDataValueException('pager_pagebuttoncount_invalid');
		$this->_pageButtonCount = $value;
	}

	public function getCurrentPage()
	{
		return $this->_currentPage;
	}

	protected function setCurrentPage($value)
	{
		if(($value=TPropertyValue::ensureInteger($value))<0)
			throw new TInvalidDataValueException('pager_currentpageindex_invalid');
		$this->_currentPage = $value;
	}

	public function getPageCount()
	{
		return $this->_pageCount;
	}

	protected function setPageCount($value)
	{
		if(($value=TPropertyValue::ensureInteger($value))<0)
			throw new TInvalidDataValueException('pager_pagecount_invalid');
		$this->_pageCount = $value;
	}
	
	public function getPageID()
	{
		return $this->_pageID;
	}
	
	public function setPageID($value)
	{
		$this->_pageID = $value;			
	}

	public function getIsFirstPage()
	{
		return $this->getCurrentPage()===1;
	}

	public function getIsLastPage()
	{
		return $this->getCurrentPage()===$this->getPageCount();
	}
	
	public function onPreRender($param)
	{
		parent::onPreRender($param);

		$controlID=$this->getControlToPaginate();
		if(($targetControl=$this->getNamingContainer()->findControl($controlID))===null || !($targetControl instanceof TDataBoundControl))
			throw new TConfigurationException('pager_controltopaginate_invalid',$controlID);

		if($targetControl->getAllowPaging())
		{
			$this->getControls()->clear();
			$itemCount = ($targetControl->VirtualItemCount > 0) ? $targetControl->VirtualItemCount : count($targetControl->DataSource);
			$this->setPageCount(ceil($itemCount/$targetControl->PageSize));
			$currentPage = $this->Request->Contains($this->PageID) ? (TPropertyValue::ensureInteger($this->Request[$this->PageID])>0 ? TPropertyValue::ensureInteger($this->Request[$this->PageID]) : 1) : 1;
			$this->setCurrentPage($currentPage);
			$this->buildPager();
		}
		else
			$this->setPageCount(1);
	}
	
	public function render($writer)
	{
		parent::render($writer);
	}
	
	protected function createPagerLink($enabled,$text,$page,$class='')
	{
		$params = $this->Request->toArray();
		$serviceParameter = '';
		if (isset($params[$this->Request->ServiceID]))
		{
			$serviceParameter = $params[$this->Request->ServiceID];
			unset($params[$this->Request->ServiceID]);
		}
		if (isset($params[$this->PageID]))
			unset($params[$this->PageID]);
		$params[$this->PageID] = $page;
		$link = new THyperLink;
		$link->Enabled = $enabled;
		$link->Text = $text;
		$link->NavigateUrl = $this->Service->ConstructUrl($serviceParameter,$params);
		if ($class!='') $link->CssClass = $class;
		return $link;
	}
	
	protected function buildPager()
	{
		$this->buildLinkPager();
	}
	
	protected function buildLinkPager()
	{
		$controls = $this->getControls();
		$maxPage=$this->getPageCount();
		$currentPage=$this->getCurrentPage();
		$pageButtonCount=$this->getPageButtonCount();
		
		// create 'first' link
		$controls->add($this->createPagerLink(!$this->IsFirstPage,"First",1,"text"));
		// create 'prev' link
		$controls->add($this->createPagerLink(!$this->IsFirstPage,"Prev",$currentPage-1,"text"));
		// create numeric links
		$halfPageButtonCount = TPropertyValue::ensureInteger($pageButtonCount/2);
		$page=(($currentPage+$halfPageButtonCount <= $maxPage) ? (($currentPage > $halfPageButtonCount) ? $currentPage-$halfPageButtonCount : 1) : (($maxPage > $pageButtonCount) ? $maxPage-($pageButtonCount-1) : 1));
		for($i=0; $i<$pageButtonCount; $i++)
		{
			$controls->add($this->createPagerLink($page!==$currentPage,$page,$page));
			$page++;
		}
		// create 'next' link
		$controls->add($this->createPagerLink(!$this->IsLastPage,"Next",$currentPage+1,"text"));
		// create 'last' link
		$controls->add($this->createPagerLink(!$this->IsLastPage,"Last",$maxPage,"text"));
	}
}

?>