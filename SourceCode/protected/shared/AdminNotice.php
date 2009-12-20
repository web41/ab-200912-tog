<?php

class AdminNotice extends TTemplateControl
{
	public function onPreRender($param)
	{
		parent::onPreRender($param);
		if (strlen($this->Text)<=0) $this->setVisible(false);
	}
	public function getVisible()
	{
		return $this->getViewState('Visible',true);
	}
	
	public function setVisible($value)
	{
		$this->setViewState('Visible',TPropertyValue::ensureBoolean($value),true);
	}
	
	public function getIconImage()
	{
		switch($this->Type)
		{
			case AdminNoticeType::Error:
				return "icon_error.png";
			case AdminNoticeType::Success:
				return "icon_success.png";
			case AdminNoticeType::Information:
				return "icon_inform.png";
			case AdminNoticeType::Notice:
				return "icon_attention.png";
			default:
				return "icon_error.png";
		}
	}
	
	public function getCssClass()
	{
		switch($this->Type)
		{
			case AdminNoticeType::Error:
				return "general_error";
			case AdminNoticeType::Success:
				return "general_success";
			case AdminNoticeType::Information:
				return "general_inform";
			case AdminNoticeType::Notice:
				return "general_attention";
			default:
				return "general_error";
		}
	}
	
	public function setType($value)
	{
		$this->setViewState('AdminNoticeType',TPropertyValue::ensureEnum($value,'AdminNoticeType'),AdminNoticeType::Error);
	}
	
	public function getType()
	{
		return $this->getViewState('AdminNoticeType',AdminNoticeType::Error);
	}
	
	public function getText()
	{
		return $this->getViewState('Text','');
	}
	
	public function setText($value)
	{
		$this->setViewState('Text',$value,'');
	}
}

class AdminNoticeType extends TEnumerable
{
	const Error = "Error";
	const Success = "Success";
	const Information = "Information";
	const Notice = "Notice";
}

?>