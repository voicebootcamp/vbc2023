<?php
defined('_JEXEC') or die('Restricted access');

class JFormFieldApi extends JFormField
{
	protected $type = 'api';

	public function getInput()
	{
		return '<button class="btn" onclick="window.open(\'https://www.paypal.com/cgi-bin/webscr?cmd=_get-api-signature&generic-flow=true\', \'\', \'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=400, height=540\');">GET API Access</button>';
	}
}
