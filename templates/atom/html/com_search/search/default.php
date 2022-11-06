<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_search
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JFactory::getDocument()->addScriptDeclaration("
	jQuery(function() {
		const selectFields = document.querySelectorAll('select');
		selectFields.forEach(function(selectField) {
			selectField.classList.add('qx-select');
			selectField.classList.add('qx-margin-small-top');
		});

		const getRadioButtons = document.querySelectorAll('input[type=\"radio\"]');
		getRadioButtons.forEach(function(getRadioButton) {
			getRadioButton.classList.add('qx-radio');
			getRadioButton.classList.add('qx-margin-small-left');
			getRadioButton.classList.add('qx-margin-small-right');
		});

		const getCheckboxes = document.querySelectorAll('input[type=\"checkbox\"]');
		getCheckboxes.forEach(function(getCheckbox) {
			getCheckbox.classList.add('qx-checkbox');
			getCheckbox.classList.add('qx-margin-small-right');
			getCheckbox.classList.add('qx-margin-small-left');
		});
	});
");

?>
<div class="search<?php echo $this->pageclass_sfx; ?>">
	<?php if ($this->params->get('show_page_heading')) : ?>
		<h1 class="qx-heading-small qx-margin-remove-top">
			<?php if ($this->escape($this->params->get('page_heading'))) : ?>
				<?php echo $this->escape($this->params->get('page_heading')); ?>
			<?php else : ?>
				<?php echo $this->escape($this->params->get('page_title')); ?>
			<?php endif; ?>
		</h1>
	<?php endif; ?>
	<?php echo $this->loadTemplate('form'); ?>
	<?php if ($this->error == null && count($this->results) > 0) : ?>
		<?php echo $this->loadTemplate('results'); ?>
	<?php else : ?>
		<?php echo $this->loadTemplate('error'); ?>
	<?php endif; ?>
</div>
