<?php

class UserNotice extends TTemplateControl
{
	public function getCssClass()
	{
		switch($this->Type)
		{
			case UserNoticeType::Error:
				return "error";
			case UserNoticeType::Notice:
				return "notice";
			default:
				return "error";
		}
	}

	public function setType($value)
	{
		$this->setViewState('UserNoticeType',TPropertyValue::ensureEnum($value,'UserNoticeType'),UserNoticeType::Error);
	}

	public function getType()
	{
		return $this->getViewState('UserNoticeType',UserNoticeType::Error);
	}

	public function setText($value)
	{
		$this->setViewState('Text',$value,'');
	}

	public function getText()
	{
		return $this->getViewState('Text','');
	}
}

class UserNoticeType extends TEnumerable
{
	const Error = "Error";
	const Notice = "Notice";
}

?>