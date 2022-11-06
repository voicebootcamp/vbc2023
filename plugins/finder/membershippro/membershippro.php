<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2022 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

if (version_compare(JVERSION, '4.0.0', 'ge'))
{
	require_once __DIR__ . '/membershippro.j4.php';
}
else
{
	require_once __DIR__ . '/membershippro.j3.php';
}
