<?php

class Master extends TTemplateControl
{
	public $DEFAULT_SEARCH_TEXT = "Search by Product (e.g. apple juice)";
	private $_queryParams = array("p","st","sb","b","mf","q","id","alias","subid","subalias");
	public function getQueryParameters()
	{
		return $this->_queryParams;
	}
	public function onInit($param)
	{
		parent::onInit($param);
		if (!$this->Page->IsPostBack)
		{
			$this->cboBrandSelector->DataSource = BrandRecord::finder()->getAllItems(true);
			$this->cboBrandSelector->DataBind();
		}
		$this->cboBrandSelector->SelectedValue = $this->Request->contains("b")?TPropertyValue::ensureInteger($this->Request["b"]):0;
	}
	protected function getUrlPrefix()
	{
		return $this->Request->UrlManagerModule->UrlPrefix;
	}
	
	protected function btnSearch_Clicked($sender, $param)
	{
		$this->Response->redirect($this->populateSortUrl(1,"asc",THttpUtility::htmlEncode($this->txtSearchText->SafeText),$this->cboBrandSelector->SelectedValue,$this->Request["mf"],$this->Request["id"],$this->Request["subid"]));
	}
	
	public function populateSortUrl($sortBy, $sortType, $search="", $brand=0, $mf=0, $cat=0, $subcat=0, $resetPage=true)
	{
		$params = $this->Request->toArray();
		foreach($params as $key=>$value)
		{
			if (!in_array($key,$this->QueryParameters))
				unset($params[$key]);
		}
		if (!isset($params["c"])) unset($params["calias"]);
		if (!isset($params["subc"])) unset($params["subcalias"]);
		if (!isset($params["b"])) unset($params["balias"]);
		//$serviceParameter = $this->Request->ServiceParameter;
		if (strlen($search)>0 && $search != $this->DEFAULT_SEARCH_TEXT) $params['q'] = $search;
		else if (isset($params['q'])) unset($params['q']);
		if ($brand>0) $params['b'] = $brand;
		else if (isset($params['b'])) unset($params['b']);
		if ($mf>0) $params['mf'] = $mf;
		else if (isset($params['mf'])) unset($params['mf']);
		if ($cat>0) $params['id'] = $cat;
		else if (isset($params['id'])) unset($params['id']);
		if ($subcat>0) $params['subid'] = $cat;
		else if (isset($params['subid'])) unset($params['subid']);
		if ($resetPage)	$params['p'] = 1;
		$params['sb'] = $sortBy;
		$params['st'] = $sortType;

		return $this->Service->ConstructUrl("shop.Index",$params);
	}
}

?>