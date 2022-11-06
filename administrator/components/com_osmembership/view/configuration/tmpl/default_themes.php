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
<fieldset class="form-horizontal options-form">
	<legend><?php echo Text::_('OSM_THEME_SETTINGS'); ?></legend>
	<div class="control-group">
		<div class="control-label">
			<?php echo OSMembershipHelperHtml::getFieldLabel('twitter_bootstrap_version', Text::_('OSM_TWITTER_BOOTSTRAP_VERSION'), Text::_('OSM_TWITTER_BOOTSTRAP_VERSION_EXPLAIN')); ?>
		</div>
		<div class="controls">
			<?php echo $this->lists['twitter_bootstrap_version'];?>
		</div>
	</div>
    <div class="control-group" data-showon='<?php echo OSMembershipHelperHtml::renderShowon(array('twitter_bootstrap_version' => ['2', '5'])); ?>'>
        <div class="control-label">
			<?php echo OSMembershipHelperHtml::getFieldLabel('load_twitter_bootstrap_in_frontend', Text::_('OSM_LOAD_BOOTSTRAP_CSS_IN_FRONTEND'), Text::_('OSM_LOAD_BOOTSTRAP_CSS_IN_FRONTEND_EXPLAIN')); ?>
        </div>
        <div class="controls">
			<?php echo OSMembershipHelperHtml::getBooleanInput('load_twitter_bootstrap_in_frontend', $config->get('load_twitter_bootstrap_in_frontend', '0')); ?>
        </div>
    </div>
	<div class="control-group">
		<div class="control-label">
			<?php echo OSMembershipHelperHtml::getFieldLabel('hide_active_plans', Text::_('OSM_HIDE_ACTIVE_PLANS'), Text::_('OSM_HIDE_ACTIVE_PLANS_EXPLAIN')); ?>
		</div>
		<div class="controls">
			<?php echo OSMembershipHelperHtml::getBooleanInput('hide_active_plans', isset($config->hide_active_plans) ? $config->hide_active_plans : 0); ?>
		</div>
	</div>
	<div class="control-group">
		<div class="control-label">
			<?php echo OSMembershipHelperHtml::getFieldLabel('show_price_including_tax', Text::_('OSM_SHOW_PRICE_INCLUDING_TAX'), ''); ?>
		</div>
		<div class="controls">
			<?php echo OSMembershipHelperHtml::getBooleanInput('show_price_including_tax', $config->show_price_including_tax); ?>
		</div>
	</div>
	<div class="control-group">
		<div class="control-label">
			<?php echo OSMembershipHelperHtml::getFieldLabel('hide_details_button', Text::_('OSM_HIDE_DETAILS_BUTTON'), Text::_('OSM_HIDE_DETAILS_BUTTON_EXPLAIN')); ?>
		</div>
		<div class="controls">
			<?php echo OSMembershipHelperHtml::getBooleanInput('hide_details_button', $config->hide_details_button); ?>
		</div>
	</div>
    <div class="control-group">
        <div class="control-label">
			<?php echo OSMembershipHelperHtml::getFieldLabel('display_recurring_payment_amounts', Text::_('OSM_DISPLAY_RECURRING_PAYMENT_AMOUNTS'), Text::_('OSM_DISPLAY_RECURRING_PAYMENT_AMOUNTS_EXPLAIN')); ?>
        </div>
        <div class="controls">
			<?php echo OSMembershipHelperHtml::getBooleanInput('display_recurring_payment_amounts', $config->get('display_recurring_payment_amounts', 1)); ?>
        </div>
    </div>
    <div class="control-group">
        <div class="control-label">
			<?php echo OSMembershipHelperHtml::getFieldLabel('show_renew_options_on_plan_details', Text::_('OSM_SHOW_RENEW_OPTION_ON_PLAN_DETAIL'), Text::_('OSM_SHOW_RENEW_OPTION_ON_PLAN_DETAIL_EXPLAIN')); ?>
        </div>
        <div class="controls">
			<?php echo OSMembershipHelperHtml::getBooleanInput('show_renew_options_on_plan_details', $config->show_renew_options_on_plan_details); ?>
        </div>
    </div>
    <div class="control-group">
        <div class="control-label">
			<?php echo OSMembershipHelperHtml::getFieldLabel('show_upgrade_options_on_plan_details', Text::_('OSM_SHOW_UPGRADE_OPTION_ON_PLAN_DETAIL'), Text::_('OSM_SHOW_UPGRADE_OPTION_ON_PLAN_DETAIL_EXPLAIN')); ?>
        </div>
        <div class="controls">
			<?php echo OSMembershipHelperHtml::getBooleanInput('show_upgrade_options_on_plan_details', $config->show_upgrade_options_on_plan_details); ?>
        </div>
    </div>
	<div class="control-group">
		<div class="control-label">
			<?php echo OSMembershipHelperHtml::getFieldLabel('date_format', Text::_('OSM_DATE_FORMAT'), ''); ?>
		</div>
		<div class="controls">
			<input type="text" name="date_format" class="form-control" value="<?php echo $this->config->date_format; ?>" size="10" />
		</div>
	</div>
	<div class="control-group">
		<div class="control-label">
			<?php echo OSMembershipHelperHtml::getFieldLabel('date_field_format', Text::_('OSM_DATE_FIELD_FORMAT'), Text::_('OSM_DATE_FIELD_FORMAT_EXPLAIN')); ?>
		</div>
		<div class="controls">
			<?php echo $this->lists['date_field_format']; ?>
		</div>
	</div>
	<div class="control-group">
		<div class="control-label">
			<?php echo OSMembershipHelperHtml::getFieldLabel('currency_code', Text::_('OSM_CURRENCY')); ?>
		</div>
		<div class="controls">
			<?php echo $this->lists['currency_code']; ?>
		</div>
	</div>
	<div class="control-group">
		<div class="control-label">
			<?php echo OSMembershipHelperHtml::getFieldLabel('currency_symbol', Text::_('OSM_CURRENCY_SYMBOL'), ''); ?>
		</div>
		<div class="controls">
			<input type="text" name="currency_symbol" class="form-control" value="<?php echo $this->config->currency_symbol; ?>" size="10" />
		</div>
	</div>
	<div class="control-group">
		<div class="control-label">
			<?php echo OSMembershipHelperHtml::getFieldLabel('decimals', Text::_('OSM_DECIMALS'), Text::_('OSM_DECIMALS_EXPLAIN')); ?>
		</div>
		<div class="controls">
			<input type="number" name="decimals" class="form-control" value="<?php echo isset($this->config->decimals) ? $this->config->decimals : 2; ?>" size="10" />
		</div>
	</div>
	<div class="control-group">
		<div class="control-label">
			<?php echo OSMembershipHelperHtml::getFieldLabel('dec_point', Text::_('OSM_DECIMAL_POINT'), Text::_('OSM_DECIMAL_POINT_EXPLAIN')); ?>
		</div>
		<div class="controls">
			<input type="text" name="dec_point" class="form-control" value="<?php echo isset($this->config->dec_point) ? $this->config->dec_point : '.'; ?>" size="10" />
		</div>
	</div>
	<div class="control-group">
		<div class="control-label">
			<?php echo OSMembershipHelperHtml::getFieldLabel('thousands_sep', Text::_('OSM_THOUSANDS_SEP'), Text::_('OSM_THOUSANDS_SEP_EXPLAIN')); ?>
		</div>
		<div class="controls">
			<input type="text" name="thousands_sep" class="form-control" value="<?php echo isset($this->config->thousands_sep) ? $this->config->thousands_sep : ','; ?>" size="10" />
		</div>
	</div>
	<div class="control-group">
		<div class="control-label">
			<?php echo OSMembershipHelperHtml::getFieldLabel('currency_position', Text::_('OSM_CURRENCY_POSITION'), ''); ?>
		</div>
		<div class="controls">
			<?php echo $this->lists['currency_position']; ?>
		</div>
	</div>
	<div class="control-group">
		<div class="control-label">
			<?php echo OSMembershipHelperHtml::getFieldLabel('number_columns', Text::_('OSM_NUMBER_COLUMNS_IN_COLUMNS_LAYOUT'), Text::_('OSM_NUMBER_COLUMNS_IN_COLUMNS_LAYOUT_EXPLAIN')); ?>
		</div>
		<div class="controls">
			<input type="number" name="number_columns" class="form-control" value="<?php echo $this->config->number_columns ? $this->config->number_columns : 3 ; ?>" size="10" />
		</div>
	</div>
    <div class="control-group">
        <div class="control-label">
			<?php echo OSMembershipHelperHtml::getFieldLabel('max_errors_per_field', Text::_('OSM_MAX_ERRORS_PER_FIELD'), Text::_('OSM_MAX_ERRORS_PER_FIELD_EXPLAIN')); ?>
        </div>
        <div class="controls">
            <input type="number" min="0" step="1" name="max_errors_per_field" class="form-control" value="<?php echo (int) $this->config->max_errors_per_field ; ?>" size="10" />
        </div>
    </div>
    <div class="control-group">
        <div class="control-label">
			<?php echo OSMembershipHelperHtml::getFieldLabel('display_field_description', Text::_('OSM_DISPLAY_FIELD_DESCRIPTION'), Text::_('OSM_DISPLAY_FIELD_DESCRIPTION_EXPLAIN')); ?>
        </div>
        <div class="controls">
            <?php echo $this->lists['display_field_description']; ?>
        </div>
    </div>
</fieldset>