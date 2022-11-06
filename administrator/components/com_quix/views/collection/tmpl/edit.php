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

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('formbehavior.chosen', 'select');
global $QuixBuilderType;
$QuixBuilderType = 'classic';

jimport('quix.app.init');
jimport('quix.app.bootstrap');

loadClassicBuilderAssets();
?>
<style type="text/css">
  @font-face {
    font-family: 'IcoMoon';
    src: url('../media/jui/fonts/IcoMoon.eot');
    src: url('../media/jui/fonts/IcoMoon.eot?#iefix') format('embedded-opentype'),
      url('../media/jui/fonts/IcoMoon.woff') format('woff'),
      url('../media/jui/fonts/IcoMoon.ttf') format('truetype'),
      url('../media/jui/fonts/IcoMoon.svg#IcoMoon') format('svg');
    font-weight: normal;
    font-style: normal;
  }
</style>
<script>
  jQuery(document).ready(function() {
    jQuery('#system-debug').remove();
  });
  setInterval(function() {
    var req = jQuery.ajax({
      type: "get",
      url: "index.php?option=com_quix&task=live"
    });
    req.done(function() {
      /*console.log("Request successful!");*/
    });
  }, 1000 * 60 * 5); // where X is your every X minutes
  jQuery(window).bind("load", function() {
    jQuery(".preloader").delay(100).fadeOut('slow');
  });
</script>
<div class="preloader">
  <div class="wrap">
    <div class="ball"></div>
    <div class="ball"></div>
    <div class="ball"></div>
    <div class="ball"></div>
    <p class="text">Loading...</p>
  </div>
</div>

<form
  action="<?php echo JRoute::_('index.php?option=com_quix&view=collection&type=' . $this->item->type . '&layout=edit&id=' . $this->item->id); ?>"
  method="post" enctype="multipart/form-data" name="adminForm" id="page-form" class="quix-layout-builder form-validate">
  <div class="qx-mainheader">
    <div class="container">
      <div class="row">
        <div class="col s6">
          <div class="form-group row">
            <div class="control-label col s1">
              <?php echo $this->form->getLabel('title'); ?>
            </div>
            <div class="col s8">
              <?php echo $this->form->getInput('title'); ?>
            </div>
          </div>
        </div>
        <div class="col s6">
          <div id="qx-toolbar"></div>
        </div>
      </div>
    </div>
  </div>

  <div class="qx-jappbar">
    <div class="container">
      <div class="row">
        <div class="col s12">
          <ul class="qx-appbar__navs tabs">
            <li class="tab active"><a href="#layout" data-toggle="tab"><?php echo JText::_('COM_QUIX_TITLE_PAGE_LAYOUT'); ?></a>
            </li>
            <!-- <li><a href="#metadata" data-toggle="tab"><?php echo JText::_('COM_QUIX_FORM_LBL_PAGE_METADATA'); ?></a>
            </li>
            <li><a href="#params" data-toggle="tab"><?php echo JText::_('COM_QUIX_FORM_LBL_PAGE_PARAMS'); ?></a>
            </li> -->
          </ul>
          <div id="qx-appbar"></div>
        </div>
      </div>
    </div>
  </div>

  <div id="system-message-container"></div>

  <div class="form-horizontal">

    <div class="tab-content" id="myTabContent">

      <div id="layout" class="tab-pane qx-jpane active">
        <div class="row">
          <div class="col s12 form-horizontal">
            <div class="app-mount">
              <div class="container">
                <div id='qx-mount'>

                </div>
              </div>
            </div>
            <?php echo $this->form->getInput('data'); ?>
          </div>
        </div>
      </div>

      <div id="metadata" class="tab-pane qx-jpane hide">
        <div class="container">
          <div class="row">
            <div class="col s12 form-horizontal">
              <?php echo $this->form->getControlGroup('metadata'); ?>
              <?php foreach ($this->form->getGroup('metadata') as $field) : ?>
              <?php echo $field->getControlGroup(); ?>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>

      <div id="params" class="tab-pane qx-jpane hide">
        <div class="container">
          <div class="row">
            <div class="col s12 form-horizontal">
              <fieldset class="adminform">

                <div class="control-group">
                  <div class="control-label">
                    <?php echo $this->form->getLabel('state'); ?>
                  </div>
                  <div class="controls">
                    <?php echo $this->form->getInput('state'); ?>
                  </div>
                </div>
                <div class="control-group">
                  <div class="control-label">
                    <?php echo $this->form->getLabel('access'); ?>
                  </div>
                  <div class="controls">
                    <?php echo $this->form->getInput('access'); ?>
                  </div>
                </div>
                <div class="control-group">
                  <div class="control-label">
                    <?php echo $this->form->getLabel('language'); ?>
                  </div>
                  <div class="controls">
                    <?php echo $this->form->getInput('language'); ?>
                  </div>
                </div>
              </fieldset>
            </div>
          </div>
        </div>
      </div>
      <div id="permissions" class="tab-pane hide">
        <?php if (JFactory::getUser()->authorise('core.admin', 'quix')) : ?>
        <?php echo $this->form->getInput('rules'); ?>
        <?php endif; ?>
      </div>


    </div>
  </div>

  <?php echo QuixHelper::getFooterLayout(); ?>

  <?php echo loadClassicBuilderReactScripts(); ?>

  <?php Assets::load(); ?>

  <input type="hidden" name="jform[id]" id="jform_id"
    value="<?php echo $this->item->id; ?>" />
  <input type="hidden" name="jform[ordering]"
    value="<?php echo $this->item->ordering; ?>" />
  <input type="hidden" name="jform[type]"
    value="<?php echo(!empty($this->item->type) ? $this->item->type : 'section'); ?>" />

  <?php if (empty($this->item->created_by)) { ?>
  <input type="hidden" name="jform[created_by]"
    value="<?php echo JFactory::getUser()->id; ?>" />
  <?php } else { ?>
  <input type="hidden" name="jform[created_by]"
    value="<?php echo $this->item->created_by; ?>" />
  <?php } ?>
  <input type="hidden" name="jform[checked_out]"
    value="<?php echo $this->item->checked_out; ?>" />
  <input type="hidden" name="jform[checked_out_time]"
    value="<?php echo $this->item->checked_out_time; ?>" />
  <input type="hidden" name="task" value="collection.apply" />
  <?php echo JHtml::_('form.token'); ?>
</form>
