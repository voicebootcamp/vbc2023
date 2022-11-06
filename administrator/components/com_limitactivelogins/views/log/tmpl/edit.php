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

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/html');

if(version_compare(JVERSION, '4.0', 'lt')) {
	HTMLHelper::_('behavior.tooltip');
	HTMLHelper::_('behavior.formvalidation');
}
HTMLHelper::_('formbehavior.chosen', 'select');
HTMLHelper::_('behavior.keepalive');

JText::script('COM_LIMITACTIVELOGINS_DELETE_SESSION_CONFIRMATION_MSG');
?>

<script type="text/javascript">
	js = jQuery.noConflict();
	js(document).ready(function ($) {
		
	Joomla.submitbutton = function(task)
	{
		if (task == 'log.cancel') {
			Joomla.submitform(task, document.getElementById('log-form'));
		}
		else if (task == 'log.deleteSessionAndLogoutTheUser')
		{
			var get_username = $("input[name='jform[username]']").val();
			var confirmation_text = Joomla.JText._("COM_LIMITACTIVELOGINS_DELETE_SESSION_CONFIRMATION_MSG").replace("%s", '`' + get_username + '`');
			if (confirm(confirmation_text))
			{
				Joomla.submitform(task, document.getElementById('log-form'));
			}
			else
			{
				return false;
			}
		}
		else {
			
			if (task != 'log.cancel' && document.formvalidator.isValid(document.id('log-form'))) {
				
				Joomla.submitform(task, document.getElementById('log-form'));
			}
			else {
				alert('<?php echo $this->escape(Text::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
			}
		}
	}
});
</script>

<form
	action="<?php echo JRoute::_('index.php?option=com_limitactivelogins&layout=edit&id=' . (int) $this->item->id); ?>"
	method="post" enctype="multipart/form-data" name="adminForm" id="log-form" class="form-validate form-horizontal">

	<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'log')); ?>
	<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'log', JText::_('COM_LIMITACTIVELOGINS_TAB_LOG', true)); ?>
	<div class="row-fluid">
		<div class="span10 col-10 form-horizontal">
			<fieldset class="adminform">
				
				<?php if(version_compare(JVERSION, '4.0', 'lt')): ?>
					<legend><?php echo JText::_('COM_LIMITACTIVELOGINS_FIELDSET_LOG'); ?></legend>				
				<?php endif;?>
				
				<?php echo $this->form->renderField('session_id'); ?>
				<?php echo $this->form->renderField('user_agent'); ?>
				<?php echo $this->form->renderField('country'); ?>
				<?php echo $this->form->renderField('browser'); ?>
				<?php echo $this->form->renderField('operating_system'); ?>
				<?php echo $this->form->renderField('ip_address'); ?>
				<?php echo $this->form->renderField('datetime'); ?>
				<?php echo $this->form->renderField('userid'); ?>
				<?php echo $this->form->renderField('username'); ?>
			</fieldset>
		</div>
	</div>
	<?php echo JHtml::_('bootstrap.endTab'); ?>
	<input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
	<input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>" />
	<input type="hidden" name="jform[state]" value="<?php echo $this->item->state; ?>" />
	<input type="hidden" name="jform[checked_out]" value="<?php echo $this->item->checked_out; ?>" />
	<input type="hidden" name="jform[checked_out_time]" value="<?php echo $this->item->checked_out_time; ?>" />
	<?php echo $this->form->renderField('created_by'); ?>
	<?php echo $this->form->renderField('modified_by'); ?>

	<?php echo JHtml::_('bootstrap.endTabSet'); ?>

	<input type="hidden" name="task" value=""/>
	<?php echo JHtml::_('form.token'); ?>

</form>

<?php echo Web357Framework\Functions::showFooter("com_limitactivelogins", JText::_('COM_LIMITACTIVELOGINS_CLEAN')); ?>
