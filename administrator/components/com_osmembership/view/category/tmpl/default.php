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
use Joomla\CMS\Uri\Uri;

if (OSMembershipHelper::isJoomla4())
{
	$tabApiPrefix = 'uitab.';
}
else
{
	$tabApiPrefix = 'bootstrap.';
}

$config            = OSMembershipHelper::getConfig();
$editor            = Editor::getInstance($config->get('editor') ?: Factory::getApplication()->get('editor'));
$translatable      = Multilanguage::isEnabled() && count($this->languages);
$hasCustomSettings = file_exists(__DIR__ . '/default_custom_settings.php');
$hasPlugins        = false;

foreach ($this->plugins as $plugin)
{
    if (!empty($plugin['form']))
    {
        $hasPlugins = true;
    }
}

if (!OSMembershipHelper::isJoomla4())
{
	HTMLHelper::_('behavior.tabstate');
}

HTMLHelper::_('behavior.core');

if (!OSMembershipHelper::isJoomla4())
{
	HTMLHelper::_('formbehavior.chosen', 'select');
}

HTMLHelper::_('bootstrap.tooltip', '.hasTooltip', ['html' => true, 'sanitize' => false]);

Factory::getDocument()->addScript(Uri::root(true) . '/media/com_osmembership/js/admin-category-default.min.js');

$keys = ['OSM_ENTER_CATEGORY_TITLE'];
OSMembershipHelperHtml::addJSStrings($keys);
?>
<form action="index.php?option=com_osmembership&view=category" method="post" name="adminForm" id="adminForm" class="form form-horizontal">
<?php
	echo HTMLHelper::_($tabApiPrefix . 'startTabSet', 'category', array('active' => 'general-page'));
	echo HTMLHelper::_($tabApiPrefix . 'addTab', 'category', 'general-page', Text::_('OSM_GENERAL', true));
?>
	<div class="control-group">
		<div class="control-label">
			<?php echo  Text::_('OSM_TITLE'); ?>
		</div>
		<div class="controls">
			<input class="form-control" type="text" name="title" id="title" size="40" maxlength="250" value="<?php echo $this->item->title;?>" />
		</div>
	</div>
	<div class="control-group">
		<div class="control-label">
			<?php echo  Text::_('OSM_ALIAS'); ?>
		</div>
		<div class="controls">
			<input class="form-control" type="text" name="alias" id="alias" size="40" maxlength="250" value="<?php echo $this->item->alias;?>" />
		</div>
	</div>
	<div class="control-group">
		<div class="control-label">
			<?php echo  Text::_('OSM_PARENT_CATEGORY'); ?>
		</div>
		<div class="controls">
			<?php echo OSMembershipHelperHtml::getChoicesJsSelect(OSMembershipHelperHtml::buildCategoryDropdown($this->item->parent_id), Text::_('OSM_TYPE_OR_SELECT_ONE_CATEGORY')); ?>
		</div>
	</div>
	<div class="control-group">
		<div class="control-label">
			<?php echo OSMembershipHelperHtml::getFieldLabel('exclusive_plans', Text::_('OSM_EXCLUSIVE_PLANS'), Text::_('OSM_EXCLUSIVE_PLANS_EXPLAIN')); ?>
		</div>
		<div class="controls">
			<?php echo OSMembershipHelperHtml::getBooleanInput('exclusive_plans', $this->item->exclusive_plans); ?>
		</div>
	</div>
    <div class="control-group">
        <div class="control-label">
			<?php echo OSMembershipHelperHtml::getFieldLabel('grouping_plans', Text::_('OSM_GROUPING_PLANS'), Text::_('OSM_GROUPING_PLANS_EXPLAIN')); ?>
        </div>
        <div class="controls">
			<?php echo OSMembershipHelperHtml::getBooleanInput('grouping_plans', $this->item->grouping_plans); ?>
        </div>
    </div>
	<div class="control-group">
		<div class="control-label">
			<?php echo Text::_('OSM_DESCRIPTION'); ?>
		</div>
		<div class="controls">
			<?php echo $editor->display('description', $this->item->description, '100%', '250', '75', '10') ; ?>
		</div>
	</div>
	<div class="control-group">
		<div class="control-label">
			<?php echo Text::_('OSM_ACCESS'); ?>
		</div>
		<div class="controls">
			<?php echo $this->lists['access']; ?>
		</div>
	</div>
	<div class="control-group">
		<div class="control-label">
			<?php echo Text::_('OSM_PUBLISHED'); ?>
		</div>
		<div class="controls">
			<?php echo $this->lists['published']; ?>
		</div>
	</div>
	<?php
	echo HTMLHelper::_($tabApiPrefix . 'endTab');

	echo HTMLHelper::_($tabApiPrefix . 'addTab', 'category', 'messages-page', Text::_('OSM_MESSAGES', true));
	echo $this->loadTemplate('messages', ['editor' => $editor]);
	echo HTMLHelper::_($tabApiPrefix . 'endTab');

    if ($translatable)
	{
		echo $this->loadTemplate('translation', ['editor' => $editor, 'tabApiPrefix' => $tabApiPrefix]);
	}

	if ($hasPlugins)
    {
	    $count = 0 ;

	    foreach ($this->plugins as $plugin)
	    {
		    $count++ ;
		    echo HTMLHelper::_($tabApiPrefix . 'addTab', 'category', 'tab_' . $count, Text::_($plugin['title'], true));
		    echo $plugin['form'];
		    echo HTMLHelper::_($tabApiPrefix . 'endTab');
	    }
    }

	// Add support for custom settings layout
	if ($hasCustomSettings)
	{
		echo HTMLHelper::_($tabApiPrefix . 'addTab', 'category', 'custom-settings-page', Text::_('OSM_CUSTOM_SETTINGS', true));
		echo $this->loadTemplate('custom_settings', array('editor' => $editor));
		echo HTMLHelper::_($tabApiPrefix . 'endTab');
	}

	echo HTMLHelper::_($tabApiPrefix . 'endTabSet');
	?>
	<?php echo HTMLHelper::_('form.token'); ?>
    <input type="hidden" name="id" value="<?php echo (int) $this->item->id; ?>"/>
	<input type="hidden" name="task" value="" />
</form>