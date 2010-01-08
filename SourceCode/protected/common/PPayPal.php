<?php

class PPayPal extends TControl
{
	private $_paypal_info=array();
	private $_scriptBlocks=array();
	public function onInit($param)
	{
		parent::onInit($param);
		$this->_paypal_info = TPropertyValue::ensureArray($this->Application->Parameters["PAYPAL_INFO"]);
	}
	
	public function getPayPalInfo()
	{
		return $this->_paypal_info;
	}
	
	public function getBusinessName()
	{
		return $this->PayPalInfo['business'];
	}
	
	public function getCurrency()
	{
		return $this->PayPalInfo['currency'];
	}
	
	public function getAction()
	{
		return $this->getViewState("Action","");
	}
	
	public function setAction($value)
	{
		$this->setViewState("Action",$value,"");
	}
	
	public function getTitle()
	{
		return $this->getViewState("Title","");
	}
	
	public function setTitle($value)
	{
		$this->setViewState("Title",$value,"");
	}
	
	public function getAmount()
	{
		return $this->getViewState("Amount",0);
	}
	
	public function setAmount($value)
	{
		$this->setViewState("Amount",TPropertyValue::ensureFloat($value),0);
	}
	
	public function getReturnUrl()
	{
		return $this->getViewState("ReturnUrl","");
	}
	
	public function setReturnUrl($value)
	{
		$this->setViewState("ReturnUrl",$value,"");
	}
	
	public function getCancelUrl()
	{
		return $this->getViewState("CancelUrl","");
	}
	
	public function setCancelUrl($value)
	{
		$this->setViewState("CancelUrl",$value,"");
	}
	
	public function getTimeOut()
	{
		return $this->getViewState("TimeOut",0);
	}
	
	public function setTimeOut($value)
	{
		$this->setViewState("TimeOut",TPropertyValue::ensureInteger($value),0);
	}
	
	protected function renderHiddenFieldScript($form,$name,$value)
	{
		$this->_scriptBlocks[] = "__{$name} = document.createElement('input'); __{$name}.type = 'hidden'; __{$name}.name = '{$name}'; __{$name}.value = '{$value}'; {$form}.appendChild(__$name);";
	}
	
	protected function renderFormScript($id,$method,$action)
	{
		$this->_scriptBlocks[] = "{$id} = document.createElement('form'); {$id}.id = '{$id}'; {$id}.method = 'POST'; {$id}.action = '".str_replace('&','&amp;',str_replace('&amp;','&',$action))."';";
	}
	
	public function render($writer)
	{
		$this->renderFormScript($this->ClientID,"POST",$this->Action);
		$this->renderHiddenFieldScript($this->ClientID,'business',$this->BusinessName);
		$this->renderHiddenFieldScript($this->ClientID,'cmd','_xclick');
		$this->renderHiddenFieldScript($this->ClientID,'item_name',$this->Title);
		$this->renderHiddenFieldScript($this->ClientID,'amount',$this->Amount);
		$this->renderHiddenFieldScript($this->ClientID,'currency_code',$this->Currency);
		$this->renderHiddenFieldScript($this->ClientID,'return',$this->ReturnUrl);
		$this->renderHiddenFieldScript($this->ClientID,'cancel_return',$this->CancelUrl);
		$writer->write(TJavascript::renderScriptBlocks($this->_scriptBlocks));
		
		$this->Page->ClientScript->registerEndScript("append","document.observe('dom:loaded',function(){document.body.appendChild({$this->ClientID})});");
		$this->Page->ClientScript->registerEndScript("submit","setTimeout('{$this->ClientID}.submit()',{$this->Timeout});");
	}
}

?>