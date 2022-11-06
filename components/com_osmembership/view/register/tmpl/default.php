<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2022 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die ;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

HTMLHelper::_('behavior.keepalive');

/**@var OSMembershipViewRegisterHtml $this **/

if (OSMembershipHelper::isJoomla4())
{
    $containerClass = ' osm-container-j4';
}
else
{
    $containerClass = '';
}

$selectedState = '';
$hasFeeFields  = false;

$fields = $this->form->getFields();

if (isset($fields['state']))
{
	$selectedState = $fields['state']->value;
}

foreach ($fields as $field)
{
	if ($field->row->fee_field)
	{
		$hasFeeFields = true;
		break;
	}
}

switch ($this->action)
{
	case 'upgrade' :
		$headerText = Text::_('OSM_SUBSCRIION_UPGRADE_FORM_HEADING');
		$buttonText = Text::_('OSM_PROCESS_UPGRADE');
		break;
	case 'renew' :
		$headerText = Text::_('OSM_SUBSCRIION_RENEW_FORM_HEADING');
		$buttonText = Text::_('OSM_PROCESS_RENEW');
		break;
	default :
		$headerText = Text::_('OSM_SUBSCRIPTION_FORM_HEADING');

		if ($this->plan->price > 0 || $hasFeeFields)
		{
			$buttonText = 'OSM_PROCESS_SUBSCRIPTION';
		}
		else
		{
			$buttonText = 'OSM_PROCESS_SUBSCRIPTION_FREE';
		}
		break;
}

$headerText = str_replace('[PLAN_TITLE]', $this->plan->title, $headerText);

/**@var OSMembershipHelperBootstrap $bootstrapHelper * */
$bootstrapHelper = $this->bootstrapHelper;

$controlGroupClass   = $bootstrapHelper->getClassMapping('control-group');
$inputPrependClass   = $bootstrapHelper->getClassMapping('input-prepend');
$inputAppendClass    = $bootstrapHelper->getClassMapping('input-append');
$addOnClass          = $bootstrapHelper->getClassMapping('add-on');
$controlLabelClass   = $bootstrapHelper->getClassMapping('control-label');
$controlsClass       = $bootstrapHelper->getClassMapping('controls');
$btnClass            = $bootstrapHelper->getClassMapping('btn');
$btnPrimaryClass     = $bootstrapHelper->getClassMapping('btn btn-primary');

$formFormat = $this->config->get('form_format', 'horizontal') ?: 'horizontal';

if ($formFormat == 'horizontal')
{
	$formClass = $bootstrapHelper->getClassMapping('form form-horizontal');
}
else
{
	$formClass = $bootstrapHelper->getClassMapping('form');
}

if (OSMembershipHelper::isJoomla4())
{
	$formClass .= ' ' . $bootstrapHelper->getClassMapping('row-fluid');
}

// Load necessary javascript library
$layoutData = [
	'selectedState'     => $selectedState,
	'hasFeeFields'      => $hasFeeFields,
	'inputPrependClass' => $inputPrependClass,
	'addOnClass'        => $addOnClass,
];

$this->loadCommonLayout('register/tmpl/default_js.php', $layoutData);
?>
<div id="osm-singup-page" class="osm-container<?php echo $containerClass; ?> osm-plan-<?php echo $this->plan->id; ?>">
<?php
if ($this->params->get('show_page_heading', 1))
{
	if ($this->input->getInt('hmvc_call'))
	{
		$hTag = 'h2';
	}
	else
	{
		$hTag = 'h1';
	}
?>
	<<?php echo $hTag; ?> class="osm-page-title"><?php echo $headerText; ?></<?php echo $hTag; ?>>
<?php
}

if (strlen($this->message))
{
?>
    <div class="osm-message clearfix"><?php echo HTMLHelper::_('content.prepare', $this->message); ?></div>
<?php
}

