<?php
/**
 * @package     Quix.Joomla Page Builder
 * @subpackage  Templates.Atom
 *
 * @copyright   Copyright (C) 2005 - 2020 ThemeXpert, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
require_once __DIR__.'/AtomHelper.php';

/** @var JDocumentError $this */

$app  = JFactory::getApplication();
$user = JFactory::getUser();

// Getting params from template
$this->params = $params = $app->getTemplate(true)->params;

// Detecting Active Variables
$option   = $app->input->getCmd('option', '');
$view     = $app->input->getCmd('view', '');
$layout   = $app->input->getCmd('layout', '');
$task     = $app->input->getCmd('task', '');
$itemid   = $app->input->getCmd('Itemid', '');
$format   = $app->input->getCmd('format', 'html');
$sitename = htmlspecialchars($app->get('sitename'), ENT_QUOTES, 'UTF-8');

if ($task === 'edit' || $layout === 'form') {
    $fullWidth = 1;
} else {
    $fullWidth = 0;
}

// Add JavaScript Frameworks
JHtml::_('bootstrap.framework');

// Logo file or site title param
if ($params->get('logoFile')) {
    $logo = '<img src="'.JUri::root().$params->get('logoFile').'" alt="'.$sitename.'" />';
} elseif ($params->get('sitetitle')) {
    $logo = '<span class="site-title" title="'.$sitename.'">'.htmlspecialchars($params->get('sitetitle')).'</span>';
} else {
    $logo = '<span class="site-title" title="'.$sitename.'">'.$sitename.'</span>';
}
?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
  <meta charset="utf-8" />
  <title><?php echo $this->title; ?><?php echo htmlspecialchars($this->error->getMessage(), ENT_QUOTES, 'UTF-8'); ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link href="<?php echo JUri::root().'?quix-asset=/css/quix-core.css&ver='.QUIXNXT_VERSION; ?>" rel="stylesheet" />

    <?php if ($app->get('debug_lang', '0') == '1' || $app->get('debug', '0') == '1') : ?>
      <link href="<?php echo JUri::root(true); ?>/media/cms/css/debug.css" rel="stylesheet" />
    <?php endif; ?>

    <?php // If Right-to-Left ?>
  <link href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/favicon.ico" rel="shortcut icon" type="image/vnd.microsoft.icon" />

  <!--[if lt IE 9]><script src="<?php echo JUri::root(true); ?>/media/jui/js/html5.js"></script><![endif]-->
</head>
<body class="TPL_ATOM site <?php echo AtomHelper::getBodyClass(); ?>">
<nav class="qx-navbar-container" id="header">
  <div class="qx-container qx-navbar" qx-navbar>
    <div class="qx-navbar-center">
      <a class="qx-navbar-item qx-logo" href="<?php echo $this->baseurl; ?>/">
          <?php echo AtomHelper::getSiteLogo(); ?>
      </a>
    </div>
