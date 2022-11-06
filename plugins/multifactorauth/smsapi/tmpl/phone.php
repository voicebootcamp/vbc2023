<?php
/**
 * @package   AkeebaLoginGuard
 * @copyright Copyright (c)2016-2022 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

// Prevent direct access
defined('_JEXEC') || die;

// Load media
/** @var CMSApplication $app */
$app       = $this->app;
$document  = $app->getDocument();
$token     = $app->getFormToken();
$actionURL = Uri::base() . 'index.php?option=com_users&view=callback&task=callback&method=smsapi&' . $token . '=1';
$country   = $_SERVER['GEOIP_COUNTRY_CODE'] ?? $this->geolocation();

$utilsScript = HTMLHelper::_('script', 'plg_multifactorauth_smsapi/utils.js', [
	'version'     => 'auto',
	'relative'    => true,
	'detectDebug' => false,
	'pathOnly'    => true,
]);

$utilsScript = (is_array($utilsScript) ? array_shift($utilsScript) : $utilsScript) ?: '';

$document->getWebAssetManager()->usePreset('plg_multifactorauth_smsapi.setup');
$document->addScriptOptions('multifactorauth.smsapi.actionUrl', $actionURL);
$document->addScriptOptions('multifactorauth.smsapi.utilsScript', $utilsScript);
$document->addScriptOptions('multifactorauth.smsapi.country', $country);

$phoneNumber = empty($phone) ? '' : ('+' . ltrim($phone, '+'));

?>
<div class="akeeba-form--horizontal" id="loginGuardSMSAPIForm" style="margin: 0.5em 0">
    <div class="control-group form-group">
        <label for="loginGuardSMSAPIPhone" class="form-label control-label">
			<?= Text::_('PLG_MULTIFACTORAUTH_SMSAPI_LBL_PHONE') ?>
        </label>
		<div class="controls">
        	<input type="text" name="phone-entry-field" id="loginGuardSMSAPIPhone" value="<?= $phoneNumber ?>" class="form-control input-large" />
		</div>
    </div>
    <div class="akeeba-form-group--pull-right">
            <button type="button" class="btn btn-primary loginguard-button-primary" id="loginguardSmsapiSendCode">
                <span class="icon icon-phone icon-mobile-phone"></span>
				<?= Text::_('PLG_MULTIFACTORAUTH_SMSAPI_LBL_SENDCODEBUTTON'); ?>
            </button>
    </div>
</div>
