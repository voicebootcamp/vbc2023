<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2022 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

$bootstrapHelper   = OSMembershipHelperBootstrap::getInstance();
$rowFluidClasss    = $bootstrapHelper->getClassMapping('row-fluid');
$controlGroupClass = $bootstrapHelper->getClassMapping('control-group');
$controlLabelClass = $bootstrapHelper->getClassMapping('control-label');
$controlsClass     = $bootstrapHelper->getClassMapping('controls');
?>
<div class="<?php echo $controlGroupClass; ?>">
    <div class="<?php echo $controlLabelClass; ?>">
        <?php echo OSMembershipHelperHtml::getFieldLabel('setup_fee', Text::_('OSM_SETUP_FEE'), Text::_('OSM_SETUP_FEE_EXPLAIN')); ?>
    </div>
    <div class="<?php echo $controlsClass; ?>">
        <input type="number" class="form-control input-small" name="setup_fee" id="setup_fee" value="<?php echo $this->item->setup_fee; ?>" step="0.01" />
    </div>
</div>
<div class="<?php echo $controlGroupClass; ?>">
    <div class="<?php echo $controlLabelClass; ?>">
        <?php echo OSMembershipHelperHtml::getFieldLabel('number_group_members', Text::_('PLG_GRM_MAX_NUMBER_MEMBERS'), Text::_('PLG_GRM_MAX_NUMBER_MEMBERS_EXPLAIN')); ?>
    </div>
    <div class="<?php echo $controlsClass; ?>">
        <input type="number" class="form-control input-small" name="number_group_members" id="number_group_members" value="<?php echo $this->item->number_group_members; ?>" />
    </div>
</div>
<div class="<?php echo $controlGroupClass; ?>">
    <div class="<?php echo $controlLabelClass; ?>">
        <?php echo OSMembershipHelperHtml::getFieldLabel('free_plan_subscription_status', Text::_('OSM_FREE_PLAN_STATUS'), Text::_('OSM_FREE_PLAN_STATUS_EXPLAIN')); ?>
    </div>
    <div class="<?php echo $controlsClass; ?>">
        <?php echo $this->lists['free_plan_subscription_status'];?>
    </div>
</div>
<div class="<?php echo $controlGroupClass; ?>">
    <div class="<?php echo $controlLabelClass; ?>">
        <?php echo OSMembershipHelperHtml::getFieldLabel('login_redirect_menu_id', Text::_('OSM_LOGIN_REDIRECT'), Text::_('OSM_LOGIN_REDIRECT_EXPLAIN')); ?>
    </div>
    <div class="<?php echo $controlsClass; ?>">
        <?php echo $this->lists['login_redirect_menu_id']; ?>
    </div>
</div>
<div class="<?php echo $controlGroupClass; ?>">
    <div class="<?php echo $controlLabelClass; ?>">
        <?php echo OSMembershipHelperHtml::getFieldLabel('payment_methods', Text::_('OSM_PAYMENT_METHODS'), Text::_('OSM_PAYMENT_METHODS_EXPLAIN')); ?>
    </div>
    <div class="<?php echo $controlsClass; ?>">
        <?php echo $this->lists['payment_methods'];?>
    </div>
</div>
<div class="<?php echo $controlGroupClass; ?>">
    <div class="<?php echo $controlLabelClass; ?>">
        <?php echo OSMembershipHelperHtml::getFieldLabel('currency_code', Text::_('OSM_CURRENCY'), Text::_('OSM_CURRENCY_EXPLAIN')); ?>
    </div>
    <div class="<?php echo $controlsClass; ?>">
        <?php echo $this->lists['currency'];?>
    </div>
</div>
<div class="<?php echo $controlGroupClass; ?>">
    <div class="<?php echo $controlLabelClass; ?>">
        <?php echo OSMembershipHelperHtml::getFieldLabel('currency_symbol', Text::_('OSM_CURRENCY_SYMBOL'), Text::_('OSM_CURRENCY_SYMBOL_EXPLAIN')); ?>
    </div>
    <div class="<?php echo $controlsClass; ?>">
        <input type="text" class="form-control input-small" name="currency_symbol" id="currency_symbol" value="<?php echo $this->item->currency_symbol; ?>" />
    </div>
