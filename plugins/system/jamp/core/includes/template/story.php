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
use Joomla\CMS\Uri\Uri;
?>
<head>
<meta charset="utf-8" />
<title><?php $ampStoryMaintitle = htmlspecialchars(Text::_(\JAmpHelper::$pluginParams->get('amp_story_maintitle', null)), ENT_COMPAT, 'UTF-8'); echo $ampStoryMaintitle;?></title>
<link rel="canonical" href="<?php echo \JAmpHelper::$ampUrl; ?>" />
<?php 
if($faviconImage = \JAmpHelper::$pluginParams->get('amp_favicon', null)):
$faviconInfo = \JAmpImage::getImageInfo($faviconImage);
?>
<link rel="icon" type="image/<?php echo $faviconInfo['extension'];?>" sizes="<?php echo $faviconInfo['width'] . 'x' . $faviconInfo['height'];?>" href="<?php echo JUri::base(false) . $faviconImage;?>" />
<?php endif;?>
<meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1" />
<?php 
	/* Include json script in the header */
	\JAmpHelper::headAmpstoryJsonScript();
	
	/* Include custom style for AMP pages */
	\JAmpHelper::customStyle();
	 
	/* Include custom script for AMP pages */

	echo '<script async custom-element="amp-story" src="https://cdn.ampproject.org/v0/amp-story-1.0.js"></script>';

	$pageCounter = 0;
	$pageContents = array();
	$hasVideo = false;
	$hasAudio = false;
	$audioBackground = null;
	$autoAdvanceAfterSeconds = \JAmpHelper::$pluginParams->get("amp_story_auto_advance", null);
	$autoAdvanceAfter = $autoAdvanceAfterSeconds ? ' auto-advance-after="' . $autoAdvanceAfterSeconds . 's"' : '';
	for($i=1;$i<=5;$i++) {
		// Transform all pages content to AMP HTML
		if($pageContent = \JAmpHelper::$pluginParams->get("amp_story_content_page$i", null)) {
			$pageContents[$i] = \JAmpHelper::transformContents(Text::_($pageContent), false);
			
			// Check if any video tags are included
			if($pageContents[$i]->find('amp-video')) {
				$hasVideo = true;
			}
			
			// Check if any audio tags are included
			if($pageContents[$i]->find('amp-audio')) {
				$hasAudio = true;
			}
		}
		
		// Check if the amp-video is required because of an mp4 story background or if a amp-video tag is found in the story pseudo $componentOutput
		if(\JAmpHelper::$pluginParams->get("amp_story_background_video_page$i", null)) {
			$hasVideo = true;
		}
	}
	
	if($hasVideo) {
		echo '<script async custom-element="amp-video" src="https://cdn.ampproject.org/v0/amp-video-0.1.js"></script>';
	}
		
	//  Check if the amp-audio is required because of an audio story background or if a amp-audio tag is found in the pages content
	$ampStoryAudioBackground = \JAmpHelper::$pluginParams->get("amp_story_audio_background", null);
	if($hasAudio || $ampStoryAudioBackground) {
		echo '<script async custom-element="amp-audio" src="https://cdn.ampproject.org/v0/amp-audio-0.1.js"></script>';
		if($ampStoryAudioBackground) {
			$audioBackground = 'background-audio="' . $ampStoryAudioBackground . '"';
		}
	}
	
	echo '<script async src="https://cdn.ampproject.org/v0.js"></script>';
	
	/* Include google analytics script */
	\JAmpHelper::googleAnalyticsScript();
	
	// Rescale the publisher logo to be square 1x1 ratio and at least 96x96 px wide
	$publisherLogoImage = \JAmpHelper::$pluginParams->get('amp_story_publisher_logo', null);
	$fileImagePath = JPATH_SITE . '/' . $publisherLogoImage;
	$publisherLogoImageUrl = null;
	if($publisherLogoImage && file_exists($fileImagePath)) {
		$publisherLogoImageUrl = JUri::root(false) . $publisherLogoImage;
		list( $logoWidth, $logoHeight ) = @getimagesize( $fileImagePath );
		// Not compliant image detected, recalculate, rescale size and override info and url
		if($logoHeight > 0 && ($logoWidth / $logoHeight) != 1) {
			$minSize = min(array($logoWidth, $logoHeight));
			$minSize = $minSize < 96 ? 96 : $minSize;
			$publisherLogoImageUrl = \JAmpImage::_($publisherLogoImage, $minSize, $minSize, array('ampstory_publisher_logo'));
		}
	}
	
	/** Resize and crop the poster image in all 3 different formats, must be:
	 *  1x1 square 928x928px
	 *  3:4 portrait 696x928px, width 0.75 ratio
	 *  4:3 landscape 928x696px, height 0.75 ratio
	 */
	$posterImage = \JAmpHelper::$pluginParams->get('amp_story_poster_image', null);
	$fileImagePath = JPATH_SITE . '/' . $posterImage;
	$path = pathinfo ( $fileImagePath );
	$maxSize = 928;
	$posterImageUrl = null;
	$posterImagePortraitUrl = null;
	$posterImageLandscapeUrl = null;
	if($posterImage && file_exists($fileImagePath)) {
		$posterImageUrl = JUri::root(false) . $posterImage;
		list( $posterWidth, $posterHeight ) = @getimagesize( $fileImagePath );
		// Not compliant image detected, recalculate, resize and override info and url to generate the 1x1 square 928x928px min size
		if($posterHeight > 0 && (($posterWidth / $posterHeight) != 1 || $posterWidth < 928 || $posterHeight < 928)) {
			$maxSize = max(array($posterWidth, $posterHeight));
			$maxSize = $maxSize > 928 ? $maxSize : 928;
			$posterImageUrl = \JAmpImage::_($posterImage, $maxSize, $maxSize, array('ampstory_image'), true);
		}
		
		// Now go on and rescale crop the other portrait and landscape formats
		$posterImagePortraitUrl = \JAmpImage::_($posterImage, (int)($maxSize * 0.75), $maxSize, array('ampstory_image'), true, str_replace('.', '_portait.', $path['basename']));
		$posterImageLandscapeUrl = \JAmpImage::_($posterImage, $maxSize, (int)($maxSize * 0.75), array('ampstory_image'), true, str_replace('.', '_landscape.', $path['basename']));
	}
