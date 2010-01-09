<?php

class PPanel extends TPanel
{
	public function render($writer)
	{
		$textWriter=new TTextWriter;
		parent::render(new THtmlWriter($textWriter));
		$text = $textWriter->flush();
		if ($this->SaveHtml)
		{
			$this->Session["HtmlContent"] = htmlentities($text, ENT_QUOTES, "UTF-8");
			$writer->write($text);
		}
		else 
		{	
			$writer->write($text);
			if ($this->Session->contains("HtmlContent"))
				$this->Session->remove("HtmlContent");
		}
	}
	
	public function getSaveHtml()
	{
		return $this->getViewState("SaveHtml",false);
	}
	
	public function setSaveHtml($value)
	{
		$this->setViewState("SaveHtml",TPropertyValue::ensureBoolean($value),false);
	}
}

?>