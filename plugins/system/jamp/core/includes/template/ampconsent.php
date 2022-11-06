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
use Joomla\CMS\Language\Text;
?>
<amp-consent layout="nodisplay" id="consent-element">
  <script type="application/json">
    {
      "consentInstanceId": "my-consent",
      "consentRequired": true,
      "promptUI": "consent-ui",
	  "postPromptUI": "post-consent-ui"
    }
  </script>
  <div id="consent-ui">
    <div class="text-consent-ui"><?php echo Text::_(\JAmpHelper::$pluginParams->get('user_notification_text', ''));?></div>
    <?php if(\JAmpHelper::$pluginParams->get('user_notification_button_text_accept_enable', 1)):?>
		<button class="btn-consent-ui" on="tap:consent-element.accept"><?php echo Text::_(\JAmpHelper::$pluginParams->get('user_notification_button_text_accept', 'Accept'));?></button>
    <?php endif;?>
    <?php if(\JAmpHelper::$pluginParams->get('user_notification_button_text_reject_enable', 1)):?>
		<button class="btn-consent-ui" on="tap:consent-element.reject"><?php echo Text::_(\JAmpHelper::$pluginParams->get('user_notification_button_text_reject', 'Reject'));?></button>
    <?php endif;?>
    <?php if(\JAmpHelper::$pluginParams->get('user_notification_button_text_dismiss_enable', 1)):?>
    	<button class="btn-consent-ui" on="tap:consent-element.dismiss"><?php echo Text::_(\JAmpHelper::$pluginParams->get('user_notification_button_text_dismiss', 'Dismiss'));?></button>
    <?php endif;?>
  </div>
</amp-consent>

<!-- Revocable consent button -->
<div id="post-consent-ui">
	<button class="iubenda-tp-btn iubenda-tp-btn--<?php echo \JAmpHelper::$pluginParams->get('iubenda_button_position', 'bottom-right');?>" on="tap:consent-element.prompt()"></button>
</div>