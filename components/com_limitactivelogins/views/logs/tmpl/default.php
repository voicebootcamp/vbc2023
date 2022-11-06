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
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\Layout\LayoutHelper;

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/html');
HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('formbehavior.chosen', 'select');
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
		else
		{
			Joomla.submitform(task);
		}
	};
});
</script>

<div id="limitactivelogins" class="w357kit-margin">

	<h1 class="w357kit-heading-divider">
		<?php echo Text::_('Your devices'); ?>
	</h1>

	<div class="w357kit-text-lead w357kit-margin-medium">
		<?php echo Text::sprintf('%s signed-in devices', $this->total); ?>
	</div>

	<!-- BEGIN: Let’s secure your account modal -->
	<div id="lal-secure-your-account" w357kit-modal>
		<div class="w357kit-modal-dialog">
			<button class="w357kit-modal-close-default" type="button" w357kit-close></button>
			<div class="w357kit-modal-header">
				<h2 class="w357kit-modal-title">Let’s secure your account</h2>
			</div>
			<div class="w357kit-modal-body">
				<p>If there’s a device you don’t recognize, someone else may have your password. Change your password to protect your Account.</p>
				<p>You’ll be signed out of all devices except the one you’re using now.</p>
			</div>
			<div class="w357kit-modal-footer w357kit-text-right">
				<a class="w357kit-button w357kit-button-link w357kit-modal-close w357kit-text-capitalize">Cancel</a>
				<a href="<?php echo $this->password_reset_link; ?>" class="w357kit-button w357kit-button-link w357kit-margin-left w357kit-text-bold w357kit-text-capitalize">Change password</a>
			</div>
		</div>
	</div>
	<!-- END: Let’s secure your account modal -->

	<form action="<?php echo htmlspecialchars(Uri::getInstance()->toString()); ?>" method="post" name="adminForm" id="adminForm">

		<input type="hidden" name="jform[session_id]" value="" />
		<input type="hidden" name="jform[userid]" value="" />
		<input type="hidden" name="jform[user_agent]" value="" />
		<input type="hidden" name="jform[ip_address]" value="" />

		<div class="w357kit-child-width-1-1 w357kit-grid-divider" w357kit-grid>
		
			<?php 
			$data = [];
			foreach ($this->items as $i => $item)
			{
				if ($this->session->getId() === $item->session_id)
				{
					$item->current = '1';
					$data[] = $item;
				}
				else
				{
					$item->current = '0';
					$data[] = $item;
				}
			}

			// Display the current device first.
			usort($data,function($first,$second){
				return $first->current < $second->current;
			});
	
			foreach ($data as $item) : 
			$this->detect->setUserAgent($item->user_agent);
			?>

				<!-- BEGIN: DEVICE -->
				<div data-session-id="<?php echo $item->session_id; ?>">
					<div class="w357kit-flex w357kit-flex-middle" w357kit-grid>
						<div class="w357kit-width-auto w357kit-text-center">
							<div>
								<?php if ($this->detect->isMobile()): ?>
									<span w357kit-icon="icon: phone; ratio: 2"></span>
								<?php elseif ($this->detect->isTablet()): ?>
									<span w357kit-icon="icon: tablet; ratio: 2"></span>
								<?php else: ?>
									<span w357kit-icon="icon: desktop; ratio: 2"></span>
								<?php endif; ?>
							</div>
						</div>
						
						<div class="w357kit-width-auto">

							<?php if ($item->browser != 'Unknown (?)'): ?>
							<div class="w357kit-text-bold w357kit-margin-small-bottom">
								<?php echo $item->browser; ?> 
							</div>
							<?php endif; ?>

							<div class="w357kit-margin-small-bottom">
								<?php if ($item->operating_system == 'Unknown'): ?>
									<div class="w357kit-text-bold">
										<?php if(strlen($item->user_agent) > 25): ?>
											<span title="<?php echo $this->escape($item->user_agent); ?>">
												<?php echo trim(substr($this->escape($item->user_agent), 0, 25)) . '&hellip;'; ?>
											</span>
										<?php else: ?>
											<?php echo $this->escape($item->user_agent); ?>
										<?php endif; ?>
									</div>
								<?php else: ?>
									<?php echo $item->operating_system; ?> 
								<?php endif; ?>
							</div>

							<div>
								<span><?php echo $item->country; ?></span> (<?php echo $item->ip_address; ?>) 
								<span style="margin: 0 3px;">•</span>

								<span w357kit-tooltip="title: <?php echo JHTML::_("date", $item->datetime, "d-M-Y, H:i"); ?>; delay: 500">
									<?php echo LimitactiveloginsHelpersLimitactivelogins::time_elapsed_string($item->datetime); ?>
								</span>

								<?php if ($this->session->getId() === $item->session_id): ?>
									<span style="margin: 0 3px;">•</span>
									<span class="w357kit-text-success">This device</span>
								<?php endif; ?>
							</div>
						</div>

						<?php if ($this->session->getId() != $item->session_id): ?>
						<div class="w357kit-width-auto">
							<div>
								<ul class="w357kit-subnav" w357kit-margin>
									<li>
										<a href="#"><span w357kit-icon="icon: more-vertical;"></span></a>
										<div w357kit-dropdown="mode: click;">
											<ul class="w357kit-nav w357kit-dropdown-nav">
												<li><a href="" onclick="javascript:if(confirm('Are you sure to delete this session? The user `<?php echo $item->username; ?>` will be logged out from this device.')){Joomla.submitbutton('log.deleteSessionAndLogoutTheUser', '<?php echo $this->escape($item->session_id); ?>', <?php echo $this->escape($item->userid); ?>, '<?php echo $this->escape($item->user_agent); ?>', '<?php echo $this->escape($item->ip_address); ?>');}; return false;"><?php echo Text::_('Logout'); ?></a></li>
												<li><a href="#lal-secure-your-account" w357kit-toggle>Don't recognize this device?</a></li>
											</ul>
										</div>
									</li>
								</ul>
							</div>
						</div>
						<?php endif; ?>
					</div>
				</div>
				<!-- END: DEVICE -->

			<?php endforeach; ?>
		
		</div>

		<input type="hidden" name="task" value=""/>
		<?php echo HTMLHelper::_('form.token'); ?>

	</form>

	<?php if ($this->total > 1): ?>
	<div class="w357kit-margin-medium-top">
		<a href="#lal-secure-your-account" w357kit-toggle>
			Don't recognize a device?
		</a>
	</div>
	<?php endif; ?>

</div>