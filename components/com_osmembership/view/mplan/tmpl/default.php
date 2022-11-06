<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2022 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die ;

use Joomla\CMS\Editor\Editor;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

HTMLHelper::_('bootstrap.tooltip', '.hasTooltip', ['html' => true, 'sanitize' => false]);

$config          = OSMembershipHelper::getConfig();
$editor          = Editor::getInstance($config->get('editor') ?: Factory::getApplication()->get('editor'));
$translatable    = Multilanguage::isEnabled() && count($this->languages);
$bootstrapHelper = OSMembershipHelperBootstrap::getInstance();
$rowFluid        = $bootstrapHelper->getClassMapping('row-fluid');
$span8           = $bootstrapHelper->getClassMapping('span7');
$span4           = $bootstrapHelper->getClassMapping('span5');

HTMLHelper::_('formbehavior.chosen', '#basic-information-page select, .advSelect');

if (OSMembershipHelper::isJoomla4())
{
	$tabApiPrefix = 'uitab.';

}
else
{
	$tabApiPrefix = 'bootstrap.';
	HTMLHelper::_('behavior.tabstate');
}

if (version_compare(JVERSION, '4.0.0-dev', '>='))
{
    Factory::getDocument()->getWebAssetManager()->useScript('showon');
}
else
{
	HTMLHelper::_('script', 'jui/cms.js', array('version' => 'auto', 'relative' => true));
}

Factory::getDocument()->addScript(Uri::root(true) . '/media/com_osmembership/js/site-mplan-default.min.js');