</div>
<div class="<?php echo $controlGroupClass; ?>">
    <div class="<?php echo $controlLabelClass; ?>">
        <?php echo  Text::_('OSM_SUBSCRIPTION_COMPLETE_URL'); ?>
    </div>
    <div class="<?php echo $controlsClass; ?>">
        <input type="url" class="form-control" name="subscription_complete_url" value="<?php echo $this->item->subscription_complete_url; ?>" size="50" />
    </div>
</div>
<div class="<?php echo $controlGroupClass; ?>">
    <div class="<?php echo $controlLabelClass; ?>">
        <?php echo  Text::_('OSM_OFFLINE_PAYMENT_SUBSCRIPTION_COMPLETE_URL'); ?>
    </div>
    <div class="<?php echo $controlsClass; ?>">
        <input type="url" class="form-control" name="offline_payment_subscription_complete_url" value="<?php echo $this->item->offline_payment_subscription_complete_url; ?>" size="50" />
    </div>
</div>
<div class="<?php echo $controlGroupClass; ?>">
    <div class="<?php echo $controlLabelClass; ?>">
        <?php echo OSMembershipHelperHtml::getFieldLabel('notification_emails', Text::_('OSM_NOTIFICATION_EMAILS'), Text::_('OSM_NOTIFICATION_EMAILS_EXPLAIN')); ?>
    </div>
    <div class="<?php echo $controlsClass; ?>">
        <input type="text" class="form-control input-large" name="notification_emails" value="<?php echo $this->item->notification_emails; ?>" />
    </div>
</div>
<div class="<?php echo $controlGroupClass; ?>">
    <div class="<?php echo $controlLabelClass; ?>">
        <?php echo OSMembershipHelperHtml::getFieldLabel('paypal_email', Text::_('OSM_PAYPAL_EMAIL'), Text::_('OSM_PAYPAL_EMAIL_EXPLAIN')); ?>
    </div>
    <div class="<?php echo $controlsClass; ?>">
        <input type="email" class="form-control" name="paypal_email" value="<?php echo $this->item->paypal_email; ?>" />
    </div>
</div>
<div class="<?php echo $controlGroupClass; ?>">
    <div class="<?php echo $controlLabelClass; ?>">
        <?php echo Text::_('OSM_PUBLISH_UP'); ?>
    </div>
    <div class="<?php echo $controlsClass; ?>">
        <?php echo HTMLHelper::_('calendar', $this->item->publish_up, 'publish_up', 'publish_up', $this->datePickerFormat . ' %H:%M:%S', array('class' => 'input-medium')); ?>
    </div>
</div>
<div class="<?php echo $controlGroupClass; ?>">
    <div class="<?php echo $controlLabelClass; ?>">
        <?php echo Text::_('OSM_PUBLISH_DOWN'); ?>
    </div>
    <div class="<?php echo $controlsClass; ?>">
        <?php echo HTMLHelper::_('calendar', $this->item->publish_down, 'publish_down', 'publish_down', $this->datePickerFormat . ' %H:%M:%S', array('class' => 'input-medium')); ?>
    </div>
</div>
<div class="<?php echo $controlGroupClass; ?>">
    <div class="<?php echo $controlLabelClass; ?>">
        <?php echo Text::_('OSM_TERMS_AND_CONDITIONS_ARTICLE') ; ?>
    </div>
    <div class="<?php echo $controlsClass; ?>">
        <?php echo OSMembershipHelperHtml::getArticleInput($this->item->terms_and_conditions_article_id, 'terms_and_conditions_article_id'); ?>
    </div>
</div>
<div class="<?php echo $controlGroupClass; ?>">
    <div class="<?php echo $controlLabelClass; ?>">
        <?php echo OSMembershipHelperHtml::getFieldLabel('conversion_tracking_code', Text::_('OSM_CONVERSION_TRACKING_CODE'), Text::_('OSM_CONVERSION_TRACKING_CODE_EXPLAIN')); ?>
    </div>
    <div class="<?php echo $controlsClass; ?>">
        <textarea name="conversion_tracking_code" class="form-control input-large" rows="10"><?php echo $this->item->conversion_tracking_code;?></textarea>
    </div>
</div>
