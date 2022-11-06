<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2022 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die;

use Joomla\CMS\Editor\Editor;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarHelper;

ToolbarHelper::title(Text::_('OSM_EMAIL_MESSAGES'), 'generic.png');
ToolbarHelper::apply();
ToolbarHelper::save('save');
ToolbarHelper::cancel('cancel');

$config       = OSMembershipHelper::getConfig();
$editor       = Editor::getInstance($config->get('editor') ?: Factory::getApplication()->get('editor'));
$translatable = Multilanguage::isEnabled() && count($this->languages);

HTMLHelper::_('behavior.core');
HTMLHelper::_('bootstrap.tooltip', '.hasTooltip', ['html' => true, 'sanitize' => false]);

if (OSMembershipHelper::isJoomla4())
{
	$tabApiPrefix = 'uitab.';
}
else
{
	$tabApiPrefix = 'bootstrap.';
	HTMLHelper::_('behavior.tabstate');
}
?>
<form action="index.php?option=com_osmembership&view=message" method="post" name="adminForm" id="adminForm" class="form form-horizontal<?php if (!OSMembershipHelper::isJoomla4()) echo ' joomla3'; ?> osm-messages-form">
	<?php
	echo HTMLHelper::_($tabApiPrefix . 'startTabSet', 'message', array('active' => 'general-page'));

	echo HTMLHelper::_($tabApiPrefix . 'addTab', 'message', 'general-page', Text::_('OSM_GENERAL_MESSAGES', true));
	echo $this->loadTemplate('general', array('editor' => $editor));
	echo HTMLHelper::_($tabApiPrefix . 'endTab');

	echo HTMLHelper::_($tabApiPrefix . 'addTab', 'message', 'renewal-page', Text::_('OSM_RENEWAL_MESSAGES', true));
	echo $this->loadTemplate('renewal', array('editor' => $editor));
	echo HTMLHelper::_($tabApiPrefix . 'endTab');

	echo HTMLHelper::_($tabApiPrefix . 'addTab', 'message', 'upgrade-page', Text::_('OSM_UPGRADE_MESSAGES', true));
	echo $this->loadTemplate('upgrade', array('editor' => $editor));
	echo HTMLHelper::_($tabApiPrefix . 'endTab');

	echo HTMLHelper::_($tabApiPrefix . 'addTab', 'message', 'recurring-page', Text::_('OSM_RECURRING_MESSAGES', true));
	echo $this->loadTemplate('recurring', array('editor' => $editor));
	echo HTMLHelper::_($tabApiPrefix . 'endTab');

	echo HTMLHelper::_($tabApiPrefix . 'addTab', 'message', 'reminder-page', Text::_('OSM_REMINDER_MESSAGES', true));
	echo $this->loadTemplate('reminder', array('editor' => $editor));
	echo HTMLHelper::_($tabApiPrefix . 'endTab');

	echo HTMLHelper::_($tabApiPrefix . 'addTab', 'message', 'group-membership-page', Text::_('OSM_GROUP_MEMBERSHIP_MESSAGES', true));
	echo $this->loadTemplate('group_membership', array('editor' => $editor));
	echo HTMLHelper::_($tabApiPrefix . 'endTab');

	echo HTMLHelper::_($tabApiPrefix . 'addTab', 'message', 'subscription-payment-page', Text::_('OSM_SUBSCRIPTION_PAYMENT_MESSAGES', true));
	echo $this->loadTemplate('subscription_payment', array('editor' => $editor));
	echo HTMLHelper::_($tabApiPrefix . 'endTab');

	echo HTMLHelper::_($tabApiPrefix . 'addTab', 'message', 'sms-page', Text::_('OSM_SMS_MESSAGES', true));
	echo $this->loadTemplate('sms', array('editor' => $editor));
	echo HTMLHelper::_($tabApiPrefix . 'endTab');

	// Add support for custom messages layout
	if (file_exists(__DIR__ . '/default_custom_messages.php'))
	{
		echo HTMLHelper::_($tabApiPrefix . 'addTab', 'message', 'custom-messages-page', Text::_('OSM_CUSTOM_MESSAGES', true));
		echo $this->loadTemplate('custom_messages', array('config' => $config, 'editor' => $editor));
		echo HTMLHelper::_($tabApiPrefix . 'endTab');
	}

	if ($translatable)
	{
		echo HTMLHelper::_($tabApiPrefix . 'addTab', 'message', 'translation-page', Text::_('OSM_TRANSLATION', true));
		echo $this->loadTemplate('translation', array('editor' => $editor));
		echo HTMLHelper::_($tabApiPrefix . 'endTab');
	}

	echo HTMLHelper::_($tabApiPrefix . 'endTabSet');
?>
	<input type="hidden" name="task" value="" />	
</form>