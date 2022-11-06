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
use Joomla\CMS\Language\LanguageHelper;

$customDomain = trim(\JAmpHelper::$pluginParams->get('iubenda_custom_domain', ''));
$defaultLanguageSef = 'en';
$knownLangs = LanguageHelper::getLanguages();

// Setup predefined site language
$defaultLanguageCode = Factory::getLanguage()->getTag();

foreach ($knownLangs as $knownLang) {
	if($knownLang->lang_code == $defaultLanguageCode) {
		$defaultLanguageSef = $knownLang->sef;
		break;
	}
}
?>
<amp-consent id="iubenda" layout="nodisplay">
    <!--
      It is preferred to set the consent ID as "consent" + site ID
      If you want to request consent only to EU users then replace "consentRequired": true with "promptIfUnknownForGeoGroup": "eu" -> allows to ask consent only to EU users
    -->
    <script type="application/json">
        {
			"consentInstanceId": "consent<?php echo \JAmpHelper::$pluginParams->get('iub_site_id', null);?>",
            "consentRequired": true,
            "promptUISrc": "<?php echo $customDomain ? $customDomain : \JAmpHelper::$canonicalUrl;?>?jampiubendacookiesolution=1&jampiubendalanguage=<?php echo $defaultLanguageSef;?>",
            "postPromptUI": "post-consent-ui"
        }
    </script>
</amp-consent>
<!-- Revocable consent button -->
<div id="post-consent-ui">
	<button class="iubenda-tp-btn iubenda-tp-btn--<?php echo \JAmpHelper::$pluginParams->get('iubenda_button_position', 'bottom-right');?>" on="tap:iubenda.prompt()"></button>
</div>