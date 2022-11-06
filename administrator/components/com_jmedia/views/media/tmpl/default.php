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

JMediaHelper::addMediaCommonScript();
JFactory::getDocument()->addScriptDeclaration("
window.onload = window.onresize = () => {
  jQuery('.filemanagerBody').css('height', jQuery('body').height() - (jQuery('.filemanagerBody').offset().top + 80) + 'px');
};");
?>
<div class="jmedia-wrapper">
  <div id="JMediaWrapper" class="JMediaWrapper-<?php echo JMDEDIA_LICENSE == 'FREE' ? 'free' : 'pro' ?>"></div>
</div>

<?php
echo JMediaHelper::getFooter();

echo JHtml::_(
    'bootstrap.renderModal',
    'aboutModal',
    [
        'title'  => JText::_('JMedia Filemanager for Joomla! and Quix'),
        'footer' => 'Powered by ThemeXpert',
    ],
    $this->loadTemplate('about')
);
echo JHtml::_(
    'bootstrap.renderModal',
    'upgradeModal',
    [
        'title'  => JText::_('JMedia Filemanager for Joomla! and Quix'),
        'footer' => 'Powered by ThemeXpert',
    ],
    $this->loadTemplate('upgrade')
);