// Login form for existing user
echo $this->loadTemplate('login', array('fields' => $fields));
?>
<form method="post" name="os_form" id="os_form" action="<?php echo Route::_('index.php?option=com_osmembership&task=register.process_subscription&Itemid=' . $this->Itemid, false, $this->config->use_https ? 1 : 0); ?>" enctype="multipart/form-data" autocomplete="off" class="<?php echo $formClass; ?>">
	<?php
	echo $this->loadTemplate('form', array('fields' => $fields));

	if ((isset($this->fees['amount']) && $this->fees['amount'] > 0) || $this->form->containFeeFields() || $this->plan->recurring_subscription)
	{
	?>
		<h3 class="osm-heading"><?php echo Text::_('OSM_PAYMENT_INFORMATION');?></h3>
	<?php
		echo $this->loadTemplate('payment_information');
		echo $this->loadTemplate('payment_methods');
	}
	elseif ($this->plan->require_coupon)
    {
    ?>
        <div class="<?php echo $controlGroupClass ?> osm-coupon-container">
            <div class="<?php echo $controlLabelClass; ?>">
                <label><?php echo Text::_('OSM_COUPON'); ?><span class="star"> *</span></label>
            </div>
            <div class="<?php echo $controlsClass; ?>">
                <input type="text" class="form-control validate[required] <?php echo $bootstrapHelper->getClassMapping('input-medium'); ?>" name="coupon_code" id="coupon_code" value="<?php echo $this->escape($this->input->getString('coupon_code', ''));?>" />
            </div>
        </div>
    <?php
    }

	$layoutData = [
		'controlGroupClass' => $controlGroupClass,
		'controlLabelClass' => $controlLabelClass,
		'controlsClass'     => $controlsClass,
	];

	if ($this->config->show_privacy_policy_checkbox || $this->config->show_subscribe_newsletter_checkbox)
    {
        echo $this->loadTemplate('gdpr', $layoutData);
    }

    echo $this->loadTemplate('terms_conditions', $layoutData);

	if ($this->showCaptcha)
	{
		if ($this->captchaPlugin == 'recaptcha_invisible')
		{
			$style = ' style="display:none;"';
		}
		else
		{
			$style = '';
		}
	?>
		<div class="<?php echo $controlGroupClass ?> osm-captcha-container">
			<div class="<?php echo $controlLabelClass; ?>"<?php echo $style; ?>>
				<?php echo Text::_('OSM_CAPTCHA'); ?><span class="required">*</span>
			</div>
			<div class="<?php echo $controlsClass; ?>">
				<?php echo $this->captcha;?>
			</div>
		</div>
	<?php
	}
	?>
	<div class="form-actions">
		<input type="submit" class="<?php echo $btnPrimaryClass; ?>" name="btnSubmit" id="btn-submit" value="<?php echo  Text::_($buttonText) ;?>">
		<img id="ajax-loading-animation" src="<?php echo Uri::root(true); ?>/media/com_osmembership/ajax-loadding-animation.gif" style="display: none;" alt="<?php echo Text::_('OSM_ALT_LOADING'); ?>" />
	</div>
<?php
	if (count($this->methods) == 1)
	{
	?>
		<input type="hidden" name="payment_method" value="<?php echo $this->methods[0]->getName(); ?>" />
	<?php
	}
?>
	<input type="hidden" name="plan_id" value="<?php echo $this->plan->id ; ?>" />
	<input type="hidden" name="act" value="<?php echo $this->action ; ?>" />
	<input type="hidden" name="renew_option_id" value="<?php echo $this->renewOptionId ; ?>" />
	<input type="hidden" name="upgrade_option_id" value="<?php echo $this->upgradeOptionId ; ?>" />
	<input type="hidden" name="show_payment_fee" value="<?php echo (int) $this->showPaymentFee ; ?>" />
	<input type="hidden" name="vat_number_field" value="<?php echo $this->config->eu_vat_number_field ; ?>" />
	<input type="hidden" name="country_base_tax" value="<?php echo $this->countryBaseTax; ?>" />	
	<input type="hidden" name="default_country" id="default_country" value="<?php echo $this->config->default_country; ?>" />
    <input type="hidden" id="card-nonce" name="nonce" />
	<?php echo $this->loadTemplate('anti_spam'); ?>
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
</div>