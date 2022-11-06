<?php
/**  
 * @package JAMP::plugins::system
 * @subpackage fields
 * @author Joomla! Extensions Store
 * @copyright (C) 2016 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html   
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );
use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Factory;

/**
 * Form Field for css purpouse
 *
 * @package JAMP::plugins::system
 * @subpackage fields
 * @since 1.0
 */
class JFormFieldComponentViews extends ListField {
	/**
	 * Method to attach a JForm object to the field.
	 *
	 * @param SimpleXMLElement $element
	 *        	The SimpleXMLElement object representing the `<field>` tag for the form field object.
	 * @param mixed $value
	 *        	The form field value to validate.
	 * @param string $group
	 *        	The field name group control value. This acts as as an array container for the field.
	 *        	For example if the field has name="foo" and the group value is set to "bar" then the
	 *        	full field name would end up being "bar[foo]".
	 *        	
	 * @return boolean True on success.
	 *        
	 * @since 11.1
	 */
	public function setup(\SimpleXMLElement $element, $value, $group = null) {
		parent::setup ( $element, $value, $group );
		
		$this->default = isset ( $element ['value'] ) ? ( string ) $element ['value'] : array (
				$this->default 
		);
		
		// Add custom JS to rework bootstrap popovers for the label description
		$doc = Factory::getApplication()->getDocument();
		// Include jQuery/Bootstrap framework
		$wa = $doc->getWebAssetManager();
		$wa->useScript('jquery');
		$wa->useScript('jquery-noconflict');
		array_map ( function ($script) use ($wa) {
			$wa->useScript ( 'bootstrap.' . $script );
		}, [
				'popover'
		] );
		$script = <<<EOL
		jQuery(function($){
			var smallText = $('div.control-group small.form-text').hide();
			smallText.each(function(index, elem){
				var parentContainer = $(elem).parents('div.control-group');
				var targetLabel = $('div.control-label label,div.controls legend', parentContainer);
				var sourceDescription = $(elem).html();
				targetLabel.attr('title', $(targetLabel.get(0)).text());
				targetLabel.attr('data-bs-content', sourceDescription);
				targetLabel.addClass('hasPopover');
				targetLabel.attr('aria-haspopup', 'true');
			});
			[].slice.call(document.querySelectorAll('div.control-group label.hasPopover,div.controls legend.hasPopover')).map(function (popoverEl) {
					return new bootstrap.Popover(popoverEl, {
												 "template":'<div class="popover"><div class="popover-arrow"></div><h3 class="popover-header"></h3><div class="popover-body"></div></div>',
												 "container":"body",
												 "html":true,
												 "placement":"top",
												 "trigger":"hover focus"
				});
			});

			// Manage the hide/show of subcontrols for device switcher handler
			var redirectSwitcher = $('select[name*=redirect_mobile_devices]').val();
			if(redirectSwitcher != 3) {
				$('input[class*=redirect_mobile],select[class*=redirect_mobile],fieldset[class*=redirect_mobile],div.switcher input[id*=redirect_mobile]').parents('div.control-group').hide();
			}
			$('select[name*=redirect_mobile_devices_toamp_page]').on('change', function(){
				if($(this).val() == 3) {
					$('input[class*=redirect_mobile],select[class*=redirect_mobile],fieldset[class*=redirect_mobile],div.switcher input[id*=redirect_mobile]').parents('div.control-group').slideDown();
				} else {
					$('input[class*=redirect_mobile],select[class*=redirect_mobile],fieldset[class*=redirect_mobile],div.switcher input[id*=redirect_mobile]').parents('div.control-group').slideUp();
				}
			});

			$('input.field-media-input').each(function(index, elem){
				$(elem).css('visibility','hidden');
			});
			// Observe media field image selection change
			$('div.field-media-preview').each(function(index, elem){
				// Create an observer instance for each element to observe
				var observer = new MutationObserver(function(mutations) {
					var image = $('img', elem);
					if(image.length) {
						let relatedInputField = $(elem).next('div').find('input.field-media-input');
						relatedInputField.val(relatedInputField.val().split('#')[0]);
					}
				});
				observer.observe(elem, { childList: true });
			});
			setTimeout(function(){
				$('input.field-media-input').each(function(index, elem){
					elem.value = elem.value.split('#')[0];
					$(elem).css('visibility','visible');
				});
			}, 100);
		});
EOL;
		$doc->getWebAssetManager()->addInlineScript($script);
		
		return true;
	}
	
