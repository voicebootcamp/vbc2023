<?php
/**
 * @package JAMP::plugins::system
 * @subpackage jamp
 * @author Joomla! Extensions Store
 * @copyright (C)2015 Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined ( '_JEXEC' ) or die ();
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Event\SubscriberInterface;
use Joomla\Event\Event;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Factory;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Language\LanguageHelper;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\Registry\Registry;
use Joomla\String\StringHelper;
use Joomla\CMS\Version as JVersion;

/**
 * JAmp plugin for Joomla
 *
 * @package JAMP::plugins::system
 * @subpackage jamp
 * @since 1.0
 */
class PlgSystemJAmp extends CMSPlugin implements SubscriberInterface {
	/**
	 * @access private
	 * @var boolean
	 */
	private $isPluginStopped;
	
	/**
	 * Original Uri object including the AMP path
	 *
	 * @access private
	 * @var Object
	 */
	private $originalUri;
	
	/**
	 * @access private
	 * @var boolean
	 */
	private $debugMode;
	
	/**
	 * Store if the app can be executed after successfully checks
	 *
	 * @access protected
	 * @var int
	 */
	protected $startApp = false;
	
	/**
	 * App reference
	 *
	 * @access protected
	 * @var Object
	 */
	protected $appInstance;
	
	/**
	 * Database connector
	 *
	 * @access protected
	 * @var Object
	 */
	protected $dbInstance;
	
	/**
	 * Document reference
	 *
	 * @access protected
	 * @var Object
	 */
	protected $doc;
	
	/**
	 * Store the dispatch request for the home page
	 *
	 * @access protected
	 * @var boolean
	 */
	protected $isHomeRequest = false;
	
	/**
	 * Check if the Joomla multilanguage is enabled
	 * 
	 * @access private
	 * @return bool
	 */
	private function isMultilangEnabled() {
		// Status of language filter plugin.
		static $enabled = null;
	
		// If being called from the front-end, we can avoid the database query.
		if (is_null($enabled) && $this->appInstance->isClient ('site')) {
			$enabled = $this->appInstance->getLanguageFilter();
			return $enabled;
		}
	
		return $enabled;
	}
	
	/**
	 * Check for the valid filtered execution of the plugin based on option and view selected
	 * 
	 * @access private
	 * @return bool
	 */
	private function validateExecution() {
		if (!$this->appInstance->isClient ('site')) {
			return false;	
		}
		
		// Ensure that the website is not offline, otherwise allows the app and document rendering by Joomla to complete
		if ($this->appInstance->get('offline') && !$this->appInstance->getIdentity()->authorise('core.login.offline')) {
			return false;
		}
		
		// Exclusion by menu item, ensure to avoid the rel amphtml on the canonical page
		$menuExcluded = $this->params->get ( 'menu_exclusions', 0 );
		if (is_array ( $menuExcluded ) && ! in_array ( 0, $menuExcluded, false )) {
			$menu = $this->appInstance->getMenu ()->getActive ();
			if (is_object ( $menu )) {
				$menuItemid = $menu->id;
				if (in_array ( $menuItemid, $menuExcluded )) {
					return false;
				}
			}
		}
		
		// Always return true if there is an AMP story enabled for the home page
		$ampStoryRequest = $this->params->get('amp_story_enable', 0) && $this->isHomeRequest;
		if($ampStoryRequest) {
			return true;
		}
		
		// Get the dispatched option, view and id
		$option = $this->appInstance->input->get('option');
		$view = $this->appInstance->input->get('view');
		$id = $this->appInstance->input->getInt('id');
		// Ensure that $id is not an array
		if(is_array($id)) {
			$id = $id[0];
		}
		
		// Fallback for old extension using a task mapping
		if(!$view && $this->appInstance->input->get('task')) {
			$view = $this->appInstance->input->get('task');
		}
		
		// Fallback for old extension using a func mapping
		if(!$view && $this->appInstance->input->get('func')) {
			$view = $this->appInstance->input->get('func');
		}
		
		// Retrieve any valid exclusions/inclusions filters by ID
		$exclusionsFilters = array();
		if(trim($this->params->get('exclusions_filters', ''))) {
			$exclusionsFilters = explode(PHP_EOL, trim($this->params->get('exclusions_filters', '')));
			if(!empty($exclusionsFilters)) {
				foreach ($exclusionsFilters as &$exclusionFilter) {
					$exclusionFilter = trim($exclusionFilter);
				}
			}
		}
		if(trim($this->params->get('inclusions_filters', ''))) {
			$inclusionsFilters = explode(PHP_EOL, trim($this->params->get('inclusions_filters', '')));
			if(!empty($inclusionsFilters)) {
				foreach ($inclusionsFilters as &$inclusionFilter) {
					$inclusionFilter = trim($inclusionFilter);
				}
			}
		}
		
		// Build the dispatched firm
		$fullDispatchedFirm = StringHelper::str_ireplace('com_', '', $option) . '.' . $view . '.' . $id;
		
		// Invalid request, an option and a view are required in order to process the AMP execution
		if(!$option || !$view) {
			return false;
		}
		
		// Additional exclusions filters validation for entities in the excluded containing category
		if($catid = $this->appInstance->input->get('catid')) {
			if(!is_array($this->appInstance->input->get('catid'))) {
				if(isset($inclusionsFilters) && in_array(StringHelper::str_ireplace('com_', '', $option) . '.category.' . $catid , $inclusionsFilters)) {
					return true;
				}
				
				if(in_array(StringHelper::str_ireplace('com_', '', $option) . '.category.' . $catid , $exclusionsFilters)) {
					return false;
				}
			}
		}
		
		// Concatenate the dispatched option view
		$dispatchedOptionView = $option . '.' . $view;
		if($this->params->get('debug_firm', 0) && class_exists('JAmpHelper')) {
			\JAmpHelper::$dispatchedOptionView = $dispatchedOptionView;
		}
		
		// Get and parse the filter options
		$ampComponentsViews = $this->params->get('amp_components_views', array('com_content.article'));
		if(!is_array($ampComponentsViews)) {
			$ampComponentsViews = array('com_content.article');
		}
		
		// Support for custom component views in the form: com_xxx.viewname where viewname is fallbacked to task and func param
		$customComponentViews = array();
		if(trim($this->params->get('custom_components_view', ''))) {
			$customComponentViews = explode(PHP_EOL, trim($this->params->get('custom_components_view', '')));
			if(!empty($customComponentViews)) {
				foreach ($customComponentViews as &$customComponentView) {
					$customComponentView = trim($customComponentView);
				}
				$ampComponentsViews = array_merge($ampComponentsViews, $customComponentViews);
			}
		}
		
		// An invalid execution detected for this component and view, no inclusions are matched for the ID
		if(isset($inclusionsFilters) && in_array($dispatchedOptionView, $ampComponentsViews) && !in_array($fullDispatchedFirm, $inclusionsFilters)) {
			return false;
		}
		
		// A valid execution detected for this component and view, no exclusions are matched for the ID
		if(in_array($dispatchedOptionView, $ampComponentsViews) && !in_array($fullDispatchedFirm, $exclusionsFilters)) {
			return true;
		}
		
		/*
		 * Ensure that we do not have a AMP url with invalid execution, in such case redirect to the canonical
		 * \JAmpHelper::$canonicalUrl
		 */
		$currentUri = Uri::current();
		$ampSuffix = $this->params->get ( 'amp_suffix', 'amp' );
		// Check if we have plugins exclusions activated
		if(preg_match('/\/' . $ampSuffix . '$|\.' . $ampSuffix . '\./i', $currentUri)) {
			if(!class_exists('JAmpHelper')) {
				require_once (JPATH_ROOT . '/plugins/system/jamp/core/includes/helper.php');
			}
			$this->appInstance->redirect(trim(\JAmpHelper::$canonicalUrl, '/'));
		}
		
		return false;
	}
	
	/**
	 * Check if the url is an absolute URL in some way
	 * 
	 * @param string $url
	 * @return bool
	 */
	private function isFullyQualified($url) {
		$isFullyQualified = substr($url, 0, 7) == 'http://' || substr($url, 0, 8) == 'https://' || substr($url, 0, 2) == '//';
		return $isFullyQualified;
	}
	
	/**
	 * Debug mode rendering
	 *
	 * @access private
	 * @return string
	 */
	private function getArticleContent() {
		$componentOutput = null;

		$option = $this->appInstance->input->get('option');
		$view = $this->appInstance->input->get('view');
		$id = $this->appInstance->input->getInt('id');

		if($option == 'com_content' && $view == 'article' && $id) {
			$query = $this->dbInstance->getQuery(true);
			$query->select($this->dbInstance->quoteName('title'));
			$query->select($this->dbInstance->quoteName('introtext'));
			$query->select($this->dbInstance->quoteName('fulltext'));
			$query->from($this->dbInstance->quoteName('#__content'));
			$query->where($this->dbInstance->quoteName('id') . ' = ' . $id);
			$articleObject = $this->dbInstance->setQuery($query)->loadObject();
			
			if(!$this->params->get('force_component_output_title', 1)) {
				$articleObject->title = null;
			}
			
			if(!$this->params->get('force_component_output_fulltext', 0)) {
				$articleObject->fulltext = null;
			}
			
			$componentOutput = '<h2>' . $articleObject->title . '</h2>' . '<div>' . $articleObject->introtext . $articleObject->fulltext . '</div>';

			PluginHelper::importPlugin('content');
	
			$dummyParams = new Registry();
			$elm = new stdClass();
			$elm->text = $componentOutput;
	
			// Trigger to create a new Joomla user aggregating data from Facebook user profile, pre-populate bind $joomlaUserObject
			$this->appInstance->getDispatcher()->dispatch('onContentPrepare', new Event('onContentPrepare', [
				'com_content.article',
				&$elm,
				&$dummyParams,
				0
			]));
			
			$componentOutput = $elm->text;
		}

		return $componentOutput;
	}
	
