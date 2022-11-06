<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_messages
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die; 
?>
<div class="alert clearfix">
	<a target="_blank" href="https://www.themexpert.com/quix-pagebuilder?utm_medium=button&utm_campaign=quix-pro&utm_source=joomla-admin" class="btn btn-danger pull-right" style="margin-top: 20px;">
	    <?php echo JText::_('COM_QUIX_GET_PRO_TITLE'); ?>
  	</a>
	<h4 class="alert-heading">
		<?php echo JText::_('COM_QUIX_FREE_NOTICE_TITLE'); ?>
	</h4>
	<p style="line-height: 1.4;">
		<?php echo JText::_('COM_QUIX_FREE_NOTICE_DESC'); ?>
	</p>
</div>
<p>
	<img 
		src="<?php echo JUri::root() . '/media/quix/images/elements/pro_elements.png' ?>" 
		style="width: 100%;margin: 0px -10px;"
	/>
</p>