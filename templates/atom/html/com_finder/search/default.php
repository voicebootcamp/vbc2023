<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_finder
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('behavior.core');
// JHtml::_('formbehavior.chosen');
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('stylesheet', 'com_finder/finder.css', array('version' => 'auto', 'relative' => true));

JFactory::getDocument()->addScriptDeclaration("
	jQuery(function() {
		const selectFields = document.querySelectorAll('select');
		selectFields.forEach(function(selectField) {
			selectField.classList.add('qx-select');
			selectField.classList.add('qx-margin-small-top');
		});
	});
");

?>
<div class="finder<?php echo $this->pageclass_sfx; ?>">
	<?php if ($this->params->get('show_page_heading')) : ?>
		<h1 class="qx-heading-small qx-margin-remove-top">
			<?php if ($this->escape($this->params->get('page_heading'))) : ?>
				<?php echo $this->escape($this->params->get('page_heading')); ?>
			<?php else : ?>
				<?php echo $this->escape($this->params->get('page_title')); ?>
			<?php endif; ?>
		</h1>
	<?php endif; ?>
	<?php if ($this->params->get('show_search_form', 1)) : ?>
		<div id="search-form">
			<?php echo $this->loadTemplate('form'); ?>
		</div>
	<?php endif; ?>
	<?php // Load the search results layout if we are performing a search. ?>
	<?php if ($this->query->search === true) : ?>
		<div id="search-results">
			<?php echo $this->loadTemplate('results'); ?>
		</div>
	<?php endif; ?>
</div>
