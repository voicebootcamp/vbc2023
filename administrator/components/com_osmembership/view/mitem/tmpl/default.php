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
use Joomla\CMS\Uri\Uri;

HTMLHelper::_('bootstrap.tooltip', '.hasTooltip', ['html' => true, 'sanitize' => false]);

if (!OSMembershipHelper::isJoomla4())
{
	HTMLHelper::_('formbehavior.chosen', 'select');
}

$language = Factory::getLanguage();

$translatable = $this->item->translatable && Multilanguage::isEnabled() && count($this->languages);

if (OSMembershipHelper::isJoomla4())
{
	$tabApiPrefix = 'uitab.';
}
else
{
	HTMLHelper::_('behavior.tabstate');

	$tabApiPrefix = 'bootstrap.';
}

$editor = Editor::getInstance(Factory::getApplication()->get('editor'));
$tags   = OSMembershipHelperHtml::getSupportedTags($this->item->name);
?>
<form action="index.php?option=com_osmembership&view=mitem" method="post" name="adminForm" id="adminForm" class="form form-horizontal<?php if (!OSMembershipHelper::isJoomla4()) echo ' joomla3'; ?> osm-mitem-form">
	<?php
	if ($translatable)
	{
		echo HTMLHelper::_($tabApiPrefix . 'startTabSet', 'mitem', array('active' => 'general-page'));
		echo HTMLHelper::_($tabApiPrefix . 'addTab', 'mitem', 'general-page', Text::_('OSM_GENERAL', true));
	}
	?>
	<div class="control-group">
		<div class="control-label">
			<?php
                if ($language->hasKey($this->item->title . '_EXPLAIN'))
                {
                    $messageDescription = Text::_($this->item->title . '_EXPLAIN');
                }
                else
                {
                    $messageDescription = '';
                }

                echo OSMembershipHelperHtml::getFieldLabel($this->item->name, Text::_($this->item->title), $messageDescription);

                if (count($tags))
                {
	                $availableTags = '[' . implode(']<br /> [', $tags) . ']';
                ?>
                    <p class="osm-available-tags">
		                <?php echo Text::_('OSM_AVAILABLE_TAGS'); ?>: <br /><strong><?php echo $availableTags; ?></strong>
                    </p>
                <?php
                }
            ?>
		</div>
		<div class="controls">
            <?php
                if ($this->item->type == 'text')
                {
                ?>
                    <input type="text" name="<?php echo $this->item->name; ?>" class="input-xxlarge form-control" value="<?php echo $this->escape($this->message->get($this->item->name)); ?>" />
                <?php
                }
                elseif ($this->item->type == 'textarea')
                {
                ?>
                    <textarea name="<?php echo $this->item->name; ?>" class="input-xxlarge form-control" rows="10"><?php echo $this->message->get($this->item->name); ?></textarea>
                <?php
                }
                else
                {
	                echo $editor->display($this->item->name, $this->message->get($this->item->name), '100%', '550', '90', '6');
                }
            ?>
		</div>
	</div>
    <?php
        if ($translatable)
        {
	        echo HTMLHelper::_($tabApiPrefix . 'endTab');
	        echo HTMLHelper::_($tabApiPrefix . 'addTab', 'mitem', 'translation-page', Text::_('EB_TRANSLATION', true));
	        echo HTMLHelper::_($tabApiPrefix . 'startTabSet', 'mitem-translation', array('active' => 'translation-page-' . $this->languages[0]->sef));

	        $rootUri = Uri::root(true);

            foreach ($this->languages as $language)
	        {
		        $sef       = $language->sef;
		        $inputName = $this->item->name . '_' . $sef;
		        echo HTMLHelper::_($tabApiPrefix . 'addTab', 'mitem-translation', 'translation-page-' . $sef, $language->title . ' <img src="' . $rootUri . '/media/mod_languages/images/' . $language->image . '.gif" />');
		    ?>
                <div class="control-group">
                    <div class="control-label">
	                    <?php
	                        echo OSMembershipHelperHtml::getFieldLabel($inputName, Text::_($this->item->title), $messageDescription);

                            if (count($tags))
                            {
                                $availableTags = '[' . implode(']<br /> [', $tags) . ']';
                                ?>
                                <p class="osm-available-tags">
                                    <?php echo Text::_('OSM_AVAILABLE_TAGS'); ?>:<br /> <strong><?php echo $availableTags; ?></strong>
                                </p>
                                <?php
                            }
                        ?>
                    </div>
                    <div class="controls">
				        <?php
				        if ($this->item->type == 'text')
				        {
					    ?>
                            <input type="text" name="<?php echo $inputName; ?>" class="input-xxlarge form-control" value="<?php echo $this->escape($this->message->get($inputName)); ?>" />
					    <?php
				        }
				        elseif ($this->item->type == 'textarea')
                        {
                        ?>
                            <textarea name="<?php echo $inputName; ?>" class="input-xxlarge form-control" rows="10"><?php echo $this->message->get($inputName); ?></textarea>
                        <?php
                        }
				        else
				        {
					        echo $editor->display($inputName, $this->message->get($inputName), '100%', '180', '90', '6');
				        }
				        ?>
                    </div>
                </div>
            <?php
		        echo HTMLHelper::_($tabApiPrefix . 'endTab');
	        }

	        echo HTMLHelper::_($tabApiPrefix . 'endTabSet');
        }
    ?>

	<div class="clearfix"></div>
	<?php echo HTMLHelper::_('form.token'); ?>
	<input type="hidden" name="id" value="<?php echo (int) $this->item->id; ?>"/>
	<input type="hidden" name="task" value=""/>
</form>