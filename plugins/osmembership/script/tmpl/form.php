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
use Joomla\Registry\Registry;

/**
 * Layout variables
 * -----------------
 * @var   OSMembershipTablePlan $row
 */

$params = new Registry($row->params);
?>
<p class="text-warning">
    This feature is usually used by developers that know how to write PHP code. Please only use this
    feature if you know how to program in PHP and understand what you are doing.
</p>
<div class="control-group">
    <div class="control-label">
		<?php echo OSMembershipHelperHtml::getFieldLabel('subscription_store_script', Text::_('OSM_SUBSCRIPTION_STORED_SCRIPT'), Text::_('OSM_SUBSCRIPTION_STORED_SCRIPT_EXPLAIN')); ?>
    </div>
    <div class="controls">
		<textarea rows="10" cols="70" class="form-control input-xxlarge" name="subscription_store_script"><?php echo $params->get('subscription_store_script'); ?></textarea>
    </div>
</div>
<div class="control-group">
    <div class="control-label">
		<?php echo OSMembershipHelperHtml::getFieldLabel('subscription_store_script', Text::_('OSM_SUBSCRIPTION_ACTIVE_SCRIPT'), Text::_('OSM_SUBSCRIPTION_ACTIVE_SCRIPT_EXPLAIN')); ?>
    </div>
    <div class="controls">
        <textarea rows="10" cols="70" class="form-control input-xxlarge" name="subscription_active_script"><?php echo $params->get('subscription_active_script'); ?></textarea>
    </div>
</div>
<div class="control-group">
    <div class="control-label">
		<?php echo OSMembershipHelperHtml::getFieldLabel('subscription_store_script', Text::_('OSM_SUBSCRIPTION_EXPIRED_SCRIPT'), Text::_('OSM_SUBSCRIPTION_EXPIRED_SCRIPT_EXPLAIN')); ?>
    </div>
    <div class="controls">
        <textarea rows="10" cols="70" class="form-control input-xxlarge" name="subscription_expired_script"><?php echo $params->get('subscription_expired_script'); ?></textarea>
    </div>
</div>

