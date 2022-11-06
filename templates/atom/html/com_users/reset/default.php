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
<div class="reset<?php echo $this->pageclass_sfx; ?>">
	<?php if ($this->params->get('show_page_heading')) : ?>
		<h1 class="qx-heading-small qx-margin-remove-top">
			<?php echo $this->escape($this->params->get('page_heading')); ?>
		</h1>
	<?php endif; ?>
	<form id="user-registration" action="<?php echo JRoute::_('index.php?option=com_users&task=reset.request'); ?>" method="post" class="form-validate form-horizontal well qx-form-stacked">
		<?php foreach ($this->form->getFieldsets() as $fieldset) : ?>
			<fieldset class="qx-fieldset">
				<?php if (isset($fieldset->label)) : ?>
					<p><?php echo JText::_($fieldset->label); ?></p>
				<?php endif; ?>
				<?php //echo $this->form->renderFieldset($fieldset->name); ?>
				<div class="qx-margin">
					<div class="qx-form-controls">
						<label class="qx-form-label" ><?php echo JText::_('COM_USERS_FIELD_PASSWORD_RESET_LABEL'); ?></label>
						<input id="jform_email" type="text" name="jform[email]" class="qx-input" tabindex="0" size="18" />
					</div>
				</div>				
			</fieldset>
		<?php endforeach; ?>
		<div class="qx-margin">
			<div class="qx-form-controls">
				<button type="submit" class="qx-button qx-button-primary validate">
					<?php echo JText::_('JSUBMIT'); ?>
				</button>
			</div>
		</div>
		<?php echo JHtml::_('form.token'); ?>
	</form>
</div>
