<?php
/**
 * @package     Quix
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @version     3.0.0
 */

defined('_JEXEC') or die;
// Load toolbar
$layout = new JLayoutFile('blocks.toolbar');
echo $layout->render(['active' => '']);
?>
<div class="quix qx-container qx-margin-medium-top qx-text-small">
  <form action="<?php
  echo JRoute::_('index.php?option=com_quix'); ?>" method="post" name="adminForm" id="adminForm"
        class="form-validate">

    <div class="qx-child-width-1-4 qx-grid-small" qx-grid>
      <?php
      $fieldSets = $this->form->getFieldsets();
      foreach ($fieldSets as $name => $fieldSet) :
        foreach ($this->form->getFieldset($name) as $field) :
          $name = str_replace('enable_', '', $field->fieldname);
          if ($name === 'custom_context') {
            continue;
          }
          ?>

          <div>
            <div class="qx-card qx-card-default qx-card-body qx-card-small qx-flex qx-flex-middle qx-flex-between">
              <div class="qx-flex qx-flex-middle">
                <img src="<?php
                echo JUri::root() ?>/media/quixnxt/images/integrations/<?php
                echo $name; ?>.png"
                     alt="<?php
                     echo $name; ?>" width="22">
                <h4 class="qx-margin-small-left">
                  <?php
                  echo JText::_(strtoupper('COM_QUIX_CONFIG_' . $field->fieldname . '_LABEL')); ?>
                </h4>
              </div>
              <div class="switch">
                <label>
                  <input
                    id="jform_<?php echo $field->fieldname; ?>" class="toggleIntegration"
                    name="<?php echo $field->name; ?>"
                    data-element-slug="<?php echo $name; ?>" type="checkbox"
                    <?php echo $field->value ? 'checked' : '' ?>
                  />
                  <span class="lever"></span>
                </label>
              </div>
            </div>
          </div>
        <?php
        endforeach; ?>
      <?php
      endforeach; ?>
    </div>

    <div id="common_groups" class="qx-margin-large-top qx-card qx-card-default qx-card-body qx-card-small">
      <div class="qx-flex qx-flex-middle qx-flex-between">

      <?php
      $fieldSets = $this->form->getFieldsets();
      foreach ($fieldSets as $name => $fieldSet) :
        foreach ($this->form->getFieldset($name) as $field) :
          $name = str_replace('enable_', '', $field->fieldname);
          if ($name !== 'custom_context') {
            continue;
          }
          ?>
          <?php echo $field->renderField(); ?>
        <?php
        endforeach;
      endforeach;
      ?>
      <button id="customIntegrationSave" class="qx-button qx-button-primary"><?php echo JText::_('JAPPLY'); ?></button>
      </div>

      <div class="qx-alert qx-alert-primary qx-flex qx-flex-between qx-flex-middle qx-margin-medium-top">
        <?php
        echo JText::_('COM_QUIX_CONFIG_COMPONENT_SUPPORT_BANNER_LABEL'); ?>
      </div>
    </div>



    <?php
    echo QuixHelper::getFooterLayout(); ?>

    <input type="hidden" name="task" value=""/>
    <?php
    echo JHtml::_('form.token'); ?>
</div>
</form>
</div>
