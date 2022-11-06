<?php
/**
* @version    1.0.0
* @package    com_quix
* @author     ThemeXpert <info@themexpert.com>
* @copyright  Copyright (C) 2015. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/
    // No direct access
defined('_JEXEC') or die;
?>

<div class="quix qx-modal-full" id="new-template" qx-modal>
  <div class="qx-modal-dialog">

    <button class="qx-modal-close-full qx-close-large" type="button" qx-close></button>
    <div class="qx-grid-collapse qx-child-width-1-2@s qx-flex-middle" qx-grid>
        <div class="qx-background-cover" style="background-image: url('//images.unsplash.com/photo-1500462918059-b1a0cb512f1d?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=668&q=80');" qx-height-viewport></div>
        <div class="qx-padding-large">
            <h1>Create Template</h1>
            <form action="<?php echo JRoute::_('index.php?option=com_quix&view=collections&format=json'); ?>" method="post" name="templateForm"
              id="templateForm" class="qx-margin-medium-top">

              <div class="qx-margin">
                <label for="templateName" class="qx-form-label">Template Name*</label>
                <div class="qx-form-controls">
                  <input require type="text" name="jform[title]" class="qx-input required" id="templateName" placeholder="Template name" required>
                </div>
              </div>

              <div class="qx-margin">
                <label for="templateType" class="qx-form-label">Template Type</label>
                <div class="qx-form-controls qx-width-expand">
                  <select require id="templateType" name="jform[type]" class="qx-select" required>
                    <option value="layout">Layout / Section</option>
<!--                    <option value="section" selected>Single Section</option>-->
                    <!-- <option value="article">Single Article Layout</option> -->
                    <option value="header">Header</option>
                    <option value="footer">Footer</option>
                  </select>
                </div>
              </div>

              <p class="working qx-text-meta" style="display: none;">
                Creating your template...
              </p>
              <p class="qx-alert qx-alert-danger" style="display: none;">
                Something went wrong! can't create templates!
              </p>
              <p class="qx-alert qx-alert-success" style="display: none;">
                Template created and taking you to the builder
              </p>

              <button type="submit" class="qx-button qx-button-primary">
                Create Template<span class="qxuicon-long-arrow-right qx-margin-small-left"></span>
              </button>

              <div class="qx-alert qx-margin-medium-top">
                <h3 class="qx-margin-remove-top"><span class="qxuicon-info-circle qx-margin-small-right"></span>Template Type</h3>
                <p><strong>Single Section:</strong> Create single re-useable section. You can't create more than one section.</p>
                <p><strong>Page Layout:</strong> Re-useable full page layout contain multiple section.</p>
                <p><strong>Header:</strong> Create global and page specific header with display condition.</p>
                <p><strong>Footer:</strong> Create global and page specific footer with display condition.</p>
              </div>

              <input type="hidden" name="jform[state]" value="1" />
              <input type="hidden" name="jform[builder]" value="frontend" />
              <input type="hidden" name="jform[builder_version]" value="<?php echo QUIXNXT_VERSION ?>" />
              <input type="hidden" name="jform[data]" value="[]" />
              <input type="hidden" name="release" value="true" />
              <input type="hidden" name="task" value="collection.apply" />
              <?php echo JHtml::_('form.token'); ?>
            </form>
        </div>
    </div>
  </div>
</div>
