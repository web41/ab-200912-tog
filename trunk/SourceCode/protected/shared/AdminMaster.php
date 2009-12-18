<?php

class AdminMaster extends TTemplateControl
{
	protected function getUrlPrefix()
	{
		return $this->Request->UrlManagerModule->UrlPrefix;
	}
}

?>