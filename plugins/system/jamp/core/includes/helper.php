<?php
/**
 * @package JAMP::plugins::system
 * @subpackage jamp
 * @subpackage core
 * @subpackage includes
 * @author Joomla! Extensions Store
 * @copyright (C)2016 Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined ( '_JEXEC' ) or die ();
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Date\Date;
use Joomla\String\StringHelper;

/**
 * Class to manage all the stuff to render and transform AMP pages output
 * @package JAMP::plugins::system
 * @subpackage jamp
 * @subpackage core
 * @subpackage includes
 */
class JAmpHelper {
	/**
	 * Hold plugin configuration object
	 * @var Object
	 * @access public
	 * @static
	 */
	public static $pluginParams;
	
	/**
	 * Reference the Joomla document object
	 * @var Object
	 * @access public
	 * @static
	 */
	public static $document;
	
	/**
	 * Reference the Joomla application object
	 * @var Object
	 * @access public
	 * @static
	 */
	public static $application;
	
	/**
	 * Buffer got from the Document instance holding the component output to be transformed to AMP
	 * @var string
	 * @access public
	 * @static
	 */
	public static $componentOutput;
	
	/**
	 * Hold the canonical real URL
	 * @var string
	 * @access public
	 * @static
	 */
	public static $canonicalUrl;
	
	/**
	 * Hold the current AMP URL
	 * @var string
	 * @access public
	 * @static
	 */
	public static $ampUrl;
	
	/**
	 * Hold the home page AMP URL
	 * @var string
	 * @access public
	 * @static
	 */
	public static $ampHomeUrl;
	
	/**
	 * Store a valid AMP JAmp request for external components checking
	 * @var string
	 * @access public
	 * @static
	 */
	public static $isJAmpRequest;
	
	/**
	 * Check if the dispatched request is for the home page
	 * @var boolean
	 * @access public
	 * @static
	 */
	public static $isHomepageRequest;
	
	/**
	 * Detection for amp-anim GIF images
	 * 
	 * @var boolean
	 * @access public
	 * @static
	 */
	public static $gifImages;
	
	/**
	 * Store and output the dispatched option view to a debug label
	 *
	 * @var string
	 * @access public
	 * @static
	 */
	public static $dispatchedOptionView;
	
	/**
	 * Store custom tags added by external components through the public API
	 * @var array
	 * @access public
	 * @static
	 */
	public static $customTags = array();
	
	/**
	 * Disable the optional meta tags at runtime through the public API
	 * @var array
	 * @access public
	 * @static
	 */
	public static $disableMetaTags = false;
	
	/**
	 * Disable the AMP core meta tags at runtime through the public API
	 * @var array
	 * @access public
	 * @static
	 */
	public static $disableJsonMetaTags = false;
	
	/**
	 * Check if the url is an absolute URL in some way
	 *
	 * @param string $url
	 * @return bool
	 */
	private static function isFullyQualified($url) {
		$isFullyQualified = substr($url, 0, 7) == 'http://' || substr($url, 0, 8) == 'https://' || substr($url, 0, 2) == '//';
		return $isFullyQualified;
	}
	
	/**
	 * Check if the url is an image URL
	 *
	 * @param string $url
	 * @return bool
	 */
	private static function isImage( $url ) {
		static $explodedCustomExcludePathsArray = null;
		
		$pos = strrpos( $url, ".");
		$excludeAmpifyPaths = trim(self::$pluginParams->get('exclude_ampify_paths', ''));
		if ($pos === false && !$excludeAmpifyPaths) {
			return false;
		}
		
		$ext = strtolower(trim(substr( $url, $pos)));
		$imgExts = array(".gif", ".jpg", ".jpeg", ".png", ".tiff", ".tif", ".pdf", ".doc", '.docx', ".odt");
		
		// Exclude images if detected
		if ( in_array($ext, $imgExts) ) {
			return true;
		}
		
		// Support for custom exclude paths from the AMPify
		if($excludeAmpifyPaths) {
			if(!$explodedCustomExcludePathsArray) {
				$explodedCustomExcludePathsArray = explode(',', trim($excludeAmpifyPaths, ','));
				if(!empty($explodedCustomExcludePathsArray)) {
					$explodedCustomExcludePathsArray = array_map(function($item){ return trim($item); }, $explodedCustomExcludePathsArray);				
				}
			}
			
			foreach ($explodedCustomExcludePathsArray as $excludeAmpifyPath) {
				if(strpos($url, $excludeAmpifyPath) !== false || $excludeAmpifyPath === '*') {
					return true;
				}
			}
		}

		return false;
	}
	
	/**
	 * This function will sanitize the tags to make AMP compatible content
	 * 
	 * @access private
	 * @return string
	 */
	private static function sanitizeTags( $html, $sDomContent ) {
		// Set vars
		$ampSuffix = self::$pluginParams->get('amp_suffix', 'amp');
		$sefSuffix = self::$application->get('sef_suffix', 1);
		if (defined ( 'SH404SEF_IS_RUNNING' )) {
			if(class_exists('\Sh404sefFactory')) {
				$sefSuffix = (bool)\Sh404sefFactory::getConfig ()->suffix;
			}
		}
		// Replace src links.
		$base = Uri::base(true) . '/';
		$fixRelativeLinks = self::$pluginParams->get('fix_relative_links', 0);

		// Check if remove default prefix is active and initialize language plugin params
		$removeLanguageDefaultPrefixLanguageFilterPlugin = null;
		if(self::$pluginParams->get('remove_language_default_prefix', 0)) {
			$removeLanguageDefaultPrefixLanguageFilterPlugin = PluginHelper::isEnabled('system', 'languagefilter');
			if($removeLanguageDefaultPrefixLanguageFilterPlugin) {
				$pluginLangFilter = PluginHelper::getPlugin('system', 'languagefilter');
				$pluginLangFilterParams = json_decode($pluginLangFilter->params);
			}
		}
		
		$removedElementsBycssSelectors = str_replace("'", '', trim(self::$pluginParams->get('removed_elements_bycss_selectors', '')));
		
		// Load additional custom config selectors for this dispatched component if any
		if($manifestConfigRemovedSelectors = self::loadManifestFile(self::$application->input->get('option'), 'removed_elements_bycss_selectors')) {
			$removedElementsBycssSelectors .= ',' . $manifestConfigRemovedSelectors;
			$removedElementsBycssSelectors = trim($removedElementsBycssSelectors, ',');
		}
		
		// All the following tags are not admitted
		$aBlkTags = array(
				'script',
				'noscript',
				'style',
				'frame',
				'frameset',
				'object',
				'param',
				'applet',
				'link',
				'picture',
				'embed',
				'embedvideo',
				'base'
		);
		
		if(!self::$pluginParams->get('enable_form', 0) || stripos(self::$ampUrl, 'https') === false) {
			$aBlkTags = array_merge($aBlkTags, array(
					'form',
					'input',
					'textarea',
					'select',
					'option'
			));
			$formProcessing = false;
		} else {
			$formProcessing = true;
		}
		
		// Choose custom excluded tags
		if($customBlockedTags = trim(self::$pluginParams->get('disallowed_tags', ''))) {
			$customBlockedTagsArray = explode(',', $customBlockedTags);
			if(!empty($customBlockedTagsArray)) {
				$aBlkTags = array_merge($aBlkTags, $customBlockedTagsArray);
			}
		}
		
		// Check if some config selectors are available for this component
		if($manifestConfigSelectors = self::loadManifestFile(self::$application->input->get('option'), 'disallowed_tags')) {
			$blockedTagsConfigSelectors = explode(',', $manifestConfigSelectors);
			if(!empty($blockedTagsConfigSelectors)) {
				$aBlkTags = array_merge($aBlkTags, $blockedTagsConfigSelectors);
			}
		}
		
		foreach( $aBlkTags as $sTag ) {
			foreach ( $sDomContent->find( $sTag ) as $element ) {
				$element->outertext = '';
				
				// Add support for picture tag fallback to included img if any transforming picture -> img
				$nodeName = $element->nodeName();
				if($nodeName == 'picture') {
					$innerImg = $element->getElementByTagName('img');
					if($innerImg) {
						$element->outertext = self::transformImages($innerImg->outertext(), self::$pluginParams->get('images_responsive', 0))->outertext;
					}
				}
			}
		}
		
		// Special removing for certain elements by css selector parameter
		if($removedElementsBycssSelectors) {
			foreach ($sDomContent->find($removedElementsBycssSelectors) as $removedElement) {
					$removedElement->outertext = '';
			}
		}
	
		// Special treatment for a tags
		foreach ( $sDomContent->find( 'a' ) as $element ) {
			// href is not detected, assign a generic one because it's required
			if( !$element->hasAttribute( 'href' ) ) {
				$element->setAttribute( 'href', '#' );
			}
	
			// Only certain attributes are admitted
			$allowed_tags = array(
					'href',
					'target',
					'rel',
					'name',
					'id',
					'class',
					'on'
			);
			foreach( $element->getAllAttributes() as $attrKey => $attrVal ) {
				// Remove not allowed attributes
				if( !in_array( $attrKey, $allowed_tags ) ) {
					$element->removeAttribute( $attrKey );
					continue;
				}
				
				// Ensure that no javascript: href are found, not valid for AMP so remove href attribute
				if(stripos($attrVal, 'javascript') !== false) {
					$element->removeAttribute( $attrKey );
					continue;
				}
				
				// Leave unaltered the mailto links
				if(stripos($attrVal, 'mailto:') !== false) {
					continue;
				}
				
				// Leave unaltered the tel links
				if(stripos($attrVal, 'tel:') !== false) {
					continue;
				}
				
				// Fix the default language module #1, skip all links already AMPed
				$alreadyAMPedLink = false;
				if(stripos($attrVal, '.' . $ampSuffix) !== false || stripos($attrVal, '/' . $ampSuffix) !== false) {
					$alreadyAMPedLink = true;
				}
				
				// Fix the default language module #2, remove prefix is enabled do the same on the real language module link
				if(self::$pluginParams->get('remove_language_default_prefix', 0) && $removeLanguageDefaultPrefixLanguageFilterPlugin) {
					if($pluginLangFilterParams->remove_default_prefix && $parentUL = $element->find_ancestor_tag('ul')) {
						$parentULClass = $parentUL->getAttribute('class');

						if(stripos($parentULClass, 'lang-inline') !== false) {
							$defaultSiteLanguage = ComponentHelper::getParams('com_languages')->get('site', 'en-GB');
							$defaultSiteLanguageSEF = @array_shift(explode('-', $defaultSiteLanguage));

							if(stripos($attrVal, '/' . $defaultSiteLanguageSEF) !== false && $attrVal != '/' . $defaultSiteLanguageSEF . '.html') {
								$attrVal = str_ireplace('/' . $defaultSiteLanguageSEF, '', $attrVal);
							} elseif(stripos($attrVal, $defaultSiteLanguageSEF . '.html')) {
								// Home page link detected
								$attrVal = '/';
							}
						}
					}
				}
				
				// Add the AMP suffix to all inner page links
				if($attrKey == 'href' && (!self::isFullyQualified($attrVal) || stripos($attrVal, Uri::root(false)) !== false) && !self::isImage($attrVal) && !$alreadyAMPedLink) {
					// Manage query string raw params
					if(strpos($attrVal, '?')) {
						if(stripos($attrVal, '?start=') !== false) {
							if($sefSuffix && stripos($attrVal, '.html') !== false) {
								$element->setAttribute( 'href', str_replace('.html', '.' . $ampSuffix . '.html' , rtrim($attrVal, '/')));
							} else {
								$attrVal = str_replace('/?start=', '?start=', $attrVal);
								$element->setAttribute( 'href', str_replace('?start=', '/' . $ampSuffix . '?start=', rtrim($attrVal, '/')));
							}
						} elseif(stripos($attrVal, '?limitstart=') !== false) {
							$attrVal = str_replace('/?limitstart=', '?limitstart=', $attrVal);
							$element->setAttribute( 'href', str_replace('?limitstart=', '/' . $ampSuffix . '/?limitstart=', rtrim($attrVal, '/')));
						} else {
							$element->setAttribute( 'href', $attrVal . '&' . $ampSuffix . '=1' );
						}
					} else {
						// Manage the amp suffix with html suffix variants /amp or .amp.html
						if($sefSuffix) {
							if(stripos($attrVal, '.html') !== false) {
								$newSuffixAmpLink = str_replace('.html', '.' . $ampSuffix . '.html' , rtrim($attrVal, '/'));
							} else {
								// Suffix enabled but this is the link to home page
								if(stripos($attrVal, '#') === false)  {
									$newSuffixAmpLink = rtrim($attrVal, '/') . '/' . $ampSuffix;
								} else {
									// Leave untouched if a naked anchor
									$newSuffixAmpLink = $attrVal;
								}
							}
							$element->setAttribute( 'href', $newSuffixAmpLink );
						} else {
							// By default append at the end the suffix /amp
							if(stripos($attrVal, '#') === false && stripos($attrVal, '/' . $ampSuffix) === false) {
								$element->setAttribute( 'href', rtrim($attrVal, '/') . '/' . $ampSuffix  );
							} else {
								// Link with an anchor tag detected, preserve the ending anchor tag but exclude naked anchore secche
								if(!stripos($attrVal, '#') == 0) {
									$newAnchorAmpLink = str_ireplace('#', '/' . $ampSuffix . '#' , $attrVal);
									$newAnchorAmpLink = str_ireplace('//' . $ampSuffix . '#', '/' . $ampSuffix . '#', $newAnchorAmpLink);
									$element->setAttribute('href', $newAnchorAmpLink);
								}
							}
						}
					}
				}
				
				// Fix and route relative links
				if(	$fixRelativeLinks && $attrKey == 'href' &&
					(!self::isFullyQualified($attrVal) && stripos($attrVal, Uri::root(false)) === false) &&
					substr($attrVal, 0, 1) != '/' && substr($attrVal, 0, 1) != '#') {
					$buffer = $element->getAttribute('href');
					$element->setAttribute( 'href', $base . ltrim($buffer, '/'));
				
					// Replace index.php URI by SEF URI
					if(preg_match('/^index.php\?([^"]+)/i', $attrVal)) {
						// Retrieve the original non-AMP $attrVal
						$buffer = $attrVal;
						$buffer =  Route::_($buffer);
						
						// Check exclusions for this link before AMPifying it
						$newSuffixAmpLink = $buffer;
						if(!self::isImage($buffer)) {
							if(strpos($buffer, '?')) {
								$newSuffixAmpLink =  $buffer . '&' . $ampSuffix . '=1';
							} else {
								if($sefSuffix) {
									if(stripos($buffer, '.html') !== false) {
										$newSuffixAmpLink = str_replace('.html', '.' . $ampSuffix . '.html' , rtrim($buffer, '/'));
									} else {
										$newSuffixAmpLink = rtrim($buffer, '/') . '/' . $ampSuffix;
									}
								} else {
									// By default append at the end the suffix /amp
									$newSuffixAmpLink = rtrim($buffer, '/') . '/' . $ampSuffix  ;
								}
							}
						}
						$element->setAttribute( 'href', $newSuffixAmpLink );
					}
				}
			}
		}
		
		// Special treatment for replacement elements
		if(self::$pluginParams->get('replace_buttons_enable', 0)) {
			$replaceBtnsSelector = self::$pluginParams->get('replace_buttons_selector', '');
			
			// Load additional custom config selectors for this dispatched component if any
			$option = self::$application->input->get('option');
			if($manifestConfigSelectors = self::loadManifestFile(self::$application->input->get('option'), 'replace_buttons_css_selectors')) {
				$replaceBtnsSelector .= ',' . $manifestConfigSelectors;
				$replaceBtnsSelector = trim($replaceBtnsSelector, ',');
			}
			
			foreach ( $sDomContent->find( $replaceBtnsSelector ) as $element ) {
				// Remove typical <input> tag attributes
				$element->removeAttribute('value');
				$element->removeAttribute('type');
				
				// Retrieve existin attributes that can be reused for the new tag
				$elementId = $element->getAttribute('id');
				$elementName = $element->getAttribute('name');
				$elementClass = $element->getAttribute('class');
				$elementTitle = $element->getAttribute('title');
				
				// Setup the link text
				$replaceButtonText = Text::_(trim(self::$pluginParams->get('replace_buttons_text', '')));
				if((!$replaceButtonText && $elementTitle) || (self::$pluginParams->get('replace_buttons_text_usetitle', 0) && $elementTitle)) {
					$replaceButtonText = $elementTitle;
				}
				
				// Overwrite the element actual HTML code
				$element->outertext = '<a id="' . $elementId . '" name="' . $elementName . '" class="' . $elementClass . '" href="' . self::$canonicalUrl . '" title="' . $elementTitle . '">' . $replaceButtonText . '</a>';
			}
		}
		
		// Special treatment for form tags if the form processing is enabled
		if($formProcessing) {
			// Define the selected form to process based on the css selectors and input types not allowed
			$selectedForms = self::$pluginParams->get('enable_form_bycss_selectors', 'form');
			
			// Load additional custom config selectors for this dispatched component if any
			if($manifestConfigSelectors = self::loadManifestFile(self::$application->input->get('option'), 'enable_form_bycss_selectors')) {
				$selectedForms .= ',' . $manifestConfigSelectors;
				$selectedForms = trim($selectedForms, ',');
			}
			
			$setFormAction = self::$pluginParams->get('set_form_action', 1);
			$formBlockedTags = array (
					'input[type=file]',
					'input[type=image]',
					'input[type=password]',
					'input[type=button]'
			);
			
			// 1° STEP: process the form to transform to the AMP format 
			foreach ( $sDomContent->find( $selectedForms ) as $formElement ) {
				// Mark as a valid form to not be removed further
				$formElement->no_remove = true;
				
				// Set the method to be always GET
				if(!$formElement->getAttribute('action-xhr')) {
					$formElement->setAttribute('method', 'GET');
				}
				
				// Set the action to the current AMP page
				if(($setFormAction || !$formElement->getAttribute('action')) && !$formElement->getAttribute('action-xhr')) {
					$formElement->setAttribute('action', str_ireplace('http://', 'https://', self::$ampUrl));
				}
				
				// Set the target to the current window
				$formElement->setAttribute('target', '_top');
				
				foreach( $formBlockedTags as $formBlockedTag ) {
					$foundTagsToRemove = $formElement->find( $formBlockedTag );
					foreach ( $foundTagsToRemove as $formElement ) {
						$formElement->outertext = '';
					}
				}
			}
			
			// 2° STEP: remove all other forms not to transform
			foreach ( $sDomContent->find( 'form' ) as $formElementToRemove ) {
				if(!$formElementToRemove->no_remove) {
					$formElementToRemove->outertext = '';
				}
			}
		}
		
		return $html->save();
	}

