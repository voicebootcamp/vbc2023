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
// JHtml::_('formbehavior.chosen', 'select');
// JHtml::_('bootstrap.tooltip');



// Load user_profile plugin language
$lang = JFactory::getLanguage();
$lang->load('plg_user_profile', JPATH_ADMINISTRATOR);

$script = "jQuery(function() {
	const labels = document.querySelectorAll('label');	
	labels.forEach(function(label){
		label.classList.add('qx-form-label');
		label.removeAttribute('title');
		label.removeAttribute('data-content');
		label.removeAttribute('data-original-title');
	});	

	const inputTexts = document.querySelectorAll('input[type=\"text\"], input[type=\"password\"], input[type=\"email\"]');
	inputTexts.forEach(function(inputText){
		inputText.classList.add('qx-input');
	});

	const inputRadios = document.querySelectorAll('input[type=\"radio\"]');
	inputRadios.forEach(function(inputRadio) {
		inputRadio.classList.add('qx-radio');
	});

	const controls = document.querySelectorAll('.controls');
	controls.forEach(function(control) {
		control.classList.add('qx-form-controls')
	});
	
	const controlGroups = document.querySelectorAll('.control-group');
	controlGroups.forEach(function(controlGroup) {
		controlGroup.classList.add('qx-margin');
	});
	
	const selectFields = document.querySelectorAll('select');
	selectFields.forEach(function(selectField) {
		selectField.classList.add('qx-select');
	});	
	
	const fieldSets = document.querySelectorAll('fieldset');
	fieldSets.forEach(function(fieldSet) {
		fieldSet.classList.add('qx-fieldset');
	});

	const controlLabels = document.querySelectorAll('.control-label');
	controlLabels.forEach(function(controlLabel) {
		controlLabel.classList.add('qx-flex');
		controlLabel.classList.add('qx-flex-top');
		controlLabel.classList.add('qx-margin-small-bottom');
		const label = controlLabel.querySelector('label');
		label.classList.add('qx-margin-small-right');
		label.style.fontSize = '16px';
		label.style.marginBottom = '0';
	});

	const checkBoxes = document.querySelectorAll('input[type=\"checkbox\"]');
	checkBoxes.forEach(function(checkBox) {
		checkBox.classList.add('qx-checkbox');
	});

	const checkBoxLabels = document.querySelectorAll('#jform_actionlogs_actionlogsExtensions label.qx-form-label');
	checkBoxLabels.forEach(function(checkBoxLabel) {
		checkBoxLabel.style.display = 'inline-block';
	});
})";

JFactory::getDocument()->addScriptDeclaration($script);

JFactory::getDocument()->addStyleDeclaration("
	.label {
		display: flex;
		align-items: baseline;
	}
	#jform_actionlogs_actionlogsNotify input[type=\"radio\"] {
		float: left;
		margin-top: 2px;
		margin-right: 4px;
	}
	#jform_actionlogs_actionlogsNotify label {
		margin-bottom: 0;
		float: left;
		margin-right: 6px;
	}
");