	/**
	 * Method to be called everytime a head section has to be compiled and manipulated
	 *
	 * @return void
	 */
	private function injectMobileToAmpPage() {
		$document = $this->doc;
		
		// Scripts loading
		$wa = $document->getWebAssetManager();
		if($this->params->get('redirect_mobile_devices_devicewidth_load_jquery', 1)) {
			$wa->useScript('jquery');
		}
		if($this->params->get ( 'redirect_mobile_devices_devicewidth_jquery_noconflict', 1 )) {
			$wa->useScript('jquery-noconflict');
		}
		
		// Manage the loading effect, parameters, etc
		if($this->params->get('redirect_mobile_devices_devicewidth_hide_website', 1)) {
			$wa->addInlineStyle("html{visibility:hidden}");
		}
		if($this->params->get('redirect_mobile_devices_devicewidth_loader_effect', 0)) {
			$loadingEffectColor = $this->params->get('redirect_mobile_devices_devicewidth_loader_effect_color', '#0067A2');
			$wa->addInlineStyle("
				#jamp_loader {
					border: 16px solid #f3f3f3;
					border-radius: 50%;
					border-top: 16px solid $loadingEffectColor;
					border-bottom: 16px solid $loadingEffectColor;
					width: 150px;
					height: 150px;
					animation: spin 2s linear infinite;
				    visibility: visible;
				    position: fixed;
				    top: 50%;
				    left: 50%;
				    margin-left: -75px;
				    margin-top: -75px;
					box-sizing: border-box;
				}
				@keyframes spin {
					0% { transform: rotate(0deg); }
					100% { transform: rotate(360deg); }
				}
			");
		}
		$wa->addInlineScript("var jamp_live_site='" . JUri::root () . "';"  .
							 "var jamp_redirect_devicewidth=" . (int)$this->params->get( 'redirect_mobile_devices_devicewidth', 1024 ) . ";" .
							 "var jamp_redirect_width_value='" . $this->params->get('redirect_mobile_devices_devicewidth_width_value', 'outerWidth') . "';" .
							 "var jamp_redirect_loader_effect=" . $this->params->get('redirect_mobile_devices_devicewidth_loader_effect', 1) . ";" .
							 "var jamp_redirect_hide_website=" . $this->params->get('redirect_mobile_devices_devicewidth_hide_website', 1) . ";" .
							 "var jamp_redirect_session_delay=" . $this->params->get('redirect_mobile_devices_devicewidth_session_delay', 200) . ";");
		
		$wa->addInlineScript('!function(e){var i=function(){var i=window.sessionStorage.getItem("jamp_device_width"),t=!1;switch(jamp_redirect_width_value){case"outerWidth":var d=window.outerWidth;break;case"innerWidth":d=window.innerWidth;break;case"jQueryWidth":d=jQuery(window).width()}if((!i||i!=d)&&(window.sessionStorage.setItem("jamp_device_width",d),d<=jamp_redirect_devicewidth)){var r=e("link[rel=amphtml]").attr("href");r&&(t=!0,jamp_redirect_loader_effect&&e("body").append(\'<div id="jamp_loader"></div>\'),window.location.href=r)}!jamp_redirect_hide_website||t||e("#responsivizer_loader").length||(document.querySelector("html").style.visibility="visible")};e(function(){setTimeout(function(){window.JAmpMobileDetection=new i},jamp_redirect_session_delay)})}(jQuery);');
	}
	
	/**
	 * If the feature to redirect to the AMP page is enabled check if this is a mobile device and if so redirect here accordingly
	 *
	 * @access private
	 * @param String $ampLink
	 * @return void
	 */
	private function redirectMobileToAmpPage($ampLink) {
		$isMobile = false;
		
		// Exclude explicit main page version
		if($this->appInstance->input->exists('jampmain')) {
			return;
		}
		
		// Setup the redirection mode
		$redirectMode = (int) $this->params->get('redirect_mobile_devices_toamp_page', 0);
		
		require_once (JPATH_ROOT . '/plugins/system/jamp/core/includes/device.php');
		$ua = new \JAmpUAgentInfo($this->params->get('enable_device_debug', 0));
		
		$isSmartPhone = $ua->DetectSmartphone();
		$isTablet = $ua->DetectTierTablet();
		$isMobile = ($isSmartPhone || $isTablet);
		
		if ($redirectMode == 1) {
			if ($ua->DetectTierTablet()) {
				$isMobile = false;
			}
		}
		
		// Do redirect
		if($isMobile) {
			$this->appInstance->redirect($ampLink);
		}
	}
	
	/**
	 * Debug mode rendering
	 * 
	 * @access private
	 * @param String $ampLink
	 * @return void
	 */
	private function debugBar($ampLink) {
		$debugWidth = $this->params->get('debug_mode_width', 320);
		$debugHeight = $this->params->get('debug_mode_height', 480);
		
		// Purify the debug toolbar amp link
		$ampLink = trim($ampLink, '/');
		
		// If debug mode render a toolbar with a AMP link to test it
		$this->doc->getWebAssetManager()->addInlineScript('jQuery(function($){$("body").append("<div id=\"jamp_debug_toolbar\">URL of the AMP version of this page: <a onclick=\"window.open(\'' . $ampLink . '\', \'jamp\', \'width=' . $debugWidth . ',height=' . $debugHeight . '\');return false;\" href=\"' . $ampLink . '\">' . $ampLink . '</a> | <a class=\"amptest\" target=\"_blank\" href=\"https://search.google.com/test/amp?url=' . rawurlencode($ampLink) . '\">AMP test</a></div>")});');
		$this->doc->getWebAssetManager()->addInlineStyle('#jamp_debug_toolbar a{color:#005e8d;}#jamp_debug_toolbar{position:fixed;z-index:999999;bottom:0;left:0;width:100%;background-color:#ff9800;padding:20px;font-size:20px;line-height:30px;}a.amptest{border:1px solid #333;border-radius:5px;background-color:#CCC;padding:4px 8px;}');
	}
	
	/**
	 * Get a document object based on the container
	 *
	 * @return Document object
	 */
	private function getDocument() {
		$container = Factory::getContainer();
		
		// Build language object
		$conf = $this->appInstance->getConfig ();
		$locale = $conf->get ( 'language' );
		$debug = $conf->get ( 'debug_lang' );
		$languageInstance = $container->get ( \Joomla\CMS\Language\LanguageFactoryInterface::class )->createLanguage ( $locale, $debug );

		$input = $this->appInstance->input;
		$type = $input->get ( 'format', 'html', 'cmd' );
	
		// Build document object
		$version = new JVersion ();
		$attributes = array (
				'charset' => 'utf-8',
				'lineend' => 'unix',
				'tab' => "\t",
				'language' => $languageInstance->getTag (),
				'direction' => $languageInstance->isRtl () ? 'rtl' : 'ltr',
				'mediaversion' => $version->getMediaVersion ()
		);
	
		$documentInstance = $container->get ( \Joomla\CMS\Document\FactoryInterface::class )->createDocument ( $type, $attributes );
	
		return $documentInstance;
	}
	
