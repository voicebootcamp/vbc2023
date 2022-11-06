<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidator');

?>
<div class="registration<?php echo $this->pageclass_sfx; ?>">
	<?php if ($this->params->get('show_page_heading')) : ?>
		<h1 class="qx-heading-small qx-margin-remove-top"><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
	<?php endif; ?>
	<form id="member-registration" action="<?php echo JRoute::_('index.php?option=com_users&task=registration.register'); ?>" method="post" class="form-validate form-horizontal well qx-form-stacked" enctype="multipart/form-data">
		<?php // Iterate through the form fieldsets and display each one. ?>
		<?php foreach ($this->form->getFieldsets() as $fieldset) : ?>
			<?php $fields = $this->form->getFieldset($fieldset->name); ?>
			<?php if (count($fields)) : ?>
				<fieldset class="qx-fieldset">
					<?php // If the fieldset has a label set, display it as the legend. ?>
					<?php if (isset($fieldset->label)) : ?>
						<legend><?php echo JText::_($fieldset->label); ?></legend>
					<?php endif; ?>
					<div class="qx-margin">
						<div class="qx-form-controls">
							<label class="qx-form-label" ><?php echo JText::_('COM_USERS_REGISTER_REQUIRED'); ?></label>
						</div>
					</div>
					<div class="qx-margin">
						<div class="qx-form-controls">
							<label class="qx-form-label" ><?php echo JText::_('COM_USERS_REGISTER_NAME_LABEL'); ?><span class="star">&nbsp;*</span></label>
							<input id="jform_name" type="text" name="jform[name]" class="qx-input" tabindex="0" size="18" />
						</div>
					</div>
					<div class="qx-margin">
						<div class="qx-form-controls">
							<label class="qx-form-label" ><?php echo JText::_('COM_USERS_REGISTER_USERNAME_LABEL'); ?><span class="star">&nbsp;*</span></label>
							<input id="jform_username" type="text" name="jform[username]" class="qx-input" tabindex="0" size="18" />
						</div>
					</div>
					<div class="qx-margin">
						<div class="qx-form-controls">
							<label class="qx-form-label" ><?php echo JText::_('COM_USERS_PROFILE_PASSWORD1_LABEL'); ?><span class="star">&nbsp;*</span></label>
							<input id="jform_password1" type="password" name="jform[password1]" class="qx-input" tabindex="0" size="18" />
						</div>
					</div>
					<div class="qx-margin">
						<div class="qx-form-controls">
							<label class="qx-form-label" ><?php echo JText::_('COM_USERS_PROFILE_PASSWORD2_LABEL'); ?><span class="star">&nbsp;*</span></label>
							<input id="jform_password2" type="password" name="jform[password2]" class="qx-input" tabindex="0" size="18" />
						</div>
					</div>
					<div class="qx-margin">
						<div class="qx-form-controls">
							<label class="qx-form-label" ><?php echo JText::_('COM_USERS_FIELD_REMIND_EMAIL_LABEL'); ?><span class="star">&nbsp;*</span></label>
							<input id="jform_email1" type="email" name="jform[email1]" class="qx-input" tabindex="0" size="18" />
						</div>
					</div>
					<div class="qx-margin">
						<div class="qx-form-controls">
							<label class="qx-form-label" ><?php echo JText::_('COM_USERS_REGISTER_EMAIL2_LABEL'); ?><span class="star">&nbsp;*</span></label>
							<input id="jform_email2" type="email" name="jform[email2]" class="qx-input" tabindex="0" size="18" />
						</div>
					</div>
					<?php //echo $this->form->renderFieldset($fieldset->name); ?>										
				</fieldset>
			<?php endif; ?>
		<?php endforeach; ?>
		<div class="qx-margin">
			<div class="qx-form-controls">
				<button type="submit" class="qx-button qx-button-primary validate">
					<?php echo JText::_('JREGISTER'); ?>
				</button>
				<a class="qx-button qx-button-default" href="<?php echo JRoute::_(''); ?>" title="<?php echo JText::_('JCANCEL'); ?>">
					<?php echo JText::_('JCANCEL'); ?>
				</a>
				<input type="hidden" name="option" value="com_users" />
				<input type="hidden" name="task" value="registration.register" />
			</div>
		</div>
		<?php echo JHtml::_('form.token'); ?>
	</form>
</div>
