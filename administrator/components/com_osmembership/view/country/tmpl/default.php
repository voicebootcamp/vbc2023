<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2022 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

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

$useTabs = Multilanguage::isEnabled() && count($this->languages);

if ($useTabs && !OSMembershipHelper::isJoomla4())
{
	HTMLHelper::_('behavior.tabstate');
}
?>
<form action="index.php?option=com_osmembership&view=country" method="post" name="adminForm" id="adminForm" class="form form-horizontal">
	<?php
	if ($useTabs)
	{
		echo HTMLHelper::_($tabApiPrefix . 'startTabSet', 'country', array('active' => 'general-page'));
		echo HTMLHelper::_($tabApiPrefix . 'addTab', 'country', 'general-page', Text::_('OSM_GENERAL', true));
	}
	?>
	<div class="control-group">
		<div class="control-label">
			<?php echo  Text::_('OSM_COUNTRY_NAME'); ?>
		</div>
		<div class="controls">
			<input class="form-control" type="text" name="name" id="name" size="40" maxlength="250" value="<?php echo $this->item->name;?>" />
		</div>
	</div>	
	<div class="control-group">
		<div class="control-label">
			<?php echo  Text::_('OSM_COUNTRY_CODE_3'); ?>
		</div>
		<div class="controls">
			<input class="form-control" type="text" name="country_3_code" id="country_3_code" maxlength="250" value="<?php echo $this->item->country_3_code;?>" />
		</div>
	</div>
	<div class="control-group">
		<div class="control-label">
			<?php echo  Text::_('OSM_COUNTRY_CODE_2'); ?>
		</div>
		<div class="controls">
			<input class="form-control" type="text" name="country_2_code" id="country_2_code" maxlength="250" value="<?php echo $this->item->country_2_code;?>" />
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
    if ($useTabs)
    {
	    echo HTMLHelper::_($tabApiPrefix . 'endTab');

	    echo HTMLHelper::_($tabApiPrefix . 'addTab', 'country', 'translation-page', Text::_('OSM_TRANSLATION', true));
	    echo HTMLHelper::_($tabApiPrefix . 'startTabSet', 'country-translation', ['active' => 'translation-page-' . $this->languages[0]->sef]);
	    $rootUri = Uri::root(true);

	    foreach ($this->languages as $language)
	    {
		    $sef = $language->sef;
		    echo HTMLHelper::_($tabApiPrefix . 'addTab', 'country-translation', 'translation-page-' . $sef, $language->title . ' <img src="' . $rootUri . '/media/mod_languages/images/' . $language->image . '.gif" />');
		    ?>
            <div class="control-group">
                <div class="control-label">
				    <?php echo  Text::_('OSM_NAME'); ?>
                </div>
                <div class="controls">
                    <input class="form-control input-xlarge" type="text" name="name_<?php echo $sef; ?>" id="name_<?php echo $sef; ?>" size="" maxlength="250" value="<?php echo $this->item->{'name_' . $sef}; ?>" />
                </div>
            </div>
		    <?php
		    echo HTMLHelper::_($tabApiPrefix . 'endTab');
	    }

	    echo HTMLHelper::_($tabApiPrefix . 'endTabSet');
	    echo HTMLHelper::_($tabApiPrefix . 'endTab');
	    echo HTMLHelper::_($tabApiPrefix . 'endTabSet');
    }
    ?>
	<?php echo HTMLHelper::_('form.token'); ?>
    <input type="hidden" name="id" value="<?php echo (int) $this->item->id; ?>"/>
	<input type="hidden" name="task" value="" />
</form>