<?php
/* ======================================================
 # Limit Active Logins for Joomla! - v1.1.2 (pro version)
 # -------------------------------------------------------
 # For Joomla! CMS (v4.x)
 # Author: Web357 (Yiannis Christodoulou)
 # Copyright (©) 2014-2022 Web357. All rights reserved.
 # License: GNU/GPLv3, http://www.gnu.org/licenses/gpl-3.0.html
 # Website: https:/www.web357.com
 # Demo: https://limitactivelogins.web357.com/
 # Support: support@web357.com
 # Last modified: Thursday 31 March 2022, 12:05:22 PM
 ========================================================= */

// No direct access
defined('_JEXEC') or die;

use \Joomla\CMS\HTML\HTMLHelper;
use \Joomla\CMS\Factory;
use \Joomla\CMS\Uri\Uri;
use \Joomla\CMS\Router\Route;
use \Joomla\CMS\Layout\LayoutHelper;
use \Joomla\CMS\Language\Text;

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/');
HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.multiselect');

if (version_compare(JVERSION, '4.0', 'lt')) {
	JHtml::_('formbehavior.chosen', 'select');
	JHTML::_('behavior.modal');
}

$user      = Factory::getUser();
$userId    = $user->get('id');
$listOrder = $this->state->get('list.ordering');
$listDirn  = $this->state->get('list.direction');
$canOrder  = $user->authorise('core.edit.state', 'com_limitactivelogins');

JText::script('COM_LIMITACTIVELOGINS_DELETE_CONFIRMATION_MSG');
?>

<script type="text/javascript">
js = jQuery.noConflict();
js(document).ready(function ($) {
		
	Joomla.submitbutton = function(task, session_id, userid, user_agent, ip_address)
	{
		var form = document.getElementById('adminForm');
		if (task == 'log.deleteSessionAndLogoutTheUser')
		{
			$("input[name='jform[session_id]']").val(session_id);
			$("input[name='jform[userid]']").val(userid);
			$("input[name='jform[user_agent]']").val(user_agent);
			$("input[name='jform[ip_address]']").val(ip_address);

			Joomla.submitform(task, form);
		}
		else if (task == 'logs.delete')
		{
			if (confirm(Joomla.JText._("COM_LIMITACTIVELOGINS_DELETE_CONFIRMATION_MSG")))
			{
				$("input[name='jform[session_id]']").val(session_id);
				$("input[name='jform[userid]']").val(userid);
				$("input[name='jform[user_agent]']").val(user_agent);
				$("input[name='jform[ip_address]']").val(ip_address);
				Joomla.submitform(task, form);
			}
			else
			{
				return false;
			}
		}
		else
		{
			Joomla.submitform(task);
		}
	};
});
</script>

