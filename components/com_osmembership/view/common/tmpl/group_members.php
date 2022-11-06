<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2022 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

$names = [];

foreach ($rowMembers as $rowMember)
{
	$names[] = trim($rowMember->first_name . ' ' . $rowMember->last_name);
}

echo implode("\r\n", $names);
