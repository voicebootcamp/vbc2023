<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_finder
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

if ($this->params->get('show_advanced', 1) || $this->params->get('show_autosuggest', 1))
{
	JHtml::_('jquery.framework');

	$script = "
jQuery(function() {";

	if ($this->params->get('show_advanced', 1))
	{
		/*
		* This segment of code disables select boxes that have no value when the
		* form is submitted so that the URL doesn't get blown up with null values.
		*/
		$script .= "
	jQuery('#finder-search').on('submit', function(e){
		e.stopPropagation();
		// Disable select boxes with no value selected.
		jQuery('#advancedSearch').find('select').each(function(index, el) {
			var el = jQuery(el);
			if(!el.val()){
				el.attr('disabled', 'disabled');
			}
		});
	});";
	}

	/*
	* This segment of code sets up the autocompleter.
	*/
	if ($this->params->get('show_autosuggest', 1))
	{
		JHtml::_('script', 'jui/jquery.autocomplete.min.js', array('version' => 'auto', 'relative' => true));

		$script .= "
	var suggest = jQuery('#q').autocomplete({
		serviceUrl: '" . JRoute::_('index.php?option=com_finder&task=suggestions.suggest&format=json&tmpl=component') . "',
		paramName: 'q',
		minChars: 1,
		maxHeight: 400,
		width: 300,
		zIndex: 9999,
		deferRequestBy: 500
	});";
	}

	$script .= "
});";

	JFactory::getDocument()->addScriptDeclaration($script);
}
JFactory::getDocument()->addStyleDeclaration('
form#finder-search fieldset.word {border:0;}
');

?>
<form id="finder-search" action="<?php echo JRoute::_($this->query->toUri()); ?>" method="get" class="form-inline">
	<?php echo $this->getFields(); ?>
	<?php // DISABLED UNTIL WEIRD VALUES CAN BE TRACKED DOWN. ?>
	<?php if (false && $this->state->get('list.ordering') !== 'relevance_dsc') : ?>
		<input type="hidden" name="o" value="<?php echo $this->escape($this->state->get('list.ordering')); ?>" />
	<?php endif; ?>
	<fieldset class="word qx-padding-remove-left">
		<div class="qx-grid-small" qx-grid>
			<div class="qx-width-expand@s">
				<input type="text" name="q" id="q" size="30" value="<?php echo $this->escape($this->query->input); ?>" class="qx-input" />
			</div>
			<div class="qx-width-auto@s">
				<div class="qx-grid-small" qx-grid>
				<?php if ($this->escape($this->query->input) != '' || $this->params->get('allow_empty_query')) : ?>
					<div class="qx-width-auto@s">
						<button name="Search" type="submit" class="qx-button qx-button-primary qx-width-1-1">
							<span class="icon-search icon-white"></span>
							<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>
						</button>
					</div>
				<?php else : ?>
					<div class="qx-width-auto@s">
						<button name="Search" type="submit" class="qx-button qx-button-primary qx-width-1-1 disabled">
							<span class="icon-search icon-white"></span>
							<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>
						</button>
					</div>
				<?php endif; ?>
				<?php if ($this->params->get('show_advanced', 1)) : ?>
					<div class="qx-width-auto@s">
						<a href="#advancedSearch" qx-toggle="target: #advancedSearch" class="qx-button qx-button-default qx-width-1-1" qx-toggle>
							<span class="icon-list" aria-hidden="true"></span>
							<?php echo JText::_('COM_FINDER_ADVANCED_SEARCH_TOGGLE'); ?>
						</a>
					</div>				
				<?php endif; ?>
				</div>
			</div>
		</div>
	</fieldset>
	<?php if ($this->params->get('show_advanced', 1)) : ?>
		<div id="advancedSearch" class="qx-margin-medium" <?php if (!$this->params->get('expand_advanced', 0)) echo ' hidden'; ?>>
			<?php if ($this->params->get('show_advanced_tips', 1)) : ?>
				<?php echo JText::_('COM_FINDER_ADVANCED_TIPS'); ?>
			<?php endif; ?>
			<div id="finder-filter-window" style="display:flow-root;">
				<?php echo JHtml::_('filter.select', $this->query, $this->params); ?>
			</div>
		</div>
	<?php endif; ?>
</form>