	/**
	 * onAfterInitialise handler to manage content and system plugins exclusion
	 *
	 * @param Event $event
	 * @access	public
	 * @return void
	 */
	public function ampRoutingExclusions(Event $event) {
		if (!$this->appInstance->isClient ('site')) {
			return;
		}
	
		// Register event handlers and store pristine URI parserule, before that the Joomla! router modifies it
		$joomlaRouter = Factory::getContainer()->has('SiteRouter') ? Factory::getContainer()->get('SiteRouter'): $this->appInstance::getRouter();
		if(defined('\Joomla\CMS\Router\Router::PROCESS_BEFORE')) {
			$joomlaRouter->attachParseRule ( array (
					$this,
					'preProcessParseRule'
			), \Joomla\CMS\Router\Router::PROCESS_BEFORE );
		} else {
			$joomlaRouter->attachParseRule ( array (
					$this,
					'preProcessParseRule'
			));
		}
	
		// Ensure that the preProcessParseRule stage of JAmp is always executed before the language filter plugin one
		if($this->isMultilangEnabled()) {
			$languageRule = null;
			$rules = $joomlaRouter->getRules();
			$rulesListPreProcess = $rules['parsepreprocess'];
			$plgSystemLanguageFilterIndex = null;
			$plgSystemJampIndex = null;
			
			// Cycle all the parsepreprocess rules to identify the PlgSystemLanguageFilter and PlgSystemJAmp parse rules
			foreach ($rulesListPreProcess as $ruleIndex=>$rule) {
				if($rule[0] instanceof \PlgSystemLanguageFilter) {
					$plgSystemLanguageFilterIndex = $ruleIndex;
					$languageRule = $rule[0];
				}
				if($rule[0] instanceof \PlgSystemJAmp) {
					$plgSystemJampIndex = $ruleIndex;
				}
			}
			
			// In the case that the PlgSystemLanguageFilter is there and is executed before PlgSystemJAmp, postpone it to the last one
			if($plgSystemLanguageFilterIndex && $plgSystemJampIndex > $plgSystemLanguageFilterIndex) {
				$reflection = new \ReflectionProperty($joomlaRouter, 'rules');
				$reflection->setAccessible(true);
				
				// Splice and push to postpone the PlgSystemLanguageFilter parse rule to the end of the stack
				array_splice($rulesListPreProcess, $plgSystemLanguageFilterIndex, 1, null);
				array_push($rulesListPreProcess, array($languageRule, 'parseRule'));
				$rules['parsepreprocess'] = $rulesListPreProcess;
				$reflection->setValue($joomlaRouter, $rules);
			}
		}
		
		// Detect in some way an AMP URL request at this stage before all routing will happen
		$ampSuffix = $this->params->get ( 'amp_suffix', 'amp' );
		$currentUri = Uri::current();
	
		// Check if we have plugins exclusions activated
		if($this->params->get('enable_plugins_exclusions', 0) && preg_match('/\/' . $ampSuffix . '$|\.' . $ampSuffix . '\./i', $currentUri)) {
			// Load plugins to exclude
			$excludedPlugins = $this->params->get('plugins_excluded', []);
				
			// Always detach the Responsivizer plugin template switcher
			if(class_exists('PlgSystemResponsivizer')) {
				$excludedPlugins[] = 'system-responsivizer';
			}
	
			if(count($excludedPlugins) && !in_array('0', $excludedPlugins)) {
				// Ensure preloading of needed plugins groups to be excluded
				PluginHelper::importPlugin('content');
				PluginHelper::importPlugin('system');
				PluginHelper::importPlugin('captcha');
	
				$excludedPluginsClassname = array();
				foreach ($excludedPlugins as $excludedPlugin) {
					list($pluginType, $pluginName) = explode('-', $excludedPlugin);
					// Build plugins structure
					$pluginToExclude = array('type'=>$pluginType, 'name'=>$pluginName);
	
					$pluginClassName = 'plg' . ($pluginToExclude['type']) . ($pluginToExclude['name']);
					// Manage plugins exclusions at a early stage in the Joomla CMS app execution lifecycle
					$excludedPluginsClassname[$pluginClassName] = true;
					
					$pluginNamespacedClassName = 'joomla\\plugin\\' . ($pluginToExclude['type']) . '\\' . ($pluginToExclude['name']) . '\\extension\\' . ($pluginToExclude['name']);
					// Manage plugins exclusions at a early stage in the Joomla CMS app execution lifecycle
					$excludedPluginsClassname[$pluginNamespacedClassName] = true;
				}
	
				// Get all Joomla events
				$dispatcherObject = $this->appInstance->getDispatcher();
				$allSupportedEvents = array(
						'onContentPrepare',
						'onContentAfterTitle',
						'onContentBeforeDisplay',
						'onContentAfterDisplay',
						'onContentBeforeSave',
						'onContentAfterSave',
						'onContentPrepareForm',
						'onContentPrepareData',
						'onContentBeforeDelete',
						'onContentAfterDelete',
						'onContentChangeState',
						'onAfterInitialise',
						'onAfterRoute',
						'onAfterDispatch',
						'onAfterRender',
						'onBeforeRender',
						'onBeforeCompileHead',
						'onBeforeRespond',
						'onAfterRespond',
						'onSearch',
						'onSearchAreas',
						'onGetWebServices'
				);
				foreach ($allSupportedEvents as $eventName) {
					$registeredEventListeners = $dispatcherObject->getListeners($eventName);
						
					foreach ($registeredEventListeners as $registeredEventListener) {
						// We have a legacy plugin with a legacy 'Closure' attached
						if(!is_array($registeredEventListener)) {
							$reflectionFunctionClosure = new ReflectionFunction($registeredEventListener);
							$closureThisPluginClass = $reflectionFunctionClosure->getClosureThis();
								
							$reflectionClassClosure = new ReflectionClass($closureThisPluginClass);
							$closureThisPluginClassName = strtolower($reflectionClassClosure->getName());
								
							if(array_key_exists($closureThisPluginClassName, $excludedPluginsClassname)) {
								$this->appInstance->getDispatcher()->removeListener($eventName, $registeredEventListener);
							}
						} else {
							// We have a new plugin with a SubscriberInterface
							$subscriberClassInstance = $registeredEventListener[0];
							$subscriberClassName = strtolower(get_class($registeredEventListener[0]));
							if(class_exists($subscriberClassName) && array_key_exists($subscriberClassName, $excludedPluginsClassname)) {
								$this->appInstance->getDispatcher()->removeSubscriber($subscriberClassInstance);
							}
						}
					}
				}
			}
		}
	
		// Support for the GET form submission
		$ampSuffix = $this->params->get ( 'amp_suffix', 'amp' );
		$currentUri = Uri::current();
		if(strpos($currentUri, '.' . $ampSuffix) || strpos($currentUri, '/' . $ampSuffix)) {
			if($this->params->get('enable_form', 0) && $this->appInstance->input->get->get('jform', null, 'raw') && !empty($this->appInstance->input->get->get('jform', null, 'raw'))) {
				$token = Session::getFormToken();
				$this->appInstance->input->server->set('HTTP_X_CSRF_TOKEN', $token);
				// Copy all from GET to POST
				$requestArray = $GLOBALS;
				$requestName = '_' . strtoupper('get');
				$getArrayToCopy = $this->appInstance->input->getArray($requestArray[$requestName]);
				foreach ($getArrayToCopy as $name=>$data) {
					$this->appInstance->input->post->set($name, $data);
				}
			}
		}
	}
	
	/**
	 * Store at this stage the plugin params into the JAmpHelper core class
	 *
	 * @param Event $event
	 * @access public
	 * @return boolean
	 */
	public function initializeAmpPlugin(Event $event) {
		// Fully exclude all applications but the site one
		if(!$this->appInstance->isClient ('site')) {
			return false;
		}
		
		$sefEnabled = $this->appInstance->get ( 'sef', 1);
		$ampSuffix = '/' . $this->params->get ( 'amp_suffix', 'amp' );
		$ampSuffixHtmlSuffix = '.' . $this->params->get ( 'amp_suffix', 'amp' );
		$uriBase = rtrim($this->originalUri->base(), '/');
		$canonicalUrl = StringHelper::str_ireplace([$ampSuffix, $ampSuffixHtmlSuffix], '', rtrim(Uri::current (), '/'));
		$urlPathLength = $sefEnabled ? StringHelper::strlen(StringHelper::str_ireplace(array($uriBase, '/index.php', '.html'), '', $canonicalUrl)) : 0;
		
		// Inject objects to the static class helper, this is the case when a canonical page is routed
		if(! $this->startApp) {
			if($this->appInstance->getMenu()->getActive()) {
				$this->isHomeRequest = (bool)$this->appInstance->getMenu()->getActive()->home && $urlPathLength <= 3;
			}
			return false;
		}
		
		// Setup the static class helper, this is the case when an AMP page is routed
		if(class_exists('JAmpHelper')) {
			if($this->appInstance->getMenu()->getActive()) {
				$this->isHomeRequest = (bool)$this->appInstance->getMenu()->getActive()->home && $urlPathLength <= 3;
			}
			\JAmpHelper::$pluginParams = &$this->params;
			// Store the Homepage request state needed for the AMP Story evaluation
			\JAmpHelper::$isHomepageRequest = $this->isHomeRequest;
		}
		
		// If an AMP page is requested AND there is Joomla view cache enabled AND there are plugins exclusion, SO disable caching for AMP pages
		if ($this->startApp && (int)$this->appInstance->getConfig()->get('caching') > 0 && $this->params->get('enable_plugins_exclusions', 0)) {
			// Disable Joomla view cache for AMP pages if any
			$this->appInstance->getConfig()->set('caching', 0);
		}
		
		// Override with a specific Joomla source template
		if ($this->startApp && $tpl = $this->params->get ( 'joomla_template_source', '' )) {
			$this->appInstance->setTemplate($tpl);
		}
	}
	
