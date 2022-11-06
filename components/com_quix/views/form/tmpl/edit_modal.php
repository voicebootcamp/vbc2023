<?php
/**
 * @version    1.8.0
 * @package    com_quix
 * @author     ThemeXpert <info@themexpert.com>
 * @copyright  Copyright (C) 2015. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access

defined('_JEXEC') or die;
$url = JUri::getInstance();
$url->setVar('force', true);

?>
<!-- Preloader -->
<div class="preloader">
  <style>
    .preloader {
      background: linear-gradient(90deg,rgba(85,34,250,.6) 0,rgba(0,116,228,.6) 100%);
      -webkit-backdrop-filter: blur(20px);
      backdrop-filter: blur(20px);
      position: fixed;
      top: 0;
      left: 0;
      left: 0;
      width: 100%;
      height: 100vh;
      z-index: 1039;
      bottom: 0;
      right: 0;
    }
  </style>
  <div class="wrap">
    <div class="ball"></div>
    <div class="ball"></div>
    <div class="ball"></div>
    <div class="ball"></div>
  </div>
  <p id="loaderMessage">Initializing Builder</p>
  <p class="qx-hints hide qx-hide text-hide"><?php echo QuixFrontendHelper::getHints(); ?></p>
</div>
<!-- //Preloader -->

<div id="hidden-for-editor" style="display: none!important;">
  <?php echo $this->form->renderField('editor'); ?>
</div>