?>
<div class="profile-edit<?php echo $this->pageclass_sfx; ?>">
	<?php if ($this->params->get('show_page_heading')) : ?>
		<h1 class="qx-heading-small qx-margin-remove-top">
			<?php echo $this->escape($this->params->get('page_heading')); ?>
		</h1>		
	<?php endif; ?>
	<script type="text/javascript">
		Joomla.twoFactorMethodChange = function(e)
		{
			var selectedPane = 'com_users_twofactor_' + jQuery('#jform_twofactor_method').val();

			jQuery.each(jQuery('#com_users_twofactor_forms_container>div'), function(i, el)
			{
				if (el.id != selectedPane)
				{
					jQuery('#' + el.id).hide(0);
				}
				else
				{
					jQuery('#' + el.id).show(0);
				}
			});
		};
	</script>
	<form id="member-profile" action="<?php echo JRoute::_('index.php?option=com_users&task=profile.save'); ?>" method="post" class="form-validate form-horizontal well qx-form-stacked" enctype="multipart/form-data">
		<?php // Iterate through the form fieldsets and display each one. ?>
		<?php foreach ($this->form->getFieldsets() as $group => $fieldset) : ?>
			<?php $fields = $this->form->getFieldset($group); ?>
			<?php if (count($fields)) : ?>
				<fieldset class="qx-fieldset">
					<?php // If the fieldset has a label set, display it as the legend. ?>
					<?php if (isset($fieldset->label)) : ?>
						<legend class="qx-margin-small-bottom qx-margin-small-top qx-text-bold">
							<?php echo JText::_($fieldset->label); ?>
						</legend>
					<?php endif; ?>
					<?php if (isset($fieldset->description) && trim($fieldset->description)) : ?>
						<p>
							<?php echo $this->escape(JText::_($fieldset->description)); ?>
						</p>
					<?php endif; ?>
					<?php // Iterate through the fields in the set and display them. ?>
					<?php foreach ($fields as $field) : ?>
						<?php // If the field is hidden, just display the input. ?>
						<?php if ($field->hidden) : ?>
							<?php echo $field->input; ?>
						<?php else : ?>
							<div class="control-group">
								<div class="label">
									<?php echo $field->label; ?>
									<?php if (!$field->required && $field->type !== 'Spacer') : ?>
										<span class="optional">
											<?php echo JText::_('COM_USERS_OPTIONAL'); ?>
										</span>
									<?php endif; ?>
								</div>
								<div class="controls">
									<?php if ($field->fieldname === 'password1') : ?>
										<?php // Disables autocomplete ?>
										<input type="password" style="display:none">
									<?php endif; ?>
									<?php echo $field->input; ?>
								</div>
							</div>
						<?php endif; ?>
					<?php endforeach; ?>
				</fieldset>
			<?php endif; ?>
		<?php endforeach; ?>
		<?php if (count($this->twofactormethods) > 1) : ?>
			<fieldset class="qx-fieldset">
				<legend class="qx-margin-small-bottom qx-margin-small-top qx-text-bold"><?php echo JText::_('COM_USERS_PROFILE_TWO_FACTOR_AUTH'); ?></legend>
				<div class="qx-margin">
					<div class="label">
						<label id="jform_twofactor_method-lbl" for="jform_twofactor_method" class="hasTooltip"
							title="<?php echo '<strong>' . JText::_('COM_USERS_PROFILE_TWOFACTOR_LABEL') . '</strong><br />' . JText::_('COM_USERS_PROFILE_TWOFACTOR_DESC'); ?>">
							<?php echo JText::_('COM_USERS_PROFILE_TWOFACTOR_LABEL'); ?>
						</label>
					</div>
					<div class="controls">
						<?php echo JHtml::_('select.genericlist', $this->twofactormethods, 'jform[twofactor][method]', array('onchange' => 'Joomla.twoFactorMethodChange()'), 'value', 'text', $this->otpConfig->method, 'jform_twofactor_method', false); ?>
					</div>
				</div>
				<div id="com_users_twofactor_forms_container">
					<?php foreach ($this->twofactorform as $form) : ?>
						<?php $style = $form['method'] == $this->otpConfig->method ? 'display: block' : 'display: none'; ?>
						<div id="com_users_twofactor_<?php echo $form['method']; ?>" style="<?php echo $style; ?>">
							<?php echo $form['form']; ?>
						</div>
					<?php endforeach; ?>
				</div>
			</fieldset>
			<fieldset class="qx-fieldset">
				<legend class="qx-margin-small-bottom qx-margin-small-top qx-text-bold">
					<?php echo JText::_('COM_USERS_PROFILE_OTEPS'); ?>
				</legend>
				<div class="qx-alert qx-alert-success">
					<?php echo JText::_('COM_USERS_PROFILE_OTEPS_DESC'); ?>
				</div>
				<?php if (empty($this->otpConfig->otep)) : ?>
					<div class="qx-alert qx-alert-warning">
						<?php echo JText::_('COM_USERS_PROFILE_OTEPS_WAIT_DESC'); ?>
					</div>
				<?php else : ?>
					<?php foreach ($this->otpConfig->otep as $otep) : ?>
						<span class="span3">
							<?php echo substr($otep, 0, 4); ?>-<?php echo substr($otep, 4, 4); ?>-<?php echo substr($otep, 8, 4); ?>-<?php echo substr($otep, 12, 4); ?>
						</span>
					<?php endforeach; ?>
					<div class="clearfix"></div>
				<?php endif; ?>
			</fieldset>
		<?php endif; ?>
		<div class="qx-margin">
			<div class="controls">
				<button type="submit" class="qx-button qx-button-primary validate">
					<?php echo JText::_('JSUBMIT'); ?>
				</button>
				<a class="qx-button qx-button-default" href="<?php echo JRoute::_('index.php?option=com_users&view=profile'); ?>" title="<?php echo JText::_('JCANCEL'); ?>">
					<?php echo JText::_('JCANCEL'); ?>
				</a>
				<input type="hidden" name="option" value="com_users" />
				<input type="hidden" name="task" value="profile.save" />
			</div>
		</div>
		<?php echo JHtml::_('form.token'); ?>
	</form>
</div>