	/**
	 * Build the multiple select list for Menu Links/Pages
	 *
	 * @access public
	 * @return array
	 */
	protected function getOptions() {
		// Add the css file for plugin settings styling
		$doc = Factory::getApplication()->getDocument ();
		$doc->getWebAssetManager()->registerAndUseStyle ( 'jamp.maincss', 'plugins/system/jamp/css/main.css');
		
		$doc->getWebAssetManager()->addInlineScript("document.addEventListener('DOMContentLoaded',function(){var mediaFields = document.querySelectorAll('input.field-media-input');[].forEach.call(mediaFields, function(mediafield) {mediafield.removeAttribute('readonly');});});");
		
		$options = array ();
		
		// Start the components and views list generation
		$path = JPATH_SITE . '/components';
		$iterator = new \DirectoryIterator ( $path );
		
		$componentsArray = array();
		foreach ( $iterator as $fileEntity ) {
			if (! $fileEntity->isDot () && $fileEntity->isDir ()) {
				$folderName = $fileEntity->getFilename ();
				$folderNameUser = ucfirst ( str_replace ( 'com_', '', $folderName ) );
				
				$componentsArray[] = array('foldername' => $folderName, 'foldernameuser' => $folderNameUser);
			}
		}
		
		// Sort
		asort($componentsArray);
		foreach ( $componentsArray as $componentArray ) {
			// Skip not relevant core extensions
			if (in_array ( $componentArray ['foldername'], array (
					'com_ajax',
					'com_config',
					'com_wrapper',
					'com_finder',
					'com_privacy'
			) )) {
				continue;
			}
			
			$viewPath = false;
			
			// Check if the views folder exists for this component, supporting both MVC models, namespaced or not and single or plural
			if(file_exists ( JPATH_SITE . '/components/' . $componentArray['foldername'] . '/views' )) {
				$viewPath = JPATH_SITE . '/components/' . $componentArray['foldername'] . '/views';
			}
			if(file_exists ( JPATH_SITE . '/components/' . $componentArray['foldername'] . '/view' )) {
				$viewPath = JPATH_SITE . '/components/' . $componentArray['foldername'] . '/view';
			}
			if(file_exists( JPATH_SITE . '/components/' . $componentArray['foldername'] . '/View' )) {
				$viewPath = JPATH_SITE . '/components/' . $componentArray['foldername'] . '/View';
			}
			if(file_exists( JPATH_SITE . '/components/' . $componentArray['foldername'] . '/src/View' )) {
				$viewPath = JPATH_SITE . '/components/' . $componentArray['foldername'] . '/src/View';
			}
			
			if ($viewPath) {
				$options [] = HTMLHelper::_ ( 'select.option', '<OPTGROUP>', $componentArray['foldernameuser'] );
				
				$viewsIterator = new \DirectoryIterator ( $viewPath );
				$optionsArray = array ();
				foreach ( $viewsIterator as $viewEntity ) {
					if (! $viewEntity->isDot () && $viewEntity->isDir ()) {
						$folderViewName = strtolower($viewEntity->getFilename ());
						$folderViewNameUser = ucfirst ( $folderViewName );
						
						$optionsArray [] = array (
								'componentview' => $componentArray['foldername'] . '.' . $folderViewName,
								'folderviewnameuser' => $folderViewNameUser 
						);
					}
				}
				
				// Sort
				asort ( $optionsArray );
				foreach ( $optionsArray as $optionArray ) {
					$options [] = HTMLHelper::_ ( 'select.option', $optionArray ['componentview'], $optionArray ['folderviewnameuser'] );
				}
				
				$options [] = HTMLHelper::_ ( 'select.option', '</OPTGROUP>' );
			}
		}
		return $options;
	}
}