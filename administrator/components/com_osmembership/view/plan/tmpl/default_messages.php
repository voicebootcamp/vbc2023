<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2022 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
?>
<div class="control-group">
	<p class="text-error" style="font-size:16px;"><?php echo Text::_('OSM_PLAN_MESSAGES_EXPLAIN'); ?></p>
</div>
<div class="control-group">
	<div class="control-label">
		<strong><?php echo Text::_('OSM_PLAN_SUBSCRIPTION_FORM_MESSAGE'); ?></strong>
	</div>
	<div class="controls">
		<?php echo $editor->display('subscription_form_message', $this->item->subscription_form_message, '100%', '250', '75', '10'); ?>
	</div>
</div>
<div class="control-group">
	<div class="control-label">
		<?php echo Text::_('OSM_USER_EMAIL_SUBJECT'); ?>
	</div>
	<div class="controls">
		<input type="text" name="user_email_subject" class="form-control"
		       value="<?php echo $this->item->user_email_subject; ?>" size="40"/>
	</div>
</div>
<div class="control-group">
	<div class="control-label">
		<?php echo Text::_('OSM_USER_EMAIL_BODY'); ?>
	</div>
	<div class="controls">
		<?php echo $editor->display('user_email_body', $this->item->user_email_body, '100%', '250', '75', '8'); ?>
	</div>
</div>
<div class="control-group">
	<div class="control-label">
		<?php echo Text::_('OSM_USER_EMAIL_BODY_OFFLINE_PAYMENT'); ?>
	</div>
	<div class="controls">
		<?php echo $editor->display('user_email_body_offline', $this->item->user_email_body_offline, '100%', '250', '75', '8'); ?>
	</div>
</div>
<div class="control-group">
    <div class="control-label">
		<?php echo Text::_('OSM_ADMIN_EMAIL_BODY'); ?>
    </div>
    <div class="controls">
		<?php echo $editor->display('admin_email_body', $this->item->admin_email_body, '100%', '250', '75', '8'); ?>
    </div>
</div>
<div class="control-group">
	<div class="control-label">
		<?php echo Text::_('OSM_THANK_MESSAGE'); ?>
	</div>
	<div class="controls">
		<?php echo $editor->display('thanks_message', $this->item->thanks_message, '100%', '250', '75', '8'); ?>
	</div>
</div>
<div class="control-group">
	<div class="control-label">
		<?php echo Text::_('OSM_THANK_MESSAGE_OFFLINE'); ?>
	</div>
	<div class="controls">
		<?php echo $editor->display('thanks_message_offline', $this->item->thanks_message_offline, '100%', '250', '75', '8'); ?>
	</div>
</div>
<div class="control-group">
	<div class="control-label">
		<?php echo Text::_('OSM_RENEW_USER_EMAIL_SUBJECT'); ?>
	</div>
	<div class="controls">
		<input type="text" name="user_renew_email_subject" class="form-control"
		       value="<?php echo $this->item->user_renew_email_subject; ?>" size="40"/>
	</div>
</div>
<div class="control-group">
	<div class="control-label">
		<?php echo Text::_('OSM_RENEW_USER_EMAIL_BODY'); ?>
	</div>
	<div class="controls">
		<?php echo $editor->display('user_renew_email_body', $this->item->user_renew_email_body, '100%', '250', '75', '8'); ?>
	</div>
</div>
<div class="control-group">
	<div class="control-label">
		<?php echo Text::_('OSM_RENEW_USER_EMAIL_BODY_OFFLINE'); ?>
	</div>
	<div class="controls">
		<?php echo $editor->display('user_renew_email_body_offline', $this->item->user_renew_email_body_offline, '100%', '250', '75', '8'); ?>
	</div>
</div>
<div class="control-group">
    <div class="control-label">
		<?php echo Text::_('OSM_RENEW_ADMIN_EMAIL_BODY'); ?>
    </div>
    <div class="controls">
		<?php echo $editor->display('admin_renew_email_body', $this->item->admin_renew_email_body, '100%', '250', '75', '8'); ?>
    </div>