	/**
	 * This function will sanitize the video to make AMP compatible
	 * 
	 * @access private
	 * @return string
	 */
	private static function sanitizeVideo( $html, $sDomContent ) {
		foreach ( $sDomContent->find( 'video' ) as $element ) {
			// Only video tags with valid src
			if( !$element->hasAttribute( 'src' ) && !$element->hasChildNodes()) {
				$element->outertext = '';
				continue;
			}
	
			// Always ensure and force the protocol to th video source to be https, otherwise not accepted
			$element_src = $element->getAttribute( 'src' );
			if ( strpos( $element_src, '//' ) !== false ) {
				$protocol = explode( "//", $element_src );
				$perma = strpos( $protocol[0], 'https' );
				if ( $perma === false && $protocol[0] != '' ) {
					$new_src = 'https://';
					foreach( $protocol as $pro => $proval ) {
						if( $pro == 0 ) { continue; }
						$new_src .= $protocol[$pro];
					}
					$element->setAttribute( 'src', $new_src );
				}
			}
			
			// Allowed attributes only
			$allowed_tags = array(
					'src',
					'poster',
					'autoplay',
					'controls',
					'loop',
					'layout',
					'class',
					'width',
					'height',
					'id'
			);
			foreach( $element->getAllAttributes() as $attrKey => $attrVal ) {
				if( !in_array( $attrKey, $allowed_tags ) ) {
					$element->removeAttribute( $attrKey );
				}
			}
			
			// Manage the desired layout for videos, being them responsive AMP or not
			if( !$element->hasAttribute( 'layout' ) && self::$pluginParams->get('videos_responsive', 1) ) {
				$element->setAttribute( 'layout', 'responsive' );
			}
	
			if( !$element->hasAttribute( 'width' )) {
				$element->setAttribute( 'width', '1280' );
			}
			
			if( !$element->hasAttribute( 'height' )) {
				$element->setAttribute( 'height', '720' );
			}
			
			$new_tag = 'amp-video';
			$element->outertext = strtr( $element->outertext, array( '<video' => '<' . $new_tag ) );
			$element->outertext = strtr( $element->outertext, array( '</video' => '</' . $new_tag ) );
		}
		return $html->save();
	} 
	
	/**
	 * This function will sanitize the audio to make AMP compatible
	 * 
	 * @access private
	 * @return string
	 */
	private static function sanitizeAudio( $html, $sDomContent ) {
		foreach ( $sDomContent->find( 'audio' ) as $element ) {
	
			if( !$element->hasAttribute( 'src' ) && !$element->hasChildNodes()) {
				$element->outertext = '';
				continue;
			}
	
			// Always ensure and force the protocol to th video source to be https, otherwise not accepted
			$element_src = $element->getAttribute( 'src' );
			if ( strpos( $element_src, '//' ) !== false ) {
				$protocol = explode( "//", $element_src );
				$perma = strpos( $protocol[0], 'https' );
				if ( $perma === false && $protocol[0] != '' ) {
					$new_src = 'https://';
					foreach( $protocol as $pro => $proval ) {
						if( $pro == 0 ) { continue; }
						$new_src .= $protocol[$pro];
					}
					$element->setAttribute( 'src', $new_src );
				}
			}
	
			// Allowed attributes only
			$allowed_tags = array(
					'src',
					'autoplay',
					'controls',
					'loop',
					'class',
					'width',
					'height',
					'id'
			);
			foreach( $element->getAllAttributes() as $attrKey => $attrVal ) {
				if( !in_array( $attrKey, $allowed_tags ) ) {
					$element->removeAttribute( $attrKey );
				}
			}
	
			$new_tag = 'amp-audio';
			$element->outertext = strtr( $element->outertext, array( '<audio' => '<' . $new_tag ) );
			$element->outertext = strtr( $element->outertext, array( '</audio' => '</' . $new_tag ) );
		}
		return $html->save();
	}
	
	/**
	 * This function will sanitize the image to make AMP compatible
	 *
	 * @access private
	 * @return string
	 */
	private static function sanitizeImage( $html, $sDomContent, $responsive, $lightbox = true ) {
		$lightboxAttributes = null;
		$imagesResponsiveSelectors = self::$pluginParams->get('images_responsive_selectors', '');
		$imagesNotResponsiveSelectors = self::$pluginParams->get('images_not_responsive_selectors', '');
		$minWidthForResponsiveImages = (int)self::$pluginParams->get('minwidth_responsive_images', '250');
		$preloadAmpImagesSelectors = self::$pluginParams->get('preload_amp_images', '');
		
		// Load additional custom config selectors for this dispatched component if any
		if($manifestConfigSelectors = self::loadManifestFile(self::$application->input->get('option'), 'images_responsive_selectors')) {
			$imagesResponsiveSelectors .= ',' . $manifestConfigSelectors;
			$imagesResponsiveSelectors = trim($imagesResponsiveSelectors, ',');
		}
		
		// Load additional custom config selectors for this dispatched component if any
		if($manifestConfigNotSelectors = self::loadManifestFile(self::$application->input->get('option'), 'images_not_responsive_selectors')) {
			$imagesNotResponsiveSelectors .= ',' . $manifestConfigNotSelectors;
			$imagesNotResponsiveSelectors = trim($imagesNotResponsiveSelectors, ',');
		}
		
		// Set responsive AMP images by CSS selectors
		if($imagesResponsiveSelectors) {
			foreach ( $sDomContent->find( $imagesResponsiveSelectors ) as $element ) {
				$element->setAttribute( 'layout', 'responsive' );
			}
		}
		
		// Set NOT responsive AMP images by CSS selectors
		if($imagesNotResponsiveSelectors) {
			foreach ( $sDomContent->find( $imagesNotResponsiveSelectors ) as $element ) {
				$element->removeAttribute( 'layout' );
			}
		}
		
		// Set preloaded AMP images by CSS selectors
		if($preloadAmpImagesSelectors) {
			foreach ( $sDomContent->find( $preloadAmpImagesSelectors ) as $element ) {
				$element->setAttribute( 'data-hero', '' );
			}
		}
		
		// Patch for subdomained images with relative links
		$uriInstance = Uri::getInstance();
		$lastSubdomainPiece = null;
		$urlPieces = explode('/', Uri::root(false));
		$lastSubdomainIndex = count($urlPieces);
		if(isset($urlPieces[$lastSubdomainIndex - 2])) {
			$lastSubdomainPiece = '/' . $urlPieces[$lastSubdomainIndex - 2];
		}
		
		foreach ( $sDomContent->find( 'img' ) as $element ) {
			// Skip imvalid images without the src attribute
			if( !$element->hasAttribute( 'src' ) && !$element->hasAttribute( 'data-src' ) ) {
				$element->outertext = '';
				continue;
			}
			
			// Set if images must be rendered using the AMP lightbox web component
			if($lightbox && self::$pluginParams->get('enable_images_lightbox', 0)) {
				$lightboxAttributes = ' on="tap:amp_lightbox" role="button" tabindex="0"';
			}
			
			// Remove any href attribute to nullify parent anchor tags, this ensure the correct lightbox working mode
			if($lightboxAttributes) {
				$parentNode = $element->parentNode();
				$parentNodeName = $parentNode->nodeName();
				if($parentNode && $parentNodeName == 'a') {
					if(self::$pluginParams->get('enable_images_lightbox', 0) == 2) {
						// Remove the href of the parent tag, the lightbox effect is destructive in the mode 2
						$parentNode->removeAttribute('href');
					} elseif(self::$pluginParams->get('enable_images_lightbox', 0) == 1) {
						// Preserve the href of the parent tag, revert the lightbox effect for linked images in the mode 1
						$lightboxAttributes = null;
					}
				}
			}
			
			$remoteAbsolute = false;
			$dataSrcImage = false;
			$srcAttribute = $element->getAttribute( 'src' );
			
			// Remove any query string
			if(self::$pluginParams->get('remove_images_query_string', 1) && stripos($srcAttribute, '?') !== false) {
				$srcAttribute = preg_replace('/\?.*/', '', $srcAttribute);
			}
			
			// If no src attribute has been found fallback to the data-src one
			if(!$srcAttribute) {
				$srcAttribute = $element->getAttribute( 'data-src' );
				$dataSrcImage = true;
			}
			
			// If the data-src attribute has a higher priority and exists, override the standard src attribute
			$overrideDataSrc = self::$pluginParams->get('override_image_data_src', 0);
			if($overrideDataSrc && $dataSrcAttribute = $element->getAttribute( 'data-src' )) {
				$srcAttribute = $dataSrcAttribute;
				$dataSrcImage = true;
			}
			
			// Are we dealing with a relative image URL?
			$subSrcAttribute = substr($srcAttribute, 0, 8);
			if(strpos($subSrcAttribute, 'http') === false && strpos($subSrcAttribute, '//') !== 0 && stripos($srcAttribute, 'data:image') === false) {
				$element_path = JPATH_ROOT . '/' . $srcAttribute;
				if(self::$pluginParams->get('fully_qualified_images', 1)) {
					if($lastSubdomainPiece && stripos($srcAttribute, $lastSubdomainPiece) === 0) { // Found a subdomained relative image URL
						$element_src = rtrim($uriInstance->getScheme() . '://' . $uriInstance->getHost(), '/') . $srcAttribute;
						$element->setAttribute( 'src', $element_src );
						$element_path = JPATH_ROOT . '/' . str_replace($lastSubdomainPiece . '/', '', $srcAttribute);
					} else {
						// No subdomained relative image URL
						$element_src = Uri::root(false) . ltrim($srcAttribute, '/');
						$element->setAttribute( 'src', $element_src );
					}
				} else {
					$element_src = $srcAttribute;
				}
			} else {
				// We deal with an already absolute URL, ok for image src but not for img path so clean it
				$element_src = $srcAttribute;
				
				// Always set the absolute URL src attribute if it's a dummy data-src image
				if($dataSrcImage) {
					$element->setAttribute( 'src', $element_src );
				}
				
				// We must have a local absolute URL in order to reference a local path
				if(stripos($element_src, Uri::root(false)) !== false) {
					$element_path = JPATH_ROOT . '/' . str_replace(Uri::root(false), '', $srcAttribute);
				} else {
					$element_path = $element_src;
					$remoteAbsolute = true;
				}
			}
				
			// An explicit width/height attributes are missing so detect them by PHP
			if( !$element->hasAttribute( 'width' ) || !$element->hasAttribute( 'height' ) ) {
				$width = $height = null;
				if(!$remoteAbsolute) {
					list( $width, $height ) = @getimagesize( $element_path );
				} else {
					// Check if URL is incomplete format
					if(strpos($element_path, '//') === 0) {
						$element_path = 'http:' . $element_path;
					}
					$image = new \JAmpFastImage($element_path);
					if($image->getHandle() !== false) {
						list( $width, $height ) = $image->getSize();
					}
				}
				
				// Always override images attributes with native image size
				if(self::$pluginParams->get('images_override_attributes', 0)) {
					if( $width != '' )
						$element->setAttribute( 'width', $width );
					else
						$element->setAttribute( 'width', self::$pluginParams->get('fallback_images_width', 300) );
					if( $height != '' )
						$element->setAttribute( 'height', $height );
					else
						$element->setAttribute( 'height', self::$pluginParams->get('fallback_images_height', 150) );
				} else {
					// Preserve one or both images attributes
					if( !$element->hasAttribute( 'width' ) ) {
						if( $width != '' )
							$element->setAttribute( 'width', $width );
						else
							$element->setAttribute( 'width', self::$pluginParams->get('fallback_images_width', 300) );
					}
					if( !$element->hasAttribute( 'height' ) ) {
						if( $height != '' )
							$element->setAttribute( 'height', $height );
						else
							$element->setAttribute( 'height', self::$pluginParams->get('fallback_images_height', 150) );
					}
				}
			}
	
			// Manage the desired layout for images, being them responsive AMP or not
			if( !$element->hasAttribute( 'layout' ) && $responsive ) {
				$element->setAttribute( 'layout', 'responsive' );
			}
			
			// Remove the responsive layout for images less than a minimum width
			if( $minWidthForResponsiveImages && $element->hasAttribute( 'width' ) && $element->getAttribute( 'width' ) < $minWidthForResponsiveImages) {
				$element->removeAttribute( 'layout' );
			}
			
			// Allowed attributes only
			$allowed_tags = array(
					'src',
					'alt',
					'title',
					'class',
					'srcset',
					'width',
					'height',
					'layout',
					'id',
					'data-hero'
			);
			foreach( $element->getAllAttributes() as $attrKey => $attrVal ) {
				if( !in_array( $attrKey, $allowed_tags ) ) {
					$element->removeAttribute( $attrKey );
				}
			}
	
			$elementSources = explode( '.', $element_src );
			$ext = strtolower( end( $elementSources ) );
			if ( $ext == 'gif' && self::$gifImages) {
				$new_tag = 'amp-anim';
			} else {
				$new_tag = 'amp-img';
			}
			
			// Check if it's needed to replace the CDN domain
			if(self::$pluginParams->get('images_cdn_rewriting', 0)) {
				$currentSrcAttribute = $element->getAttribute( 'src' );
				$currentSrcAttribute = StringHelper::str_ireplace(Uri::root(false), trim(self::$pluginParams->get('images_cdn_rewriting_domain')), $currentSrcAttribute);
				$element->setAttribute( 'src', $currentSrcAttribute );
			}
			
			$element->outertext = strtr( $element->outertext, array( '<img' => '<' . $new_tag . $lightboxAttributes ) )  . '</' . $new_tag . '>';
		}
		return $html->save();
	}
	