	/**
	 * Adds to the document <head> of normal canonical pages, the rel ampthml metatag
	 * for the component.view that are enabled to have a valid AMP version
	 * 
	 * @param Event $event
	 * @access public
	 * @return void
	 */
	public function addHeadAmpLink(Event $event) {
		// Assign document object from the app instance
		$this->doc = $this->appInstance->getDocument();
		
		// Check for menu exclusion
		if ($this->startApp) {
			// Exclusion by menu item, ensure to redirect to the canonical page if the AMP page is detected but excluded
			$menuExcluded = $this->params->get ( 'menu_exclusions', 0 );
			if (is_array ( $menuExcluded ) && ! in_array ( 0, $menuExcluded, false )) {
				$menu = $this->appInstance->getMenu ()->getActive ();
				if (is_object ( $menu )) {
					$menuItemid = $menu->id;
					if (in_array ( $menuItemid, $menuExcluded )) {
						$this->appInstance->redirect(trim(\JAmpHelper::$canonicalUrl, '/'));
					}
				}
			}
		}
		
		// Check if the app can start, if yes a valid AMP request is detected
		if (! $this->validateExecution()) {
			return;
		}
		
		// Never execute it on an AMP page
		if($this->startApp) {
			return;
		}
		
		// Retrieve Uri informations
		$path = $this->originalUri->getPath ();
		$query = $this->originalUri->getQuery ();
		$base = rtrim($this->originalUri->getScheme() . '://' . $this->originalUri->getHost(), '/') . '/';
		
		// Assignment and amp suffix detection
		$rawPath = $path;
		$ampSuffix = $this->params->get ( 'amp_suffix', 'amp' );
		
		// apply index.php, normally home page without SEF rewriting
		if (! $this->isFullyQualified ( $path )) {
			// apply index.php
			if (! $this->appInstance->get ( 'sef_rewrite' ) && stripos($path, 'index.php') === false) {
				$path = '/index.php/' . ltrim($path, '/');
			}
		}
		
		// The Joomla SEF could be disabled, in such case we can avoid further processing
		if(!$this->appInstance->get ( 'sef', 1)) {
			$rawAmpUrl = trim($base, '/') . '/' . trim($path, '/') . '?' . $query . '&' . $ampSuffix . '=1';
			// Add the amphtml rel metatag optionally excluding the home page for an AMP Story
			$ampStoryRequest = $this->params->get('amp_story_enable', 0) && $this->isHomeRequest;
			if(!$ampStoryRequest) {
				$this->doc->addHeadLink ( htmlspecialchars ( $rawAmpUrl, ENT_COMPAT, 'UTF-8' ), 'amphtml' );
			}
			
			// Check if a redirect to the AMP version of this page for a mobile device is required
			if($redirectMode = $this->params->get('redirect_mobile_devices_toamp_page', 0)) {
				// Prevent mobile redirection for bots
				$isBot = false;
				if (isset ( $_SERVER ['HTTP_USER_AGENT'] )) {
					$user_agent = $_SERVER ['HTTP_USER_AGENT'];
					$botRegexPattern = "(Googlebot\/|Googlebot\-Mobile|Googlebot\-Image|Google favicon|Mediapartners\-Google|bingbot|slurp|java|wget|curl|Commons\-HttpClient|Python\-urllib|libwww|httpunit|nutch|phpcrawl|msnbot|jyxobot|FAST\-WebCrawler|FAST Enterprise Crawler|biglotron|teoma|convera|seekbot|gigablast|exabot|ngbot|ia_archiver|GingerCrawler|webmon |httrack|webcrawler|grub\.org|UsineNouvelleCrawler|antibot|netresearchserver|speedy|fluffy|bibnum\.bnf|findlink|msrbot|panscient|yacybot|AISearchBot|IOI|ips\-agent|tagoobot|MJ12bot|dotbot|woriobot|yanga|buzzbot|mlbot|yandexbot|purebot|Linguee Bot|Voyager|CyberPatrol|voilabot|baiduspider|citeseerxbot|spbot|twengabot|postrank|turnitinbot|scribdbot|page2rss|sitebot|linkdex|Adidxbot|blekkobot|ezooms|dotbot|Mail\.RU_Bot|discobot|heritrix|findthatfile|europarchive\.org|NerdByNature\.Bot|sistrix crawler|ahrefsbot|Aboundex|domaincrawler|wbsearchbot|summify|ccbot|edisterbot|seznambot|ec2linkfinder|gslfbot|aihitbot|intelium_bot|facebookexternalhit|yeti|RetrevoPageAnalyzer|lb\-spider|sogou|lssbot|careerbot|wotbox|wocbot|ichiro|DuckDuckBot|lssrocketcrawler|drupact|webcompanycrawler|acoonbot|openindexspider|gnam gnam spider|web\-archive\-net\.com\.bot|backlinkcrawler|coccoc|integromedb|content crawler spider|toplistbot|seokicks\-robot|it2media\-domain\-crawler|ip\-web\-crawler\.com|siteexplorer\.info|elisabot|proximic|changedetection|blexbot|arabot|WeSEE:Search|niki\-bot|CrystalSemanticsBot|rogerbot|360Spider|psbot|InterfaxScanBot|Lipperhey SEO Service|CC Metadata Scaper|g00g1e\.net|GrapeshotCrawler|urlappendbot|brainobot|fr\-crawler|binlar|SimpleCrawler|Livelapbot|Twitterbot|cXensebot|smtbot|bnf\.fr_bot|A6\-Indexer|ADmantX|Facebot|Twitterbot|OrangeBot|memorybot|AdvBot|MegaIndex|SemanticScholarBot|ltx71|nerdybot|xovibot|BUbiNG|Qwantify|archive\.org_bot|Applebot|TweetmemeBot|crawler4j|findxbot|SemrushBot|yoozBot|lipperhey|y!j\-asr|Domain Re\-Animator Bot|AddThis)";
					$isBot = preg_match("/{$botRegexPattern}/i", $user_agent);
				}
				if(!$isBot) {
					if($redirectMode == 3) {
						$this->injectMobileToAmpPage();
					} else {
						$this->redirectMobileToAmpPage($rawAmpUrl);
					}
				}
			}
			
			// Check if the debug mode is activated
			if($this->params->get('debug_mode', 0)) {
				$this->debugBar($rawAmpUrl);
			}
			return;
		}
		
		// Does it have a query string to remove/split?
		if (empty ( $query )) {
			$questionMarkPosition = strpos ( $path, '?' );
			if ($questionMarkPosition !== false) {
				$path = substr ( $path, 0, $questionMarkPosition );
				$query = substr ( $rawPath, $questionMarkPosition );
			}
		}
		
		// Does it start with a slash? (but not a protocol relative URL)
		$hasLeadingSlash = substr ( $path, 0, 1 ) == '/' && substr ( $path, 0, 2 ) != '//';
		
		// Does it end with a slash?
		$hasTrailingSlash = substr ( $path, - 1 ) == '/';
		
		// Do we have an html suffix?
		$htmlSuffix = null;
		$joomlaHtmlSuffix = $this->appInstance->get ( 'sef_suffix', 1 );
		if ( $joomlaHtmlSuffix ) {
			$htmlSuffix = '.html';
		}
		if (defined ( 'SH404SEF_IS_RUNNING' )) {
			if(class_exists('\Sh404sefFactory')) {
				$htmlSuffix = \Sh404sefFactory::getConfig ()->suffix;
			}
		}
		
		if (substr ( $path, - 5 ) == $htmlSuffix) {
			$ampPath = substr ( $path, 0, - 5 ) . (empty ( $ampSuffix ) ? '' : '.' . $ampSuffix) . $htmlSuffix;
		} 		
		// Is a slash
		else if ($path == '/') {
			$ampPath = $ampSuffix;
		} 		
		// Ends with a slash
		else if ($hasTrailingSlash) {
			$ampPath = $path . (empty ( $ampSuffix ) ? '' : $ampSuffix . '/');
		} 		
		// Anything else
		else {
			$ampPath = $path . (empty ( $ampSuffix ) ? '' : '/' . $ampSuffix);
		}
		
		// Normalize path
		$ampPath = ltrim ( $ampPath, '/' );
		
		// Full URL, make sure the path was not already fully qualified
		if (! $this->isFullyQualified ( $ampPath )) {
			$ampPath = $base . $ampPath;
		}
		
		if(empty($query)) {
			$ampLink = $ampPath . $query;
		} else {
			$ampLink = trim($ampPath, '?') . '?' . trim($query, '?');
		}
		
		// Add the amphtml rel metatag optionally excluding the home page for an AMP Story
		$ampStoryRequest = $this->params->get('amp_story_enable', 0) && $this->isHomeRequest;
		if(!$ampStoryRequest) {
			$this->doc->addHeadLink ( htmlspecialchars ( rtrim($ampLink, '/'), ENT_COMPAT, 'UTF-8' ), 'amphtml' );
		}
		
		// Check if a redirect to the AMP version of this page for a mobile device is required
		if($redirectMode = $this->params->get('redirect_mobile_devices_toamp_page', 0)) {
			// Prevent mobile redirection for bots
			$isBot = false;
			if (isset ( $_SERVER ['HTTP_USER_AGENT'] )) {
				$user_agent = $_SERVER ['HTTP_USER_AGENT'];
				$botRegexPattern = "(Googlebot\/|Googlebot\-Mobile|Googlebot\-Image|Google favicon|Mediapartners\-Google|bingbot|slurp|java|wget|curl|Commons\-HttpClient|Python\-urllib|libwww|httpunit|nutch|phpcrawl|msnbot|jyxobot|FAST\-WebCrawler|FAST Enterprise Crawler|biglotron|teoma|convera|seekbot|gigablast|exabot|ngbot|ia_archiver|GingerCrawler|webmon |httrack|webcrawler|grub\.org|UsineNouvelleCrawler|antibot|netresearchserver|speedy|fluffy|bibnum\.bnf|findlink|msrbot|panscient|yacybot|AISearchBot|IOI|ips\-agent|tagoobot|MJ12bot|dotbot|woriobot|yanga|buzzbot|mlbot|yandexbot|purebot|Linguee Bot|Voyager|CyberPatrol|voilabot|baiduspider|citeseerxbot|spbot|twengabot|postrank|turnitinbot|scribdbot|page2rss|sitebot|linkdex|Adidxbot|blekkobot|ezooms|dotbot|Mail\.RU_Bot|discobot|heritrix|findthatfile|europarchive\.org|NerdByNature\.Bot|sistrix crawler|ahrefsbot|Aboundex|domaincrawler|wbsearchbot|summify|ccbot|edisterbot|seznambot|ec2linkfinder|gslfbot|aihitbot|intelium_bot|facebookexternalhit|yeti|RetrevoPageAnalyzer|lb\-spider|sogou|lssbot|careerbot|wotbox|wocbot|ichiro|DuckDuckBot|lssrocketcrawler|drupact|webcompanycrawler|acoonbot|openindexspider|gnam gnam spider|web\-archive\-net\.com\.bot|backlinkcrawler|coccoc|integromedb|content crawler spider|toplistbot|seokicks\-robot|it2media\-domain\-crawler|ip\-web\-crawler\.com|siteexplorer\.info|elisabot|proximic|changedetection|blexbot|arabot|WeSEE:Search|niki\-bot|CrystalSemanticsBot|rogerbot|360Spider|psbot|InterfaxScanBot|Lipperhey SEO Service|CC Metadata Scaper|g00g1e\.net|GrapeshotCrawler|urlappendbot|brainobot|fr\-crawler|binlar|SimpleCrawler|Livelapbot|Twitterbot|cXensebot|smtbot|bnf\.fr_bot|A6\-Indexer|ADmantX|Facebot|Twitterbot|OrangeBot|memorybot|AdvBot|MegaIndex|SemanticScholarBot|ltx71|nerdybot|xovibot|BUbiNG|Qwantify|archive\.org_bot|Applebot|TweetmemeBot|crawler4j|findxbot|SemrushBot|yoozBot|lipperhey|y!j\-asr|Domain Re\-Animator Bot|AddThis)";
				$isBot = preg_match("/{$botRegexPattern}/i", $user_agent);
			}
			if(!$isBot) {
				if($redirectMode == 3) {
					$this->injectMobileToAmpPage();
				} else {
					$this->redirectMobileToAmpPage($ampLink);
				}
			}
		}
		
		// Check if the debug mode is activated
		if($this->params->get('debug_mode', 0)) {
			$this->debugBar($ampLink);
		}
	}
	
