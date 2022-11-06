<?php
/**
 * @package    com_quix
 * @author     ThemeXpert <info@themexpert.com>
 * @copyright  Copyright (C) 2015. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    3.0.0
 */

// No direct access
defined('_JEXEC') or die;
?>
<div id="hidden-for-editor" style="display: none!important;">
    <?php //echo $this->form->renderField('editor'); ?>

    <?php
    $conf   = JFactory::getConfig();
    $editor = $conf->get('editor');
    if ($editor == 'jce') {
        require_once(JPATH_ADMINISTRATOR.'/components/com_jce/includes/base.php');

        wfimport('admin.models.editor');
        $editor   = new WFModelEditor();
        $app      = JFactory::getApplication();
        $settings = $editor->getEditorSettings();
        $app->triggerEvent('onBeforeWfEditorRender', array(&$settings));
        echo $editor->render($settings);
    } else {
        if(JFile::exists(JPATH_SITE . '/media/editors/tinymce/tinymce.min.js')){
            JFactory::getDocument()->addScript(JUri::root().'media/editors/tinymce/tinymce.min.js');
        }else{
            JFactory::getDocument()->addScript('https://cdnjs.cloudflare.com/ajax/libs/tinymce/5.6.2/tinymce.min.js');
        }
    }
    ?>


    <?php if (JFactory::getUser()->authorise('core.admin', 'quix')) : ?>
        <?php echo $this->form->getInput('rules'); ?>
    <?php endif; ?>

    <?php foreach ($this->form->getGroup('params') as $field) : ?>
        <?php echo $field->renderField(); ?>
    <?php endforeach; ?>

    <?php foreach ($this->form->getGroup('metadata') as $field) : ?>
        <?php echo $field->renderField(); ?>
    <?php endforeach; ?>

    <?php echo $this->form->getInput('menutype'); ?>
    <?php echo $this->form->getInput('templatestyle'); ?>
    <?php echo $this->form->getInput('conditions'); ?>
    <?php echo $this->form->getInput('state'); ?>
    <?php echo $this->form->getInput('access'); ?>
    <?php echo $this->form->getInput('language'); ?>
</div>