?>
</head>
<body>
<amp-story standalone title="<?php echo $ampStoryMaintitle;?>" <?php echo $audioBackground;?>
	publisher="<?php echo htmlspecialchars(\JAmpHelper::$pluginParams->get('amp_story_publisher_name', null), ENT_COMPAT, 'UTF-8');?>"
	publisher-logo-src="<?php echo $publisherLogoImageUrl;?>"
	poster-portrait-src="<?php echo $posterImagePortraitUrl;?>"
	poster-square-src="<?php echo $posterImageUrl;?>"
	poster-landscape-src="<?php echo $posterImageLandscapeUrl;?>">
<?php \JAmpHelper::googleAnalyticsCode(); ?>
<?php for($i=1;$i<=5;$i++) :?>
<?php if(\JAmpHelper::$pluginParams->get('amp_story_page' . $i . '_enable', 0)):?>
<amp-story-page id="page-<?php echo $i;?>"<?php echo $autoAdvanceAfter;?>>
	<amp-story-grid-layer template="fill">
		<?php if($backgroundVideoPage = \JAmpHelper::$pluginParams->get('amp_story_background_video_page' . $i, null)): 
			$loop = \JAmpHelper::$pluginParams->get('amp_story_background_video_loop_page' . $i, 1) ? 'loop' : '';
			?>
			<amp-video autoplay <?php echo $loop;?> width="<?php echo \JAmpHelper::$pluginParams->get('amp_story_background_video_width_page' . $i, 720);?>" height="<?php echo \JAmpHelper::$pluginParams->get('amp_story_background_video_height_page' . $i, 960);?>"
				poster="<?php echo JUri::root(false) . \JAmpHelper::$pluginParams->get('amp_story_videoposter_page' . $i, null);?>"
				layout="responsive">
				<source src="<?php echo $backgroundVideoPage;?>" type="video/mp4">
			</amp-video>
		<?php endif;?>
		<?php if(!$backgroundVideoPage && $backgroundImagePage = \JAmpHelper::$pluginParams->get('amp_story_background_image_page' . $i, null)) {
			$fileImagePath = JPATH_SITE . '/' . $backgroundImagePage;
			$path = pathinfo ( $fileImagePath );
			$backgroundImagePageUrl = null;
			if($backgroundImagePage && file_exists($fileImagePath)) {
				$backgroundImagePageUrl = JUri::root(false) . $backgroundImagePage;
				list( $backgroundImagePageWidth, $backgroundImagePageHeight ) = @getimagesize( $fileImagePath );
			}
			echo '<amp-img src="' . $backgroundImagePageUrl . '" width="' . $backgroundImagePageWidth . '" height="' . $backgroundImagePageHeight . '" layout="responsive"></amp-img>';
		}
		?>
	</amp-story-grid-layer>
	<?php 
		$titleAnimationEffect = \JAmpHelper::$pluginParams->get('amp_story_title_animation_page' . $i, 'fly-in-top');
		$titleAnimation = $titleAnimationEffect != 'none' ? ' animate-in="' . $titleAnimationEffect . '"' : '';
		$contentAnimationEffect = \JAmpHelper::$pluginParams->get('amp_story_content_animation_page' . $i, 'fly-in-bottom');
		$contentAnimation = $contentAnimationEffect != 'none' ? ' animate-in="' . $contentAnimationEffect . '"' : '';
		$linksAnimationEffect = \JAmpHelper::$pluginParams->get('amp_story_links_animation_page' . $i, 'fly-in-bottom');
		$linksAnimation = $linksAnimationEffect != 'none' ? ' animate-in="' . $linksAnimationEffect . '"' : '';
		
		$titleAnimationEffectDuration = \JAmpHelper::$pluginParams->get('amp_story_title_animation_duration_page' . $i, '0.5');
		$titleAnimationtDuration = $titleAnimationEffectDuration ? ' animate-in-duration="' . $titleAnimationEffectDuration . 's"' : '';
		$contentAnimationEffectDuration = \JAmpHelper::$pluginParams->get('amp_story_content_animation_duration_page' . $i, '0.5');
		$contentAnimationDuration = $contentAnimationEffectDuration ? ' animate-in-duration="' . $contentAnimationEffectDuration . 's"' : '';
		$linksAnimationEffectDuration = \JAmpHelper::$pluginParams->get('amp_story_links_animation_duration_page' . $i, '0.5');
		$linksAnimationDuration = $linksAnimationEffectDuration ? ' animate-in-duration="' . $linksAnimationEffectDuration . 's"' : '';
		$pageCounter++;
	?>
	<amp-story-grid-layer template="<?php echo \JAmpHelper::$pluginParams->get('amp_story_content_template_page' . $i, 'vertical')?>">
		<h1<?php echo $titleAnimation;?><?php echo $titleAnimationtDuration;?>><?php echo Text::_(\JAmpHelper::$pluginParams->get('amp_story_title_page' . $i, null));?></h1>
		<p<?php echo $contentAnimation;?><?php echo $contentAnimationDuration;?>><?php echo isset($pageContents[$i]) ? $pageContents[$i] : '';?></p>
	</amp-story-grid-layer>
	<?php if($pageCounter > 1):?>
	<amp-story-cta-layer>
		<span<?php echo $linksAnimation;?><?php echo $linksAnimationDuration;?>><?php echo \JAmpHelper::transformContents(Text::_(\JAmpHelper::$pluginParams->get('amp_story_links_page' . $i, null)), false);?></span>
	</amp-story-cta-layer>
	<?php endif;?>
</amp-story-page>
<?php endif;?>
<?php endfor;?>
<amp-story-bookend src="<?php echo Uri::root(false) . 'plugins/system/jamp/core/bookconfig.json';?>" layout="nodisplay"></amp-story-bookend>
</amp-story>
</body>