	/*
	 * This function will sanitize the iframe to make AMP compatible
	*/
	private static function sanitizeIframe( $html, $sDomContent ) {
		foreach ( $sDomContent->find( 'iframe' ) as $element ) {
	
			// Required src attribute
			if( !$element->hasAttribute( 'src' ) ) {
				$element->outertext = '';
				continue;
			}
	
			// Manage responsive rendering of the iframe
			if( !$element->hasAttribute( 'layout' ) && self::$pluginParams->get('iframes_responsive', 1)) {
				$element->setAttribute( 'layout', 'responsive' );
			}
	
			// Frameborder attribute required
			if( !$element->hasAttribute( 'frameborder' ) ) {
				$element->setAttribute( 'frameborder', '0' );
			}
	
			// Sandbox attribute required to be managed in special way
			if(self::$pluginParams->get('allowsameorigin_iframes', 1)) {
				$sandbox_class = array( 'allow-scripts', 'allow-same-origin', 'allow-popups', 'allow-popups-to-escape-sandbox' );
			} else {
				$sandbox_class = array( 'allow-scripts', 'allow-popups', 'allow-popups-to-escape-sandbox' );
			}
			
			if( !$element->hasAttribute( 'sandbox' ) ) {
				$element->setAttribute( 'sandbox', implode( " ", $sandbox_class ) );
			} else {
				$sandbox_class_val = '';
				$element_sandbox = explode( " ", $element->getAttribute( 'sandbox' ) );
				foreach( $sandbox_class as $key => $val ) {
					if( !in_array( $sandbox_class[$key], $element_sandbox ) ) {
						$sandbox_class_val .= ' ' . $sandbox_class[$key];
					}
				}
				$element->setAttribute( 'sandbox', implode( " ", $element_sandbox ) . $sandbox_class_val );
			}
	
			// Always https iframe src attribute
			$element_src = $element->getAttribute( 'src' );
			if ( strpos( $element_src, '//' ) !== false ) {
				$protocol = explode( "//", $element_src );
				$perma = strpos( $protocol[0], 'https' );
				if ( $perma === false ) {
					$new_src = 'https://';
					foreach( $protocol as $pro => $proval ) {
						if( $pro == 0 ) { continue; }
						$new_src .= $protocol[$pro];
					}
					$element->setAttribute( 'src', $new_src );
				}
			}
	
			
			if( !$element->hasAttribute( 'width' ) || !$element->hasAttribute( 'height' ) ) {
				if( !$element->hasAttribute( 'width' ) ) {
					$element->setAttribute( 'width', self::$pluginParams->get('fallback_iframes_width', 600) . 'px');
				}
				if( !$element->hasAttribute( 'height' ) ) {
					$element->setAttribute( 'height', self::$pluginParams->get('fallback_iframes_height', 400) . 'px');
				}
			}
	
			// Allowed attributes only
			$allowed_tags = array(
					'src',
					'class',
					'frameborder',
					'sandbox',
					'layout',
					'width',
					'height',
					'id'
			);
			foreach( $element->getAllAttributes() as $attrKey => $attrVal ) {
				if( !in_array( $attrKey, $allowed_tags ) ) {
					$element->removeAttribute( $attrKey );
				}
				// Check and remove width=100%
				if($attrKey == 'width' && $attrVal == '100%') {
					$element->setAttribute( $attrKey, '600' );
				}
			}
	
			$new_tag = 'amp-iframe';
			$element->outertext = strtr( $element->outertext, array( '<iframe' => '<' . $new_tag ) );
			$element->outertext = strtr( $element->outertext, array( '</iframe' => '</' . $new_tag ) );
		}
		return $html->save();
	}
	
	/**
	* This function will sanitize Instagram to make AMP compatible
	* @access private
	* @return string
	*/
	private static function sanitizeInstagram($html, $sDomContent) {
		$instagramCssSelector = rtrim(str_replace("'", '', trim(self::$pluginParams->get('amp_instagram_transform_css_selector', 'a.instagram-media'))), ',');
		foreach ( $sDomContent->find ( $instagramCssSelector ) as $element ) {
			$insta_id = '';
			$href = $element->getAttribute ( 'href' );
			$matches = [];
			if (preg_match ( '/instagram.*.com\/p\/([a-zA-Z0-9\-_]*)/i', $href, $matches )) {
				if (! empty ( $matches [1] )) {
					$insta_id = $matches [1];
				}
			}
			
			// Process only correct links if a tweet id has been found in the expected position
			if(!$insta_id) {
				continue;
			}
			
			// Allowed attributes only
			$allowed_tags = array (
					'data-shortcode',
					'data-captioned',
					'height',
					'layout',
					'width'
			);
			foreach ( $element->getAllAttributes () as $attrKey => $attrVal ) {
				if (! in_array ( $attrKey, $allowed_tags )) {
					$element->removeAttribute ( $attrKey );
				}
			}
			$new_tag = 'amp-instagram';
			$element->outertext = strtr ( $element->outertext, array (
					'<a' => '<' . $new_tag . ' layout="responsive" data-captioned width="400"  height="400" data-shortcode="' . $insta_id . '"'
			) );
			$element->outertext = strtr ( $element->outertext, array (
					'</a' => '</' . $new_tag
			) );
		}
		return $html->save ();
	}
	
	
	/**
	 * This function will sanitize Twitter to make AMP compatible
	 * @access private
	 * @return string
	 */
	private static function sanitizeTwitter($html, $sDomContent) {
		$twitterCssSelector = rtrim(str_replace("'", '', trim(self::$pluginParams->get('amp_twitter_transform_css_selector', 'a.twitter-tweet'))), ',');
		foreach ( $sDomContent->find ( $twitterCssSelector ) as $element ) {
			$tweet_id = '';
			$href = $element->getAttribute ( 'href' );
			
			$matches = [];
			if (preg_match ( '/twitter.com\/[a-zA-Z0-9-_]*\/status\/([0-9]*)/i', $href, $matches )) {
				if (! empty ( $matches [1] )) {
					$tweet_id = $matches [1];
				}
			}
			
			// Process only correct links if a tweet id has been found in the expected position
			if(!$tweet_id) {
				continue;
			}
			
			// Allowed attributes only
			$allowed_tags = array (
					'data-tweetid',
					'height',
					'layout',
					'width'
			);
			
			foreach ( $element->getAllAttributes () as $attrKey => $attrVal ) {
				if (! in_array ( $attrKey, $allowed_tags )) {
					$element->removeAttribute ( $attrKey );
				}
			}
			
			$new_tag = 'amp-twitter';
			$element->outertext = strtr ( $element->outertext, array (
					'<a' => '<' . $new_tag . ' layout="responsive" width="375"  height="472" data-tweetid="' . $tweet_id . '"'
			) );
			$element->outertext = strtr ( $element->outertext, array (
					'</a' => '</' . $new_tag
			) );
			
		}
		
		return $html->save ();
	}
	
	/**
	 * This function will sanitize the Attributes to make AMP compatible content
	 * 
	 * @access private
	 * @return string
	 */
	private static function sanitizeAttributes( $sContent ) {
		$aBlkAttr = array(  'size',
							'onclick',
							'onmouseover',
							'onmouseout',
							'onsubmit',
							'onfocus',
							'onblur'
		);
		
		// Check if inline style attribute must be removed
		if(self::$pluginParams->get('remove_inline_css', 1)) {
			$aBlkAttr[] = 'style';
		}
		
		// Choose custom blocked attributes
		if($customBlockedAttributes = trim(self::$pluginParams->get('disallowed_attributes', ''))) {
			$customBlockedAttributesArray = explode(',', rtrim($customBlockedAttributes, ','));
			if(!empty($customBlockedAttributesArray)) {
				$aBlkAttr = array_merge($aBlkAttr, $customBlockedAttributesArray);
			}
		}
		
		// Check if some config selectors are available for this component
		if($manifestConfigSelectors = self::loadManifestFile(self::$application->input->get('option'), 'disallowed_attributes')) {
			$blockedAttributesConfigSelectors = explode(',', $manifestConfigSelectors);
			if(!empty($blockedAttributesConfigSelectors)) {
				$aBlkAttr = array_merge($aBlkAttr, $blockedAttributesConfigSelectors);
			}
		}
		
		foreach( $aBlkAttr as $sAttr ) {
			$sAttr = trim($sAttr);
			$sContent = preg_replace( '/(<[^>]+) ' . $sAttr . '=".*?"/iu', '$1', $sContent );
			$sContent = preg_replace( '#(<[a-z ]*)(' . $sAttr . '=("|\')(.*?)("|\'))([a-z ]*>)#iu', '\\1\\6', $sContent );
			$sContent = preg_replace( "/(<[^>]+) " . $sAttr . "='.*?'/iu", "$1", $sContent );
			$sContent = preg_replace( "#(<[a-z ]*)(" . $sAttr . "=('|\')(.*?)('|\'))([a-z ]*>)#iu", "\\1\\6", $sContent );
			$sContent = preg_replace( "#(<[^>]+)\s(" . $sAttr . ")([\s>])([^=])#iu", "\\1\\3\\4", $sContent );
		}
		
		// Fix for not closing <source> tags
		if(StringHelper::strpos($sContent, '<source') !== false) {
			$sContent = preg_replace('/(<source)(.*)(>)/isU', '$1$2/$3', $sContent);
		}
		
		return $sContent;
	}
	
	/**
	 * This function will transform menus using accordion to a nested set of accordions based on parent deeper class
	 * 
	 * @access private
	 * @param string $html
	 * @param boolean $sDomContent
	 * @return string
	 */
	private static function transformMenus( $html, $sDomContent ) {
		// Search for every li elements having a submenu tree
		$parentElementsSelector = trim(self::$pluginParams->get('menu_module_nested_accordion_selector', 'li.parent'));
		$animateMenu = self::$pluginParams->get('menu_module_animate', 1) ? ' animate' : '';
		$elements = $sDomContent->find( $parentElementsSelector );
		
		// If some parent elements are found fo on and process them recursively
		foreach ($elements as $element) {
			// Get the first child element that must be cleaned from all attributes and replaced with a header tag
			$firstChild = $element->firstChild();
			$firstChild->outertext = strtr( $firstChild->outertext, array( '<a' => '<header><a' ) );
			$firstChild->outertext = strtr( $firstChild->outertext, array( '</a' => '</a></header' ) );
			$firstChild->outertext = strtr( $firstChild->outertext, array( '<span' => '<header><span' ) );
			$firstChild->outertext = strtr( $firstChild->outertext, array( '</span' => '</span></header' ) );
			
			// Grab the inner text of the element and wrap it in the amp-accordion structure
			$currentInnerText = $element->innertext;
			$wrapHtml = '<amp-accordion class="menu-accordion"' . $animateMenu . '><section>' . $currentInnerText . '</section></amp-accordion>';
			// Then reasign the wrapped structure
			$element->innertext = $wrapHtml;
			
			// Analyze again the updated inner HTML, if some nested parent are found go on recursively
			$innerHtml = new \JAmpSimpleHtmlDom();
			$innerSDomContent = $innerHtml->load( $element->innertext );
			if($innerSDomContent->find( $parentElementsSelector )) {
				$element->innertext = self::transformMenus($innerHtml, $innerSDomContent);
			}
		}
		
		return $html->save();
	}
	
	/**
	 * This function will transform images using a slideshow that is an AMP carousel
	 * 
	 * @access private
	 * @param string $html
	 * @param boolean $sDomContent
	 * @return string
	 */
	private static function transformSlideshows( $html, $sDomContent ) {
		$autoplaySlideshow = self::$pluginParams->get('slideshow_autoplay', 1) ? 'autoplay' : '';
		$delaySlides = self::$pluginParams->get('slideshow_delay', 2000);
		
		$slideshowImagesSelector = trim(self::$pluginParams->get('slideshow_byselector', ''));
		
		// Support for multiple separated slideshows
		$slideshowImagesSelectorArray = explode(',', $slideshowImagesSelector);
		
		// Process all found slideshows
		if(!empty($slideshowImagesSelectorArray)) {
			foreach ($slideshowImagesSelectorArray as $slideshowIndex=>$slideshowElementSelector) {
				// Process images for an automated slideshow conversion
				$imagesContentBuffer = null;
				$removedElement = null;
				
				// Set max images dimensions
				$maxImageWidth = 0;
				$maxImageHeight = 0;
				
				// Select and remove all targeted images, then build the buffer with reconstructed images for the slideshow
				foreach ($sDomContent->find($slideshowElementSelector) as $removedElement) {
					list($width, $height) = [$removedElement->getAttribute('width'), $removedElement->getAttribute('height')];
					$maxImageWidth = $width && ($width > $maxImageWidth) ? $width : $maxImageWidth;
					$maxImageHeight = $height && ($height > $maxImageHeight) ? $height : $maxImageHeight;
		
					$imagesContentBuffer .= str_replace('/>', '></amp-img>', $removedElement->outertext ());
					$removedElement->outertext = '';
				}
		
				// Assign the carousel to the latest image found used as a placeholder
				if($imagesContentBuffer && $removedElement) {
					// Store the slideshow HTML code
					$ampSlideshowCode = '<amp-carousel id="ampcarousel_' . ($slideshowIndex + 1) . '" width="' .  $maxImageWidth . '"' . ' height="' . $maxImageHeight . '"  layout="responsive" type="slides" ' . $autoplaySlideshow . ' delay="' . $delaySlides . '">' . $imagesContentBuffer . '</amp-carousel>';
					
					// Check if the removed element has a <a> parent tag, in such case replace it
					if($removedElementAncestorATag = $removedElement->find_ancestor_tag('a')) {
						$removedElementAncestorATag->outertext = $ampSlideshowCode;
					} else {
						$removedElement->outertext = $ampSlideshowCode;
					}
				}
			}
		}
	
		return $html->save();
	}
	