<form action="<?php echo Route::_('index.php?option=com_limitactivelogins&view=logs'); ?>" method="post" class="form-validate" 	name="adminForm" id="adminForm">

	<input type="hidden" name="jform[session_id]" value="" />
	<input type="hidden" name="jform[userid]" value="" />
	<input type="hidden" name="jform[user_agent]" value="" />
	<input type="hidden" name="jform[ip_address]" value="" />

	<?php if(!empty($this->sidebar) && version_compare(JVERSION, '4.0', 'lt')): ?>
		<div id="j-sidebar-container" class="span2 col-2">
			<?php echo $this->sidebar; ?>
		</div>
		<div id="j-main-container" class="span10 col-10">
	<?php else: ?>
		<div id="j-main-container">
	<?php endif; ?>

			<?php echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>

			<div class="clearfix"></div>
			<table class="table table-striped" id="logList">
				<thead>
					<tr>
						<th width="1%"><input type="checkbox" name="checkall-toggle" value="" title="<?php echo Text::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" /></th>
						<th><?php echo JHtml::_('searchtools.sort',  'Session ID', 'a.`id`', $listDirn, $listOrder); ?></th>
						<th><?php echo JHtml::_('searchtools.sort',  'Username', 'a.`username`', $listDirn, $listOrder); ?></th>
						<th><?php echo JHtml::_('searchtools.sort',  'COM_LIMITACTIVELOGINS_LOGS_IP_ADDRESS', 'a.`ip_address`', $listDirn, $listOrder); ?></th>
						<th><?php echo JHtml::_('searchtools.sort',  'COM_LIMITACTIVELOGINS_LOGS_COUNTRY', 'a.`country`', $listDirn, $listOrder); ?></th>
						<th><?php echo JHtml::_('searchtools.sort',  'COM_LIMITACTIVELOGINS_LOGS_USER_AGENT', 'a.`user_agent`', $listDirn, $listOrder); ?></th>
						<th><?php echo JHtml::_('searchtools.sort',  'COM_LIMITACTIVELOGINS_LOGS_BROWSER', 'a.`browser`', $listDirn, $listOrder); ?></th>
						<th><?php echo JHtml::_('searchtools.sort',  'COM_LIMITACTIVELOGINS_LOGS_OPERATING_SYSTEM', 'a.`operating_system`', $listDirn, $listOrder); ?></th>
						<th><?php echo JHtml::_('searchtools.sort',  'COM_LIMITACTIVELOGINS_LOGS_DATETIME', 'a.`datetime`', $listDirn, $listOrder); ?></th>
						
						<th class='left'>
							<?php echo JText::_('COM_LIMITACTIVELOGINS_LOGS_LOGOUT_THE_USERS'); ?>
						</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="<?php echo isset($this->items[0]) ? count(get_object_vars($this->items[0])) : 10; ?>">
							<?php echo $this->pagination->getListFooter(); ?>
						</td>
					</tr>
				</tfoot>
				<tbody>
					<?php 
					foreach ($this->items as $i => $item) :
					$ordering   = ($listOrder == 'a.ordering');
					$canCreate  = $user->authorise('core.create', 'com_limitactivelogins');
					$canEdit    = $user->authorise('core.edit', 'com_limitactivelogins');
					$canCheckin = $user->authorise('core.manage', 'com_limitactivelogins');
					$canChange  = $user->authorise('core.edit.state', 'com_limitactivelogins');
					?>
					<tr class="row<?php echo $i % 2; ?>">
						<td><?php echo HTMLHelper::_('grid.id', $i, $item->id); ?></td>
						
						<td>
							<?php if ($canEdit): ?>
								<?php if (isset($item->checked_out) && $item->checked_out && ($canEdit || $canChange)) : ?>
									<?php echo JHtml::_('jgrid.checkedout', $i, $item->uEditor, $item->checked_out_time, 'logs.', $canCheckin); ?>
								<?php endif; ?>
								<a href="<?php echo JRoute::_('index.php?option=com_limitactivelogins&task=log.edit&id='.(int) $item->id); ?>">
									<?php echo $item->id; ?>
								</a>
							<?php else: ?>
								<?php echo $item->id; ?>
							<?php endif; ?>
						</td>

						<td>
							
							<?php if ($this->showGravatar): ?>
								<img class="w357-gravatar hasTooltip" src="<?php echo LimitactiveloginsHelper::get_gravatar($item->user_email); ?>" alt="<?php echo $this->escape($item->user_fullname); ?>" title="<?php echo JHtml::_('tooltipText', $this->escape('User ID: '.$item->userid.'. Full name: '.$item->user_fullname)); ?>">
							<?php endif; ?>
							
							<span class="hasTooltip" title="<?php echo JHtml::_('tooltipText', $this->escape('User ID: '.$item->userid.'. Full name: '.$item->user_fullname)); ?>">
								<?php echo $item->username; ?>
							</span>
						</td>
						<td><?php echo $item->ip_address; ?></td>
						<td><?php echo $item->country; ?></td>
						<td>
							<?php if(strlen($item->user_agent) > 25): ?>
								<span class="hasTooltip" title="<?php echo JHtml::_('tooltipText', $this->escape($item->user_agent)); ?>">
									<?php echo trim(substr($this->escape($item->user_agent), 0, 25)) . '&hellip;'; ?>
								</span>
							<?php else: ?>
								<?php echo $this->escape($item->user_agent); ?>
							<?php endif; ?>
						</td>

						<td><?php echo ($item->browser != 'Unknown (?)' ? $item->browser : '--unknown--'); ?></td>

						<td><?php echo ($item->operating_system != 'Unknown' ? $item->operating_system : '--unknown--'); ?></td>

						<td><?php echo JHtml::_('date', $item->datetime, JText::_('d M Y, H:i:s')); ?></td>

						<td class="left">
							<?php if ($this->session->getId() === $item->session_id): ?>
							<p style="color: orange;">Current session (It's you)</p>
							<?php else: ?>
								<div class="btn-group btn-group-sm" role="group" aria-label="">
									<button class="btn btn-small btn-danger" onclick="javascript:if(confirm('Are you sure to delete this session? The user `<?php echo $item->username; ?>` will be logged out from this device.')){Joomla.submitbutton('log.deleteSessionAndLogoutTheUser', '<?php echo $this->escape($item->session_id); ?>', <?php echo $this->escape($item->userid); ?>, '<?php echo $this->escape($item->user_agent); ?>', '<?php echo $this->escape($item->ip_address); ?>');}; return false;"><?php echo Text::_('Force Logout'); ?></button>
								</a>
								</div>
							<?php endif; ?>
						</td>

					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>

			<input type="hidden" name="task" value="" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="list[fullorder]" value="<?php echo $listOrder; ?> <?php echo $listDirn; ?>" />
			<?php echo HTMLHelper::_('form.token'); ?>

			<?php echo Web357Framework\Functions::showFooter("com_limitactivelogins", JText::_('COM_LIMITACTIVELOGINS_CLEAN')); ?>
			
		</div>
</form>

<script>
	window.toggleField = function (id, task, field) {

		var f = document.adminForm,
			i = 0,
			cbx, cb = f[id];

		if (!cb) return false;

		while (true) {
			cbx = f['cb' + i];

			if (!cbx) break;

			cbx.checked = false;
			i++;
		}

		var inputField = document.createElement('input');

		inputField.type = 'hidden';
		inputField.name = 'field';
		inputField.value = field;
		f.appendChild(inputField);

		cb.checked = true;
		f.boxchecked.value = 1;
		window.submitform(task);

		return false;
	};
</script>