<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2022 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

HTMLHelper::_('behavior.core');

OSMembershipHelperJquery::validateForm();
Factory::getDocument()->addScriptOptions('paymentMethod', $this->subscription->payment_method)
	->addScript(Uri::root(true) . '/media/com_osmembership/js/site-card-default.min.js');

/* @var OSMembershipHelperBootstrap $bootstrapHelper */
$bootstrapHelper   = $this->bootstrapHelper;
$controlGroupClass = $bootstrapHelper->getClassMapping('control-group');
$controlLabelClass = $bootstrapHelper->getClassMapping('control-label');
$controlsClass     = $bootstrapHelper->getClassMapping('controls');
?>
<div id="osm-update-credit-card" class="osm-container">
<h1 class="osm-page-title"><?php echo Text::_('OSM_UPDATE_CARD'); ?></h1>
<form method="post" name="os_form" id="os_form" action="<?php echo Route::_('index.php?option=com_osmembership&task=profile.update_card&Itemid=' . $this->Itemid, false, $this->config->use_https ? 1 : 0); ?>" autocomplete="off" class="<?php echo $bootstrapHelper->getClassMapping('form form-horizontal'); ?>">
	<div class="<?php echo $controlGroupClass; ?> payment_information" id="tr_card_number">
		<div class="<?php echo $controlLabelClass; ?>">
			<label><?php echo  Text::_('AUTH_CARD_NUMBER'); ?><span class="required">*</span></label>
		</div>
		<div class="<?php echo $controlsClass; ?>">
			<input type="text" name="x_card_num" class="form-control validate[required,creditCard]" value="<?php echo $this->escape($this->input->post->getAlnum('x_card_num'));?>" size="20" />
		</div>
	</div>
	<div class="<?php echo $controlGroupClass; ?> payment_information" id="tr_exp_date">
		<div class="<?php echo $controlLabelClass; ?>">
			<label>
				<?php echo Text::_('AUTH_CARD_EXPIRY_DATE'); ?><span class="required">*</span>
			</label>
		</div>
		<div class="<?php echo $controlsClass; ?>">
			<?php echo $this->lists['exp_month'] . '  /  ' . $this->lists['exp_year'] ; ?>
		</div>
	</div>
	<div class="<?php echo $controlGroupClass; ?> payment_information" id="tr_cvv_code">
		<div class="<?php echo $controlLabelClass; ?>">
			<label>
				<?php echo Text::_('AUTH_CVV_CODE'); ?><span class="required">*</span>
			</label>
		</div>
		<div class="<?php echo $controlsClass; ?>">
			<input type="text" name="x_card_code" class="validate[required,custom[number]] form-control input-small" value="<?php echo $this->escape($this->input->post->getString('x_card_code')); ?>" size="20" />
		</div>
	</div>
	<div class="<?php echo $controlGroupClass; ?> payment_information" id="tr_card_holder_name">
		<div class="<?php echo $controlLabelClass; ?>">
			<label>
				<?php echo Text::_('OSM_CARD_HOLDER_NAME'); ?><span class="required">*</span>
			</label>
		</div>
		<div class="<?php echo $controlsClass; ?>">
			<input type="text" name="card_holder_name" class="validate[required] form-control"  value="<?php echo $this->input->post->getString('card_holder_name'); ?>" size="40" />
		</div>
	</div>
	<div class="form-actions">
		<input type="submit" class="<?php echo $this->bootstrapHelper->getClassMapping('btn btn-primary'); ?>" name="btnSubmit" id="btn-submit" value="<?php echo  Text::_('OSM_UPDATE') ;?>" />
	</div>

	<input type="hidden" name="subscription_id" value="<?php echo $this->subscription->subscription_id; ?>" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
</div>