	/**
	 * Here happens the magic, the Joomla output is blocked and converted to AMP
	 *
	 * @param Event $event
	 * @access public
	 */
	public function convertToAmp(Event $event) {
		// Check if the app can start, if yes a valid AMP request is detected
		if (! $this->validateExecution()) {
			return;
		}
		
		// Check if the AMP execution app is requested and activated
		if(! $this->startApp) {
			return false;
		}
		
		// Inject objects to the static class helper
		\JAmpHelper::$document = &$this->doc;
		\JAmpHelper::$application = &$this->appInstance;
		\JAmpHelper::$componentOutput = $this->doc->getBuffer ( 'component' );
		
		// Force single article component output if empty
		if(!\JAmpHelper::$componentOutput && $this->params->get('force_component_output', 0)) {
			\JAmpHelper::$componentOutput = $this->getArticleContent();
		}
		
		// Special treatment for replacement strings
		if($this->params->get('remove_strings_enable', 0)) {
			$replaceBtnsSelector = $this->params->get('remove_strings_regexs', '');
			
			$removalStrings = array();
			if(trim($this->params->get('remove_strings_regexs', ''))) {
				$removalStrings = explode(PHP_EOL, trim($this->params->get('remove_strings_regexs', '')));
				if(!empty($removalStrings)) {
					foreach ($removalStrings as &$removalString) {
						$removalString = trim($removalString);
						\JAmpHelper::$componentOutput = preg_replace('#' . $removalString .'#i', '', \JAmpHelper::$componentOutput);
					}
				}
			}
		}
		
		// Generate the AMP template
		require_once (JPATH_ROOT . '/plugins/system/jamp/core/includes/simplehtmldom.php');
		
		if($this->params->get('preload_amp_script', 0)) {
			header("Link: <https://cdn.ampproject.org/v0.js>; rel=preload; as=script");
		}
		
		// Prevent conflicts, avoid to have error for locked WebAssetManager
		$wa = $this->appInstance->getDocument()->getWebAssetManager();
		$reflection = new \ReflectionProperty($wa, 'locked');
		$reflection->setAccessible(true);
		$reflection->setValue($wa, false);
		
		if($this->params->get('activate_local_cache', 0)) {
			$cacheId = 'plg_jamp_component_output_' . $this->originalUri;
			if (strpos($this->appInstance->input->server->get('HTTP_ACCEPT'), 'webp') !== false) {
				$cacheId = $cacheId . 'webp';
			}
			$cacheId = md5($cacheId);
			
			$aOptions = array (
					'defaultgroup' => 'plg_jamp',
					'checkTime' => true,
					'application' => 'site',
					'language' => 'en-GB',
					'cachebase' => $this->appInstance->get('cache_path', JPATH_CACHE),
					'storage' => 'file'
			);
			
			$oCache = Factory::getContainer()->get(\Joomla\CMS\Cache\CacheControllerFactoryInterface::class)->createCacheController( 'output', $aOptions );
			$oCache->setCaching ( true );
			$oCache->setLifeTime ( $this->params->get('local_cache_lifetime', 60) );
			
			if($cachedPage = $oCache->get($cacheId)) {
				echo $cachedPage;
			} else {
				ob_start();
				require_once (JPATH_ROOT . PLG_JAMP_TEMPLATE_PATH . 'main.php');
				$pageContents = ob_get_contents();
				ob_end_clean();

				// AMP consent replacements
				$blockedElementsArray = $this->params->get ( 'iubenda_blocked_elements', [] );
				if ((\JAmpHelper::$pluginParams->get ( 'enable_user_notification', 0 ) == 2 || \JAmpHelper::$pluginParams->get ( 'enable_user_notification', 0 ) == 3) &&
					  is_array ( $blockedElementsArray ) && !in_array('0', $blockedElementsArray, true)) {
					foreach ( $this->params->get ( 'iubenda_blocked_elements' ) as $blockedComponent ) {
						$pageContents = StringHelper::str_ireplace ( '<' . $blockedComponent, '<' . $blockedComponent . ' data-block-on-consent', $pageContents );
					}
				}
				
				$oCache->store($pageContents, $cacheId);
				echo $pageContents;
			}
		} else {
			// Manage the iubenda data block on consent
			if(\JAmpHelper::$pluginParams->get('enable_user_notification', 0) == 2 || \JAmpHelper::$pluginParams->get ( 'enable_user_notification', 0 ) == 3) {
				ob_start();
				require_once (JPATH_ROOT . PLG_JAMP_TEMPLATE_PATH . 'main.php');
				$pageContents = ob_get_contents();
				ob_end_clean();
				
				// AMP consent replacements
				$blockedElementsArray = $this->params->get ( 'iubenda_blocked_elements', [] );
				if (is_array ( $blockedElementsArray ) && !in_array('0', $blockedElementsArray, true)) {
					foreach ( $this->params->get ( 'iubenda_blocked_elements' ) as $blockedComponent ) {
						$pageContents = StringHelper::str_ireplace ( '<' . $blockedComponent, '<' . $blockedComponent . ' data-block-on-consent', $pageContents );
					}
				}
				
				echo $pageContents;
			} else {
				require_once (JPATH_ROOT . PLG_JAMP_TEMPLATE_PATH . 'main.php');
			}
		}
		
		// Exit the Joomla process here
		jexit ();
	}
	
