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
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Joomla\String\StringHelper;
?>
</section>
<footer class="container">
	<?php if(\JAmpHelper::$pluginParams->get('amp_ad_activation', 0) && trim(\JAmpHelper::$pluginParams->get('amp_ad_code', '')) && in_array('footer', \JAmpHelper::$pluginParams->get('amp_ad_code_position', 'header'))) {
		echo trim(\JAmpHelper::$pluginParams->get('amp_ad_code', ''));
	}?>
	<?php if(\JAmpHelper::$pluginParams->get('enable_bottom_module', 0) && \JAmpHelper::$pluginParams->get('bottom_module_name', '')) {
		include (JPATH_ROOT . PLG_JAMP_TEMPLATE_PATH . 'bottom_module.php');
	}?>
	<?php if(\JAmpHelper::$pluginParams->get('enable_footer_module', 0) && \JAmpHelper::$pluginParams->get('footer_module_name', '')) {
		include (JPATH_ROOT . PLG_JAMP_TEMPLATE_PATH . 'footer_module.php');
	}?>
	<?php if(\JAmpHelper::$pluginParams->get('enable_menu_module', 0) && in_array(\JAmpHelper::$pluginParams->get('menu_module_position', 'top'), array('bottom', 'both'))) {
		include (JPATH_ROOT . PLG_JAMP_TEMPLATE_PATH . 'menu.php');
	}?>
	<?php if(\JAmpHelper::$pluginParams->get('enable_user_notification', 0) == 2) {
		include (JPATH_ROOT . PLG_JAMP_TEMPLATE_PATH . 'ampconsent.php');
	}?>
	<?php if(\JAmpHelper::$pluginParams->get('enable_user_notification', 0) == 3) {
		include (JPATH_ROOT . PLG_JAMP_TEMPLATE_PATH . 'iubampconsent.php');
	}?>

	<?php if(\JAmpHelper::$pluginParams->get('amp_facebook_page', 0) && trim(\JAmpHelper::$pluginParams->get('amp_facebook_page_url', null))):?>
		<amp-facebook-page 
			width="340"
			height="130"
			layout="responsive"
		  	data-href="<?php echo trim(\JAmpHelper::$pluginParams->get('amp_facebook_page_url', null));?>">
		</amp-facebook-page>
	<?php endif;?>
	
	<?php if(\JAmpHelper::$pluginParams->get('amp_facebook_comments', 0)):?>
		<amp-facebook-comments 
			width="486"
		  	height="657"
		  	layout="responsive"
		  	data-numposts="5"
		  	data-href="<?php echo Uri::current();?>">
		</amp-facebook-comments>
	<?php endif;?>
	
	<?php if(\JAmpHelper::$pluginParams->get('amp_instagram_enable_bottom', 0) && \JAmpHelper::isAMPComponentPublished('amp_instagram_pages') && $instagramBottomShortcode = trim(\JAmpHelper::$pluginParams->get('amp_instagram_bottom_shortcode', ''))):?>
		<amp-instagram
		    data-shortcode="<?php echo $instagramBottomShortcode;?>"
		    data-captioned
		    width="400"
		    height="400"
		    layout="responsive">
		</amp-instagram>
	<?php endif;?>
	
	<?php if(\JAmpHelper::$pluginParams->get('amp_twitter_enable_bottom', 0) && \JAmpHelper::isAMPComponentPublished('amp_twitter_pages') && $twitterBottomID = trim(\JAmpHelper::$pluginParams->get('amp_twitter_bottom_tweetid', ''))):?>
		<amp-twitter width="375"
		  height="472"
		  layout="responsive"
		  data-tweetid="<?php echo $twitterBottomID;?>">
		</amp-twitter>
	<?php endif;?>
	
    <?php if(\JAmpHelper::$pluginParams->get('social_buttons', 1) && in_array(\JAmpHelper::$pluginParams->get('social_buttons_position', 'bottom'), array('bottom', 'both'))) {
    	include (JPATH_ROOT . PLG_JAMP_TEMPLATE_PATH . 'socialbuttons.php');
	}?>
	
	<div id="footer">
		<?php if(\JAmpHelper::$pluginParams->get('amp_footer_copyright', 1)):?>
	        <p>
	            <?php echo Text::_('Copyright') . ' &copy;' . date("Y"); ?> <?php echo \JAmpHelper::$application->get('sitename'); ?>
	        </p>
        <?php endif;?>
        <?php 
        	if(\JAmpHelper::$pluginParams->get('amp_footer_mainlink', 1)):
        	$redirectToMobilePages = \JAmpHelper::$pluginParams->get('redirect_mobile_devices_toamp_page', 0);
        	$mainVersionQueryString = $redirectToMobilePages ? (stripos(\JAmpHelper::$canonicalUrl, '?') ? '&jampmain' : '?jampmain') : null;
        	$mainVersionCanonical = $trailingSlashCanonicals && !$joomlaSefSuffix ? preg_replace('/\/{2,}$/i', '/', \JAmpHelper::$canonicalUrl . '/') : preg_replace('/\/{2,}$/i', '/', \JAmpHelper::$canonicalUrl);
        ?>
			<hr id="footer-hr" />
        	<p id="footer_main_version"><a class="mainsite-link" <?php echo \JAmpHelper::$pluginParams->get('amp_footer_mainlink_nofollow', 1) ? 'rel="nofollow"' : '';?> href="<?php echo $mainVersionCanonical . $mainVersionQueryString; ?>"><?php echo \JAmpHelper::transformContents(StringHelper::strtolower(StringHelper::str_ireplace('N_V', 'N V', Text::_('MAIN_VERSION'))));?></a></p>
        <?php endif;?>
    </div>
</footer>

<?php if(\JAmpHelper::$pluginParams->get('enable_images_lightbox', 0)):?>
	<amp-image-lightbox id="amp_lightbox" layout="nodisplay"></amp-image-lightbox>
<?php endif;?>

<?php if(\JAmpHelper::$pluginParams->get('enable_user_notification', 0) == 1):?>
	<amp-user-notification layout=nodisplay id="amp-user-notification-standard">
    	<div class="text-consent-ui"><?php echo Text::_(\JAmpHelper::$pluginParams->get('user_notification_text', ''));?></div>
    	<button class="btn-consent-ui" on="tap:amp-user-notification-standard.dismiss"><?php echo Text::_(\JAmpHelper::$pluginParams->get('user_notification_button_text_accept', 'Accept'));?></button>
  	</amp-user-notification>
<?php endif;?>