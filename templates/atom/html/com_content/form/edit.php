<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// JHtml::_('behavior.tabstate');
JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidator');
// JHtml::_('formbehavior.chosen', '#jform_catid', null, array('disable_search_threshold' => 0));
// JHtml::_('formbehavior.chosen', '#jform_tags', null, array('placeholder_text_multiple' => JText::_('JGLOBAL_TYPE_OR_SELECT_SOME_TAGS')));
// JHtml::_('formbehavior.chosen', 'select');
$this->tab_name = 'com-content-form';
$this->ignore_fieldsets = array('image-intro', 'image-full', 'jmetadata', 'item_associations');

// Create shortcut to parameters.
$params = $this->state->get('params');

// This checks if the editor config options have ever been saved. If they haven't they will fall back to the original settings.
$editoroptions = isset($params->show_publishing_options);

if (!$editoroptions)
{
	$params->show_urls_images_frontend = '0';
}

JFactory::getDocument()->addStyleDeclaration("
#quix-switch-mode-button, .toggle-editor a.qx-button {
	background-color: transparent;
    color: #333;
	border: 1px solid #e5e5e5;
    padding: 0 30px;
    vertical-align: middle;
    font-size: .875rem;
	line-height: 38px;
	text-transform: uppercase;		
}
.toggle-editor a.qx-button {margin-top: 20px;}
");

