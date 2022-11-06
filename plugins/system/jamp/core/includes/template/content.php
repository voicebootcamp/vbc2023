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
?>
<div id="main">
	<?php if(\JAmpHelper::$pluginParams->get('enable_main_module', 0) && \JAmpHelper::$pluginParams->get('main_module_name', '')) {
		include (JPATH_ROOT . PLG_JAMP_TEMPLATE_PATH . 'main_module.php');
	}?>
	<article class="post">
		<?php if(\JAmpHelper::$pluginParams->get('amp_facebook_like', 0) && in_array(\JAmpHelper::$pluginParams->get('amp_facebook_like_button_position', 'bottom'), array('top', 'both'))):?>
			<amp-facebook-like 
				width="90"
				height="<?php echo \JAmpHelper::$pluginParams->get('amp_facebook_like_button', 'button_count') == 'button_count' ? '28' : '58';?>"
			 	layout="fixed"
			 	data-layout="<?php echo \JAmpHelper::$pluginParams->get('amp_facebook_like_button', 'button_count');?>"
			 	data-size="large"
			 	data-href="<?php echo Uri::current();?>">
			</amp-facebook-like>
		<?php endif;?>
		
		<?php if(\JAmpHelper::$pluginParams->get('amp_ad_activation', 0) && trim(\JAmpHelper::$pluginParams->get('amp_ad_code', '')) && in_array('beforecomponent', \JAmpHelper::$pluginParams->get('amp_ad_code_position', 'header'))) {
			echo trim(\JAmpHelper::$pluginParams->get('amp_ad_code', ''));
		}?>
	
		<div class="amp-meta">
			<div class="amp-byline">
					<?php echo \JAmpHelper::transformContents( \JAmpHelper::$componentOutput ); ?>
                </div>
		</div>
		
		<?php if(\JAmpHelper::$pluginParams->get('amp_ad_activation', 0) && trim(\JAmpHelper::$pluginParams->get('amp_ad_code', '')) && in_array('aftercomponent', \JAmpHelper::$pluginParams->get('amp_ad_code_position', 'header'))) {
			echo trim(\JAmpHelper::$pluginParams->get('amp_ad_code', ''));
		}?>

		<?php if(\JAmpHelper::$pluginParams->get('amp_facebook_like', 0) && in_array(\JAmpHelper::$pluginParams->get('amp_facebook_like_button_position', 'bottom'), array('bottom', 'both'))):?>
			<amp-facebook-like 
				width="90"
				height="<?php echo \JAmpHelper::$pluginParams->get('amp_facebook_like_button', 'button_count') == 'button_count' ? '28' : '58';?>"
			 	layout="fixed"
			 	data-layout="<?php echo \JAmpHelper::$pluginParams->get('amp_facebook_like_button', 'button_count');?>"
			 	data-size="large"
			 	data-href="<?php echo Uri::current();?>">
			</amp-facebook-like>
		<?php endif;?>
		<div class="clearfix"></div>
	</article>
	<?php if(\JAmpHelper::$pluginParams->get('enable_center_module', 0) && \JAmpHelper::$pluginParams->get('center_module_name', '')) {
		include (JPATH_ROOT . PLG_JAMP_TEMPLATE_PATH . 'center_module.php');
	}?>
</div>