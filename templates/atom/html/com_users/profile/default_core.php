<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

?>
<fieldset id="users-profile-core" class="qx-fieldset qx-margin">
	<legend>
		<h4 class="qx-h4 qx-text-bold qx-margin-remove-bottom"><?php echo JText::_('COM_USERS_PROFILE_CORE_LEGEND'); ?></h4>
	</legend>
	<div class="qx-margin">
		<div class="qx-margin-small">
			<label class="qx-text-bold"><?php echo JText::_('COM_USERS_PROFILE_NAME_LABEL'); ?>:</label>
			<?php echo $this->escape($this->data->name); ?>
		</div>
		<div class="qx-margin-small">
			<label class="qx-text-bold"><?php echo JText::_('COM_USERS_PROFILE_USERNAME_LABEL'); ?>:</label>
			<?php echo $this->escape($this->data->username); ?>
		</div>
		<div class="qx-margin-small">
			<label class="qx-text-bold"><?php echo JText::_('COM_USERS_PROFILE_REGISTERED_DATE_LABEL'); ?>:</label>
			<?php echo JHtml::_('date', $this->data->registerDate, JText::_('DATE_FORMAT_LC1')); ?>
		</div>
		<?php if ($this->data->lastvisitDate != $this->db->getNullDate()) : ?>
			<div class="qx-margin-small">
				<label class="qx-text-bold"><?php echo JText::_('COM_USERS_PROFILE_LAST_VISITED_DATE_LABEL'); ?>:</label>
				<?php echo JHtml::_('date', $this->data->lastvisitDate, JText::_('DATE_FORMAT_LC1')); ?>
			</div>
		<?php else : ?>
			<div class="qx-margin-small">
				<?php echo JText::_('COM_USERS_PROFILE_NEVER_VISITED'); ?>
			</div>
		<?php endif; ?>
	</div>
</fieldset>
