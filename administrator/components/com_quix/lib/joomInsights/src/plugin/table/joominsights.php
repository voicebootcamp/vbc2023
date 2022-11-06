<?php

defined('_JEXEC') or die();

class TableJoomInsights extends JTable
{
	public function __construct($db)
	{
		parent::__construct( '#__joominsights', 'id', $db );
	}
}