	/**
	 * This function will transform a set of HTML elements into an AMP Lightbox component
	 * It will take a set of elements based on a CSS selector, group them and add a button to open that group
	 *
	 * @access private
	 * @param string $html
	 * @param boolean $sDomContent
	 * @return string
	 */
	private static function transformAmpLightbox( $html, $sDomContent ) {
		// Generator for the static counter
		static $staticIdentifier = 1;
		
		// Process elements nodes for an automated lightbox conversion
		$elementsContentBuffer = null;
		$removedElement = null;
	
		// Lightbox params
		$lightboxEffect = self::$pluginParams->get('lightbox_effect', 'fade-in');
		$lightboxNodesSelector = trim(self::$pluginParams->get('lightbox_byselector', ''));
	
		// Select and remove all targeted elements nodes, then build the buffer with reconstructed nodes to wrap into the lightbox container
		foreach ($sDomContent->find($lightboxNodesSelector) as $removedElement) {
			$elementsContentBuffer .= $removedElement->outertext ();
			$removedElement->outertext = '';
		}
	
		// Assign the carousel to the latest image found used as a placeholder
		if($elementsContentBuffer && $removedElement) {
			// Build the lightbox HTML code
			$ampLightboxCode =	'<button class="amp-lightbox-opener" on="tap:jamp-lightbox-identifier' . $staticIdentifier . '">' . strtolower(str_ireplace('D_M', 'D M', JText::_('READ_MORE'))) . '</button>' .
								'<amp-lightbox id="jamp-lightbox-identifier' . $staticIdentifier . '" layout="nodisplay" animate-in="' . $lightboxEffect . '">' .
									'<div>' . $elementsContentBuffer . '</div>' .
									'<button class="amp-lightbox-closer" on="tap:jamp-lightbox-identifier' . $staticIdentifier . '.close">X</button>' .
								'</amp-lightbox>';
			
			// Assign the lightbox structure to the latest target node
			$removedElement->outertext = $ampLightboxCode;

			// Increment for the next lightbox item
			$staticIdentifier++;
		}
		
	
		return $html->save();
	}
	
	/**
	 * This function will handle, resize, recrop, refactor, etc the page image representative to be compliant with the latest Google spec
	 * of at least 1200x675px AKA 16:9 AKA 800.000px multiplied
	 *
	 * @access private
	 * @param string $pageImageRepresentative
	 * @param int $width
	 * @param int $height
	 * @return mixed New data image associative array if the image has been modified, false otherwise
	 */
	private static function handlePageImageRepresentative( $pageImageRepresentative, $width, $height ) {
		$modified = false;
		
		// Not compliant image detected, recalculate, rescale size and override info and url
		if($width < 1200) {
			$pageImageRepresentativeUrl = \JAmpImage::_($pageImageRepresentative, 1200);
			$imageInstance = \JAmpImage::getLastInstance();
			$width = 1200;
			$height = (int)$imageInstance->newHeight;
			$modified = true;
		}
		
		// Ensure that the image is at least 16:9 aspect ratio 1200x675 and 800.000px, if not force a resize with or without cropping
		$leastAspectRatio = (int)(($width * 9) / 16);
		if($height < $leastAspectRatio) {
			$width = $width >= 1200 ? $width : 1200;
			$height = $leastAspectRatio;
			$pageImageRepresentativeUrl = \JAmpImage::_($pageImageRepresentative, $width, $height, array('page'), false, false, true);
			$modified = true;
		}
		
		// Ensure that the image does not exceed 1:1 aspect ratio, AKA height > width. If so rescale it to 1:1 based on highest height size
		if($height > $width) {
			$width = $height;
			$pageImageRepresentativeUrl = \JAmpImage::_($pageImageRepresentative, $width, $height, array('page'), false, false, true);
			$modified = true;
		}
		
		if($modified) {
			$newImageData = array('width'=>$width, 'height'=>$height, 'imageurl'=>$pageImageRepresentativeUrl);
			return $newImageData;
		}
		
		return false;
	}
	
	/**
	 * It will convert all the standard tags to amp-* tags to make it AMP compatible
	 * 
	 * @access public
	 * @param string $sContent The main body output contents
	 * @param boolean $lightbox Set if the enable_images_lightbox param must be evaluated or if the lightbox is always excluded
	 * @param boolean $isMenu Evaluate if contents are rendered by a menu module
	 * @return string
	 */
	public static function transformContents( $sContent, $lightbox = true, $isMenu = false ) {
		// Do not process empty strings
		if(!$sContent) {
			return $sContent;
		}
		
		$sContent = self::sanitizeAttributes( $sContent );
	
		$html = new \JAmpSimpleHtmlDom();
		$sDomContent = $html->load( $sContent );
	
		self::sanitizeTags( $html, $sDomContent );
		self::sanitizeImage( $html, $sDomContent, self::$pluginParams->get('images_responsive', 0), $lightbox );
		self::sanitizeIframe( $html, $sDomContent );
		self::sanitizeVideo( $html, $sDomContent );
		self::sanitizeAudio( $html, $sDomContent );
		
		// Check if a special processing for menus is required
		if($isMenu && self::$pluginParams->get('menu_module_nested_accordion', 0)) {
			self::transformMenus( $html, $sDomContent );
		}
		
		if(!$isMenu && self::$pluginParams->get('enable_slideshow_byselector', 0)) {
			self::transformSlideshows( $html, $sDomContent);
		}
		
		if(!$isMenu && self::$pluginParams->get('enable_lightbox_byselector', 0)) {
			self::transformAmpLightbox( $html, $sDomContent);
		}
		
		if(self::$pluginParams->get('amp_twitter_transform', 0)) {
			self::sanitizeTwitter( $html, $sDomContent );
		}
		
		if(self::$pluginParams->get('amp_instagram_transform', 0)) {
			self::sanitizeInstagram( $html, $sDomContent );
		}
		
		return $sDomContent;
	}
	
	/**
	 * It will convert all the img tags to amp-img to make it AMP compatible
	 * 
	 * @access public
	 * @return string
	 */
	public static function transformImages( $imageHtml, $responsive ) {
		$imageHtml = self::sanitizeAttributes( $imageHtml );
	
		$htmlDomObject = new \JAmpSimpleHtmlDom();
		$sDomContent = $htmlDomObject->load( $imageHtml );
	
		self::sanitizeImage( $htmlDomObject, $sDomContent, $responsive, false);
		return $sDomContent;
	}
	
	/**
	 * Find the image representative URL using the parameter CSS selector
	 *
	 * @access public
	 * @return string
	 */
	public static function findSchemaType( ) {
		// If it's a com_content article, retrieve metadata settings to check if an AMP image is chosen
		$option = self::$application->input->get('option');
		$view = self::$application->input->get('view');
		$catid = self::$application->input->getInt('catid');
		$id = self::$application->input->getInt('id');
		
		// PRIORITY 1: it's a component com_content article request, try to check if the article has an AMP meta image parameter set
		if($option == 'com_content' && $view == 'article' && $catid && $id) {
			$db = Factory::getContainer()->get('DatabaseDriver');
			$query = $db->getQuery(true);
			$query->select('metadata');
			$query->from('#__categories');
			$query->where('id = ' . $catid);
			$metadata = $db->setQuery($query)->loadResult();
			$metadata = json_decode($metadata);
			if(isset($metadata->jamp_schema_type) && $metadata->jamp_schema_type) {
				return $metadata->jamp_schema_type;
			}
		}
		
		return null;
	}
	
	/**
	 * Find the image representative URL using the parameter CSS selector
	 * 
	 * @access public
	 * @return string
	 */
	public static function findPageRepresentativeImage( $sContent ) {
		// Null init
		$pageRepresentativeImageUrl = '';
		
		// If it's a com_content article, retrieve metadata settings to check if an AMP image is chosen
		$option = self::$application->input->get('option');
		$view = self::$application->input->get('view');
		$id = self::$application->input->getInt('id');
		
		// PRIORITY 1: it's a component com_content article request, try to check if the article has an AMP meta image parameter set
		if($option == 'com_content' && $view == 'article' && $id) {
			$db = Factory::getContainer()->get('DatabaseDriver');
			$query = $db->getQuery(true);
			$query->select('metadata');
			$query->from('#__content');
			$query->where('id = ' . $id);
			$metadata = $db->setQuery($query)->loadResult();
			$metadata = json_decode($metadata);
			if(isset($metadata->jamp_image) && $metadata->jamp_image) {
				// For J4 query string is needed to remove it
				$metadata->jamp_image = StringHelper::substr($metadata->jamp_image, 0, StringHelper::strpos($metadata->jamp_image, '#'));
				$pageRepresentativeImageUrl = $metadata->jamp_image;
			}
		}
		
		// PRIORITY 2: first page image from a CSS selector
		if(!$pageRepresentativeImageUrl) {
			$html = new \JAmpSimpleHtmlDom();
			$sDomContent = $html->load( $sContent );
			$pageRepresentativeRelativeImageUrlSelectors = self::$pluginParams->get('page_image_css_selector', 'img.ampmetaimage');
			if($manifestConfigSelectors = self::loadManifestFile($option, 'page_image_css_selector')) {
				$pageRepresentativeRelativeImageUrlSelectors .= ',' . $manifestConfigSelectors;
				$pageRepresentativeRelativeImageUrlSelectors = trim($pageRepresentativeRelativeImageUrlSelectors, ',');
			}
			foreach ( $sDomContent->find( $pageRepresentativeRelativeImageUrlSelectors ) as $element ) {
				$pageRepresentativeImageUrl = $element->getAttribute( 'src' );
				break;
			}
		}
		
		// Always return a local relative URL, replace the absolute part of a local site link if any
		$pageRepresentativeRelativeImageUrl = str_replace(Uri::root(false), '', $pageRepresentativeImageUrl);
		
		// Double check to ensure that an incomplete absolute URL is not passed through
		// Ensure that the image is hosted on the local path server in the case that it has an absolute URL in one of the 3 shapes
		$uriRootToCompare = preg_replace('/http(s)?:/i', '', Uri::root(false));
		if(substr($pageRepresentativeImageUrl, 0, 2) == '//' && stripos($pageRepresentativeImageUrl, $uriRootToCompare) !== false) {
			$pageRepresentativeRelativeImageUrl = str_replace($uriRootToCompare, '', $pageRepresentativeImageUrl);
		}
		
		return $pageRepresentativeRelativeImageUrl;
	}
	
