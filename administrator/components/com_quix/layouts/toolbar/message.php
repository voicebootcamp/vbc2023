<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_messages
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
$app      = JFactory::getApplication();
$messages = $app->getMessageQueue(true);
if ($messages): ?>
  <style>#system-message-container {
          display: none;
      }</style>
  <div id="quix-system-message">
      <?php foreach ($messages as $key => $message) {
          ?>
        <div class="qx-container qx-margin">
          <div
                  class="qx-box-shadow-small qx-alert qx-alert-<?php echo str_replace(['message', 'error', 'notice'], ['success', 'danger', 'info'],
                      $message['type']); ?>" qx-alert>
            <a class="qx-alert-close" qx-close></a>
            <!--<h4 class="qx-alert-heading">--><?php //echo ucfirst($message['type']); ?>
            <!--</h4>-->
            <div class="qx-alert-message"><?php echo $message['message']; ?>
            </div>
          </div>
        </div>
          <?php
      }
      ?>
  </div>
<?php
endif;
