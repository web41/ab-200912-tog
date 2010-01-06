<?php

class PMenuList extends TBulletedList 
{
	private $_manager;
	public function getManager () 
	{
		if ($this->_manager===null)
		{
			foreach ($this->getApplication()->getModules() as $module)
			{
				if ($module instanceof PMenuManager) $this->_manager=$module;
			}
			if ($this->_manager===null) 
			throw new TConfigurationException("menu_manager_module_not_found");
		}
		return $this->_manager;
	}
	
	// always link
	public function setDisplayMode($value)
	{
		$this->setViewState('DisplayMode',TBulletedListDisplayMode::HyperLink);
	}
	
	public function getDisplayMode()
	{
		return $this->getViewState('DisplayMode',TBulletedListDisplayMode::HyperLink);
	}
	
	public function getBulletStyle()
	{
		return $this->getViewState('BulletStyle',TBulletStyle::NotSet);
	}

	public function setBulletStyle($value)
	{
		$this->setViewState('BulletStyle',TBulletStyle::NotSet);
	}
	/**
	 * @var boolean cached property value of Enabled
	 */
	private $_isEnabled;
	/**
	 * @var TPostBackOptions postback options
	 */
	private $_postBackOptions;
	
	public function createChildControls()
	{
		foreach($this->Manager->Nodes as $node)
		{
			if ($node->HasChildren)
			{
				$item = new TListItem;
				$item->Text = $node->Title;
				$item->Value = $this->Service->ConstructUrl($node->ServiceParameter,$node->Parameters);
				$this->getItems()->add($item);
			}
		}
	}
	
	public function renderContents($writer)
	{
		$this->_isEnabled=$this->getEnabled(true);
		$this->_postBackOptions=$this->getPostBackOptions();
		$writer->writeLine();
		foreach($this->Manager->Nodes as $index=>$node)
			if ($node->HasChildren)
				$this->renderNode($writer,$node,$index);
	}
	
	protected function renderNode($writer,$node,$index)
	{
		$item = new TListItem;
		$item->Text = $node->Title;
		if ($node->ServiceParameter) $item->Value = $this->Service->ConstructUrl($node->ServiceParameter,$node->Parameters);
		else $item->Value = "#";
		if($item->getHasAttributes())
			$writer->addAttributes($item->getAttributes());
		$writer->renderBeginTag('li');
		$this->renderBulletText($writer,$item,$index);
		if ($node->HasChildren)
		{
			$writer->renderBeginTag('ul');
			foreach($node->Children as $idx=>$child)
				$this->renderNode($writer,$child,$idx);
			$writer->renderEndTag();
			$writer->writeLine();
		}
		$writer->renderEndTag();
		$writer->writeLine();
	}
	
	protected function renderHyperLinkItem($writer, $item, $index)
	{
		if(!$this->_isEnabled || !$item->getEnabled())
			$writer->addAttribute('disabled','disabled');
		else
		{
			$writer->addAttribute('href',$item->getValue());
			if(($target=$this->getTarget())!=='')
				$writer->addAttribute('target',$target);
		}
		if(($accesskey=$this->getAccessKey())!=='')
			$writer->addAttribute('accesskey',$accesskey);
		$writer->renderBeginTag('a');
		$writer->write(THttpUtility::htmlEncode($item->getText()));
		$writer->renderEndTag();
	}
	
	public function render($writer)
	{
		$this->renderBeginTag($writer);
		$this->renderContents($writer);
		$this->renderEndTag($writer);
	}
}

?>