	/**
	 * This function will add custom style required for AMP Pages to display
	 * 
	 * @access public
	 * @return string
	 */
	public static function customStyle() {
		// Check for Google fonts custom
		$customGoogleFont = trim(self::$pluginParams->get ( 'custom_google_font', '' ));
		if($customGoogleFont) {
			$fontEncoded = urlencode($customGoogleFont);
			echo "<link href='https://fonts.googleapis.com/css?family=$fontEncoded' rel='stylesheet' type='text/css'>";
		}
		
		// Check for Font Awesome inclusion
		if(self::$pluginParams->get('include_font_awesome', 0)) {
			echo "<link rel='stylesheet' href='https://use.fontawesome.com/releases/v5.3.1/css/all.css' integrity='sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU' crossorigin='anonymous'>";
		}
		
		$amp_bg_color = self::$pluginParams->get('amp_bg_color', '#FF9800');
		$amp_text_color = self::$pluginParams->get('amp_text_color', '#FFF');
		$amp_body_bg_color = self::$pluginParams->get('amp_body_bg_color', '#FFF');
		$amp_body_text_color = self::$pluginParams->get('amp_body_text_color', '#333');
		$amp_footer_bg_color = self::$pluginParams->get('amp_footer_bg_color', '#EAEAEA');
		$amp_footer_text_color = self::$pluginParams->get('amp_footer_text_color', '#333');
		
		$amp_max_width = self::$pluginParams->get('amp_max_width', '479');
		$amp_title_header_sitename_fontsize = self::$pluginParams->get('amp_title_header_sitename_fontsize', 24);
		$amp_page_title_fontsize = self::$pluginParams->get('amp_page_title_fontsize', 20);
		$custom_css = self::$pluginParams->get('custom_css_styles', null);
		
		$menu_module_header_bg_color = self::$pluginParams->get('menu_module_header_bg_color', '#FF9800');
		$menu_module_header_icon = self::$pluginParams->get('menu_module_header_icon', 'mobile_menu_black');
		$menu_module_header_text_color = self::$pluginParams->get('menu_module_header_text_color', '#333');
		
		$menu_module_body_bg_color = self::$pluginParams->get('menu_module_body_bg_color', '');
		$menu_module_body_text_color = self::$pluginParams->get('menu_module_body_text_color', '');
		$menu_module_innerbody_bg_color = self::$pluginParams->get('menu_module_innerbody_bg_color', '');
		$menu_module_innerbody_text_color = self::$pluginParams->get('menu_module_innerbody_text_color', '');
		$menu_module_innerbody_elements_bg_color = self::$pluginParams->get('menu_module_innerbody_elements_bg_color', '');
		$menu_module_innerbody_icon = self::$pluginParams->get('menu_module_innerbody_icon', 'mobile_menu_black');
		$menu_module_separator = self::$pluginParams->get('menu_module_separator', 0);
		
		$sidebar_header_bg_color = self::$pluginParams->get('sidebar_header_bg_color', '#FFFFFF');
		$sidebar_header_text_color = self::$pluginParams->get('sidebar_header_text_color', '#333');
		
		$sidebar_body_bg_color = self::$pluginParams->get('sidebar_body_bg_color', '#FFFFFF');
		$sidebar_body_text_color = self::$pluginParams->get('sidebar_body_text_color', '#333');
		
		$always_visible_navigation = self::$pluginParams->get('always_visible_navigation', 1);
		$navigation_buttons_opacity = self::$pluginParams->get('navigation_buttons_opacity', '0.5');
		
		$user_notification_bg_color = self::$pluginParams->get('user_notification_bg_color', '#000000');
		$user_notification_text_color = self::$pluginParams->get('user_notification_text_color', '#FFFFFF');
		
		$article_headers_center_alignment = self::$pluginParams->get('article_headers_center_alignment', 0);
		
		$lightbox_bg_color = self::$pluginParams->get('lightbox_bg_color', '#FFFFFF');
		$lightbox_color = self::$pluginParams->get('lightbox_color', '#000000');
		$lightbox_bg_transparency = dechex(self::$pluginParams->get('lightbox_bg_transparency', 255));
		$lightbox_border_color = self::$pluginParams->get('lightbox_border_color', '#9A9A9A');
		?>
		<style amp-boilerplate>body{-webkit-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-moz-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-ms-animation:-amp-start 8s steps(1,end) 0s 1 normal both;animation:-amp-start 8s steps(1,end) 0s 1 normal both}@-webkit-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-moz-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-ms-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-o-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}</style><noscript><style amp-boilerplate>body{-webkit-animation:none;-moz-animation:none;-ms-animation:none;animation:none}</style></noscript>
		<style amp-custom>
			body {
				font-family: calibri;
				font-size: 16px;
				color: <?php echo $amp_body_text_color;?>;
				width: 100%;
				margin:0 auto;
				background: <?php echo $amp_body_bg_color;?>;
			}
			<?php if($customGoogleFont):?>
			body {
				font-family: <?php echo str_replace('+', ' ', $customGoogleFont);?>
			}
			<?php endif;?>
			a {
				color: <?php echo $amp_body_text_color;?>; 
				text-decoration: none;
			}
			a:hover { 
				color: <?php echo $amp_body_text_color;?>; 
				text-decoration: none;
			}
			body #sidebar {
				width: 320px;
				background: <?php echo $sidebar_body_bg_color;?>;
				color: <?php echo $sidebar_body_text_color;?>;
			}
			body #sidebar-trigger {
				position: absolute;
			    top: 6px;
			}
			body #sidebar-trigger.trigger-right {
				top: 2px;
				transform: rotate(180deg);
				right: 0;
			}
			body #sidebar-close {
				float: right;
				top: 8px;
			}
			body #sidebar-close.trigger-right {
				float:left;
				transform: rotate(180deg);
			}
			body #sidebar div.topheader {
				font-size: 24px;
				background: <?php echo $sidebar_header_bg_color;?>;
				color: <?php echo $sidebar_header_text_color;?>;
			    height: 48px;
				text-align: center;
			    line-height: 2em;
			}
			body #sidebar hr {
				margin: 0;
			}
			body #sidebar section {
				padding: 0 10px;
			}
			body > header.container {
				max-width: <?php echo $amp_max_width;?>px;
			    margin: 0 auto;
    			padding: 0;
			}
			body > header #header{
				text-align: center; 
			    padding: 5px 15px 15px 15px;
				background: <?php echo $amp_bg_color;?>;
				color: <?php echo $amp_text_color;?>;
			}
			body > header #header h1{
				text-align: center;
				font-size: 20px;
				font-weight: bold;
				line-height: 1;
				margin: 5px 0 0 0;
			}
			body > header #header h1 a{
				color: <?php echo $amp_text_color;?>; 	
			}
		    body > header #header a{
				color: <?php echo $amp_text_color;?>; 	
			}
			body #header div.amp_title_header_sitename {
				margin-bottom: 5px;
				font-size: <?php echo $amp_title_header_sitename_fontsize;?>px;
			}
			body #header a.amp_page_title {
				font-size: <?php echo $amp_page_title_fontsize;?>px;
			}
			body #header div.amp_sitelogo {
				margin: 3px 0 5px 0;
			}
			body header div.header_module_title,
			body footer div.footer_module_title,
			body div.main_module_title,
			body div.center_module_title {
				background-color: #EAEAEA;
    			border: 1px solid #dfdfdf;
				font-size: 22px;
				padding: 16px;
				line-height: 32px;
				margin-top: 5px;
			}
			body header	div.header_module_content,
			body footer div.footer_module_content,
			body div.main_module_content,
			body div.center_module_content {
				background: #fafafa;
				color: #333;
				padding: 16px;
		    }
			body header	div.header_module_content p, body header div.header_module_content h3 {
				margin-top: 0;
			}
			<?php if($always_visible_navigation):?>
			body #ampcarousel div.amp-carousel-button.amp-carousel-button-prev,
			body #ampcarousel div.amp-carousel-button.amp-carousel-button-next {
				pointer-events: all;
				visibility: visible;			}
			<?php endif;?>
			body #ampcarousel div.amp-carousel-button.amp-carousel-button-prev,
			body #ampcarousel div.amp-carousel-button.amp-carousel-button-next {
				opacity: <?php echo $navigation_buttons_opacity;?>;
			}
			body header #ampcarousel {
				margin-top: 10px;	
			}
			body amp-accordion.menu-accordion > section > h4 {
				font-size: 24px;
				background: <?php echo $menu_module_header_bg_color;?> url(<?php echo Uri::root(false) . 'plugins/system/jamp/core/images/' . $menu_module_header_icon . '.png'?>) no-repeat;
			    background-position: right 4px top 2px;
				color: <?php echo $menu_module_header_text_color;?>;
				height: 36px;
    			line-height: 1.5em;
			    padding: 1px 4px 0 4px;
			    border: none;
				outline:none;
			}
			body amp-accordion.menu-accordion > section {
				font-size: 20px;
			}
			body amp-accordion.menu-accordion > section {
				text-align: center;
			}
			body amp-accordion.menu-accordion > section > h4 + ul,
			body amp-accordion.menu-accordion > section ul {
				list-style-type: none;
			    padding-left: 0;
			    margin-left: 0;
			    text-align: center;
			}
			body amp-accordion.menu-accordion > section > h4 + ul li,
			body amp-accordion.menu-accordion > section ul li {
				padding: 4px;
			}
			<?php if($menu_module_body_bg_color):?>
			body amp-accordion.menu-accordion > section > h4 + * {
				background-color: <?php echo $menu_module_body_bg_color;?>;
			}
			<?php endif;?>
			
			<?php if($menu_module_body_text_color):?>
			body amp-accordion.menu-accordion > section > h4 + * a {
				color: <?php echo $menu_module_body_text_color;?>;
			}
			<?php endif;?>

			body amp-accordion.menu-accordion > section > header {
				padding-right: 0;
			}
			<?php if($menu_module_innerbody_bg_color):?>
			body amp-accordion.menu-accordion > section > header {
				background: <?php echo $menu_module_innerbody_bg_color;?> url(<?php echo Uri::root(false) . 'plugins/system/jamp/core/images/' . $menu_module_innerbody_icon . '.png'?>) no-repeat;
			    background-position: right 4px top 4px;
			}
			<?php endif;?>
			
			<?php if($menu_module_innerbody_elements_bg_color):?>
			body amp-accordion.menu-accordion > section > header + ul {
				background-color: <?php echo $menu_module_innerbody_elements_bg_color;?>;
			}
			<?php endif;?>
			
			<?php if($menu_module_innerbody_text_color):?>
			body amp-accordion.menu-accordion > section > header > a,
			body amp-accordion.menu-accordion > section > header > span {
				color: <?php echo $menu_module_innerbody_text_color;?>;
			}
			<?php endif;?>

			body amp-accordion.menu-accordion > section > header > a {
				display: inline-block;
			}
			
			<?php if($menu_module_separator):?>
			body amp-accordion.menu-accordion > section > h4 + * {
				border-top: 1px solid <?php echo $menu_module_body_text_color;?>;
			}
			<?php endif;?>
			
			body amp-accordion.menu-accordion ul.mod-menu {
				margin: 0;
			}

			body > section {
				margin: 0 auto;
				padding: 0;
				min-height: 400px;
				max-width: <?php echo $amp_max_width;?>px;
			}
			body > section article.post{
				-moz-border-radius: 2px;
				-webkit-border-radius: 2px;
				border-radius: 2px;
				-moz-box-shadow: 0 2px 3px rgba(0,0,0,.05);
				-webkit-box-shadow: 0 2px 3px rgba(0,0,0,.05);
				box-shadow: 0 2px 3px rgba(0,0,0,.05);
				padding: 15px;
				background: <?php echo $amp_body_bg_color;?>;
				color: <?php echo $amp_body_text_color;?>;
				margin: 0px;
			}
			body > section article.post h1, 
			body > section article.post h1 a{
				line-height: 34px;
				font-size: 32px;
				margin: 5px 0 5px 0px;
				<?php if($article_headers_center_alignment):?>
				text-align: center;
				<?php endif;?>
			}
			body > section article.post h2, 
			body > section article.post h2 a{
				line-height: 26px;
				font-size: 26px;
				margin: 5px 0 5px 0px;
				<?php if($article_headers_center_alignment):?>
				text-align: center;
				<?php endif;?>
			}
			body > section article.post h3, 
			body > section article.post h3 a{
				line-height: 22px;
				font-size: 20px;
				margin: 10px 0 10px 0px;
				<?php if($article_headers_center_alignment):?>
				text-align: center;
				<?php endif;?>
			}
			body > section article.post dl.article-info dt {
				display: none;
			}
			body > section article.post dl.article-info dd {
				margin-left: 0;
			}
			body > section article.post p{
				margin-top: 5px;
				font-size: 15px;
				line-height: 20px;
				margin-bottom: 15px;
				text-align: justify;
			}
			body > section article.post ul.amp-meta {
				padding: 5px 0 0 0;
				margin: 0 0 5px 0;
			}
			body > section article.post div.amp-meta div.amp-byline {
				list-style: none;
				display: inline-block;
				margin: 0;
				line-height: 24px;
				overflow: hidden;
				text-overflow: ellipsis;
				max-width: 100%;
			}
			body > section article.post ul.amp-meta li.amp-byline {
				text-transform: capitalize;	
			}
			body > section article.post .amp-byline amp-img:not([layout=fill]) {
				border: 0;
				position: relative;
				top: 6px;
				margin-right: 6px;
			}
			.clearfix{
				clear: both;
			}
			body > section article.post ul.pagenav { 
				width: 100%;
				padding-top: 10px;
				border-top: 1px dotted #EAEAEA;
				margin-bottom: 12px;
				list-style: none;
				padding-left: 0;
				margin-left: 0;
			}
			body > section article.post ul.pagenav li.next {
				float: right;
				width:50%;
				text-align: right;
				height: 30px;
			}
			body > section article.post ul.pagenav li.previous {
				float: left;
				width:50%;
				text-align: left;
				height: 30px;
			}
			body > section article.post ul.pagenav li.next a, 
			body > section article.post ul.pagenav li.previous a {
				margin-bottom: 12px;
				background: #fefefe;
				color: #333;
				-moz-border-radius: 2px;
				-webkit-border-radius: 2px;
				border-radius: 2px;
				-moz-box-shadow: 0 2px 3px rgba(0,0,0,.05);
				-webkit-box-shadow: 0 2px 3px rgba(0,0,0,.05);
				box-shadow: 0 2px 3px rgba(0,0,0,.05);
			    padding: 5px;
    			border: 1px solid #CCC;
			}
			body > section article.post ul.pagenav li.previous a:before {
			    content: "<";
			}
			body > section article.post ul.pagenav li.next a:after {
			    content: ">";
			}
			body > footer.container {
				max-width: <?php echo $amp_max_width;?>px;
			    margin: 0 auto;
    			padding: 0;
			}
			body > footer > #footer{
				font-size: 13px;
				text-align: center;
				padding: 15px 0;
				background: <?php echo $amp_footer_bg_color;?>;
				color: <?php echo $amp_footer_text_color;?>;
				margin-top: 4px;
			}
			body > footer > #footer p{
				margin: 0;
				color: <?php echo $amp_footer_text_color;?>;
			}
			body > footer > #footer a{
				color: <?php echo $amp_footer_text_color;?>;
			}
			body > footer > #footer a:hover {
				text-decoration: underline;
			}
			body > footer > amp-accordion.menu-accordion {
				margin-bottom: 10px;
			}
			body > footer > #footer a.mainsite-link {
			    padding: 5px;
    			display: block;
    			font-size: 18px;
			}
			body > footer #footer_main_version,
			body > footer #footer_main_version + *{
				text-transform: capitalize;			
			}
			single_img img{
				width: 100%;
				height: 100%
			}
			#title h2{
				margin: 20px 0px 18px 0px;
				text-align: center;
			}
			.postmeta{
				font-size: 12px; 
				padding-bottom: 10px;
				border-bottom: 1px solid #DADADA;
			}
			.postmeta p{
				margin: 0;
			}
			.postmeta span{
				float: right;
			}
			.single_img{
				text-align: center;
			}
			amp-img, 
			img, 
			object, 
			video {
				max-width: 100%;
				height: auto;
			}
			h2.screen-reader-text{ 
				display:none;
			}
			.sitelogo{
				max-width:250px;
				max-height:150px;
			}
			*.pull-left,div.pull-left,*.pull-right,div.pull-right {
				float: none;	
			}
			amp-user-notification,
			#consent-element {
		      box-sizing: border-box;
	          text-align: center;
		      padding: 8px;
		      background: <?php echo $user_notification_bg_color;?>;
		      color: <?php echo $user_notification_text_color;?>;
		    }
			body > header > #socialshare { 
				text-align:center;
				margin-top: 10px;
			}
			body > footer > #socialshare { 
				text-align:center;
			}
			body > footer #footer-hr { 				width: 40%;	
			}
			body amp-addthis[data-widget-type=floating] {
				z-index: 999999;
			}
			<?php if(!self::$pluginParams->get('list_item_nostyle', 1)):?>
				ul {list-style-type: none;padding-left:0;}
				ol {list-style-type: none;padding-left:0;}
			<?php endif;?>			<?php if($custom_css) {
				echo trim($custom_css);
			}?>
			body .label, body .badge {
			    display: inline-block;
			    padding: 2px 4px;
			    font-size: 10.998px;
			    font-weight: bold;
			    line-height: 14px;
			    color: #fff;
			    vertical-align: baseline;
			    white-space: nowrap;
			    text-shadow: 0 -1px 0 rgba(0,0,0,0.25);
			    background-color: #999;
			    border-radius: 3px;
			}
			body .label-info[href], body .badge-info[href] {
			    background-color: #2d6987;
			}
			body ul.inline, body ol.inline {
				margin-left: 0;
   				list-style: none;
			}
			body *[class*=pagination] li {
				list-style-type: none;
				padding-left: 0;	
			}
			body *.pagination ul > li > a, body *.pagination ul > li > span {
				float: left;
			    padding: 4px 12px;
			    line-height: 18px;
			    text-decoration: none;
			    background-color: #fff;
			    border: 1px solid #ddd;
			    color: #005e8d;
			}
			body *.pagination ul > li *[class^="icon-"], body *.pagination ul > li *[class*=" icon-"] {
			    display: inline-block;
			    width: 14px;
			    height: 14px;
			    margin-right: .25em;
			    line-height: 14px;
			}	
			body *.pagination ul > li > a:hover,
			body *.pagination ul > li > a:focus,
			body *.pagination ul > .active > 
			body *.pagination ul > .active > span {
				background-color: #f5f5f5;
			}
			body *.pagination ul > .active > a, body *.pagination ul > .active > span {
			    color: #999;
				cursor: default;
			}
			body *.pagination ul .icon-first:before {
			    content: "<<";
			}
			body *.pagination ul .icon-previous:before, .icon-backward:before {
			    content: "<";
			}
			body *.pagination ul .icon-next:before, .icon-forward:before {
			    content: ">";
			}
			body *.pagination ul .icon-last:before {
			    content: ">>";
			}
			body .uk-pagination-previous {float: left}
			body .uk-pagination-next {float:right}
			body ul.uk-pagination li {
			    border: 1px solid #CCC;
			    border-radius: 5px;
			    padding: 2px 4px;
			}
			body amp-lightbox {
	       		background: <?php echo $lightbox_bg_color . $lightbox_bg_transparency;?>;
				color: <?php echo $lightbox_color;?>;
			    padding: 5px;
			    border: 15px solid <?php echo $lightbox_border_color;?>;
			    border-radius: 20px;
		    }
			body amp-lightbox button.amp-lightbox-closer {
				position: fixed;
				top: 0;
				right: 0;
			    background: #0a0a0a;
			    border: 2px solid #9a9a9a;
			    border-radius: 15px;
			    color: #fff;
			    font-size: 20px;
			    width: 30px;
			    height: 30px;
				padding: 0;
			}
			body button.amp-lightbox-opener {
			    text-transform: capitalize;
			    border-radius: .2rem;
			    color: #fff;
			    background-color: #6c757d;
			    border-color: #6c757d;
			    display: inline-block;
			    font-weight: 400;
			    text-align: center;
			    vertical-align: middle;
			    -webkit-user-select: none;
			    -moz-user-select: none;
			    -ms-user-select: none;
			    user-select: none;
			    border: 1px solid transparent;
			    padding: .375rem .75rem;
			    font-size: 1rem;
			    line-height: 1.5;
			    transition: color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out;
			}
			body amp-lightbox > div {
				overflow: auto;	
			}
			<?php if(self::$pluginParams->get('enable_form', 0)):?>
				body input, body textarea, body button, body select {
					padding:5px;
				}
				body input.user-invalid {
					border:2px solid #b94a48;
					color:#b94a48;
				}
				body .amp-form-submit-success [submit-success],
				body .amp-form-submit-error [submit-error],
				body .amp-form-submitting [submitting] {
					margin-top:16px;
					font-weight:bold;
					padding:15px;
					border:2px solid;
					border-radius:4px
				}
				body .amp-form-submit-success [submit-success] {
					color:#468847;
					background-color:#dff0d8;
				}
				body .amp-form-submit-error [submit-error] {
					color:#b94a48;
					background-color:#f2dede;
				}
				body .amp-form-submitting [submitting]{
					color:#3a87ad;
					background-color:#d9edf7;
				}
				/* @todo remove these 2 CSS rules below when https://github.com/ampproject/amphtml/issues/8601 is fixed */
				form [submitting]{
					display:none
				}
				.amp-form-submitting [submitting]{
					display:block
				}
			<?php endif;?>
			<?php if(self::$pluginParams->get('amp_story_enable', 0)):?>
				<?php for($i=1;$i<=5;$i++) :?>
				#page-<?php echo $i;?> amp-story-grid-layer > h1 {
					color: <?php echo self::$pluginParams->get('amp_story_title_color_page' . $i, '#333');?>;
					font-size: <?php echo self::$pluginParams->get('amp_story_title_fontsize_page' . $i, 32);?>px;
					text-align: <?php echo self::$pluginParams->get('amp_story_title_alignment_page' . $i, 'left');?>;
				}
				#page-<?php echo $i;?> amp-story-grid-layer > p {
					color: <?php echo self::$pluginParams->get('amp_story_content_color_page' . $i, '#333');?>;
					font-size: <?php echo self::$pluginParams->get('amp_story_content_fontsize_page' . $i, 24);?>px;
					text-align: <?php echo self::$pluginParams->get('amp_story_content_alignment_page' . $i, 'left');?>;
				}
				#page-<?php echo $i;?> amp-story-cta-layer span {
					display: block;
					padding: 0 20px;
					text-align: <?php echo self::$pluginParams->get('amp_story_links_alignment_page' . $i, 'left');?>;
					color: <?php echo self::$pluginParams->get('amp_story_links_color_page' . $i, '#333');?>;
					font-size: <?php echo self::$pluginParams->get('amp_story_links_fontsize_page' . $i, 24);?>px;
				}
				#page-<?php echo $i;?> amp-story-cta-layer a {
					color: <?php echo self::$pluginParams->get('amp_story_links_color_page' . $i, '#333');?>;
					font-size: <?php echo self::$pluginParams->get('amp_story_links_fontsize_page' . $i, 24);?>px;
				}
				#page-<?php echo $i;?> amp-story-cta-layer p {
					color: <?php echo self::$pluginParams->get('amp_story_links_color_page' . $i, '#333');?>;
					font-size: <?php echo self::$pluginParams->get('amp_story_links_fontsize_page' . $i, 24);?>px;
					margin: 2px;
				}
				<?php endfor;?>
			<?php endif;
			if(\JAmpHelper::$pluginParams->get('enable_user_notification', 0) == 2 || \JAmpHelper::$pluginParams->get('enable_user_notification', 0) == 3):?>
				.iubenda-tp-btn {
			        position: fixed;
			        z-index: 2147483647;
			        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='32' height='32' viewBox='0 0 32 32'%3E%3Cpath fill='%231CC691' fill-rule='evenodd' d='M16 7a4 4 0 0 1 2.627 7.016L19.5 25h-7l.873-10.984A4 4 0 0 1 16 7z'/%3E%3C/svg%3E");
			        background-repeat: no-repeat;
			        background-size: 32px 32px;
			        background-position: top .5px left 1px;
			        width: 34px;
			        height: 34px;
			        border: none;
			        cursor: pointer;
			        margin: 16px;
			        padding: 0;
			        box-shadow: 0 0 0 1px rgba(0, 0, 0, .15);
			        background-color: #fff;
			        display: inline-block;
			        height: 34px;
			        min-width: 34px;
			        border-radius: 4px;
			        bottom: 0;
			        right: 0;
			    }
			    .iubenda-tp-btn--top-left {
			        top: 0;
			        left: 0;
			    }
			    .iubenda-tp-btn--top-right {
			        top: 0;
			        right: 0;
			    }
			    .iubenda-tp-btn--bottom-left {
			        bottom: 0;
			        left: 0;
			    }
			    .iubenda-tp-btn--bottom-right {
			        bottom: 0;
			        right: 0;
			    }
		    <?php endif;?>
		    <?php if(\JAmpHelper::$pluginParams->get('enable_user_notification', 0)):?>
		    	div.text-consent-ui {
		    		font-size: 16px;
				    padding: 0 8px;
					margin-bottom: 12px;
		    	}
			    button.btn-consent-ui {
		            flex: 1;
				    appearance: none;
				    margin: 0 2px;
				    padding: 4px 14px;
				    border-radius: 64px;
				    cursor: pointer;
				    font-weight: 700;
				    font-size: 14px;
				    background-color: #0073ce;
				    color: #fff;
				    text-align: center;
				    border-color: transparent;
			    }
		    <?php endif;?>
		    <?php if(JAmpHelper::$pluginParams->get('enable_scroll_to_top', 0)):?>
			:root {
			    --color-secondary: #00DCC0;
			    --color-text-light: #fff;
			    --space-2: 1rem;
			    --box-shadow-1: 0 1px 1px 0 rgba(0,0,0,.14), 0 1px 1px -1px rgba(0,0,0,.14), 0 1px 5px 0 rgba(0,0,0,.12);
			}
			.ampscrollToTop {
				color: var(--color-text-light);
			    padding: 15px 10px 0 10px;
			    font-size: 16px;
			    box-shadow: var(--box-shadow-1);
			    width: fit-content;
			    height: 50px;
			    border-radius: 10px;
			    border: none;
			    outline: none;
				background: #000 url(<?php echo JUri::root(false) . 'plugins/system/jamp/core/images/gototop.svg'?>) center -8px/40px 40px no-repeat;
			    z-index: 9999;
			    bottom: var(--space-2);
			    right: var(--space-2);
			    position: fixed;
			    opacity: 0;
			    visibility: hidden;
			}
			.amptarget-anchor {
			    position: absolute;
			    top: -72px;
			    left: 0;
			}
		    <?php endif;?>
			</style>
		<?php 
	} 
	
	/**
	 * This function search for a specific tag in a called module
	 *
	 * @access public
	 * @param string $selector
	 * @return boolean
	 */
	public static function findTagInModule($selector) {
		static $sDomHeaderModulesContents, $sDomTopModulesContents, $sDomBottomModulesContents, $sDomFooterModulesContents;
		$foundTag = false;
		
		// Is there any iframe tag in an additional header module?
		if(self::$pluginParams->get('enable_header_module', 0) && self::$pluginParams->get('header_module_name', '')) {
			if(is_null($sDomHeaderModulesContents)) {
				ob_start();
				include (JPATH_ROOT . PLG_JAMP_TEMPLATE_PATH . 'header_module.php');
				$headerModulesContents = ob_get_clean();
				$html = new \JAmpSimpleHtmlDom();
				$sDomHeaderModulesContents = $html->load( $headerModulesContents );
			}
			if($sDomHeaderModulesContents->find($selector)) {
				$foundTag = true;
			}
		}

		// Is there any iframe tag in an additional top module?
		if(!$foundTag && self::$pluginParams->get('enable_top_module', 0) && self::$pluginParams->get('top_module_name', '')) {
			if(is_null($sDomTopModulesContents)) {
				ob_start();
				include (JPATH_ROOT . PLG_JAMP_TEMPLATE_PATH . 'top_module.php');
				$topModulesContents = ob_get_clean();
				$html = new \JAmpSimpleHtmlDom();
				$sDomTopModulesContents = $html->load( $topModulesContents );
			}
			if($sDomTopModulesContents->find($selector)) {
				$foundTag = true;
			}
		}
		
		// Is there any iframe tag in an additional bottom module?
		if(!$foundTag && self::$pluginParams->get('enable_bottom_module', 0) && self::$pluginParams->get('bottom_module_name', '')) {
			if(is_null($sDomBottomModulesContents)) {
				ob_start();
				include (JPATH_ROOT . PLG_JAMP_TEMPLATE_PATH . 'bottom_module.php');
				$bottomModulesContents = ob_get_clean();
				$html = new \JAmpSimpleHtmlDom();
				$sDomBottomModulesContents = $html->load( $bottomModulesContents );
			}
			if($sDomBottomModulesContents->find($selector)) {
				$foundTag = true;
			}
		}
		
		// Is there any iframe tag in an additional footer module?
		if(!$foundTag && self::$pluginParams->get('enable_footer_module', 0) && self::$pluginParams->get('footer_module_name', '')) {
			if(is_null($sDomFooterModulesContents)) {
				ob_start();
				include (JPATH_ROOT . PLG_JAMP_TEMPLATE_PATH . 'footer_module.php');
				$footerModulesContents = ob_get_clean();
				$html = new \JAmpSimpleHtmlDom();
				$sDomFooterModulesContents = $html->load( $footerModulesContents );
			}
			if($sDomFooterModulesContents->find($selector)) {
				$foundTag = true;
			}
		}

		return $foundTag;
	}
	
	/**
	 * This function returns the required code only for the head tag accordingly to AMP required scripts and web components
	 * 
	 * @access public
	 * @return string
	 */
	public static function customScript($sContent) {
		// Pre emptive analisys to determine if we have certain tags requiring AMP scripts
		$sContent = self::sanitizeAttributes( $sContent );
		$html = new \JAmpSimpleHtmlDom();
		$sDomContent = $html->load( $sContent );
		
		// Is there any iframe tag in the main content?
		$ampIframeAdded = false;
		if($sDomContent->find('iframe') || self::findTagInModule('amp-iframe')) {
			$customHeadCode = self::$pluginParams->get('custom_code_before_head', '');
			if(StringHelper::strpos($customHeadCode, 'amp-iframe-0.1.js') === false) {
				echo '<script async custom-element="amp-iframe" src="https://cdn.ampproject.org/v0/amp-iframe-0.1.js"></script>';
				$ampIframeAdded = true;
			}
		}
		
		// Is there any GIF requiring anim script in the main content?
		if($sDomContent->find('img[src*=.gif]') || self::findTagInModule('amp-img[src*=.gif]')) {
			self::$gifImages = true;
			echo '<script async custom-element="amp-anim" src="https://cdn.ampproject.org/v0/amp-anim-0.1.js"></script>';
		}
		
		// AMP video
		if($sDomContent->find('video') || self::findTagInModule('amp-video')) {
			echo '<script async custom-element="amp-video" src="https://cdn.ampproject.org/v0/amp-video-0.1.js"></script>';
		}
		
		// AMP audio
		if($sDomContent->find('audio') || self::findTagInModule('amp-audio')) {
			echo '<script async custom-element="amp-audio" src="https://cdn.ampproject.org/v0/amp-audio-0.1.js"></script>';
		}
		
		// Sidebar web component
		if (self::$pluginParams->get('enable_sidebar_module', 0) && self::$pluginParams->get('sidebar_module_name', '')) {
			echo '<script async custom-element="amp-sidebar" src="https://cdn.ampproject.org/v0/amp-sidebar-0.1.js"></script>';
		}
		
		// Carousel web component
		$slideshowBySelector = false;
		if(self::$pluginParams->get('enable_slideshow_byselector', 0)) {
			if($slideshowSelector = trim(self::$pluginParams->get('slideshow_byselector', ''))) {
				if($sDomContent->find($slideshowSelector)) {
					$slideshowBySelector = true;
				}
			}
		}
		if(self::$pluginParams->get('enable_slideshow', 0) || $slideshowBySelector) {
			$activeMenu = self::$application->getMenu()->getActive();
			$slideshowOnlyHome = self::$pluginParams->get('enable_slideshow_only_home', 0);
			if(!$slideshowOnlyHome || ($slideshowOnlyHome && $activeMenu->home) || $slideshowBySelector) {
				echo '<script async custom-element="amp-carousel" src="https://cdn.ampproject.org/v0/amp-carousel-0.1.js"></script>';
			}
		}
		
		// Accordion web component
		if (self::$pluginParams->get('enable_menu_module', 0)) {
			echo '<script async custom-element="amp-accordion" src="https://cdn.ampproject.org/v0/amp-accordion-0.1.js"></script>';
		}
		
		// Social buttons web component
		if (self::$pluginParams->get('social_buttons', 1) && count(self::$pluginParams->get('social_buttons_enabled', array()))) {
			echo '<script async custom-element="amp-social-share" src="https://cdn.ampproject.org/v0/amp-social-share-0.1.js"></script>';
		}
		
		// Social buttons AddThis
		if (self::$pluginParams->get('social_buttons', 1) && self::$pluginParams->get('social_buttons_addthis', 0)) {
			echo '<script async custom-element="amp-addthis" src="https://cdn.ampproject.org/v0/amp-addthis-0.1.js"></script>';
		}
		
		// Lightbox web component
		if (self::$pluginParams->get('enable_images_lightbox', 0)) {
			echo '<script async custom-element="amp-image-lightbox" src="https://cdn.ampproject.org/v0/amp-image-lightbox-0.1.js"></script>';
		}
		
		// AMP Youtube component
		if(self::$pluginParams->get('youtube_videos_activation', 0)) {
			if($sDomContent->find('amp-youtube') || self::findTagInModule('amp-youtube')) {
				echo '<script async custom-element="amp-youtube" src="https://cdn.ampproject.org/v0/amp-youtube-0.1.js"></script>';
			}
		}
		
		// AMP Ad
		if(self::$pluginParams->get('amp_ad_activation', 0)) {
			if($sDomContent->find('amp-ad') || self::findTagInModule('amp-ad') || trim(self::$pluginParams->get('amp_ad_code', ''))) {
				echo '<script async custom-element="amp-ad" src="https://cdn.ampproject.org/v0/amp-ad-0.1.js"></script>';
			}
		}
		
		// AMP Auto Ad
		if(self::$pluginParams->get('amp_auto_ad_activation', 0)) {
			echo '<script async custom-element="amp-auto-ads" src="https://cdn.ampproject.org/v0/amp-auto-ads-0.1.js"></script>';
		}
		
		// User notification web component
		if (self::$pluginParams->get('enable_user_notification', 0) == 1) {
			echo '<script async custom-element="amp-user-notification" src="https://cdn.ampproject.org/v0/amp-user-notification-0.1.js"></script>';
		}
		
		// AMP form web component
		if (self::$pluginParams->get('enable_form', 0)) {
			$selectedForms = self::$pluginParams->get('enable_form_bycss_selectors', 'form');
			// Load additional custom config selectors for this dispatched component if any
			if($manifestConfigSelectors = self::loadManifestFile(self::$application->input->get('option'), 'enable_form_bycss_selectors')) {
				$selectedForms .= ',' . $manifestConfigSelectors;
				$selectedForms = trim($selectedForms, ',');
			}
			if($sDomContent->find($selectedForms) || self::findTagInModule($selectedForms)) {
				echo '<script async custom-element="amp-form" src="https://cdn.ampproject.org/v0/amp-form-0.1.js"></script>';
				if($sDomContent->find('template') || self::findTagInModule($selectedForms)) {
					echo '<script async custom-template="amp-mustache" src="https://cdn.ampproject.org/v0/amp-mustache-0.2.js"></script>';
				}
			}
		}

		// AMP Facebook Comment
		if(self::$pluginParams->get('amp_facebook_comments', 0)) {
			echo '<script async custom-element="amp-facebook-comments" src="https://cdn.ampproject.org/v0/amp-facebook-comments-0.1.js"></script>';
		}
		
		// AMP Facebook Like
		if(self::$pluginParams->get('amp_facebook_like', 0)) {
			echo '<script async custom-element="amp-facebook-like" src="https://cdn.ampproject.org/v0/amp-facebook-like-0.1.js"></script>';
		}
		
		// AMP Facebook Page
		if(self::$pluginParams->get('amp_facebook_page', 0) && trim(self::$pluginParams->get('amp_facebook_page_url', ''))) {
			echo '<script async custom-element="amp-facebook-page" src="https://cdn.ampproject.org/v0/amp-facebook-page-0.1.js"></script>';
		}
		
		// AMP Instagram
		$ampInstagram = false;
		if(	self::$pluginParams->get('amp_instagram_enable_top', 0) && trim(self::$pluginParams->get('amp_instagram_top_shortcode', '')) ||
			self::$pluginParams->get('amp_instagram_enable_bottom', 0) && trim(self::$pluginParams->get('amp_instagram_bottom_shortcode', ''))) {
			echo '<script async custom-element="amp-instagram" src="https://cdn.ampproject.org/v0/amp-instagram-0.1.js"></script>';
			$ampInstagram = true;
		}
		
		// AMP Twitter
		$ampTwitter = false;
		if(	self::$pluginParams->get('amp_twitter_enable_top', 0) && trim(self::$pluginParams->get('amp_twitter_top_tweetid', '')) ||
			self::$pluginParams->get('amp_twitter_enable_bottom', 0) && trim(self::$pluginParams->get('amp_twitter_bottom_tweetid', ''))) {
			echo '<script async custom-element="amp-twitter" src="https://cdn.ampproject.org/v0/amp-twitter-0.1.js"></script>';
			$ampTwitter = true;
		}
		
		// AMP instagram
		if(self::$pluginParams->get('amp_instagram_transform', 0) && !$ampInstagram) {
			$instagramCssSelector = rtrim(str_replace("'", '', trim(self::$pluginParams->get('amp_instagram_transform_css_selector', 'a.instagram-media'))), ',');
			if($sDomContent->find($instagramCssSelector) || self::findTagInModule('amp-instagram')) {
				echo '<script async custom-element="amp-instagram" src="https://cdn.ampproject.org/v0/amp-instagram-0.1.js"></script>';
			}
		}
		
		// AMP twitter
		if(self::$pluginParams->get('amp_twitter_transform', 0) && !$ampTwitter) {
			$twitterCssSelector = rtrim(str_replace("'", '', trim(self::$pluginParams->get('amp_twitter_transform_css_selector', 'a.twitter-tweet'))), ',');
			if($sDomContent->find($twitterCssSelector) || self::findTagInModule('amp-twitter')) {
				echo '<script async custom-element="amp-twitter" src="https://cdn.ampproject.org/v0/amp-twitter-0.1.js"></script>';
			}
		}
		
		// AMP Lightbox
		if(self::$pluginParams->get('enable_lightbox_byselector', 0) && $lightboxBySelector = trim(self::$pluginParams->get('lightbox_byselector', ''))) {
			if($sDomContent->find($lightboxBySelector) || self::findTagInModule($lightboxBySelector)) {
				echo '<script async custom-element="amp-lightbox" src="https://cdn.ampproject.org/v0/amp-lightbox-0.1.js"></script>';
			}
		}
		
		// AMP Iubenda Cookie Consent
		if(self::$pluginParams->get('enable_user_notification', 0) == 2 || self::$pluginParams->get('enable_user_notification', 0) == 3) {
			echo '<script async custom-element="amp-consent" src="https://cdn.ampproject.org/v0/amp-consent-latest.js"></script>';
			if(!$ampIframeAdded && self::$pluginParams->get('enable_user_notification', 0) == 3) {
				echo '<script async custom-element="amp-iframe" src="https://cdn.ampproject.org/v0/amp-iframe-0.1.js"></script>';
			}
		}
		
		// AMP Scroll to top
		if(self::$pluginParams->get('enable_scroll_to_top', 0)) {
			echo '<script async custom-element="amp-position-observer" src="https://cdn.ampproject.org/v0/amp-position-observer-0.1.js"></script>';
			echo '<script async custom-element="amp-animation" src="https://cdn.ampproject.org/v0/amp-animation-0.1.js"></script>';
		}
		
		echo '<script async src="https://cdn.ampproject.org/v0.js"></script>';
	}
	
	/**
	 * This function will generate script code for head tag section including all meta data
	 * according to the ld-json format
	 * It also generated metatags for Google, Open graph and Twitter cards
	 * 
	 * @access public
	 * @return string
	 */
	public static function headJsonScript() {
		$sOutput = '';
		$width = null;
		$height = null;
		
		$outputNoTags = preg_replace('#<script(.*?)>(.*?)</script>#is', '', self::$componentOutput);
		$outputNoTags = preg_replace('#<style(.*?)>(.*?)</style>#is', '', $outputNoTags);
		$outputNoTags = preg_replace('/\r\n+|\n+|\t+|\s+/iu', ' ', strip_tags($outputNoTags, 'p'));
		$outputNoTags = preg_replace('/\s+/iu', ' ', $outputNoTags);
		
		// Manage excerpt meta description or extrapolation from the article contents
		if(self::$pluginParams->get('excerpt_source', 'content') == 'metadescription') {
			$metaDescription = self::$document->getDescription();
			$excerptSource = $metaDescription != '' ? trim($metaDescription) : $outputNoTags;
			$excerpt = HTMLHelper::_('string.truncate', $excerptSource, self::$pluginParams->get('excerpt_limit', 300));
		} else {
			$excerpt = HTMLHelper::_('string.truncate', $outputNoTags, self::$pluginParams->get('excerpt_limit', 300));
		}
		
		// Initialize default date object
		$dateObjPublished = Date::getInstance();
		$dateObjModified = Date::getInstance();
		
		$authorType = 'Organization';
		$authorName = self::$pluginParams->get('author_name', self::$application->get('sitename'));
		$publisherName = self::$pluginParams->get('publisher_name', self::$application->get('sitename'));
		
		// If it's a com_content article, retrieve the specific author for the article
		if(self::$pluginParams->get('autodetect_author', 1)) {
			if(	self::$application->input->get('option') == 'com_content' &&
			self::$application->input->get('view') == 'article' &&
			self::$application->input->getInt('id')) {
				$id = self::$application->input->getInt('id');
				$db = Factory::getContainer()->get('DatabaseDriver');
				$query = $db->getQuery(true);
				$query->select('u.name AS author');
				$query->select('a.created AS datepublished');
				$query->select('a.modified AS datemodified');
				$query->from('#__users AS u');
				$query->join('INNER', '#__content AS a on u.id = a.created_by');
				$query->where('a.id = ' . $id);
				$articleObject = $db->setQuery($query)->loadObject();
				if($articleObject) {
					$authorName = $articleObject->author;
					$authorType = 'Person';
					if($articleObject->datepublished && !in_array($articleObject->datepublished, array('0000-00-00 00:00:00', '1000-01-01 00:00:00'))) {
						$dateObjPublished = Date::getInstance($articleObject->datepublished);
					}
					if($articleObject->datemodified && !in_array($articleObject->datemodified, array('0000-00-00 00:00:00', '1000-01-01 00:00:00'))) {
						$dateObjModified = Date::getInstance($articleObject->datemodified);
					}
				}
			}
		}
		
		// Resize the publisher logo to be 600x60px using GD lib
		$publisherLogoImage = self::$pluginParams->get('publisher_logo');
		$fileImagePath = JPATH_SITE . '/' . $publisherLogoImage;
		$publisherLogoImageUrl = null;
		if($publisherLogoImage && file_exists($fileImagePath)) {
			$publisherLogoImageUrl = Uri::root(false) . $publisherLogoImage;
			list( $logoWidth, $logoHeight ) = @getimagesize( $fileImagePath );
			// Not compliant image detected, recalculate, rescale size and override info and url
			if($logoWidth != 600 || $logoHeight != 60) {
				$publisherLogoImageUrl = \JAmpImage::_($publisherLogoImage, 600, 60, array('logo'));
			}
		}
		
		// Get the page image representative to be > 1200px using GD lib
		if(!$pageImageRepresentative = self::findPageRepresentativeImage(self::$componentOutput)) {
			$pageImageRepresentative = self::$pluginParams->get('page_image', '');
		}
		
		// Are we dealing with a relative image URL?
		if(strpos($pageImageRepresentative, 'http') === false && strpos($pageImageRepresentative, '//') !== 0) {
			$fileImagePath = JPATH_ROOT . '/' . $pageImageRepresentative;
			if(strpos($fileImagePath, '?')) {
				$chunks = explode('?', $fileImagePath);
				$fileImagePath = $chunks[0];
			}
			
			$pageImageRepresentativeUrl = null;
			if($pageImageRepresentative && file_exists($fileImagePath)) {
				$pageImageRepresentativeUrl = Uri::root(false) . $pageImageRepresentative;
				list( $width, $height ) = @getimagesize( $fileImagePath );
				// Handle image
				if($updatedImage = self::handlePageImageRepresentative($pageImageRepresentative, $width, $height)) {
					$width = $updatedImage['width'];
					$height = $updatedImage['height'];
					$pageImageRepresentativeUrl = $updatedImage['imageurl'];
				}
			}
		} else { // We deal with an already absolute URL, ok for image src but not for img path so clean it
			// FastImage evaluation for the found image
			$remoteAbsolute = false;
			$pageImageRepresentativeUrl = null;
			
			// We must have a local absolute URL in order to reference a local path
			if(stripos($pageImageRepresentative, Uri::root(false)) !== false) {
				$element_path = JPATH_ROOT . '/' . str_replace(Uri::root(false), '', $pageImageRepresentative);
				$pageImageRepresentativeUrl = $pageImageRepresentative;
			} else {
				$element_path = $pageImageRepresentative;
				$pageImageRepresentativeUrl = $pageImageRepresentative;
				$remoteAbsolute = true;
			}
			// Local absolute, no problem
			if(!$remoteAbsolute) {
				list( $width, $height ) = @getimagesize( $element_path );
				// Handle image
				if($updatedImage = self::handlePageImageRepresentative($element_path, $width, $height)) {
					$width = $updatedImage['width'];
					$height = $updatedImage['height'];
					$pageImageRepresentativeUrl = $updatedImage['imageurl'];
				}
			} else {
				// Check if URL is incomplete format
				if(strpos($element_path, '//') === 0) {
					$element_path = 'http:' . $element_path;
				}
				$image = new \JAmpFastImage($element_path);
				if($image->getHandle() !== false) {
					list( $width, $height ) = $image->getSize();
					// Handle image
					if($updatedImage = self::handlePageImageRepresentative($element_path, $width, $height)) {
						$width = $updatedImage['width'];
						$height = $updatedImage['height'];
						$pageImageRepresentativeUrl = $updatedImage['imageurl'];
					}
				}
			}
		}
		
		// Check if the article is a native Joomla! content article included in a category with a specific type override
		$schemaType = self::$pluginParams->get('amp_schema_type', 'NewsArticle');
		if($schemaTypeOverride = self::findSchemaType()) {
			$schemaType = $schemaTypeOverride;
		}
		
		$authorUrl = '';
		if(self::$pluginParams->get('author_url', '')) {
			$authorUrl = '", "url":"' . trim(self::$pluginParams->get('author_url', ''));
		}
		
		// Evaluate nonce csp feature
		$appNonce = self::$application->get('csp_nonce', null);
		$nonce = $appNonce ? ' nonce="' . $appNonce . '"' : '';
		
		$sOutput = '<script type="application/ld+json"' . $nonce . '>{';
		$sOutput .= '"@context":"http:\/\/schema.org",';
		$sOutput .= '"@type":"' . $schemaType . '",';
		$sOutput .= '"mainEntityOfPage":"' . self::$canonicalUrl . '",';
		$sOutput .= '"headline":"' . HTMLHelper::_('string.truncate', (addcslashes(self::$document->getTitle(), '"\\')), 107) . '",';
		$sOutput .= '"datePublished":"' . str_replace('+00:00', 'Z', $dateObjPublished->toISO8601(false)) . '",';
		$sOutput .= '"dateModified":"' . str_replace('+00:00', 'Z', $dateObjModified->toISO8601(false)) . '",';
		$sOutput .= '"author":{"@type":"' . $authorType . $authorUrl . '", "name":"' . addcslashes($authorName, '"\\') . '"},';
		if($publisherName && $publisherLogoImageUrl) {
			$sOutput .= '"publisher":{"@type": "Organization", "name": "' . addcslashes($publisherName, '"\\') . '", "logo": {"@type": "ImageObject", "url": "' . $publisherLogoImageUrl . '","width": 600,"height": 60}},';
		}
		$sOutput .= '"description": "' . addcslashes($excerpt, '"\\') . '"';
		
		// Add the representative image if a valid one is available with width/height
		if($pageImageRepresentativeUrl && $width && $height) {
			$sOutput .= ',"image":{"@type":"ImageObject","url":"' . $pageImageRepresentativeUrl . '","width":' . $width . ', "height":' . $height . '}';
		}
		$sOutput .= '}</script>';
		
		if(self::$disableJsonMetaTags === true) {
			$sOutput = '';
		}
		
		// Open Graph and Twitter card
		$mOutput = null;
		
		// Schema.org markup for Google+
		if(self::$pluginParams->get('schemaorg_enable', 0) && !self::$disableMetaTags) {
			$mOutput .= '<meta itemprop="name" content="' . htmlspecialchars(self::$document->getTitle(), ENT_COMPAT, 'UTF-8') . '">' . PHP_EOL;
			$mOutput .= '<meta itemprop="description" content="' . htmlspecialchars($excerpt, ENT_COMPAT, 'UTF-8') . '">' . PHP_EOL;
			if($pageImageRepresentativeUrl) {
				$mOutput .= '<meta itemprop="image" content="' . $pageImageRepresentativeUrl . '">' . PHP_EOL;
			}
		}
		
		if(self::$pluginParams->get('opengraph_enable', 0) && !self::$disableMetaTags) {
			// Open Graph Tags
			if($pageImageRepresentativeUrl && $width && $height) {
				$mOutput .= '<meta property="og:image" content="' . $pageImageRepresentativeUrl . '"/>' . PHP_EOL;
				$mOutput .= '<meta property="og:image:width" content="' . $width . '"/>' . PHP_EOL;
				$mOutput .= '<meta property="og:image:height" content="' . $height . '"/>' . PHP_EOL;
			}
			$mOutput .= '<meta property="og:title" content="' . htmlspecialchars(self::$document->getTitle(), ENT_COMPAT, 'UTF-8') . '"/>' . PHP_EOL;
			$mOutput .= '<meta property="og:description" content="' . htmlspecialchars($excerpt, ENT_COMPAT, 'UTF-8') . '"/>' . PHP_EOL;
			$mOutput .= '<meta property="og:url" content="' . self::$canonicalUrl . '"/>' . PHP_EOL;
			$mOutput .= '<meta property="og:type" content="article"/>' . PHP_EOL;
			$mOutput .= '<meta property="og:site_name" content="' . $publisherName . '"/>' . PHP_EOL;
		}
		
		if(self::$pluginParams->get('twitter_card_enable', 0) && !self::$disableMetaTags) {
			// Twitter cards
			$mOutput .= '<meta name="twitter:card" content="summary">' . PHP_EOL;
			$mOutput .= '<meta name="twitter:title" content="' . htmlspecialchars(self::$document->getTitle(), ENT_COMPAT, 'UTF-8') . '">' . PHP_EOL;
			$mOutput .= '<meta name="twitter:description" content="' . htmlspecialchars($excerpt, ENT_COMPAT, 'UTF-8') . '">' . PHP_EOL;
			if($pageImageRepresentativeUrl) {
				$mOutput .= '<meta name="twitter:image" content="' . $pageImageRepresentativeUrl . '">' . PHP_EOL;
			}

			if($twitterCardSite = self::$pluginParams->get('twitter_card_site', '')) {
				$mOutput .= '<meta name="twitter:site" content="' . $twitterCardSite . '">' . PHP_EOL;
			}
			if($twitterCardCreator = self::$pluginParams->get('twitter_card_creator', '')) {
				$mOutput .= '<meta name="twitter:creator" content="' . $twitterCardCreator . '">' . PHP_EOL;
			}
		}
		if($mOutput) {
			echo $mOutput;
		}
		
		echo $sOutput;
	}
	
	/**
	 * This function will generate script code for head tag section including all meta data
	 * according to the ld-json format
	 * It also generated metatags for Google, Open graph and Twitter cards
	 *
	 * @access public
	 * @return string
	 */
	public static function headAmpstoryJsonScript() {
		$sOutput = '';
		$width = null;
		$height = null;
	
		$ampStoryContentOutput = null;
		for($i=1;$i<=5;$i++) {
			$ampStoryContentOutput .= Text::_(self::$pluginParams->get("amp_story_content_page$i", '')) . ' ';
		}
		$outputNoTags = preg_replace('#<script(.*?)>(.*?)</script>#is', '', trim($ampStoryContentOutput));
		$outputNoTags = preg_replace('#<style(.*?)>(.*?)</style>#is', '', $outputNoTags);
		$outputNoTags = preg_replace('/\r\n+|\n+|\t+|\s+/iu', ' ', strip_tags($outputNoTags, 'p'));
		$outputNoTags = preg_replace('/\s+/iu', ' ', $outputNoTags);
	
		$excerpt = HTMLHelper::_('string.truncate', $outputNoTags, self::$pluginParams->get('excerpt_limit', 300));
	
		$dateObj = Date::getInstance();
	
		$authorType = 'Organization';
		$authorName = self::$pluginParams->get('amp_story_publisher_name', self::$application->get('sitename'));
		$publisherName = self::$pluginParams->get('amp_story_publisher_name', self::$application->get('sitename'));
	
		// Resize the publisher logo to be 600x60px using GD lib
		$publisherLogoImage = self::$pluginParams->get('publisher_logo');
		$fileImagePath = JPATH_SITE . '/' . $publisherLogoImage;
		$publisherLogoImageUrl = null;
		if($publisherLogoImage && file_exists($fileImagePath)) {
			$publisherLogoImageUrl = Uri::root(false) . $publisherLogoImage;
			list( $logoWidth, $logoHeight ) = @getimagesize( $fileImagePath );
			// Not compliant image detected, recalculate, rescale size and override info and url
			if($logoWidth != 600 || $logoHeight != 60) {
				$publisherLogoImageUrl = \JAmpImage::_($publisherLogoImage, 600, 60, array('logo'));
			}
		}
	
		// Are we dealing with a relative image URL?
		$pageImageRepresentative = self::$pluginParams->get('amp_story_poster_image', '');
		$fileImagePath = JPATH_ROOT . '/' . $pageImageRepresentative;
		$pageImageRepresentativeUrl = null;
		if($pageImageRepresentative && file_exists($fileImagePath)) {
			$pageImageRepresentativeUrl = Uri::root(false) . $pageImageRepresentative;
			list( $width, $height ) = @getimagesize( $fileImagePath );
			// Handle image
			if($updatedImage = self::handlePageImageRepresentative($pageImageRepresentative, $width, $height)) {
				$width = $updatedImage['width'];
				$height = $updatedImage['height'];
				$pageImageRepresentativeUrl = $updatedImage['imageurl'];
			}
		}
	
		// Check if the article is a native Joomla! content article included in a category with a specific type override
		$schemaType = self::$pluginParams->get('amp_schema_type', 'NewsArticle');
		if($schemaTypeOverride = self::findSchemaType()) {
			$schemaType = $schemaTypeOverride;
		}
	
		$authorUrl = '';
		if(self::$pluginParams->get('author_url', '')) {
			$authorUrl = '", "url":"' . trim(self::$pluginParams->get('author_url', ''));
		}
		
		// Evaluate nonce csp feature
		$appNonce = self::$application->get('csp_nonce', null);
		$nonce = $appNonce ? ' nonce="' . $appNonce . '"' : '';
		
		$sOutput = '<script type="application/ld+json"' . $nonce . '>{';
		$sOutput .= '"@context":"http:\/\/schema.org",';
		$sOutput .= '"@type":"' . $schemaType . '",';
		$sOutput .= '"mainEntityOfPage":"' . self::$canonicalUrl . '",';
		$sOutput .= '"headline":"' . HTMLHelper::_('string.truncate', (addcslashes(Text::_(self::$pluginParams->get('amp_story_maintitle', '')), '"\\')), 107) . '",';
		$sOutput .= '"datePublished":"' . str_replace('+00:00', 'Z', $dateObj->toISO8601(false)) . '",';
		$sOutput .= '"dateModified":"' . str_replace('+00:00', 'Z', $dateObj->toISO8601(false)) . '",';
		$sOutput .= '"author":{"@type":"' . $authorType . $authorUrl . '", "name":"' . addcslashes($authorName, '"\\') . '"},';
		if($publisherName && $publisherLogoImageUrl) {
			$sOutput .= '"publisher":{"@type": "Organization", "name": "' . addcslashes($publisherName, '"\\') . '", "logo": {"@type": "ImageObject", "url": "' . $publisherLogoImageUrl . '","width": 600,"height": 60}},';
		}
		$sOutput .= '"description": "' . addcslashes($excerpt, '"\\') . '"';
	
		// Add the representative image if a valid one is available with width/height
		if($pageImageRepresentativeUrl && $width && $height) {
			$sOutput .= ',"image":{"@type":"ImageObject","url":"' . $pageImageRepresentativeUrl . '","width":' . $width . ', "height":' . $height . '}';
		}
		$sOutput .= '}</script>';
	
		// Open Graph and Twitter card
		$mOutput = null;
	
		// Schema.org markup for Google+
		if(self::$pluginParams->get('schemaorg_enable', 0) && !self::$disableMetaTags) {
			$mOutput .= '<meta itemprop="name" content="' . htmlspecialchars(self::$pluginParams->get('amp_story_maintitle', ''), ENT_COMPAT, 'UTF-8') . '">' . PHP_EOL;
			$mOutput .= '<meta itemprop="description" content="' . htmlspecialchars($excerpt, ENT_COMPAT, 'UTF-8') . '">' . PHP_EOL;
			if($pageImageRepresentativeUrl) {
				$mOutput .= '<meta itemprop="image" content="' . $pageImageRepresentativeUrl . '">' . PHP_EOL;
			}
		}
	
		if(self::$pluginParams->get('opengraph_enable', 0) && !self::$disableMetaTags) {
			// Open Graph Tags
			if($pageImageRepresentativeUrl && $width && $height) {
				$mOutput .= '<meta property="og:image" content="' . $pageImageRepresentativeUrl . '"/>' . PHP_EOL;
				$mOutput .= '<meta property="og:image:width" content="' . $width . '"/>' . PHP_EOL;
				$mOutput .= '<meta property="og:image:height" content="' . $height . '"/>' . PHP_EOL;
			}
			$mOutput .= '<meta property="og:title" content="' . htmlspecialchars(self::$pluginParams->get('amp_story_maintitle', ''), ENT_COMPAT, 'UTF-8') . '"/>' . PHP_EOL;
			$mOutput .= '<meta property="og:description" content="' . htmlspecialchars($excerpt, ENT_COMPAT, 'UTF-8') . '"/>' . PHP_EOL;
			$mOutput .= '<meta property="og:url" content="' . self::$canonicalUrl . '"/>' . PHP_EOL;
			$mOutput .= '<meta property="og:type" content="article"/>' . PHP_EOL;
			$mOutput .= '<meta property="og:site_name" content="' . $publisherName . '"/>' . PHP_EOL;
		}
	
		if(self::$pluginParams->get('twitter_card_enable', 0) && !self::$disableMetaTags) {
			// Twitter cards
			$mOutput .= '<meta name="twitter:card" content="summary">' . PHP_EOL;
			$mOutput .= '<meta name="twitter:title" content="' . htmlspecialchars(self::$pluginParams->get('amp_story_maintitle', ''), ENT_COMPAT, 'UTF-8') . '">' . PHP_EOL;
			$mOutput .= '<meta name="twitter:description" content="' . htmlspecialchars($excerpt, ENT_COMPAT, 'UTF-8') . '">' . PHP_EOL;
			if($pageImageRepresentativeUrl) {
				$mOutput .= '<meta name="twitter:image" content="' . $pageImageRepresentativeUrl . '">' . PHP_EOL;
			}
	
			if($twitterCardSite = self::$pluginParams->get('twitter_card_site', '')) {
				$mOutput .= '<meta name="twitter:site" content="' . $twitterCardSite . '">' . PHP_EOL;
			}
			if($twitterCardCreator = self::$pluginParams->get('twitter_card_creator', '')) {
				$mOutput .= '<meta name="twitter:creator" content="' . $twitterCardCreator . '">' . PHP_EOL;
			}
		}
		if($mOutput) {
			echo $mOutput;
		}
	
		echo $sOutput;
	}
	
	/**
	 * Include Google Analytics script in head section, called by the header.php
	 * 
	 * @access public
	 * @return void
	 */
	public static function googleAnalyticsScript() {
		$amp_ga4_measurement_id = self::$pluginParams->get( 'amp_ga4_measurement_id', null);
		$amp_gaid = self::$pluginParams->get('amp_gaid', null);
		$amp_gtmid = self::$pluginParams->get('amp_gtmid', null);
		if( $amp_ga4_measurement_id || $amp_gaid || $amp_gtmid ) {
			echo '<script async custom-element="amp-analytics" src="https://cdn.ampproject.org/v0/amp-analytics-0.1.js"></script>';
		}
	}
	
	/**
	 * Include custom code in the head or body section, called by the header.php
	 *
	 * @access public
	 * @param string $location
	 * @return void
	 */
	public static function addCustomCode($location) {
		$customCodeAtThisLocation = self::$pluginParams->get('custom_code_' . $location, null);

		if($customCodeAtThisLocation) {
			if(self::$pluginParams->get('custom_code_transform_contents', 0)) {
				$customCodeAtThisLocation = self::transformContents($customCodeAtThisLocation, false);
			}
			echo trim($customCodeAtThisLocation);
		}
	}
	
	/**
	 * Add debug code for the dispatched firm
	 *
	 * @access public
	 * @return void
	 */
	public static function debugFirm() {
		if(self::$pluginParams->get('debug_firm', 0)) {
			echo '<label style="font-size:14px;background-color:#8d0000;color:#FFF;border-radius:5px;padding:10px;display:inline-block;margin:2px"><span style="font-size:16px;font-weight:bold">Component.View: </span>' . self::$dispatchedOptionView . '</label>';
		}
	}
	
	/**
	 * Include custom tags added by external components through the public API
	 *
	 * @access public
	 * @return void
	 */
	public static function includeCustomTags() {
		if( !empty(self::$customTags) ) {
			echo implode(PHP_EOL, self::$customTags);
		}
	}
	
	/**
	 * Include custom tags added by external components through the public API
	 *
	 * @access public
	 * @return void
	 */
	public static function addCustomTag($tag) {
		self::$customTags[] = $tag;
	}
	
	/**
	 * Implement Google Analytics code after the footer, colled by the footer.php
	 * 
	 * @access public
	 * @return void
	 */
	public static function googleAnalyticsCode() {
		$amp_ga4_measurement_id = self::$pluginParams->get( 'amp_ga4_measurement_id' );
		$amp_gaid = self::$pluginParams->get( 'amp_gaid' );
		$amp_gtmid = self::$pluginParams->get( 'amp_gtmid' );
		$amp_pixel = self::$pluginParams->get( 'amp_pixel' );
		if( $amp_gaid ) {
			// Evaluate nonce csp feature
			$appNonce = self::$application->get('csp_nonce', null);
			$nonce = $appNonce ? ' nonce="' . $appNonce . '"' : '';
			echo '<amp-analytics type="googleanalytics" id="analytics_tracking">
					<script type="application/json"' . $nonce  . '>{"vars":{"account":"' . $amp_gaid . '"},"triggers":{"trackPageview":{"on":"visible","request":"pageview"}}}</script>
				  </amp-analytics>';
		}
		
		if($amp_ga4_measurement_id) {
			// Evaluate nonce csp feature
			$appNonce = self::$application->get('csp_nonce', null);
			$nonce = $appNonce ? ' nonce="' . $appNonce . '"' : '';
			echo '<amp-analytics type="googleanalytics" config="https://amp.analytics-debugger.com/ga4.json" data-credentials="include">
					<script type="application/json"' . $nonce  . '>
					{
					    "vars": {
					                "GA4_MEASUREMENT_ID": "' . $amp_ga4_measurement_id . '",
					                "GA4_ENDPOINT_HOSTNAME": "www.google-analytics.com",
					                "DEFAULT_PAGEVIEW_ENABLED": true,
					                "GOOGLE_CONSENT_ENABLED": false,
					                "WEBVITALS_TRACKING": false,
					                "PERFORMANCE_TIMING_TRACKING": false
					    }
					}
					</script>
					</amp-analytics>';
		}
		
		if($amp_gtmid) {
			echo '<amp-analytics config="https://www.googletagmanager.com/amp.json?id=' . $amp_gtmid . '&gtm.url=SOURCE_URL" data-credentials="include"></amp-analytics>';
		}
		
		if($amp_pixel) {
			echo '<amp-pixel src="' . $amp_pixel . '" layout="nodisplay"></amp-pixel>';
		}
	}
	
	/**
	 * Get images from a plain folder based on the module params for each instance, no support for title, caps, links
	 * 
	 * @access public
	 * @return array
	 */
	public static function getFolderImages() {
		$folderImgs = array();
	
		$imagesFolder = self::$pluginParams->get('pathfolder', null);
		$recursive = self::$pluginParams->get('recursive', false);
		if($imagesFolder) {
			$folderImgs = Folder::files(JPATH_SITE . '/images/' . $imagesFolder, '.', $recursive, true, array('index.html', '.svn', 'CVS', '.DS_Store', '__MACOSX'));
		}
	
		return $folderImgs;
	}
	
	/**
	 * Check if a given AMP component is published on the current menu item page
	 *
	 * @param string $ampComponentAssignParam
	 * @access public
	 * @return boolean
	 */
	public static function isAMPComponentPublished($ampComponentAssignParam) {
		// Check if the slideshow is enabled only in homepage
		$activeMenu = self::$application->getMenu()->getActive();
		
		// Check if the slideshow is enabled only on certain pages
		$assignedPages = self::$pluginParams->get ( $ampComponentAssignParam, 0 );
		if (is_array ( $assignedPages ) && ! in_array ( 0, $assignedPages, false )) {
			if (is_object ( $activeMenu )) {
				$menuItemid = $activeMenu->id;
				if (!in_array ( $menuItemid, $assignedPages )) {
					return false;
				}
			}
		}
		
		return true;
	}
	
	/**
	 * Load the configuration file for a certain dispatched component
	 * 
	 * @access public
	 * @static
	 * 
	 * @param string $option
	 * @param string $type
	 * @return string
	 */
	public static function loadManifestFile($option, $type) {
		static $manifestContents = null;
		$componentConfigType = null;
		
		// Ensure that the manifest loading is enabled
		if(!self::$pluginParams->get('auto_config', 1)) {
			return null;
		}
		
		// Load the manifest serialized file and assign to local variable
		if($manifestContents === null) {
			$manifestContents = @file_get_contents(JPATH_ROOT . '/plugins/system/jamp/core/manifest.json');
		}
		
		// Not valid manifest contents found
		if(!$manifestContents) {
			return null;
		}
		
		// Unserialize data and assign object data to local manifestObject property
		$manifestObject = json_decode($manifestContents);
		if(!$manifestObject) {
			return null;
		}
		
		// Check if we have a configuration for this component
		if(isset($manifestObject->{$option})) {
			$componentConfigTypes = $manifestObject->{$option};
			if(isset($componentConfigTypes->{$type})) {
				$componentConfigType = $componentConfigTypes->{$type};
			}
		}
		
		// Always load the default manifest
		$defaultConfigTypes = $manifestObject->com_jamp_default;
		if(isset($defaultConfigTypes->{$type})) {
			$componentConfigType .= ',' . $defaultConfigTypes->{$type};
			$componentConfigType = trim($componentConfigType, ',');
		}
		
		// Return unserialized manifest object
		return $componentConfigType;
	}
}