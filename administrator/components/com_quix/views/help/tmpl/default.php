<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_messages
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

  defined('_JEXEC') or die;

// Include the HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
// Load toolbar
$layout     = new JLayoutFile('blocks.toolbar');
echo $layout->render(['active' => '']);
?>
<div class="quix qx-container qx-margin-medium-top qx-text-small">
    <div class="qx-margin qx-card qx-card-primary qx-card-body qx-card-small qx-flex qx-flex-between qx-flex-middle">
      <div class="qx-flex qx-flex-middle">
        <div>
          <span class="qxuicon-thumbs-up qx-text-primary qx-margin-medium-right qx-margin-small-left" style="font-size: 50px;"></span>
        </div>
        <div>
          <h3>Rate us on Joomla Extension Directory(JED)</h3>
          <p>Your rating is our inspiration. If you like the product please leave a review on Joomla Extension Directory.</p>
        </div>
      </div>
      <div>
        <a
          class="qx-button qx-button-primary"
          href="https://extensions.joomla.org/extension/quix/" target="_blank"><span class="qxuicon-external-link qx-margin-small-right"></span>Joomla Extension Directory</a>
      </div>
    </div>
    <div class="qx-child-width-1-2@s" qx-grid>
      <div>
        <div class="qx-card qx-card-default qx-card-body">
          <?php echo $this->loadTemplate('req') ?>
        </div>
      </div>
      <div qx-margin>
        <!-- Documentation -->
        <div class="qx-card qx-card-default qx-card-small qx-card-body">
          <div class="qx-flex-middle" qx-grid>
            <div class="qx-width-expand">
              <div class="qx-flex-middle" qx-grid>
                <div><span class="qxuicon-book" style="font-size: 35px;"></span></div>
                <div>
                  <h3>Documentation</h3>
                  <p>Extensive Documenation for Quix</p>
                </div>
              </div>
            </div>
            <div class="qx-width-1-3">
              <a class="qx-button qx-button-primary qx-button-small" href="https://www.themexpert.com/docs" target="_blank">Documentation</a>
            </div>
          </div>
        </div>
        <!-- Community -->
        <div class="qx-card qx-card-default qx-card-small qx-card-body">
          <div class="qx-flex-middle" qx-grid>
            <div class="qx-width-expand">
              <div class="qx-flex-middle" qx-grid>
                <div><span class="qxuicon-users" style="font-size: 35px;"></span></div>
                <div>
                  <h3>Community</h3>
                  <p>Join our exclusive community.</p>
                </div>
              </div>
            </div>
            <div class="qx-width-1-3 qx-text-right">
              <a class="qx-button qx-button-primary qx-button-small" href="https://www.facebook.com/groups/QuixUserGroup/" target="_blank">Join Today</a>
            </div>
          </div>
        </div>
        <!-- Support -->
        <div class="qx-card qx-card-default qx-card-small qx-card-body">
          <div class="qx-flex-middle" qx-grid>
            <div class="qx-width-expand">
              <div class="qx-flex-middle" qx-grid>
                <div><span class="qxuicon-life-ring" style="font-size: 35px;"></span></div>
                <div>
                  <h3>Support</h3>
                  <p>Open a support ticket here</p>
                </div>
              </div>
            </div>
            <div class="qx-width-1-3 qx-text-right">
              <a class="qx-button qx-button-primary qx-button-small" href="https://www.themexpert.com/support" target="_blank">Get Support</a>
            </div>
          </div>
        </div>
      </div>
    </div>
<div>

  <?php echo QuixHelper::getFooterLayout(); ?>

