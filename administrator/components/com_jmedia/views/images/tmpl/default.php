<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_jmedia
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.core');
JHtml::_('jquery.framework');

JHtml::_('script', 'com_jmedia/app.js', ['version' => 'auto', 'relative' => true]);
JHtml::_('stylesheet', 'com_jmedia/app.css', ['version' => 'auto', 'relative' => true]);
?>
  <div class="jmedia-wrapper">
    <div id="JMediaWrapper"
         class="JMediaWrapper-<?php echo JMDEDIA_LICENSE == 'FREE' ? 'free' : 'pro' ?>">
    </div>
  </div>
<?php
if (JVERSION < 4) {
    JMediaHelper::addMediaModalScriptJ3();
} else {
    JMediaHelper::addMediaModalScriptJ4();
}
JFactory::getDocument()->addScriptDeclaration("
setTimeout(() => {
        jQuery('.filemanagerBody').css('height', window.innerHeight - 100 + 'px');
}, 1000);");
