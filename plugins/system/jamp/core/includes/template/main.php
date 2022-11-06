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
@header('Content-type: text/html');
$language = null;
$customHreflang = null;
$ampSuffix = \JAmpHelper::$pluginParams->get ( 'amp_suffix', 'amp' );
$ampstory = \JAmpHelper::$pluginParams->get('amp_story_enable', 0) && (bool)\JAmpHelper::$isHomepageRequest;
if(\JAmpHelper::$pluginParams->get('include_language_attribute', 0)) {
	$languageIso = \JAmpHelper::$application->getlanguage()->getTag();
	if(!$languageIso) {
		$languageIso = \JAmpHelper::$application->input->get('lang');
	}
	$languageChunks = explode('-', $languageIso);
	$language =  ' lang="' . $languageChunks[0] . '"';
	// Check for hreflang tags
	if(!empty(\JAmpHelper::$document->_links)) {
		foreach (\JAmpHelper::$document->_links as $customLink=>$customTag) {
			if($customTag['relation'] == 'alternate' && isset($customTag['attribs']['hreflang'])) {
				// Ampify rel alternate tag
				if(stripos($customLink, '.html') !== false) {
					$customAMPLink = str_replace('.html', '.' . $ampSuffix . '.html' , rtrim($customLink, '/'));
				} else {
					$customAMPLink = rtrim($customLink, '/') . '/' . $ampSuffix;
				}
				$customHreflang .= '<link href="' . $customAMPLink . '" rel="alternate" hreflang="' . $customTag['attribs']['hreflang'] . '" />';
			}
		}
	}
}
?>
<!doctype html>
<html ⚡<?php echo $language;?>>
<?php if(!$ampstory):?>
<head>
<?php require_once( JPATH_ROOT . PLG_JAMP_TEMPLATE_PATH . 'header.php' ); ?>
<?php require_once( JPATH_ROOT . PLG_JAMP_TEMPLATE_PATH . 'content.php' ); ?>
<?php require_once( JPATH_ROOT . PLG_JAMP_TEMPLATE_PATH . 'footer.php' ); ?>
</body>
<?php else:?>
<?php require_once( JPATH_ROOT . PLG_JAMP_TEMPLATE_PATH . 'story.php'); ?>
<?php endif;?>
</html>
