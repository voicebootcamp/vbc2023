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
$status  = $session->get('quix-notification-verifyLicense', 'open');
if ($status === 'collapse') {
    return '';
}

$text = JText::_('COM_QUIX_TOOLBAR_ACTIVATION');
?>
  <div class="qx-box-shadow-small qx-box-shadow-hover-medium qx-admin-box qx-alert qx-color-white qx-background-secondary qx-margin-remove qx-clearfix"
       style="margin: -5px 0px 20px;" qx-alert>
    <a class="qx-alert-close" data-session="quix-notification-verifyLicense" qx-close></a>
    <div class="qx-container">
      <p class="qx-text-center">

        <span><span class="qx-label qx-label-danger qx-text-light qx-margin-small-right">License Missing</span>
          <?php echo JText::_('COM_QUIX_AUTHORISE_MESSAGE'); ?>
          <i class="qxuicon-arrow-down qx-margin-small-right"></i>
        </span>
        <!--<a rel="{handler:'iframe', size:{x:700,y:350}}"-->
        <!--   href="index.php?option=com_quix&amp;view=config&amp;tmpl=component"-->
        <!--   title="--><?php //echo $text; ?><!--"-->
        <!--   class="quixSettings qx-button qx-button-danger qx-button-small" id="mySettings2">-->
        <!--  <span class="icon-lock"></span> --><?php //echo $text; ?>
        <!--</a>-->
      </p>

    </div>
  </div>
<?php if (JFactory::getApplication()->input->get('action', false)): ?>
  <script type="text/javascript">
      setTimeout(function() {
          jQuery('.quixSettings')[0].click();
      }, 3000);
  </script>
<?php endif;
