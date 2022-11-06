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

$trailingSlashCanonicals = \JAmpHelper::$pluginParams->get('add_trailing_slash_canonicals', 0);
$joomlaSefSuffix = \JAmpHelper::$application->get ( 'sef_suffix', 1 );
?>
<meta charset="utf-8" />
<title><?php echo htmlspecialchars(\JAmpHelper::$document->getTitle(), ENT_COMPAT, 'UTF-8');?></title>
<link rel="canonical" href="<?php echo $trailingSlashCanonicals && !$joomlaSefSuffix ? preg_replace('/\/{2,}$/i', '/', \JAmpHelper::$canonicalUrl . '/') : preg_replace('/\/{2,}$/i', '/', \JAmpHelper::$canonicalUrl); ?>" />
<?php
echo $customHreflang;
if($faviconImage = \JAmpHelper::$pluginParams->get('amp_favicon', null)):
$faviconInfo = \JAmpImage::getImageInfo($faviconImage);
?>
<link rel="icon" type="image/<?php echo $faviconInfo['extension'];?>" sizes="<?php echo $faviconInfo['width'] . 'x' . $faviconInfo['height'];?>" href="<?php echo Uri::base(false) . $faviconImage;?>" />
<?php endif;?>
<meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1" />
<?php 
	/* Include json script in the header */
	\JAmpHelper::headJsonScript();
	
	/* Include custom style for AMP pages */
	\JAmpHelper::customStyle();
	 
	/* Include custom script for AMP pages */
	\JAmpHelper::customScript(\JAmpHelper::$componentOutput);
	
	/* Include custom tags added through the public API by external components */
	\JAmpHelper::includeCustomTags();
	
	/* Include google analytics script */
	\JAmpHelper::googleAnalyticsScript();
	
	/* Include custom code before </head> */
	\JAmpHelper::addCustomCode('before_head');
?>
</head>
<body>
<?php 
/* Include custom code after <body> */
\JAmpHelper::addCustomCode('after_body');
?>
<?php if (\JAmpHelper::$pluginParams->get('amp_auto_ad_activation', 0)):?>
<amp-auto-ads type="adsense" data-ad-client="ca-pub-<?php echo \JAmpHelper::$pluginParams->get('amp_auto_ad_id', null);?>"></amp-auto-ads>
<?php endif;?>

<?php \JAmpHelper::googleAnalyticsCode(); ?>

