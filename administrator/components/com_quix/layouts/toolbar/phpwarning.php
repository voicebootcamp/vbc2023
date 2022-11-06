<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_messages
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
$session = JFactory::getSession();
$status = $session->get('quix-notification-phpwarning', 'open');
if($status === 'collapse') return '';
?>
<div class="qx-box-shadow-small qx-box-shadow-hover-medium qx-alert qx-alert-danger clearfix qx-alert qx-margin-remove" qx-alert>
  <div class=" qx-container">
    <a class="qx-alert-close" data-session="quix-notification-phpwarning" qx-close></a>
    <p>
      <span class="qx-label qx-label-warning qx-text-light qx-margin-small-right">Warning</span>
      <strong><?php echo JText::_('COM_QUIX_HEADS_UP_OLD_PHP'); ?></strong> <?php echo JText::sprintf('COM_QUIX_PHP_WARNING_OLD_VERSIONS', phpversion()); ?>
    </p>
  </div>
</div>