	/** 
	 * Inject the additional images field into article edit form
	 *
	 * @param Event $event
	 * @subparam Joomla\CMS\Form\Form $form
	 * @subparam object $data
	 * @access public
	 * @return void
	 */
	function injectImageFormFields(Event $event) {
		// subparams: $form, $data
		$arguments = $event->getArguments();
		$form = $arguments[0];
		$data = $arguments[1];
		
		if(!$this->appInstance->isClient('administrator')) {
			return true;
		}
		
		// Only works on \Joomla\CMS\Form\Form
		if (!($form instanceof \Joomla\CMS\Form\Form)) return true;
	
		// which belong to the following components
		$components_list = array(
				"com_content.article",
				"com_categories.categorycom_content"
		);
	
		$formName = $form->getName();
		if (!in_array($formName, $components_list)) return true;
		
		switch ($form->getName()) {
			case 'com_content.article':
				$wa = $this->appInstance->getDocument()->getWebAssetManager();
				$wa->addInlineScript("
							document.addEventListener('DOMContentLoaded', function (event) {
								let elem = document.querySelector('#fieldset-metadata div.field-media-preview')
							    let observer = new MutationObserver(function(mutations) {
									let image = document.querySelector('#fieldset-metadata div.field-media-preview img');
									if(image) {
										let relatedInputField = document.querySelector('#jform_metadata_jamp_image');
										relatedInputField.value = relatedInputField.value.split('#')[0];
									}
								});
								observer.observe(elem, { childList: true });
		
								setTimeout(function(){
									let articleImage = document.querySelector('#jform_metadata_jamp_image');
									articleImage.value = articleImage.value.split('#')[0];
								}, 100);
							});");
				$form->load('<form>
								<fields name="metadata">
									<fieldset name="jmetadata">
									    <field name="jamp_image" type="media" default="" label="AMP image" description="Choose an AMP meta image having minimum size of 1200x675px" />
									</fieldset>
								</fields>
							</form>');
				break;
				
			case 'com_categories.categorycom_content':
				$form->load('<form>
								<fields name="metadata">
									<fieldset name="jmetadata">
									    <field name="jamp_schema_type" class="btn-group" type="radio" default="" label="AMP metadata type" description="Choose a meta data type for your contents">
											<option value="">Use global</option>
											<option value="NewsArticle">NewsArticle</option>
											<option value="BlogPosting">BlogPosting</option>
										</field>
									</fieldset>
								</fields>
							</form>');
				break;
		}
		
		
		return true;
	}
	
	/**
	 * Validate the request before it's parsed and fully routed to identify the AMP suffix,
	 * and alter the request so that it's parsed normally
	 *
	 * We set a flag so that at a later stage we know that this is an AMP request
	 *
	 * @param Object &$router
	 * @param Object &$uri
	 *
	 * @return array
	 */
	public function preProcessParseRule(&$router, &$uri) {
		// Store some immutable data
		$uriBase = $uri->base ();
		$uriBasePath = $uri->base ( true );
		$query = '';

		$originalPath = StringHelper::str_ireplace( $uriBasePath, '', $this->originalUri->getPath () );

		$processingAmpOriginalUriPath = $this->originalUri->getPath ();
		
		// Avoid duplicated subfolders uri
		$originalUriPath = $this->originalUri->getPath ();
		if($uriBasePath && StringHelper::strpos($originalUriPath, $uriBasePath) !== false) {
			$originalUriPath = StringHelper::str_ireplace( $uriBasePath, '', $originalUriPath );
			$processingAmpOriginalUriPath = $originalUriPath;
		}

		$hasTrailingSlash = StringHelper::substr( $originalPath, - 1 ) == '/';

		// Start the list of suffixes to be parsed and recognized as AMP pages
		$ampSuffix = $this->params->get ( 'amp_suffix', 'amp' );
		$ampSuffixes = array (
				array (
						'suffix' => $ampSuffix,
						'replacer' => ''
				),
				array (
						'suffix' => $ampSuffix . '/',
						'replacer' => '/'
				)
		);
			
		// Do we have an html suffix?
		$htmlSuffix = null;
		$joomlaHtmlSuffix = $this->appInstance->get ( 'sef_suffix', 1 );
		if ( $joomlaHtmlSuffix ) {
			$htmlSuffix = '.html';
		}
		if (defined ( 'SH404SEF_IS_RUNNING' )) {
			if (class_exists ( '\Sh404sefFactory' )) {
				$htmlSuffix = \Sh404sefFactory::getConfig ()->suffix;
			}
		}
		// If the suffix is enabled and present add it to the list of suffixes
		if (! empty ( $htmlSuffix )) {
			$ampSuffixes [] = array (
					'suffix' => '.' . $ampSuffix . $htmlSuffix,
					'replacer' => ''
			);
		}

		// Check if SEF URLs are on
		$sefUrlsActive = $this->appInstance->get ( 'sef', 1);
		if(!$sefUrlsActive) {
			// Add a new suffix for no sef active
			$query = $uri->getQuery();
			$ampSuffixes [] = array (
					'suffix' => '&' . $ampSuffix . '=1',
					'replacer' => ''
			);
		}

		// Check if we have non-fully SEF URLs, aka SEF active but query string present
		$currentQueryString = $uri->getQuery();
		// J4 Router fix
		$currentQueryString = StringHelper::str_ireplace('&format=html', '', $currentQueryString);
		
		if($sefUrlsActive && $currentQueryString && (StringHelper::strpos($currentQueryString,  $ampSuffix . '=1') !== false)) {
			// Add a new suffix for no sef active
			$query = $currentQueryString;
			$ampSuffixes [] = array (
					'suffix' => $ampSuffix . '=1',
					'replacer' => -1
			);
		}
		
		// look for one of the possible suffixes at the end of the incoming URL
		foreach ( $ampSuffixes as $suffixId => $suffix ) {
			$fullSuffix = StringHelper::substr ( $suffix ['suffix'], 0, 1 ) == '.' ? $suffix ['suffix'] : '/' . $suffix ['suffix'];
			if ($originalPath == $suffix ['suffix'] || 			// home page
					StringHelper::substr ( $originalPath, - StringHelper::strlen ( $fullSuffix ) ) == $fullSuffix ||
					StringHelper::substr ( $query, - StringHelper::strlen ( $suffix['suffix'] ) ) == $suffix['suffix']) {
						$currentSuffix = $fullSuffix;
						// NB: we use here $uri->getpath() instead of $originalPath
						// because $uri->getpath() has already been urldecoded by the router
						// and this is what we need to set back in the router for futher processing
						// however the Uri class incorrectly remove the trailing slash in incoming
						// URLs, so we have to compensate for that
						$currentPath = $processingAmpOriginalUriPath . ($hasTrailingSlash ? '/' : '');
						if($suffix ['replacer'] != -1) {
							$newPath = StringHelper::substr ( $currentPath, 0, - strlen ( $fullSuffix ) ) . $suffix ['replacer'];
						} else {
							$newPath = $currentPath;
						}
						
						// Set here the valid AMP request detected
						$this->startApp = true;
						break;
					}
		}
	
		if (defined ( 'SH404SEF_IS_RUNNING' ) && $this->startApp) {
			if (class_exists ( '\Sh404sefFactory' )) {
				$htmlSuffix = \Sh404sefFactory::getConfig ()->suffix;
				if(!StringHelper::strpos($originalPath, $htmlSuffix)) {
					$nonSefUrl = '';
					$testPath = $newPath;
					$shModel = \ShlMvcModel_Base::getInstance('Sefurls', 'Sh404sefModel');
					if(method_exists($shModel, 'getNonSefUrlRecordFromDatabase')) {
						$urlRecord = $shModel->getNonSefUrlRecordFromDatabase($testPath, $nonSefUrl);
						$urlType = $urlRecord['status'];
						// Not found sh404sef URL, invert trailing slash and newpath
						if($urlType == -2) {
							$hasTrailingSlash = !$hasTrailingSlash;
							$newPath = $hasTrailingSlash ? $newPath . '/' : trim($newPath, '/');
						}
					}
				}
			}
		}
		
		// Patch for the 4SEF rewriting component integration
		$forSefRunning = false;
		if(defined('4SEF_IS_RUNNING') && $this->startApp) {
			$query = $this->dbInstance->getQuery(true);
			$query->select('value');
			$query->from('#__forsef_config');
			$query->where( $this->dbInstance->quoteName('key') . ' = ' . $this->dbInstance->quote('routing'));
			$forSefConfig = json_decode($this->dbInstance->setQuery($query)->loadResult(), true);
			if($forSefConfig['enabled']) {
				$forSefRunning = true;
				$query = $this->dbInstance->getQuery(true);
				$query->select('id');
				$query->from('#__forsef_urls');
				$query->where( $this->dbInstance->quoteName('sef') . ' = ' . $this->dbInstance->quote(ltrim($newPath, '/')));
				$urlExists = $this->dbInstance->setQuery($query)->loadResult();
				// Not found 4SEF URL, invert trailing slash and newpath
				if(!$urlExists) {
					$hasTrailingSlash = !$hasTrailingSlash;
					$newPath = $hasTrailingSlash ? $newPath . '/' : trim($newPath, '/');
				}
			}
		}

		// Valid AMP request detected
		if ($this->startApp && $sefUrlsActive) {
			// Suppresses trailing slash as Joomla does
			$truncatedPath = $hasTrailingSlash ? ltrim ( $newPath, '/' ) : trim ( $newPath, '/' );
			// J4 Router fix
			$truncatedPath = StringHelper::str_ireplace('.html', '', $truncatedPath);
			$truncatedPath = StringHelper::str_ireplace(['index.php/', 'index.php'], '', $truncatedPath );
			$truncatedPath = ltrim($truncatedPath, '/');

			// J4 subfolder fix, must be removed the subpath of the subfolder
			$extraReplacePath = '';
			$fullRootPath = ($uri->root());
			$baseRootPath = rtrim($uri->getScheme() . '://' . $uri->getHost(), '/') . '/';
			// Check if there is any extra path subfolder
			if($fullRootPath != $baseRootPath) {
				$extraReplacePath = StringHelper::str_ireplace($baseRootPath, '', $fullRootPath);
			}
			
			$uri->setPath ( StringHelper::str_ireplace(['index.php/', 'index.php', $extraReplacePath], '', $truncatedPath ));
			$uriQuery = $this->originalUri->getQuery ();
			$uriQuery = empty ( $uriQuery ) ? '' : '?' . $uriQuery;
			$urlRewritePrefix = 'index.php';
			if (defined ( 'SH404SEF_IS_RUNNING' ) && empty ( $truncatedPath )) {
				// sh404SEF: on home page, we don't keep the index.php bit as Joomla does
				$urlRewritePrefix = '';
			}
				
			$truncatedPath = rtrim ( $truncatedPath, '/' );
				
			if (! $this->appInstance->get ( 'sef_rewrite' ) && strpos($truncatedPath, 'index.php') === false) {
				// not using URL Rewriting, stick index.php at beginning of new URL
				$truncatedPath = $urlRewritePrefix . (empty ( $truncatedPath ) ? '' : '/' . $truncatedPath);
			}
		
			// Home page URL
			$sefLanguage = null;
			$languageIso = null;
			$indexPhpHome = null;
			if ($sefUrlsActive && $this->isMultilangEnabled()) {
				$sefs = LanguageHelper::getLanguages('sef');
				$path = ltrim($uri->getPath(), '/');
				$parts = explode('/', $path);
				if(array_key_exists($parts[0], $sefs)) {
					$languageIso = $parts[0];
					$sefLanguage = $parts[0] . '/';
					$indexPhpHome = 'index.php/' . $languageIso;
				}
				
				// Home page URL fix single language
				$uri->setPath($path);
			}
			
			// Optionally exclude home page multilanguage 'en', etc and index.php/en to become en.html and index.php/en.html
			if ( $joomlaHtmlSuffix && $truncatedPath && $truncatedPath != 'index.php' && StringHelper::strpos($truncatedPath, $htmlSuffix) === false && $truncatedPath != $languageIso && $truncatedPath != $indexPhpHome ) {
				$truncatedPath .= $htmlSuffix;
			}
				
			// find the canonical based on the previously calculated bits
			// not forgetting to also re-append the trailing slash Joomla stripped
			require_once (JPATH_ROOT . '/plugins/system/jamp/core/includes/image.php');
			require_once (JPATH_ROOT . '/plugins/system/jamp/core/includes/fastimage.php');
			require_once (JPATH_ROOT . '/plugins/system/jamp/core/includes/helper.php');
			\JAmpHelper::$canonicalUrl = $uriBase . $truncatedPath . ($hasTrailingSlash ? '/' : '') . $uriQuery;
			\JAmpHelper::$ampUrl = Uri::current ();
			\JAmpHelper::$isJAmpRequest = true;
			$this->appInstance->set('isJAmpRequest', true);

			$indexphp = null;
			if (! $this->appInstance->get ( 'sef_rewrite' )) {
				$indexphp = 'index.php/';
			}
			\JAmpHelper::$ampHomeUrl = Uri::base(false) . $indexphp . $sefLanguage . $ampSuffix;
		} elseif($this->startApp && !$sefUrlsActive) {
			require_once (JPATH_ROOT . '/plugins/system/jamp/core/includes/image.php');
			require_once (JPATH_ROOT . '/plugins/system/jamp/core/includes/fastimage.php');
			require_once (JPATH_ROOT . '/plugins/system/jamp/core/includes/helper.php');
			$path = $uri->getPath ();
			$base = $uri->base();
			$rawCanonicalUrl = trim($base, '/') . '/' . trim($path, '/') . '?' . StringHelper::str_ireplace('&' . $ampSuffix . '=1', '', $query);
			$rawAmpUrl = trim($base, '/') . '/' . trim($path, '/') . '?' . $query;

			\JAmpHelper::$canonicalUrl = $rawCanonicalUrl;
			\JAmpHelper::$ampUrl = $rawAmpUrl;
			\JAmpHelper::$isJAmpRequest = true;
			$this->appInstance->set('isJAmpRequest', true);
		}

		// Check for default language reset
		if ($this->startApp) {
			// Keep always the language cookie reset
			if($this->params->get('remove_language_default_prefix', 0) && PluginHelper::isEnabled('system', 'languagefilter')) {
				$pluginLangFilter = PluginHelper::getPlugin('system', 'languagefilter');
				$pluginLangFilterParams = json_decode($pluginLangFilter->params);
				if($pluginLangFilterParams->remove_default_prefix) {
					$this->appInstance->input->cookie->set(ApplicationHelper::getHash('language'), '', -1, '/');
					$this->appInstance->getSession()->remove('plg_system_languagefilter.language');
				}
			}
			
			// Patch for the 4SEF rewriting component integration
			if(defined('4SEF_IS_RUNNING') && $forSefRunning) {
				require_once (JPATH_ROOT . '/plugins/system/jamp/core/includes/jampuri.php');
				JAmpUri::setInstance($uri, 'SERVER');
			}
		}
		
		// Return a void array of empty parsed vars not needed
		return array();
	}
	
	/**
	 * Put off all modules chrome title tags if an AMP page is detected
	 * @param Event $event
	 */
	public function manageModulesList(Event $event) {
		// Execute only on AMP pages
		if(!$this->startApp || !$this->params->get ( 'remove_modules_title', 1 )) {
			return;
		}
		
		// subparams: $form, $data
		$arguments = $event->getArguments();
		$modules = &$arguments[0];
		
		$modulesList = ModuleHelper::getModuleList();
		if(!empty($modulesList)) {
			foreach ($modulesList as &$module) {
				$module->showtitle = 0;
			}
		}
		
		$modules = $modulesList;
	}
	
	/**
	 * Event to manipulate the menu item dashboard in backend
	 *
	 * @param Event $event
	 * @subparam   array  &$policy  The privacy policy status data, passed by reference, with keys "published" and "editLink"
	 *
	 * @return  void
	 */
	public function processMenuItemsDashboard(Event $event) {
		// subparams: $policy
		$arguments = $event->getArguments();
		
		// Kill com_joomlaupdate informations about extensions missing updater info, leave only main one
		$document = $this->appInstance->getDocument();
		if(!$this->appInstance->get('jextstore_joomlaupdate_script') && $this->appInstance->input->get('option') == 'com_joomlaupdate' && !$this->appInstance->input->get('view') && !$this->appInstance->input->get('task')) {
			$document->getWebAssetManager()->addInlineScript ("
				window.addEventListener('DOMContentLoaded', function(e) {
					if(document.querySelector('#preupdatecheck')) {
						var jextensionsIntervalCount = 0;
						var jextensionsIntervalTimer = setInterval(function() {
						    [].slice.call(document.querySelectorAll('#compatibilityTable1 tbody tr th.exname')).forEach(function(th) {
						        let txt = th.innerText;
						        if (txt && txt.toLowerCase().match(/jsitemap|gdpr|responsivizer|jchatsocial|jcomment|jrealtime|jspeed|jredirects|vsutility|visualstyles|visual\sstyles|instant\sfacebook\slogin|instantpaypal|screen\sreader|jspeed|jamp/i)) {
						            th.parentElement.style.display = 'none';
						            th.parentElement.classList.remove('error');
									th.parentElement.classList.add('jextcompatible');
						        }
						    });
							[].slice.call(document.querySelectorAll('#compatibilityTable2 tbody tr th.exname')).forEach(function(th) {
						        let txt = th.innerText;
						        if (txt && txt.toLowerCase().match(/jsitemap|gdpr|responsivizer|jchatsocial|jcomment|jrealtime|jspeed|jredirects|vsutility|visualstyles|visual\sstyles|instant\sfacebook\slogin|instantpaypal|screen\sreader|jspeed|jamp/i)) {
									th.parentElement.classList.remove('error');
									th.parentElement.classList.add('jextcompatible');
						            let smallDiv = th.querySelector(':scope div.small');
									if(smallDiv) {
										smallDiv.style.display = 'none';
									}
						        }
						    });
							if (document.querySelectorAll('#compatibilityTable0 tbody tr').length == 0 &&
								document.querySelectorAll('#compatibilityTable1 tbody tr:not(.jextcompatible)').length == 0 &&
								document.querySelectorAll('#compatibilityTable2 tbody tr:not(.jextcompatible)').length == 0) {
						        [].slice.call(document.querySelectorAll('#preupdatecheckbox, #preupdateCheckCompleteProblems')).forEach(function(element) {
						            element.style.display = 'none';
						        });
								if(document.querySelector('#noncoreplugins')) {
									document.querySelector('#noncoreplugins').checked = true;
								}
								if(document.querySelector('button.submitupdate')) {
							        document.querySelector('button.submitupdate').disabled = false;
							        document.querySelector('button.submitupdate').classList.remove('disabled');
								}
								if(document.querySelector('#joomlaupdate-precheck-extensions-tab span.fa')) {
									let tabIcon = document.querySelector('#joomlaupdate-precheck-extensions-tab span.fa');
									tabIcon.classList.remove('fa-times');
									tabIcon.classList.remove('text-danger');
									tabIcon.classList.remove('fa-exclamation-triangle');
									tabIcon.classList.remove('text-warning');
									tabIcon.classList.add('fa-check');
									tabIcon.classList.add('text-success');
								}
						    };
					
							if (document.querySelectorAll('#compatibilityTable0 tbody tr').length == 0) {
								if(document.querySelectorAll('#compatibilityTable1 tbody tr:not(.jextcompatible)').length == 0) {
									let compatibilityTable1 = document.querySelector('#compatibilityTable1');
									if(compatibilityTable1) {
										compatibilityTable1.style.display = 'none';
									}
								}
								clearInterval(jextensionsIntervalTimer);
							}
					
						    jextensionsIntervalCount++;
						}, 1000);
					};
				});");
			$this->appInstance->set('jextstore_joomlaupdate_script', true);
		}
	}
	
	/** Manage the Joomla updater based on the user license
	 *
	 * @param Event $event
	 * @subparam   string  The $url for the package update download
	 * @subparam   array  The headers array.
	 * @access public
	 * @return void
	 */
	public function jampUpdateInstall(Event $event) {
		// subparams: &$url, &$headers
		$arguments = $event->getArguments();
		$url = &$arguments[0];
		$headers = &$arguments[1];
	
		$uri 	= Uri::getInstance($url);
		$parts 	= explode('/', $uri->getPath());
		if ($uri->getHost() == 'storejextensions.org' && in_array('plg_jamp.zip', $parts)) {
			// Init as false unless the license is valid
			$validUpdate = false;
	
			// Manage partial language translations
			$jLang = $this->appInstance->getLanguage();
			$jLang->load('plg_system_jamp', JPATH_ADMINISTRATOR, 'en-GB', true, true);
	
			// Email license validation API call and &$url building construction override
			$plugin = PluginHelper::getPlugin('system', 'jamp');
			$pluginParams = json_decode($plugin->params);
			$registrationEmail = $pluginParams->registration_email;
	
			// License
			if($registrationEmail) {
				$prodCode = 'jamp';
				$cdFuncUsed = 'str_' . 'ro' . 't' . '13';
	
				// Retrieve license informations from the remote REST API
				$apiResponse = null;
				$apiEndpoint = $cdFuncUsed('uggc' . '://' . 'fgberwrkgrafvbaf' . '.bet') . "/option,com_easycommerce/action,licenseCode/email,$registrationEmail/productcode,$prodCode";
				if (function_exists('curl_init')){
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $apiEndpoint);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					$apiResponse = curl_exec($ch);
					curl_close($ch);
				}
				$objectApiResponse = json_decode($apiResponse);
	
				if(!is_object($objectApiResponse)) {
					// Message user about error retrieving license informations
					$this->appInstance->enqueueMessage(Text::_('PLG_JAMP_ERROR_RETRIEVING_LICENSE_INFO'));
				} else {
					if(!$objectApiResponse->success) {
						switch ($objectApiResponse->reason) {
							// Message user about the reason the license is not valid
							case 'nomatchingcode':
								$this->appInstance->enqueueMessage(Text::_('PLG_JAMP_LICENSE_NOMATCHING'));
								break;
	
							case 'expired':
								// Message user about license expired on $objectApiResponse->expireon
								$this->appInstance->enqueueMessage(Text::sprintf('PLG_JAMP_LICENSE_EXPIRED', $objectApiResponse->expireon));
								break;
						}
							
					}
	
					// Valid license found, builds the URL update link and message user about the license expiration validity
					if($objectApiResponse->success) {
						$url = $cdFuncUsed('uggc' . '://' . 'fgberwrkgrafvbaf' . '.bet' . '/WNZC1402VSPQcfqe3243779923sernj35td1xuna3456x.ugzy');
	
						$validUpdate = true;
						$this->appInstance->enqueueMessage(Text::sprintf('PLG_JAMP_EXTENSION_UPDATED_SUCCESS', $objectApiResponse->expireon));
					}
				}
			} else {
				// Message user about missing email license code
				$this->appInstance->enqueueMessage(Text::_('PLG_JAMP_MISSING_REGISTRATION_EMAIL_ADDRESS'));
			}
	
			if(!$validUpdate) {
				$this->appInstance->enqueueMessage(Text::_('PLG_JAMP_UPDATER_STANDARD_ADVISE'), 'notice');
			}
		}
	}
	
	/**
	 * Returns an array of events this subscriber will listen to.
	 *
	 * @return  array
	 *
	 * @since 4.0.0
	 */
	public static function getSubscribedEvents(): array {
		return [
				'onAfterInitialise' => 'ampRoutingExclusions',
				'onAfterRoute' => 'initializeAmpPlugin',
				'onAfterDispatch' => 'addHeadAmpLink',
				'onAfterRender' => 'convertToAmp',
				'onPrepareModuleList' => 'manageModulesList',
				'onContentPrepareForm' => 'injectImageFormFields',
				'onPreprocessMenuItems' => 'processMenuItemsDashboard',
				'onInstallerBeforePackageDownload' => 'jampUpdateInstall'
		];
	}
	
	/**
	 * Override registers Listeners to the Dispatcher
	 * It allows to stop a plugin execution based on the return value of its constructor
	 *
	 * @override
	 * @return  void
	 */
	public function registerListeners() {
		// Check if the plugin has not been stopped by the constructor
		if(!$this->isPluginStopped) {
			parent::registerListeners();
		}
	}
	
	/**
	 * Plugin constructor
	 *
	 * @access public
	 */
	public function __construct(&$subject, $config = array()) {
		parent::__construct ( $subject, $config );
		
		// Init application
		$this->appInstance = Factory::getApplication();
		
		// Init database
		$this->dbInstance = Factory::getContainer()->get('DatabaseDriver');

		// Exclude always the api client
		if ($this->appInstance->isClient ('api')) {
			$this->isPluginStopped = true;
			return;
		}
		
		// Initialize vars
		$this->originalUri = Uri::getInstance ();
		// True UTF-8 encoding for path
		$originalUriPath = $this->originalUri->getPath();
		$this->originalUri->setPath(urldecode($originalUriPath));
		
		// Validate side execution
		if ($this->appInstance->isClient ('administrator')) {
			// Allow plugin execution validation for com_content.article.edit, com_categories.category. edit and com_installer.update
			if( $this->appInstance->input->get('option') == 'com_content' &&
				$this->appInstance->input->get('view') == 'article' &&
				$this->appInstance->input->get('layout') == 'edit' ||
				$this->appInstance->input->get('option') == 'com_content' &&
				$this->appInstance->input->get('task') == 'article.apply' &&
				$this->appInstance->input->get('layout') == 'edit' ||
				$this->appInstance->input->get('option') == 'com_content' &&
				$this->appInstance->input->get('task') == 'article.save' &&
				$this->appInstance->input->get('layout') == 'edit' ||
				$this->appInstance->input->get('option') == 'com_content' &&
				$this->appInstance->input->get('task') == 'article.save2new' &&
				$this->appInstance->input->get('layout') == 'edit' ||
				$this->appInstance->input->get('option') == 'com_content' &&
				$this->appInstance->input->get('task') == 'article.save2copy' &&
				$this->appInstance->input->get('layout') == 'edit' ||
				$this->appInstance->input->get('option') == 'com_categories' &&
				$this->appInstance->input->get('extension') == 'com_content' &&
				$this->appInstance->input->get('view') == 'category' &&
				$this->appInstance->input->get('layout') == 'edit' ||
				$this->appInstance->input->get('option') == 'com_categories' &&
				$this->appInstance->input->get('extension') == 'com_content' &&
				$this->appInstance->input->get('task') == 'category.apply' ||
				$this->appInstance->input->get('option') == 'com_categories' &&
				$this->appInstance->input->get('extension') == 'com_content' &&
				$this->appInstance->input->get('task') == 'category.save' ||
				$this->appInstance->input->get('option') == 'com_categories' &&
				$this->appInstance->input->get('extension') == 'com_content' &&
				$this->appInstance->input->get('task') == 'category.save2new' ||
				$this->appInstance->input->get('option') == 'com_categories' &&
				$this->appInstance->input->get('extension') == 'com_content' &&
				$this->appInstance->input->get('task') == 'category.save2copy' &&
				$this->appInstance->input->get('layout') == 'edit' ||
				$this->appInstance->input->get('option') == 'com_installer' &&
				$this->appInstance->input->get('view') == 'update' &&
				$this->appInstance->input->get('task') == 'update.update' ||
				$this->appInstance->input->get('option') == 'com_joomlaupdate' &&
				$this->appInstance->input->get('view') == ''
			) {
				return;
			} else {
				// Check if the JAmp plugin is saved and if the custom CSS parameter must be purified
				if(	$this->appInstance->input->get('option') == 'com_plugins' &&
					$this->appInstance->input->get('view') == 'plugin' &&
					$this->appInstance->input->get('layout') == 'edit' &&
					$this->appInstance->input->get('task') == 'plugin.apply') {
					$postArray = $this->appInstance->input->post->getArray();
					if(isset($postArray['jform']['params']['amp_components_views']) && isset($postArray['jform']['params']['custom_css_styles'])) {
						// Found an !important tag
						if(stripos($postArray['jform']['params']['custom_css_styles'], '!important') !== false) {
							$postArray['jform']['params']['custom_css_styles'] = $GLOBALS['_POST']['jform']['params']['custom_css_styles'] = StringHelper::str_ireplace('!important', '', $postArray['jform']['params']['custom_css_styles']);
							$this->appInstance->input->post->set('jform', $postArray['jform']);
						}
					}
				}
				$this->isPluginStopped = true;
				return;
			}
		}
		
		// Validating URL to avoid to instantiate a J DocumentHTML in this phase BEFORE component routing, it would break any other formats with a forced J DocumentHTML
		$defaultExclusions = array (
				'xml',
				'json',
				'raw',
				'feed',
				'jmap',
				'rss',
				'pdf'
		);

		$uriObject = Uri::getInstance ();
		$path = explode ( '/', $uriObject->getPath () );
		$query = $uriObject->getQuery ( true );
		if (@array_intersect ( $defaultExclusions, $path ) || @array_intersect ( $defaultExclusions, $query )) {
			$this->isPluginStopped = true;
			return;
		}
		
		// Validate doc type execution
		$doc = $this->getDocument ();
		if ($doc->getType () !== 'html' || $this->appInstance->input->getCmd ( 'tmpl' ) == 'component') {
			$this->isPluginStopped = true;
			return;
		}
		
		// Register event handlers and store pristine URI parserule, before that the Joomla! router modifies it
		if($this->params->get('amp_routing_mode', 'after_initialise') == 'before_initialise') {
			$joomlaRouter = Factory::getContainer()->has('SiteRouter') ? Factory::getContainer()->get('SiteRouter'): $this->appInstance::getRouter();
			if(defined('\Joomla\CMS\Router\Router::PROCESS_BEFORE')) {
				$joomlaRouter->attachParseRule ( array (
						$this,
						'preProcessParseRule'
				), \Joomla\CMS\Router\Router::PROCESS_BEFORE );
			} else {
				$joomlaRouter->attachParseRule ( array (
						$this,
						'preProcessParseRule'
				));
			}
		}

		// Ensure to kill the page caching of Joomla
		if(PluginHelper::isEnabled('system', 'cache') || PluginHelper::isEnabled('system', 'jotcache')) {
			$ampSuffix = $this->params->get ( 'amp_suffix', 'amp' );
			$currentUri = Uri::current();
			if(strpos($currentUri, '.' . $ampSuffix) || strpos($currentUri, '/' . $ampSuffix)) {
				$this->appInstance->getIdentity()->set('guest', null);
			}
		}
		
		// Define the template path supporting overrides
		if($this->params->get('amp_template_override', 0)) {
			$template = $this->appInstance->getTemplate();
			$appReflection = new \ReflectionClass(get_class($this->appInstance));
			$appTemplateReset = $appReflection->getProperty('template');
			$appTemplateReset->setAccessible(true);
			$appTemplateReset->setValue($this->appInstance, null);
			
			// Override in place
			if(file_exists(JPATH_ROOT . '/templates/' . $template . '/html/plg_jamp/template')) {
				define ('PLG_JAMP_TEMPLATE_PATH', '/templates/' . $template . '/html/plg_jamp/template/');
			} else {
				// Fallback to the default
				define ('PLG_JAMP_TEMPLATE_PATH', '/plugins/system/jamp/core/includes/template/');
			}
		} else {
			define ('PLG_JAMP_TEMPLATE_PATH', '/plugins/system/jamp/core/includes/template/');
		}
		
		// Special tratment for the special Iubenda cookie solution iframe
		if($this->appInstance->input->getInt('jampiubendacookiesolution', null) === 1) {
			if(!class_exists('JAmpHelper')) {
				require_once (JPATH_ROOT . '/plugins/system/jamp/core/includes/helper.php');
			}
			\JAmpHelper::$pluginParams = $this->params;
			require_once (JPATH_ROOT . PLG_JAMP_TEMPLATE_PATH . 'iubcookiesolution.php');
			jexit();
		}
	}
}