<?php if (\JAmpHelper::$pluginParams->get('enable_sidebar_module', 0) && \JAmpHelper::$pluginParams->get('sidebar_module_name', '')) {
	include (JPATH_ROOT . PLG_JAMP_TEMPLATE_PATH . 'sidebar.php');
}?>
<header class="container">
	<?php if (JAmpHelper::$pluginParams->get('enable_scroll_to_top', 0)):?>
		<div class="target">
		  <a class="amptarget-anchor" id="top"></a>
		  <amp-position-observer on="enter:hideAnim.start; exit:showAnim.start" layout="nodisplay"></amp-position-observer>
		</div>
		<button id="scrollToTopButton" on="tap:top.scrollTo(duration=200)" class="ampscrollToTop"><?php echo JText::_(JAmpHelper::$pluginParams->get('text_scroll_to_top', 'Back to top'))?></button>
		<amp-animation id="showAnim" layout="nodisplay">
			<script type="application/json">
    			{
					"duration": "200ms",
					"fill": "both",
					"iterations": "1",
					"direction": "alternate",
					"animations": [
						{
							"selector": "#scrollToTopButton",
							"keyframes": [
							{ "opacity": "1", "visibility": "visible" }
							]
						}
					]
				}
 			 </script>
		</amp-animation>
		<amp-animation id="hideAnim" layout="nodisplay">
  			<script type="application/json">
				{
					"duration": "200ms",
					"fill": "both",
					"iterations": "1",
					"direction": "alternate",
					"animations": [
						{
							"selector": "#scrollToTopButton",
							"keyframes": [
							{ "opacity": "0", "visibility": "hidden" }
							]
						}
					]
				}
			</script>
		</amp-animation>
	<?php endif;?>
    <div id="header">
    	<?php if (\JAmpHelper::$pluginParams->get('amp_title_header_sitename', null)):?>
    		<a href="<?php echo \JAmpHelper::$ampHomeUrl;?>">
    			<div class="amp_title_header_sitename">
	    			<?php echo \JAmpHelper::$pluginParams->get('amp_title_header_sitename', null);?>
    			</div>
    		</a>
    	<?php endif;?>
    	
    	<?php if(\JAmpHelper::$pluginParams->get('enable_slideshow', 0) && \JAmpHelper::$pluginParams->get('slideshow_position', 'top') == 'top') {
			include (JPATH_ROOT . PLG_JAMP_TEMPLATE_PATH . 'slideshow.php');
		}?>
		
    	<?php if (\JAmpHelper::$pluginParams->get('amp_title_header_imagelogo', null)):?>
    		<a href="<?php echo \JAmpHelper::$ampHomeUrl;?>">
    			<div class="amp_sitelogo">
	    			<?php echo \JAmpHelper::transformImages('<img src="' . Uri::base(false) . \JAmpHelper::$pluginParams->get('amp_title_header_imagelogo', null) . '"/>', \JAmpHelper::$pluginParams->get('logo_responsive', 0));?>
    			</div>
    		</a>
    	<?php endif;?>
    	
    	<?php if (\JAmpHelper::$pluginParams->get('amp_header_h1', 1)):?>
        <h1>
            <a class="amp_page_title" href="<?php echo JAmpHelper::$ampUrl;?>">
                <?php 
                $pageTitle = \JAmpHelper::$pluginParams->get('amp_header_h1_titletype', 'page') == 'page' ? \JAmpHelper::$document->getTitle() : @\JAmpHelper::$application->getMenu()->getActive()->title;
                echo $pageTitle;
                ?>
            </a>
        </h1>
        <?php endif;?>
    </div>
    
    <?php if(\JAmpHelper::$pluginParams->get('amp_ad_activation', 0) && trim(\JAmpHelper::$pluginParams->get('amp_ad_code', '')) && in_array('beforemenu', \JAmpHelper::$pluginParams->get('amp_ad_code_position', 'header'))) {
		echo trim(\JAmpHelper::$pluginParams->get('amp_ad_code', ''));
	}?>

	<?php if(\JAmpHelper::$pluginParams->get('enable_menu_module', 0) && in_array(\JAmpHelper::$pluginParams->get('menu_module_position', 'top'), array('top', 'both'))) {
		include (JPATH_ROOT . PLG_JAMP_TEMPLATE_PATH . 'menu.php');
	}?>
	
	<?php if(\JAmpHelper::$pluginParams->get('amp_ad_activation', 0) && trim(\JAmpHelper::$pluginParams->get('amp_ad_code', '')) && in_array('aftermenu', \JAmpHelper::$pluginParams->get('amp_ad_code_position', 'header'))) {
		echo trim(\JAmpHelper::$pluginParams->get('amp_ad_code', ''));
	}?>
	
	<?php if(\JAmpHelper::$pluginParams->get('enable_slideshow', 0) && \JAmpHelper::$pluginParams->get('slideshow_position', 'top') == 'middle') {
		include (JPATH_ROOT . PLG_JAMP_TEMPLATE_PATH . 'slideshow.php');
	}?>
	
    <?php if(\JAmpHelper::$pluginParams->get('social_buttons', 1) && in_array(\JAmpHelper::$pluginParams->get('social_buttons_position', 'bottom'), array('top', 'both'))) {
    	include (JPATH_ROOT . PLG_JAMP_TEMPLATE_PATH . 'socialbuttons.php');
	}?>
	
	<?php if(\JAmpHelper::$pluginParams->get('amp_instagram_enable_top', 0) && \JAmpHelper::isAMPComponentPublished('amp_instagram_pages') && $instagramTopShortcode = trim(\JAmpHelper::$pluginParams->get('amp_instagram_top_shortcode', ''))):?>
		<amp-instagram
		    data-shortcode="<?php echo $instagramTopShortcode;?>"
		    data-captioned
		    width="400"
		    height="400"
		    layout="responsive">
		</amp-instagram>
	<?php endif;?>
	
	<?php if(\JAmpHelper::$pluginParams->get('amp_twitter_enable_top', 0) && \JAmpHelper::isAMPComponentPublished('amp_twitter_pages') && $twitterTopID = trim(\JAmpHelper::$pluginParams->get('amp_twitter_top_tweetid', ''))):?>
		<amp-twitter width="375"
		  height="472"
		  layout="responsive"
		  data-tweetid="<?php echo $twitterTopID;?>">
		</amp-twitter>
	<?php endif;?>	
	
	<?php if(\JAmpHelper::$pluginParams->get('enable_header_module', 0) && \JAmpHelper::$pluginParams->get('header_module_name', '')) {
		include (JPATH_ROOT . PLG_JAMP_TEMPLATE_PATH . 'header_module.php');
	}?>
	
	<?php if(\JAmpHelper::$pluginParams->get('enable_top_module', 0) && \JAmpHelper::$pluginParams->get('top_module_name', '')) {
		include (JPATH_ROOT . PLG_JAMP_TEMPLATE_PATH . 'top_module.php');
	}?>
	
	<?php 
	\JAmpHelper::debugFirm();
	?>
</header>
<section role="main">
	<?php if(\JAmpHelper::$pluginParams->get('amp_ad_activation', 0) && trim(\JAmpHelper::$pluginParams->get('amp_ad_code', '')) && in_array('header', \JAmpHelper::$pluginParams->get('amp_ad_code_position', 'header'))) {
		echo trim(\JAmpHelper::$pluginParams->get('amp_ad_code', ''));
	}?>