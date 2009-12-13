<?php
Prado::using('System.Exception.TErrorHandler');
class TOGErrorHandler extends TErrorHandler
{
	protected function getErrorTemplate($status, $exception)
	{
		if ($exception instanceof TException)
		{
			$template=Prado::getPathOfNamespace('Application.template.Error', '.html');
			return file_get_contents($template);
		}
		//return parent::getErrorTemplate($status, $exception);
	}
}
?>