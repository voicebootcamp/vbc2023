<?php
/**
 * @version    CVS: 1.0.0
 * @package    com_quix
 * @author     ThemeXpert <info@themexpert.com>
 * @copyright  Copyright (C) 2015. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;
?>

<?php if (isset($this->item) && ! empty($this->item->text)) : ?>
    <?php echo $this->item->text; ?>
<?php else: ?>
  <div class="qx-alert qx-alert-warning"><?php echo JText::_('COM_QUIX_ITEM_NOT_LOADED'); ?></div>
<?php endif; ?>
