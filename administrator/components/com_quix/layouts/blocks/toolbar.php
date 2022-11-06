<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_messages
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Language\Text as JText;

$active = isset($displayData['active']) ? $displayData['active'] : 'pages';
$input  = JFactory::getApplication()->input;
$links  = [
    'pages'              => [
        'title' => Text::_('COM_QUIX_PAGES'),
        'link'  => 'index.php?option=com_quix&view=pages',
        'icon'  => '',
    ],
    'collections.header' => [
        'title' => Text::_('COM_QUIX_HEADER'),
        'link'  => 'index.php?option=com_quix&view=collections&filter_collection=header',
        'icon'  => '',
    ],
    'collections.footer' => [
        'title' => Text::_('COM_QUIX_FOOTER'),
        'link'  => 'index.php?option=com_quix&view=collections&filter_collection=footer',
        'icon'  => '',
    ],
    'collections.all'    => [
        'title' => Text::_('COM_QUIX_COLLECTIONS'),
        'link'  => 'index.php?option=com_quix&view=collections&filter_collection=all',
        'icon'  => 'qxuicon-star',
    ],
];

$activated     = QuixHelper::isProActivated();
$activatedText = JText::_('COM_QUIX_TOOLBAR_ACTIVATION');
if ($activated) {
    $activatedText = JText::_('COM_QUIX_TOOLBAR_ACTIVATION_DONE');
}
$uri       = JUri::getInstance();
$returnUrl = base64_encode($uri->toString());

$session = JFactory::getSession();
$status  = $session->get('guide-quix', 'show');
$tourComplete = $input->cookie->get('guide-quix', null);

  // show_tour_guide
$config = JComponentHelper::getParams('com_quix');
$show_tour_guide = $tourComplete = $config->get('guide-quix', 'show');

