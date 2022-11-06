<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2022 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;

// Require library + register autoloader
require_once JPATH_ADMINISTRATOR . '/components/com_osmembership/loader.php';

Factory::getLanguage()->load('com_osmembershipcommon', JPATH_ADMINISTRATOR);
Factory::getLanguage()->load('com_osmembership', JPATH_ROOT, null, true);

if (version_compare(JVERSION, '4.0.0-dev', 'ge'))
{
	$source = Factory::getApplication()->input;
}
else
{
	$source = null;
}

// Prepare request data
OSMembershipHelper::prepareRequestData();
$input = new MPFInput($source);
$task  = $input->getCmd('task', '');

// Handle BC for existing payment plugins
if (in_array($task, ['payment_confirm', 'recurring_payment_confirm']))
{
	$input->set('task', 'register.' . $task);
}

$config = include JPATH_ADMINISTRATOR . '/components/com_osmembership/config.php';

MPFController::getInstance($input->getCmd('option', null), $input, $config)
	->execute()
	->redirect();
