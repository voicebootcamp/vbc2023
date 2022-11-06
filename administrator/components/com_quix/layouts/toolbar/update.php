<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_messages
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

// JHTML::_('behavior.modal');

$session = JFactory::getSession();
$status  = $session->get('quix-notification-update', 'open');
if ($status === 'collapse') {
    return '';
}

$text = JText::_('COM_QUIX_TOOLBAR_ACTIVATION');
/** @var TYPE_NAME $displayData */
$update      = $displayData['info'];
$credentials = $displayData['credentials'];
$version     = $update->version;
$exists      = JFile::exists(JPATH_ADMINISTRATOR.'/components/com_iquix/iquix.php');
if ($exists) {
    $link = JRoute::_('index.php?option=com_iquix');
} else {
    $link = JRoute::_('index.php?option=com_installer&view=update&task=update.find&'.JSession::getFormToken().'=1');
}
?>
<div class="qx-box-shadow-small qx-box-shadow-hover-medium qx-alert qx-background-primary clearfix qx-margin-remove qx-color-white" qx-alert>
  <div class="qx-container">
    <a class="qx-alert-close" data-session="quix-notification-update" qx-close></a>
    <p class="qx-alert-heading qx-flex qx-flex-between">
      <span><span class="qx-label qx-label-success qx-text-light qx-margin-small-right">Update</span>
      <strong><?php echo JText::_('COM_QUIX_NEW_UPDATE_AVAILABLE_TITLE'); ?></strong> <?php echo JText::sprintf('COM_QUIX_NEW_UPDATE_AVAILABLE_DESC',
              $version); ?>.</span>
      <span>
        <a href="#" data-toggle="modal" data-target="#quixChangeLog" class="qx-button qx-button-primary qx-button-small qx-margin-left"><span
                  class="icon-book"></span> Changelog</a>
        <a href="<?php echo $link; ?>" class="qx-button qx-button-secondary qx-button-small"><span class="icon-loop"></span> Update</a>
      </span>
    </p>
  </div>
</div>


<?php
$token      = JSession::getFormToken();
$layoutData = array(
    'selector' => 'quixChangeLog',
    'params'   => array(
        'url'    => Juri::root().'administrator/index.php?option=com_quix&task=get.getQuixChangeLogs&'.$token.'=1',
        'title'  => JText::_('Quix Changelog'),
        'height' => '400',
        'width'  => '800',
        'footer' => '<button type="button" class="btn btn-default" data-dismiss="modal">'.JText::_('Close').'</button>'
    ),
    'body'     => ''
);
echo JLayoutHelper::render('joomla.modal.main', $layoutData);
?>