</div>
<div class="control-group">
	<div class="control-label">
		<?php echo Text::_('OSM_RENEW_THANK_MESSAGE'); ?>
	</div>
	<div class="controls">
		<?php echo $editor->display('renew_thanks_message', $this->item->renew_thanks_message, '100%', '250', '75', '8'); ?>
	</div>
</div>
<div class="control-group">
	<div class="control-label">
		<?php echo Text::_('OSM_RENEW_THANK_MESSAGE_OFFLINE'); ?>
	</div>
	<div class="controls">
		<?php echo $editor->display('renew_thanks_message_offline', $this->item->renew_thanks_message_offline, '100%', '250', '75', '8'); ?>
	</div>
</div>
<div class="control-group">
	<div class="control-label">
		<?php echo Text::_('OSM_UPGRADE_USER_EMAIL_BODY'); ?>
	</div>
	<div class="controls">
		<?php echo $editor->display('user_upgrade_email_body', $this->item->user_upgrade_email_body, '100%', '250', '75', '8'); ?>
	</div>
</div>
<div class="control-group">
	<div class="control-label">
		<?php echo Text::_('OSM_UPGRADE_USER_EMAIL_BODY_OFFLINE'); ?>
	</div>
	<div class="controls">
		<?php echo $editor->display('user_upgrade_email_body_offline', $this->item->user_upgrade_email_body_offline, '100%', '250', '75', '8'); ?>
	</div>
</div>
<div class="control-group">
    <div class="control-label">
		<?php echo Text::_('OSM_UPGRADE_ADMIN_EMAIL_BODY'); ?>
    </div>
    <div class="controls">
		<?php echo $editor->display('admin_upgrade_email_body', $this->item->admin_upgrade_email_body, '100%', '250', '75', '8'); ?>
    </div>
</div>
<div class="control-group">
    <div class="control-label">
        <?php echo Text::_('OSM_UPGRADE_THANK_MESSAGE'); ?>
    </div>
    <div class="controls">
	    <?php echo $editor->display('upgrade_thanks_message', $this->item->upgrade_thanks_message, '100%', '250', '75', '8'); ?>
    </div>
</div>
<div class="control-group">
    <div class="control-label">
        <?php echo Text::_('OSM_UPGRADE_THANK_MESSAGE_OFFLINE'); ?>
    </div>
    <div class="controls">
	    <?php echo $editor->display('upgrade_thanks_message_offline', $this->item->upgrade_thanks_message_offline, '100%', '250', '75', '8'); ?>
    </div>
</div>
<div class="control-group">
	<div class="control-label">
		<?php echo Text::_('OSM_SUBSCRIPTION_APPROVED_EMAIL_SUBJECT'); ?>
	</div>
	<div class="controls">
		<input type="text" name="subscription_approved_email_subject" class="form-control"
		       value="<?php echo $this->item->subscription_approved_email_subject; ?>" size="40"/>
	</div>
</div>
<div class="control-group">
	<div class="control-label">
		<?php echo Text::_('OSM_SUBSCRIPTION_APPROVED_EMAIL_BODY'); ?>
	</div>
	<div class="controls">
		<?php echo $editor->display('subscription_approved_email_body', $this->item->subscription_approved_email_body, '100%', '250', '75', '8'); ?>
	</div>
</div>
<div class="control-group">
    <div class="control-label">
		<?php echo Text::_('OSM_INVOICE_FORMAT'); ?>
    </div>
    <div class="controls">
		<?php echo $editor->display('invoice_layout', $this->item->invoice_layout, '100%', '250', '75', '8'); ?>
    </div>
</div>
<div class="control-group">
    <div class="control-label">
		<?php echo Text::_('OSM_SUBSCRIPTION_END_EMAIL_SUBJECT'); ?>
    </div>
    <div class="controls">
        <input type="text" name="subscription_end_email_subject" class="form-control"
               value="<?php echo $this->item->subscription_end_email_subject; ?>" />
    </div>
</div>
<div class="control-group">
    <div class="control-label">
		<?php echo Text::_('OSM_SUBSCRIPTION_END_EMAIL_BODY'); ?>
    </div>
    <div class="controls">
		<?php echo $editor->display('subscription_end_email_body', $this->item->subscription_end_email_body, '100%', '250', '75', '8'); ?>
    </div>
</div>