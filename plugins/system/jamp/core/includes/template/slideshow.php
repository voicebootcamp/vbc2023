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

// Check if the slideshow is enabled only in homepage
$activeMenu = \JAmpHelper::$application->getMenu()->getActive();
if (is_object ( $activeMenu )) {
	$slideshowOnlyHome = \JAmpHelper::$pluginParams->get('enable_slideshow_only_home', 0);
	if($slideshowOnlyHome && !$activeMenu->home) {
		return;
	}
}

// Check if the slideshow is enabled only on certain pages
$slideshowPages = \JAmpHelper::$pluginParams->get ( 'enable_slideshow_by_pages', 0 );
if (is_array ( $slideshowPages ) && ! in_array ( 0, $slideshowPages, false )) {
	if (is_object ( $activeMenu )) {
		$menuItemid = $activeMenu->id;
		if (!in_array ( $menuItemid, $slideshowPages )) {
			return false;
		}
	}
}

$pathFolder = \JAmpHelper::$pluginParams->get('pathfolder');
$slideshowImages = \JAmpHelper::getFolderImages();
if(empty($slideshowImages)) {
	return;
}

$toReplacePath = JPATH_ROOT . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . $pathFolder;

$autoplaySlideshow = \JAmpHelper::$pluginParams->get('slideshow_autoplay', 1) ? 'autoplay' : '';
$delaySlides = \JAmpHelper::$pluginParams->get('slideshow_delay', 2000);

// Set max images dimensions
$maxImageWidth = 0;
$maxImageHeight = 0;

ob_start();
foreach($slideshowImages as $slideshowImage):
list($width, $height) = @getimagesize($slideshowImage);
$maxImageWidth = $width && ($width > $maxImageWidth) ? $width : $maxImageWidth;
$maxImageHeight = $height && ($height > $maxImageHeight) ? $height : $maxImageHeight;
?>
	<amp-img src="<?php echo Uri::root(false) . 'images/' . $pathFolder . '/' . trim(str_replace($toReplacePath, '', $slideshowImage), '/');?>"
        width="<?php echo $width;?>"
        height="<?php echo $height;?>"
        layout="responsive">
	</amp-img>
<?php endforeach;
$imagesContentBuffer = ob_get_contents();
ob_end_clean();
?>

<amp-carousel id="ampcarousel" width="<?php echo $maxImageWidth;?>" height="<?php echo $maxImageHeight;?>" layout="responsive" type="slides" <?php echo $autoplaySlideshow;?> delay="<?php echo $delaySlides;?>">
<?php echo $imagesContentBuffer;?>
</amp-carousel>