if ($tourComplete !== 'hide' && $status !== 'hide') {
    JFactory::getDocument()->addScript("https://cdn.jsdelivr.net/npm/shepherd.js@5.0.1/dist/js/shepherd.js");
    JFactory::getDocument()->addScript(
        JUri::root(true).'/administrator/components/com_quix/assets/guide.js'
    );
}
?>
<div class="qx-toolbar qx-background-default qx-box-shadow-hover-small">
    <?php echo QuixHelperLayout::renderGlobalMessage(); ?>

  <nav class="qx-container qx-navbar" qx-navbar>
    <div class="qx-navbar-left">

      <a class="qx-navbar-toggle qx-hidden@s" href="#toolbar-mobile-menu" qx-toggle>
        <span class="qxuicon-bars"></span>
      </a>

      <a class="qx-navbar-item qx-logo" href="index.php?option=com_quix&view=pages">
        <img class="qx-margin-small-right" width="30" height="30" src="<?php
        echo QuixAppHelper::getQuixMediaUrl().'/images/quix-logo.png' ?>" alt="Quix Logo"
             width="40">
      </a>

      <ul class="qx-navbar-nav qx-visible@s">
          <?php
          foreach ($links as $key => $link): ?>
            <li class="<?php
            echo $active === $key ? 'qx-active' : ''; ?>">
              <a href="<?php
              echo $link['link']; ?>">
                  <?php
                  if ( ! empty($link['icon'])): ?>
                    <em class="<?php
                    echo $link['icon']; ?> qx-margin-small-right"></em>
                  <?php
                  endif; ?>
                  <?php
                  echo $link['title']; ?>
              </a>
            </li>
          <?php
          endforeach; ?>
      </ul>
    </div>
    <div class="qx-navbar-right">

      <div class="qx-navbar-item qx-padding-remove-right">
        <a href="index.php?option=com_quix&view=config" id="license-activation-cta"
           class="qx-visible@m qx-button qx-button-<?php
           echo $activated ? 'default' : 'danger'; ?>">
          <i class="qx-margin-small-right <?php echo $activated ? 'qxuicon-check-circle qx-margin-small-right' : 'qxuicon-lock'; ?>"></i>
            <?php echo $activatedText; ?>
        </a>
      </div>

      <ul class="qx-navbar-nav" id="toolbar-settings-right">
        <li>
          <a href="javascript:void(0);"
             id="quixCacheClear"
             data-clear-cache=""
             qx-tooltip="title: Clear Builder Cache"
             class="qx-button qx-margin-small-left qx-visible@s">
            <span id="cache-status-normal" class="qxuicon-repeat"></span>
            <div id="cache-status-loading" class="qx-icon qx-spinner" qx-spinner="ratio: 0.4" style="display: none"></div>
          </a>
        </li>
        <li>

          <a class="qx-button qx-visible@s"
             qx-tooltip="title: Settings"
             href="index.php?option=com_config&view=component&component=com_quix&path=&return=<?php
             echo $returnUrl; ?>"
          >
            <span class="qxuicon-cog"></span>
          </a>
        </li>
        <li>
          <a class="buttons-bars" href="#" qx-tooltip="title: Menu Options"><span class="qxuicon-bars"></span></a>
          <div class="qx-navbar-dropdown" qx-dropdown="pos:bottom-right;mode:click">
            <ul class="qx-nav qx-navbar-dropdown-nav">
              <li>
                <a href="index.php?option=com_plugins&view=plugins&filter_search=System%20-%20SEO%20Site%20Attributes%20for%20Joomla">
                  <span class="qxuicon-crosshairs qx-margin-small-right"></span>SEO Settings
                </a>
              </li>
              <li>
                <a href="index.php?option=com_quix&view=integrations">
                  <span class="qxuicon-cubes qx-margin-small-right"></span>Integrations
                </a>
              </li>
              <li>
                <a href="index.php?option=com_quix&view=help"><span
                          class="qxuicon-info-circle qx-margin-small-right"></span>System Info</a></li>
              <li class="qx-nav-divider"></li>
              <li>
                <a data-quix-ajax href="index.php?option=com_quix&task=cache.cleanImages&format=json">
                  <span class="qxuicon-trash qx-margin-small-right"></span>Clean Image Cache
                </a>
              </li>
              <li>
                <a data-quix-ajax href="index.php?option=com_quix&task=cache.cleanPages&format=json">
                  <span class="qxuicon-trash qx-margin-small-right"></span>Clean Page Cache
                </a>
              </li>
              <li>
                <a data-quix-ajax href="index.php?option=com_quix&task=cache.cleanIcons&format=json">
                  <span class="qxuicon-repeat qx-margin-small-right"></span>Sync Icons
                </a>
              </li>
              <li>
                <a data-quix-ajax href="index.php?option=com_quix&task=clear_cache&step=0">
                  <span class="qxuicon-trash-alt qx-margin-small-right"></span>Clear Legacy Cache
                </a>
              </li>
            </ul>
          </div>
        </li>
      </ul>

    </div>
  </nav>
</div>

<!-- This is the off-canvas -->
<div id="toolbar-mobile-menu" qx-offcanvas="mode: reveal; overlay: true">
  <div class="qx-offcanvas-bar qx-flex qx-flex-column">
    <ul class="qx-nav qx-nav-primary qx-nav-center qx-margin-auto-vertical">
      <li class="qx-active"><a href="index.php?option=com_quix&view=pages">Pages</a></li>
      <li><a href="">Header</a></li>
      <li><a href="#">Footer</a></li>
      <li><a href="index.php?option=com_quix&view=collections"><span
                  class="qxuicon-star qx-margin-small-right"></span> My Templates</a></li>
    </ul>

    <button class="qx-offcanvas-close" type="button" qx-close></button>

  </div>
</div>

<div class="qx-container qx-margin">
    <?php echo QuixHelperLayout::getWelcomeLayout(); ?>
</div>

<?php echo QuixHelperLayout::renderSysMessage(); ?>
<div class="qx-overlay-default qx-position-cover qx-hidden" id="admin-spinner" style="z-index: 1020;">
  <span class="qx-position-center qx-position-center"></span>
</div>
