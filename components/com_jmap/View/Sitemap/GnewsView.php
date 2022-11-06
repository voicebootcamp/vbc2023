<?php
namespace JExtstore\Component\JMap\Site\View\Sitemap;
/**
 * @package JMAP::SITEMAP::components::com_jmap
 * @subpackage views
 * @subpackage sitemap
 * @author Joomla! Extensions Store
 * @copyright (C) 2021 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use JExtstore\Component\JMap\Administrator\Framework\View as JMapView;

/**
 * Main view class
 *
 * @package JMAP::SITEMAP::components::com_jmap
 * @subpackage views
 * @subpackage sitemap
 * @since 1.0
 */
class GnewsView extends JMapView {
	// Template view variables
	protected $cparams;
	protected $data;
	protected $application;
	protected $liveSite;
	protected $source;
	protected $sourceparams;
	protected $asCategoryTitleField;
	protected $xslt;
	protected $outputtedLinksBuffer;
	protected $sysLang;
	protected $globalConfig;
	
	/**
	 * Display the XML sitemap
	 * @access public
	 * @return void
	 */
	function display($tpl = null) {
		$document = $this->document;
		$document->setMimeEncoding('application/xml');
		
		// Call by cache handler get no params, so recover from model state
		if(!$tpl) {
			$tpl = $this->getModel ()->getState ( 'documentformat' );
		}

		$language = $this->app->getLanguage();
		$langTag = $language->getTag();
		$langCode = @array_shift(explode('-', $langTag));
		
		$this->sysLang = $langCode;
		$this->globalConfig = $this->app->getConfig();
		$this->data = $this->get ( 'SitemapData' );
		$this->cparams = $this->getModel ()->getState ( 'cparams' );
		$this->application = Factory::getApplication();
		$this->xslt = $this->getModel()->getState('xslt');
		
		$uriInstance = Uri::getInstance();
		if($this->cparams->get('append_livesite', true)) {
			$customHttpPort = trim($this->cparams->get('custom_http_port', ''));
			$getPort = $customHttpPort ? ':' . $customHttpPort : '';
			
			$customDomain = trim($this->cparams->get('custom_sitemap_domain', ''));
			$getDomain = $customDomain ? rtrim($customDomain, '/') : rtrim($uriInstance->getScheme() . '://' . $uriInstance->getHost(), '/');

			$this->liveSite = rtrim($getDomain . $getPort, '/');
		} else {
			$this->liveSite = null;
		}
		
		// Initialize output links buffer with exclusion for links
		$this->outputtedLinksBuffer = $this->getModel()->getExcludedLinks($this->liveSite);
		
		$this->setLayout('default');
		parent::display ( $tpl );
	}
}