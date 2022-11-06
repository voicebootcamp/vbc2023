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

<?php if ($this->item->params->get('enable_confetti', 0)): ?>
  <canvas id="confetti" width="1920" height="369" style="opacity: 1;"></canvas>
  <style>#confetti {position: fixed;top: 0;left: 0;z-index: -1;}</style>
<?php endif;
