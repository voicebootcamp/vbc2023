<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2022 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

$bootstrapHelper   = OSMembershipHelperBootstrap::getInstance();
$rowFluidClasss    = $bootstrapHelper->getClassMapping('row-fluid');
$controlGroupClass = $bootstrapHelper->getClassMapping('control-group');
$controlLabelClass = $bootstrapHelper->getClassMapping('control-label');
$controlsClass     = $bootstrapHelper->getClassMapping('controls');
?>
<fieldset class="adminform">
    <legend class="adminform"><?php echo Text::_('OSM_META_DATA'); ?></legend>
    <div class="<?php echo $controlGroupClass; ?>">
        <div class="<?php echo $controlLabelClass; ?>">
			<?php echo Text::_('OSM_PAGE_TITLE'); ?>
        </div>
        <div class="<?php echo $controlsClass; ?>">
            <input class="form-control input-large" type="text" name="page_title" id="page_title" size="" maxlength="250"
                   value="<?php echo $this->item->page_title; ?>"/>
        </div>
    </div>
    <div class="<?php echo $controlGroupClass; ?>">
        <div class="<?php echo $controlLabelClass; ?>">
			<?php echo Text::_('OSM_PAGE_HEADING'); ?>
        </div>
        <div class="<?php echo $controlsClass; ?>">
            <input class="form-control input-large" type="text" name="page_heading" id="page_heading" size="" maxlength="250"
                   value="<?php echo $this->item->page_heading; ?>"/>
        </div>
    </div>
    <div class="<?php echo $controlGroupClass; ?>">
        <div class="<?php echo $controlLabelClass; ?>">
			<?php echo Text::_('OSM_META_KEYWORDS'); ?>
        </div>
        <div class="<?php echo $controlsClass; ?>">
						<textarea rows="5" cols="30" class="input-lage"
                                  name="meta_keywords"><?php echo $this->item->meta_keywords; ?></textarea>
        </div>
    </div>
    <div class="<?php echo $controlGroupClass; ?>">
        <div class="<?php echo $controlLabelClass; ?>">
			<?php echo Text::_('OSM_META_DESCRIPTION'); ?>
        </div>
        <div class="<?php echo $controlsClass; ?>">
						<textarea rows="5" cols="30" class="input-lage"
                                  name="meta_description"><?php echo $this->item->meta_description; ?></textarea>
        </div>
    </div>
</fieldset>
