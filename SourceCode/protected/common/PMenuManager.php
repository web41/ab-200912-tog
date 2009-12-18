<?php

class PMenuManager extends TModule
{
	const MENU_FILE_EXT=".xml";
	private $_menuFile=null;
	private $_nodes=array ();
	private $_defaultNodeClass='PMenuNode';
	
	public function init ($config)
	{
		parent::init($config);
		if ($this->_menuFile!==null) 
			$this->loadMenuFile();
		$this->loadNodes($config);
	}
	
	public function setMenuFile ($value)
	{
		if(($this->_menuFile=Prado::getPathOfNamespace($value,self::MENU_FILE_EXT))===null)
			throw new TConfigurationException('menu file {0} is invalid.',$value);
	}
	
	public function getMenuFile ()
	{
		return $this->_sitemapFile;
	}
	
	protected function loadMenuFile () 
	{
		if(is_file($this->_menuFile))
		{
			$dom=new TXmlDocument;
			$dom->loadFromFile($this->_menuFile);
			$this->loadNodes($dom);
		}
		else
			throw new TConfigurationException(
				'menu file {0} is invalid.',$this->_menuFile); 
	}
	
	protected function loadNodes($xml, $parent=null)
	{
		foreach($xml->getElementsByTagName('menuNode') as $menuNode)
		{
			$properties=$menuNode->getAttributes();
			$class=$properties->remove('class');
			if($class===null)
			{
				if ($parent !== null)
					$class = get_class($parent);
				else
					$class = $this->_defaultNodeClass;
			}
			$node = Prado::createComponent($class, $parent);
			if(!($node instanceof PMenuNode))
				throw new TConfigurationException('urlpath_dispatch_invalid_pattern_class');
			foreach($properties as $name=>$value)
				$node->setSubproperty($name,$value);
			$this->_nodes[] = $node;
			// Load childs nodes
			$this->loadNodes($menuNode, $node);

		}
	}

	public function getNodes () 
	{
		return $this->_nodes;
	}
}

class PMenuNode extends TComponent
{
	private $_id;
	private $_title;
	private $_serviceParameter;
	private $_parameters;
	private $_parent;
	private $_caseSensitive=true;
	private $_children=array();
	
	public function __construct($parent=null)
	{
		$this->_parent=$parent;
		if ($parent !== null && $parent instanceof PMenuNode) $parent->addChild($this);
		$this->_parameters=prado::createComponent('System.Collections.TAttributeCollection');
	}
	
	public function getParent() 
	{
		return $this->_parent;
	}
	
	public function getChildren() 
	{
		return $this->_children;
	}
	
	public function addChild ($child) 
	{
		if ($child instanceof PMenuNode) 
			$this->_children[]=$child;
		else
			throw new TInvalidDataValueException ("Child not a PMenuNode");
	}
	
	public function getHasChildren() 
	{
		return (count($this->_children) > 0);
	}
	
	public function setID ($value)
	{
		$this->_id=$value;
	}
	
	public function getID ()
	{
		return $this->_id;
	}
	
	public function getCaseSensitive()
	{
		return $this->_caseSensitive;
	}
	
	public function setCaseSensitive($value)
	{
		$this->_caseSensitive=TPropertyValue::ensureBoolean($value);
	}
	
	public function setTitle ($value)
	{
		$this->_title=$value;
	}
	
	public function getTitle ()
	{
		return $this->_title;
	}
	
	public function setServiceParameter ($value)
	{
		$this->_serviceParameter=$value;
	}

	public function getServiceParameter()
	{
		return $this->_serviceParameter;
	}
	
	public function setParameters ($value)
	{
		$this->_parameters=TPropertyValue::ensureArray($value);
	}

	public function getParameters()
	{
		return $this->_parameters;
	}	
}

?>