</nav>
<main role="main" class="qx-section<?php echo $params->get('layout') !== '1' ? ' qx-padding-remove-vertical' : ''; ?>">
  <div class="qx-container">
    <div class="qx-grid qx-grid-stack" qx-grid>
      <div class="qx-width-3-4">
        <div class="qx-margin">
          <!-- Begin Content -->
          <h1 class="page-header"><?php echo JText::_('JERROR_LAYOUT_PAGE_NOT_FOUND'); ?></h1>
          <div class="qx-margin">
            <p><strong><?php echo JText::_('JERROR_LAYOUT_ERROR_HAS_OCCURRED_WHILE_PROCESSING_YOUR_REQUEST'); ?></strong></p>
            <p><?php echo JText::_('JERROR_LAYOUT_NOT_ABLE_TO_VISIT'); ?></p>
            <ul>
              <li><?php echo JText::_('JERROR_LAYOUT_AN_OUT_OF_DATE_BOOKMARK_FAVOURITE'); ?></li>
              <li><?php echo JText::_('JERROR_LAYOUT_MIS_TYPED_ADDRESS'); ?></li>
              <li><?php echo JText::_('JERROR_LAYOUT_SEARCH_ENGINE_OUT_OF_DATE_LISTING'); ?></li>
              <li><?php echo JText::_('JERROR_LAYOUT_YOU_HAVE_NO_ACCESS_TO_THIS_PAGE'); ?></li>
            </ul>
          </div>
          <!-- End Content -->
        </div>
      </div>
      <div class="qx-width-1-4">
        <div class="qx-card">
          <div class="qx-card-body">
              <?php if ($format === 'html' && JModuleHelper::getModule('mod_search')) : ?>
                <p><strong><?php echo JText::_('JERROR_LAYOUT_SEARCH'); ?></strong></p>
                <p><?php echo JText::_('JERROR_LAYOUT_SEARCH_PAGE'); ?></p>
                  <?php $module = JModuleHelper::getModule('mod_search'); ?>
                  <?php echo JModuleHelper::renderModule($module); ?>
              <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

    <div class="qx-margin">
      <hr />
      <p><?php echo JText::_('JERROR_LAYOUT_PLEASE_CONTACT_THE_SYSTEM_ADMINISTRATOR'); ?></p>
      <div class="qx-alert">
        <span class="label label-inverse"><?php echo $this->error->getCode(); ?></span> <?php echo htmlspecialchars($this->error->getMessage(), ENT_QUOTES,
              'UTF-8'); ?>
          <?php if ($this->debug) : ?>
            <br /><?php echo htmlspecialchars($this->error->getFile(), ENT_QUOTES, 'UTF-8'); ?>:<?php echo $this->error->getLine(); ?>
          <?php endif; ?>
      </div>
    </div>

      <?php if ($this->debug) : ?>
        <div class="qx-alert qx-padding">
          <div>
              <?php echo $this->renderBacktrace(); ?>
              <?php // Check if there are more Exceptions and render their data as well ?>
              <?php if ($this->error->getPrevious()) : ?>
                  <?php $loop = true; ?>
                  <?php // Reference $this->_error here and in the loop as setError() assigns errors to this property and we need this for the backtrace to work correctly ?>
                  <?php // Make the first assignment to setError() outside the loop so the loop does not skip Exceptions ?>
                  <?php $this->setError($this->_error->getPrevious()); ?>
                  <?php while ($loop === true) : ?>
                  <p><strong><?php echo JText::_('JERROR_LAYOUT_PREVIOUS_ERROR'); ?></strong></p>
                  <p>
                      <?php echo htmlspecialchars($this->_error->getMessage(), ENT_QUOTES, 'UTF-8'); ?>
                    <br /><?php echo htmlspecialchars($this->_error->getFile(), ENT_QUOTES, 'UTF-8'); ?>:<?php echo $this->_error->getLine(); ?>
                  </p>
                      <?php echo $this->renderBacktrace(); ?>
                      <?php $loop = $this->setError($this->_error->getPrevious()); ?>
                  <?php endwhile; ?>
                  <?php // Reset the main error object to the base error ?>
                  <?php $this->setError($this->error); ?>
              <?php endif; ?>
          </div>
        </div>
      <?php endif; ?>
  </div>
</main>

<!-- Footer -->
<footer class="qx-section qx-section-default footer" role="contentinfo">
  <div class="qx-container">

      <?php echo $this->getBuffer('modules', 'footer', array('style' => 'none')); ?>

    <hr />
    <p class="qx-align-right">
      <a href="#header" id="back-top" class="qx-link-text" qx-scroll>
          <?php echo JText::_('TPL_ATOM_BACKTOTOP'); ?>
      </a>
    </p>
    <p>
      &copy; <?php echo date('Y'); ?> <?php echo AtomHelper::getSiteName(); ?> by <a href="https://www.themexpert.com/" target="_blank" class="qx-link-text">ThemeXpert</a>
    </p>
  </div>
</footer>
<script src="<?php echo JUri::root().'?quix-asset=/js/quix.vendor.js&ver='.QUIXNXT_VERSION; ?>"></script>

<?php echo $this->getBuffer('modules', 'debug', array('style' => 'none')); ?>
</body>
</html>
