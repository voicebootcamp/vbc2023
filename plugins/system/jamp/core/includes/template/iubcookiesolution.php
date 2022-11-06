<?php
/**
 * @package JAMP::plugins::system
 * @subpackage jamp
 * @subpackage core
 * @subpackage includes
 * @subpackage template
 * @author Joomla! Extensions Store
 * @copyright (C)2016 Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined ( '_JEXEC' ) or die ();
use Joomla\CMS\Factory;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="robots" content="noindex">
    <title>AMP Cookie Consent</title>
</head>
<body>
	<span class="iubenda-cs-preferences-link"></span>
    <script type="text/javascript">
        var _iub = _iub || [];
        _iub.csConfiguration = {
            lang: '<?php echo Factory::getApplication()->input->get('jampiubendalanguage', 'en');?>',
            siteId: <?php echo \JAmpHelper::$pluginParams->get('iub_site_id', null);?>,
            cookiePolicyId: <?php echo \JAmpHelper::$pluginParams->get('iub_cookie_policy_id', null);?>,
            enableTcf: <?php echo \JAmpHelper::$pluginParams->get('enable_iubenda_tcf', 1) ? 'true' : 'false';?>,
            askConsentIfCMPNotFound: true,
            googleAdditionalConsentMode: true,
            googleAdsPreferenceManagement: true,
            floatingPreferencesButtonDisplay: false,
            banner: {
                acceptButtonDisplay: true,
                customizeButtonDisplay: true,
                rejectButtonDisplay: true
            },
        };
    </script>
    <script type="text/javascript" src="//cdn.iubenda.com/cs/iubenda_cs.js" charset="UTF-8" async></script>
</body>
</html>