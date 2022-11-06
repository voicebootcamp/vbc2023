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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;

HTMLHelper::_('behavior.core');
HTMLHelper::_('bootstrap.tooltip', '.hasTooltip', ['html' => true, 'sanitize' => false]);

$rootUri = Uri::root(true);

OSMembershipHelper::addLangLinkForAjax();
OSMembershipHelperJquery::validateForm();
Factory::getDocument()->addScriptDeclaration('var siteUrl = "' . Uri::root(true) . '/";')
	->addScriptOptions('selectedState', $selectedState)
	->addScriptOptions('maxErrorsPerField', (int) $this->config->max_errors_per_field)
	->addScript($rootUri . '/media/com_osmembership/assets/js/paymentmethods.min.js')
	->addScript($rootUri . '/media/com_osmembership/js/site-group-default.min.js');