$keys = ['OSM_ENTER_PLAN_TITLE', 'OSM_ENTER_SUBSCRIPTION_LENGTH', 'OSM_PRICE_REQUIRED', 'OSM_INVALID_SUBSCRIPTION_LENGTH'];
OSMembershipHelperHtml::addJSStrings($keys);
?>
<div id="osm-add-edit-plan" class="osm-container">
    <h1 class="osm-page-title"><?php echo $this->item->id > 0 ? Text::_('OSM_EDIT_PLAN') : Text::_('OSM_ADD_PLAN'); ?></h1>
    <div class="btn-toolbar" id="btn-toolbar">
		<?php echo JToolbar::getInstance('toolbar')->render(); ?>
    </div>
    <form action="<?php echo Route::_('index.php?option=com_osmembership&view=mplan&Itemid=' . $this->Itemid, false); ?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" class="form form-horizontal">
        <?php
        echo HTMLHelper::_($tabApiPrefix . 'startTabSet', 'plan', array('active' => 'basic-information-page'));
            echo HTMLHelper::_($tabApiPrefix . 'addTab', 'plan', 'basic-information-page', Text::_('OSM_BASIC_INFORMATION', true));
            echo $this->loadTemplate('general', array('editor' => $editor));
            echo HTMLHelper::_($tabApiPrefix . 'endTab');

            echo HTMLHelper::_($tabApiPrefix . 'addTab', 'plan', 'recurring-settings-page', Text::_('OSM_RECURRING_SETTINGS', true));
            echo $this->loadTemplate('recurring_settings');
            echo HTMLHelper::_($tabApiPrefix . 'endTab');

            echo HTMLHelper::_($tabApiPrefix . 'addTab', 'plan', 'renew-options-page', Text::_('OSM_RENEW_OPTIONS', true));
            echo $this->loadTemplate('renew_options');
            echo HTMLHelper::_($tabApiPrefix . 'endTab');

            echo HTMLHelper::_($tabApiPrefix . 'addTab', 'plan', 'upgrade-options-page', Text::_('OSM_UPGRADE_OPTIONS', true));
            echo $this->loadTemplate('upgrade_options');
            echo HTMLHelper::_($tabApiPrefix . 'endTab');

            echo HTMLHelper::_($tabApiPrefix . 'addTab', 'plan', 'renewal-discounts-page', Text::_('OSM_EARLY_RENEWAL_DISCOUNTS', true));
            echo $this->loadTemplate('renewal_discounts');
            echo HTMLHelper::_($tabApiPrefix . 'endTab');

            echo HTMLHelper::_($tabApiPrefix . 'addTab', 'plan', 'reminders-settings-page', Text::_('OSM_REMINDERS_SETTINGS', true));
            echo $this->loadTemplate('reminders_settings');
            echo HTMLHelper::_($tabApiPrefix . 'endTab');

            echo HTMLHelper::_($tabApiPrefix . 'addTab', 'plan', 'advanced-settings-page', Text::_('OSM_ADVANCED_SETTINGS', true));
            echo $this->loadTemplate('advanced_settings');
            echo HTMLHelper::_($tabApiPrefix . 'endTab');

            echo HTMLHelper::_($tabApiPrefix . 'addTab', 'plan', 'metadata-page', Text::_('OSM_META_DATA', true));
            echo $this->loadTemplate('metadata');
            echo HTMLHelper::_($tabApiPrefix . 'endTab');

            if ($this->config->activate_member_card_feature)
            {
                echo HTMLHelper::_($tabApiPrefix . 'addTab', 'plan', 'member-card-page', Text::_('OSM_MEMBER_CARD_SETTINGS', true));
                echo $this->loadTemplate('member_card', array('editor' => $editor));
                echo HTMLHelper::_($tabApiPrefix . 'endTab');
            }

            echo HTMLHelper::_($tabApiPrefix . 'addTab', 'plan', 'messages-page', Text::_('OSM_MESSAGES', true));
            echo $this->loadTemplate('messages', array('editor' => $editor));
            echo HTMLHelper::_($tabApiPrefix . 'endTab');

            echo HTMLHelper::_($tabApiPrefix . 'addTab', 'plan', 'reminder-messages-page', Text::_('OSM_REMINDER_MESSAGES', true));
            echo $this->loadTemplate('reminder_messages', array('editor' => $editor));
            echo HTMLHelper::_($tabApiPrefix . 'endTab');

            if ($translatable)
            {
                echo HTMLHelper::_($tabApiPrefix . 'addTab', 'plan', 'translation-page', Text::_('OSM_TRANSLATION', true));
                echo $this->loadTemplate('translation', array('editor' => $editor));
                echo HTMLHelper::_($tabApiPrefix . 'endTab');
            }

            if (count($this->plugins))
            {
                $count = 0 ;

                foreach ($this->plugins as $plugin)
                {
	                if (is_array($plugin) && array_key_exists('title', $plugin) && array_key_exists('form', $plugin))
                    {
	                    $count++ ;
	                    echo HTMLHelper::_($tabApiPrefix . 'addTab', 'plan', 'tab_' . $count, Text::_($plugin['title'], true));
	                    echo $plugin['form'];
	                    echo HTMLHelper::_($tabApiPrefix . 'endTab');
                    }
                }
            }

            // Add support for custom settings layout
            if (file_exists(__DIR__ . '/default_custom_settings.php'))
            {
                echo HTMLHelper::_($tabApiPrefix . 'addTab', 'plan', 'custom-settings-page', Text::_('OSM_CUSTOM_SETTINGS', true));
                echo $this->loadTemplate('custom_settings', array('editor' => $editor));
                echo HTMLHelper::_($tabApiPrefix . 'endTab');
            }

            echo HTMLHelper::_($tabApiPrefix . 'endTabSet');
        ?>
        <div class="clearfix"></div>
        <?php echo HTMLHelper::_('form.token'); ?>
        <input type="hidden" name="id" value="<?php echo (int) $this->item->id; ?>"/>
        <input type="hidden" name="task" value="apply" />
        <input type="hidden" id="recurring" name="recurring" value="<?php echo (int) $this->item->recurring_subscription;?>" />
    </form>
</div>