JFactory::getDocument()->addScriptDeclaration("
	Joomla.submitbutton = function(task)
	{
		if (task == 'article.cancel' || document.formvalidator.isValid(document.getElementById('adminForm')))
		{
			" . $this->form->getField('articletext')->save() . "
			Joomla.submitform(task);
		}
	};

	jQuery(function() {
		const qxTabList = document.querySelector('.nav.nav-tabs');
		qxTabList.setAttribute('qx-tab', '');
		qxTabList.classList.remove('nav');
		qxTabList.classList.remove('nav-tabs');

		const qxTabContent = document.querySelector('.tab-content');
		qxTabContent.classList.add('qx-switcher');
		qxTabContent.classList.remove('tab-content');

		const inputFields = document.querySelectorAll('input');
		inputFields.forEach(function(inputField){
			inputField.classList.add('qx-input');
		});

		const controls = document.querySelectorAll('.controls');
		controls.forEach(function(control) {
			control.classList.add('qx-form-controls')
		});

		const labels = document.querySelectorAll('label');	
		labels.forEach(function(label){
			label.classList.add('qx-form-label');
			label.removeAttribute('title');
			label.removeAttribute('data-content');
			label.removeAttribute('data-original-title');
		});

		const buttons = document.querySelectorAll('.btn');
		buttons.forEach(function(button){
			button.classList.add('qx-button');
			button.classList.remove('btn');
		});

		const buttonsPrimary = document.querySelectorAll('.btn-primary');
		buttonsPrimary.forEach(function(primaryButton) {
			primaryButton.classList.add('qx-button-primary');
			primaryButton.classList.remove('btn-primary');
		});

		const buttonsDefault = document.querySelectorAll('.btn-default');
		buttonsDefault.forEach(function(defaultButton) {
			defaultButton.classList.add('qx-button-default');
			defaultButton.classList.remove('btn-default');
		});	

		const buttonsSecondary = document.querySelectorAll('.btn-secondary');
		buttonsSecondary.forEach(function(secondaryButton) {
			secondaryButton.classList.add('qx-button-secondary');
			secondaryButton.classList.remove('btn-secondary');
		});	

		const selectFields = document.querySelectorAll('select');
		selectFields.forEach(function(selectField) {
			selectField.classList.add('qx-select');
		});

		const controlGroups = document.querySelectorAll('.control-group');
		controlGroups.forEach(function(controlGroup) {
			controlGroup.classList.add('qx-margin');
		});

		const calendarIcons = document.querySelectorAll('.icon-calendar');
		calendarIcons.forEach(function(calendarIcon) {
			const calendarText = document.createTextNode('Add Date');
			const makeCalendarDiv = document.createElement('span');
			makeCalendarDiv.appendChild(calendarText);		
			calendarIcon.appendChild(makeCalendarDiv);
		});
    
    if(document.getElementById('jform_publish_up_btn')){
      document.getElementById('jform_publish_up_btn').style.width = '140px';
      document.getElementById('jform_publish_down_btn').style.width = '140px';
    }
		const calendarFields = document.querySelectorAll('.field-calendar > .input-append');
		calendarFields.forEach(function(calendarField) {
			calendarField.classList.add('qx-flex');
		});

		const textAreas = document.querySelectorAll('textarea');
		textAreas.forEach(function(textarea) {
			textarea.classList.add('qx-textarea');
		});
	});
	
");
?>
<div class="edit item-page<?php echo $this->pageclass_sfx; ?>">
	<?php if ($params->get('show_page_heading')) : ?>
	<h1 class="qx-heading-small qx-margin-remove-top">
		<?php echo $this->escape($params->get('page_heading')); ?>
	</h1>
	<?php endif; ?>

	<form action="<?php echo JRoute::_('index.php?option=com_content&a_id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate form-vertical qx-form-stacked">
		<fieldset class="qx-fieldset">			
			<?php echo JHtml::_('bootstrap.startTabSet', $this->tab_name, array('active' => 'editor')); ?>
			<?php echo JHtml::_('bootstrap.addTab', $this->tab_name, 'editor', JText::_('COM_CONTENT_ARTICLE_CONTENT')); ?>
				<?php echo $this->form->renderField('title'); ?>

				<?php if (is_null($this->item->id)) : ?>
					<?php echo $this->form->renderField('alias'); ?>
				<?php endif; ?>

				<?php echo $this->form->getInput('articletext'); ?>

				<?php if ($this->captchaEnabled) : ?>
					<?php echo $this->form->renderField('captcha'); ?>
				<?php endif; ?>
			<?php echo JHtml::_('bootstrap.endTab'); ?>

			<?php if ($params->get('show_urls_images_frontend')) : ?>
			<?php echo JHtml::_('bootstrap.addTab', $this->tab_name, 'images', JText::_('COM_CONTENT_IMAGES_AND_URLS')); ?>
				<?php echo $this->form->renderField('image_intro', 'images'); ?>
				<?php echo $this->form->renderField('image_intro_alt', 'images'); ?>
				<?php echo $this->form->renderField('image_intro_caption', 'images'); ?>
				<?php echo $this->form->renderField('float_intro', 'images'); ?>
				<?php echo $this->form->renderField('image_fulltext', 'images'); ?>
				<?php echo $this->form->renderField('image_fulltext_alt', 'images'); ?>
				<?php echo $this->form->renderField('image_fulltext_caption', 'images'); ?>
				<?php echo $this->form->renderField('float_fulltext', 'images'); ?>
				<?php echo $this->form->renderField('urla', 'urls'); ?>
				<?php echo $this->form->renderField('urlatext', 'urls'); ?>
				<div class="control-group">
					<div class="controls">
						<?php echo $this->form->getInput('targeta', 'urls'); ?>
					</div>
				</div>
				<?php echo $this->form->renderField('urlb', 'urls'); ?>
				<?php echo $this->form->renderField('urlbtext', 'urls'); ?>
				<div class="control-group">
					<div class="controls">
						<?php echo $this->form->getInput('targetb', 'urls'); ?>
					</div>
				</div>
				<?php echo $this->form->renderField('urlc', 'urls'); ?>
				<?php echo $this->form->renderField('urlctext', 'urls'); ?>
				<div class="control-group">
					<div class="controls">
						<?php echo $this->form->getInput('targetc', 'urls'); ?>
					</div>
				</div>
			<?php echo JHtml::_('bootstrap.endTab'); ?>
			<?php endif; ?>

			<?php echo JLayoutHelper::render('joomla.edit.params', $this); ?>

			<?php echo JHtml::_('bootstrap.addTab', $this->tab_name, 'publishing', JText::_('COM_CONTENT_PUBLISHING')); ?>
				<?php echo $this->form->renderField('catid'); ?>
				<?php echo $this->form->renderField('tags'); ?>
				<?php echo $this->form->renderField('note'); ?>
				<?php if ($params->get('save_history', 0)) : ?>
					<?php echo $this->form->renderField('version_note'); ?>
				<?php endif; ?>
				<?php if ($params->get('show_publishing_options', 1) == 1) : ?>
					<?php echo $this->form->renderField('created_by_alias'); ?>
				<?php endif; ?>
				<?php if ($this->item->params->get('access-change')) : ?>
					<?php echo $this->form->renderField('state'); ?>
					<?php echo $this->form->renderField('featured'); ?>
					<?php if ($params->get('show_publishing_options', 1) == 1) : ?>
						<?php echo $this->form->renderField('publish_up'); ?>
						<?php echo $this->form->renderField('publish_down'); ?>
					<?php endif; ?>
				<?php endif; ?>
				<?php echo $this->form->renderField('access'); ?>
				<?php if (is_null($this->item->id)) : ?>
					<div class="control-group">
						<div class="control-label">
						</div>
						<div class="controls">
							<?php echo JText::_('COM_CONTENT_ORDERING'); ?>
						</div>
					</div>
				<?php endif; ?>
			<?php echo JHtml::_('bootstrap.endTab'); ?>

			<?php echo JHtml::_('bootstrap.addTab', $this->tab_name, 'language', JText::_('JFIELD_LANGUAGE_LABEL')); ?>
				<?php echo $this->form->renderField('language'); ?>
			<?php echo JHtml::_('bootstrap.endTab'); ?>

			<?php if ($params->get('show_publishing_options', 1) == 1) : ?>
				<?php echo JHtml::_('bootstrap.addTab', $this->tab_name, 'metadata', JText::_('COM_CONTENT_METADATA')); ?>
					<?php echo $this->form->renderField('metadesc'); ?>
					<?php echo $this->form->renderField('metakey'); ?>
				<?php echo JHtml::_('bootstrap.endTab'); ?>
			<?php endif; ?>

			<?php //echo JHtml::_('bootstrap.endTabSet'); ?>

			<input type="hidden" name="task" value="" />
			<input type="hidden" name="return" value="<?php echo $this->return_page; ?>" />
			<?php echo JHtml::_('form.token'); ?>
		</fieldset>
		<div class="qx-margin">
				<button type="button" class="qx-button qx-button-primary" onclick="Joomla.submitbutton('article.save')">
					<span class="icon-ok"></span><?php echo JText::_('JSAVE') ?>
				</button>
				<button type="button" class="qx-button qx-button-default" onclick="Joomla.submitbutton('article.cancel')">
					<span class="icon-cancel"></span><?php echo JText::_('JCANCEL') ?>
				</button>
			<?php //if ($params->get('save_history', 0) && $this->item->id) : ?>
			<!--<div class="qx-margin">
				<?php //echo $this->form->getInput('contenthistory'); ?>
			</div>-->
			<?php //endif; ?>
		</div>
	</form>
</div>
