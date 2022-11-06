<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  System.joominsights
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

extract($displayData);
?>
<div class="joominsights-modal"
    id="joominsights-form-modal">
    <div class="joominsights-modal-wrap">
        <div class="joominsights-modal-header">
            <h3><?php echo JText::_('If you have a moment, please let us know why you are disabling the extension:'); ?></h3>
        </div>

        <div class="joominsights-modal-body">
            <ul class="reasons">
                <?php $i=0; foreach ($reasons as $reason) { ?>
                <li data-type="<?php echo $reason['type']; ?>"
                    data-placeholder="<?php echo $reason['placeholder']; ?>">
                    <label for="item-<?php echo $i; ?>">
                        <input id="item-<?php echo $i; ?>" type="radio" name="selected-reason" value="<?php echo $reason['id']; ?>">
                        <?php echo $reason['text']; ?>
                    </label>
                </li>
                <?php $i++;} ?>
            </ul>
        </div>

        <div class="joominsights-modal-footer">
            <a href="#" class="dont-bother-me"><?php echo JText::_('I rather wouldn\'t say'); ?></a>
            <button class="btn btn-secondary"><?php echo JText::_('Submit & Deactivate'); ?></button>
            <button class="btn btn-primary"><?php echo JText::_('Cancel'); ?></button>
        </div>
    </div>
</div>