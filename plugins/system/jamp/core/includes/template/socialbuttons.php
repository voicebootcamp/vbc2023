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

$socialButtonsEnabled = \JAmpHelper::$pluginParams->get('social_buttons_enabled', array());
$btnsWidth = \JAmpHelper::$pluginParams->get('social_buttons_width', 60);
$btnsHeight = \JAmpHelper::$pluginParams->get('social_buttons_height', 44);
$urlToShare = \JAmpHelper::$pluginParams->get('social_buttons_url_to_share', 'canonical');
?>
<div id="socialshare">
	<?php if(in_array('facebook', $socialButtonsEnabled)):?>
		<amp-social-share type="facebook"
		width="<?php echo $btnsWidth;?>"
		height="<?php echo $btnsHeight;?>"
		<?php echo $urlToShare != 'amp' ? '' : 'data-param-href="' . \JAmpHelper::$ampUrl . '"';?>
		data-param-app_id="<?php echo trim(\JAmpHelper::$pluginParams->get('social_button_facebook_appid', '1443045965967453'));?>"></amp-social-share>
	<?php endif;?>

    <?php if(in_array('twitter', $socialButtonsEnabled)):?>
		<amp-social-share type="twitter"
		<?php echo $urlToShare != 'amp' ? '' : 'data-param-url="' . \JAmpHelper::$ampUrl . '"';?>
        width="<?php echo $btnsWidth;?>"
        height="<?php echo $btnsHeight;?>"> </amp-social-share>
	<?php endif;?>

	<?php if(in_array('pinterest', $socialButtonsEnabled)):?>
    	<amp-social-share type="pinterest"
    	<?php echo $urlToShare != 'amp' ? '' : 'data-param-url="' . \JAmpHelper::$ampUrl . '"';?>
        width="<?php echo $btnsWidth;?>"
        height="<?php echo $btnsHeight;?>"></amp-social-share>
    <?php endif;?>

    <?php if(in_array('linkedin', $socialButtonsEnabled)):?>
    	<amp-social-share type="linkedin"
    	<?php echo $urlToShare != 'amp' ? '' : 'data-param-url="' . \JAmpHelper::$ampUrl . '"';?>
        width="<?php echo $btnsWidth;?>"
        height="<?php echo $btnsHeight;?>"></amp-social-share>
    <?php endif;?>

    <?php if(in_array('whatsapp', $socialButtonsEnabled)):?>
    	<amp-social-share type="whatsapp"
    	<?php echo $urlToShare != 'amp' ? '' : 'data-param-url="' . \JAmpHelper::$ampUrl . '"';?>
        width="<?php echo $btnsWidth;?>"
        height="<?php echo $btnsHeight;?>"></amp-social-share>
    <?php endif;?>
    
    <?php if(in_array('tumblr', $socialButtonsEnabled)):?>
    	<amp-social-share type="tumblr"
    	<?php echo $urlToShare != 'amp' ? '' : 'data-param-url="' . \JAmpHelper::$ampUrl . '"';?>
        width="<?php echo $btnsWidth;?>"
        height="<?php echo $btnsHeight;?>"></amp-social-share>
    <?php endif;?>

    <?php if(in_array('email', $socialButtonsEnabled)):?>
    	<amp-social-share type="email"
    	<?php echo $urlToShare != 'amp' ? '' : 'data-param-body="' . \JAmpHelper::$ampUrl . '"';?>
        width="<?php echo $btnsWidth;?>"
        height="<?php echo $btnsHeight;?>"></amp-social-share>
    <?php endif;?>
    
    <?php if(in_array('system', $socialButtonsEnabled)):?>
    	<amp-social-share type="system"
    	<?php echo $urlToShare != 'amp' ? '' : 'data-param-url="' . \JAmpHelper::$ampUrl . '"';?>
        width="<?php echo $btnsWidth;?>"
        height="<?php echo $btnsHeight;?>"></amp-social-share>
    <?php endif;?>
</div>

<?php if(\JAmpHelper::$pluginParams->get('social_buttons_addthis', 0)):?>
<amp-addthis
  <?php echo $urlToShare != 'amp' ? '' : 'data-url="' . \JAmpHelper::$ampUrl . '"';?>
  width="320"
  height="92"
  layout="responsive"
  data-pub-id="<?php echo \JAmpHelper::$pluginParams->get('social_buttons_addthis_pubid', 'ra-5c191331410932ff');?>"
  data-widget-id="<?php echo \JAmpHelper::$pluginParams->get('social_buttons_addthis_widgetid', '957l');?>"
  <?php if(\JAmpHelper::$pluginParams->get('social_buttons_addthis_fixed', 1)):?>
  data-widget-type="floating"
  <?php endif;?>
>
</amp-addthis>
<?php endif;?>