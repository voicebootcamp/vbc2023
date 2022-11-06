<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$canEdit = $displayData['params']->get('access-edit');

?>

<ul class="qx-subnav qx-margin-medium-top">
	<?php if (empty($displayData['print'])) : ?>

		<?php if ($canEdit || $displayData['params']->get('show_print_icon') || $displayData['params']->get('show_email_icon')) : ?>
			<?php if ($displayData['params']->get('show_print_icon')) : ?>
				<li><?php echo JHtml::_('icon.print_popup', $displayData['item'], $displayData['params']); ?></li>
			<?php endif; ?>
			<?php if ($displayData['params']->get('show_email_icon')) : ?>
				<li><?php echo JHtml::_('icon.email', $displayData['item'], $displayData['params']); ?></li>
			<?php endif; ?>
			<?php if ($canEdit) : ?>
				<li><?php echo JHtml::_('icon.edit', $displayData['item'], $displayData['params']); ?></li>
			<?php endif; ?>
		<?php endif; ?>

	<?php else : ?>
		<div class="qx-align-right">
			<?php echo JHtml::_('icon.print_screen', $displayData['item'], $displayData['params']); ?>
		</div>

	<?php endif; ?>
</ul>
