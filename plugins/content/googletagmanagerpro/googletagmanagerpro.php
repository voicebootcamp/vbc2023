<?php
/**
 *
 * @version		0.1.3
 * @package		Google TagManager Pro
 * @subpackage  Content.GoogleTagManagerPro
 * @copyright	2021 Tools for Joomla, www.toolsforjoomla.com
 * @license		GNU GPL
 * fixed location of GTM code.
 * Added option to place dataLayer declaration at the top of the <head> area
 * Fix so that dataLayer is not added to <header> tags
 * Add scrolling updates
 * 0.0.11 Update to handle multiple <body> and multiple <head> tags
 * 0.0.12 Update to load javascript in the <head> tag and iframe in the <body> section.
 * 1.0.0 Added the update server
 * 1.0.1 Corrected the JavaScript reference location
 * PRO
 * 0.0.2 New Pro Version - includes:
 * advanced user information - no password or username
 * 'Hashed' email for uses as User ID in Google Analytics
 * Article Information for com_content item
 *  - use com_content article model to retreive item information
 *  - use com_k2 model to retreive item information
 * 0.1.0 Joomla 4 version of Pro
 *  - change to Joomla namespace
 *  - no K2 support - waiting for a Joomla 4 version of K2
 * 0.1.1 Changed to a content plugin
 * 0.1.2 Update to accept a params parameter that is an 
 * object or array
 * 0.1.3 Change param to use the 'def' method to avoid null param issues. 
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Event\Event;
use Joomla\Event\SubscriberInterface;

class plgContentGoogleTagManagerPro extends CMSPlugin {
	/**
	 * Load the language file on instantiation
	 *
	 * @var    boolean
	 * @since  3.1
	 */
	protected $autoloadLanguage = true;
	/**
	 * Application object.
	 *
	 * @var    \Joomla\CMS\Application\CMSApplication
	 * @since  3.9.0
	 */
	protected $app;

	/**
	 * Returns an array of events this subscriber will listen to.
	 *
	 * @return  array
	 */
