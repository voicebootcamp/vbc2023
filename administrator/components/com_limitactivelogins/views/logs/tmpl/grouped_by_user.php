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

<form action="<?php echo Route::_('index.php?option=com_limitactivelogins&view=logs&layout=grouped_by_user'); ?>" method="post" class="form-validate" 	name="adminForm" id="adminForm">

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
						<th><?php echo Text::_('User ID'); ?></th>
						<th><?php echo Text::_('Username'); ?></th>
						<th><?php echo Text::_('Name'); ?></th>
						<th><?php echo Text::_('Email'); ?></th>
						<th><?php echo Text::_('Logged in devices'); ?></th>
						<th><?php echo Text::_('Last visit datetime'); ?></th>
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
					$grouped_items_by_user = [];
					foreach ($this->items as $i => $item)
					{
						$grouped_items_by_user[$item->userid][] = $item;
					}

					foreach ($grouped_items_by_user as $user_id => $sessions):
					$logged_in_user = Factory::getUser($user_id);
					$canChange  = $user->authorise('core.edit.state', 'com_limitactivelogins');
					?>
					<tr>
						<td>
							<?php echo $logged_in_user->id; ?>
						</td>
						<td>
							
							<?php if ($this->showGravatar): ?>
								<img class="w357-gravatar" src="<?php echo LimitactiveloginsHelper::get_gravatar($logged_in_user->email); ?>" alt="<?php echo $this->escape($logged_in_user->username); ?>">
							<?php endif; ?>
							
							<span>
								<?php echo $logged_in_user->username; ?>
							</span>
						</td>
						<td>
							<?php echo $logged_in_user->name; ?>
						</td>
						<td>
							<?php echo $logged_in_user->email; ?>
						</td>
						<td>
							<a href="index.php?option=com_limitactivelogins&view=logs&filter[search]=userid:<?php echo $logged_in_user->id; ?>">
								<span class="badge bg-<?php echo (count($sessions) > 1 ? 'warning text-dark' : 'success'); ?> badge-<?php echo (count($sessions) > 1 ? 'warning' : 'success'); ?> hasTooltip" title="<?php echo JHtml::_('tooltipText', 'The User is currently logged in '.count($sessions).' device'.(count($sessions) > 1 ? 's' : '').'.'); ?>">
									<?php echo count($sessions); ?>
								</span>
							</a>
						</td>
						<td>
							<?php echo JHtml::_('date', $sessions[0]->datetime, JText::_('d M Y, H:i:s')); ?>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>

			<input type="hidden" name="task" value="" />
			<input type="hidden" name="boxchecked" value="0" />
			<?php echo HTMLHelper::_('form.token'); ?>

			<?php echo Web357Framework\Functions::showFooter("com_limitactivelogins", JText::_('COM_LIMITACTIVELOGINS_CLEAN')); ?>
			
		</div>
</form>