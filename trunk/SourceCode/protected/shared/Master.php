<?php

class Master extends TTemplateControl
{
	protected function getUrlPrefix()
	{
		return $this->Request->UrlManagerModule->UrlPrefix;
	}
}

?>