/*	Not subscribing to events at this point will add in future versions
	public static function getSubscribedEvents(): array
	{
		return [
			'onContentPrepare' => 'prepareDataLayer',
			'onAfterRender' => 'addGtmCode'
		];
	}
*/
	public function getUserInfoDataLayer($user) {
		// Function to return user information in json format to 
		// push to the dataLayer
		// all secure information will be removed first
		$userCopy = clone $user;
		unset($userCopy->password);
		unset($userCopy->username);
		// Retrieve the two salts and add to the Hashed Email
		$salt1 = $this->params->get('track_userSalt1','');
		$salt2 = $this->params->get('track_userSalt2','');
		$userCopy->hashedEmail = hash("sha256",$salt1.$userCopy->email.$salt2);
		return $userCopy;
	}
	public function getMenuInfoDataLayer() {
		$menuInfo = $this->app->getMenu()->getActive();
		return $menuInfo;
	}
	public function onContentPrepare($context, &$row, &$params, $page = 0)
	{
	// Function to output the content information
	//  Supports:
	//   - Articles (com_content)
	//   - Item (com_k2)
	// remove jrequest and change to $input->method->get

	$rowDatalayer = new stdClass();
	$updateDataLayer = false;
	// Build Article Information - com_content
	// update to work with params as an object or an array
		$mode = (int) $this->params->def('mode', 1);

	if (($context == 'com_content.article') || 
			($context = 'com_content.featured') && (null !== $this->params->def('article_layout', null))) {
		$rowDatalayer->title = $row->title;
		$rowDatalayer->alias = $row->alias;
		$rowDatalayer->id = $row->id;
		$rowDatalayer->category = $row->category_title;
		$rowDatalayer->author = $row->author;
		$rowDatalayer->created = $row->created;
		$rowDatalayer->modified = $row->modified;
		$rowDatalayer->hits = $row->hits;
		$rowDatalayer->version = $row->version;
		$updateDataLayer = true;
	} elseif (($context == 'com_k2.item') && ($view == 'item')) {
		$rowDatalayer->title = $row->title;
		$rowDatalayer->alias = $row->alias;
		$rowDatalayer->id = $row->id;
		$rowDatalayer->category = $row->category->name;
		$rowDatalayer->author = $row->author->name;
		$rowDatalayer->created = $row->created;
		$rowDatalayer->modified = $row->modified;
		$rowDatalayer->hits = $row->hits;
		$rowDatalayer->version = '(not set)';
		$updateDataLayer = true;
	} elseif (true) {
/*		$rowDatalayer->context = $context;
		$rowDatalayer->view = $view;
		$rowDatalayer->params = $params;
		if (isset($params['article_layout'])) {
			$rowDatalayer->layout = $params['article_layout'];
		}
		$rowDatalayer->id = "TEST";
		$rowDatalayer->title = $row->title;
		$rowDatalayer->alias = $row->alias;
		$rowDatalayer->id = $row->id;
		$rowDatalayer->category = $row->category_title;
		$rowDatalayer->author = $row->author;
		$rowDatalayer->created = $row->created;
		$rowDatalayer->modified = $row->modified;
		$rowDatalayer->hits = $row->hits;
		$rowDatalayer->version = $row->version;
*/
	}
	if ($updateDataLayer) {
		$dataLayerName = $this->params->get('datalayer_name','dataLayer');
		$dataLayerCode = $dataLayerName.".push({'event': 'content_update', 'content_info' : ".json_encode($rowDatalayer)."});";

		$document = JFactory::getDocument();
		$document->addScriptDeclaration( $dataLayerCode );
	}
}

	function onAfterRender() {

		// don't run if we are in the index.php or we are not in an HTML view
		// Check to see if we are in the admin and if we should track
		$trackadmin = $this->params->get('trackadmin','');
		if($this->app->isClient('administrator') && ($trackadmin != 'on')) {
			return;
			}
		
		// Get the Body of the HTML
		$buffer = $this->app->getBody();
		// Get our Container ID
		$container_id = $this->params->get('container_id','');
		$addDataLayer = $this->params->get('add_datalayer','');
		$dataLayerName = $this->params->get('datalayer_name','dataLayer');
		$addTrackLogin = $this->params->get('track_userLogin','');

		// String containing the Google Tag Manager JavaScript code including the container id 
		$gtm_js_container_code = "\n<!-- Google Tag Manager Pro JS V.0.1.3 from Tools for Joomla -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','".$dataLayerName."','".$container_id."');</script>
<!-- End Google Tag Manager JS -->";

		$dataLayerCode = '';
		if ($addDataLayer == 'on') {
			$dataLayerCode = 'window.'.$dataLayerName.' = window.'.$dataLayerName.' || [];';
			// Tracked Logged in User here
			$user = JFactory::getUser();
			if ($addTrackLogin == 'on' && !$user->guest) {
				$userDataLayerInfo = $this->getUserInfoDataLayer($user);
				$dataLayerCode .= $dataLayerName.".push({'event': 'user_loggedin', 'user_info' : ".json_encode($userDataLayerInfo)."});";
			}
			// output Menu Info here
			if (true) {
				$menuDataLayerInfo = $this->getMenuInfoDataLayer();
				$dataLayerCode .= $dataLayerName.".push({'event': 'menu_info_event', 'menu_info' : ".json_encode($menuDataLayerInfo)."});";
			}

			// Match on head tag and new expression to NOT match on header tag
			$buffer = preg_replace ("/(<head(?!er).*>)/i", "$1"."\n<script>".$dataLayerCode."</script>".$gtm_js_container_code, $buffer, 1);
		}
		else {
			$buffer = preg_replace ("/(<head(?!er).*>)/i", "$1".$gtm_js_container_code, $buffer, 1);
		}
		// String containing the iframe code to be placed after the <body> tag
		$gtm_iframe_container_code = "\n<!-- Google Tag Manager Pro iframe V.0.1.3 from Tools for Joomla -->
<noscript><iframe src='//www.googletagmanager.com/ns.html?id=".$container_id."'
height='0' width='0' style='display:none;visibility:hidden'></iframe></noscript>
<!-- End Google Tag Manager iframe -->";
		
		// update to limit = 1 to add tag to only the first <body.*> tag
		$buffer = preg_replace ("/(<body.*?>)/is", "$1".$gtm_iframe_container_code, $buffer, 1);
		
		$this->app->setBody($buffer);
		
		return true;
		}
	}
?>