<?php
namespace JExtstore\Component\JMap\Administrator\Model;
/**
 *
 * @package JMAP::AIGENERATOR::administrator::components::com_jmap
 * @subpackage models
 * @author Joomla! Extensions Store
 * @copyright (C) 2021 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\String\StringHelper;
use JExtstore\Component\JMap\Administrator\Framework\Model as JMapModel;
use JExtstore\Component\JMap\Administrator\Framework\Http;
use JExtstore\Component\JMap\Administrator\Framework\Http\Transport\Socket;
use JExtstore\Component\JMap\Administrator\Framework\Http\Transport\Curl;
use JExtstore\Component\JMap\Administrator\Framework\Http\Response;
use JExtstore\Component\JMap\Administrator\Framework\Exception as JMapException;
use JExtstore\Component\JMap\Administrator\Framework\Language\Multilang;
use JExtstore\Component\JMap\Administrator\Framework\Html\Languages;
use JExtstore\Component\JMap\Administrator\Framework\Helpers\Html as JMapHelpersHtml;
use JExtstore\Component\JMap\Administrator\Framework\AIGenerator\Readability;
use JExtstore\Component\JMap\Administrator\Framework\AIGenerator\HtmLawed;

/**
 * Google model responsibilities for access Google Analytics and Webmasters Tools API
 *
 * @package JMAP::AIGENERATOR::administrator::components::com_jmap
 * @subpackage models
 * @since 3.1
 */
interface IModelAIGenerator {
	/**
	 * Generate data method for the AI contents API
	 *
	 * @access public
	 * @param string $keywordPhrase
	 * @param string $selectedApi
	 * @return mixed string
	 */
	public function generateAIContentData($keywordPhrase, $selectedApi);
}

/**
 * Sources model concrete implementation <<testable_behavior>>
 *
 * @package JMAP::GOOGLE::administrator::components::com_jmap
 * @subpackage models
 * @since 3.1
 */
class AigeneratorModel extends JMapModel implements IModelAIGenerator {
	/**
	 * Google_Client object
	 *
	 * @access private
	 * @var Google_Client
	 */
	private $client;
	
	/**
	 * Build list entities query
	 *
	 * @access protected
	 * @return string
	 */
	protected function buildListQuery() {
		// WHERE
		$where = array ();
		$whereString = null;
		$orderString = null;
		
		// TEXT FILTER
		if ($this->state->get ( 'searchword' )) {
			$where [] = "(s.keywords_phrase LIKE " . $this->dbInstance->quote("%" . $this->state->get ( 'searchword' ) . "%") . ")";
		}
		
		// LANGUAGE FILTER
		$language = $this->state->get ( 'language' );
		if ($language && $language != '*' && $this->getLanguagePluginEnabled()) {
			$where [] = "(s.language = " . $this->dbInstance->quote($this->state->get ( 'language' )) . ")";
		}
		
		// API FILTER
		$contentsApi = $this->state->get ( 'contentsapi' );
		if ($contentsApi) {
			$where [] = "(s.api = " . $this->dbInstance->quote($this->state->get ( 'contentsapi' )) . ")";
		}
		
		if (count ( $where )) {
			$whereString = "\n WHERE " . implode ( "\n AND ", $where );
		}
		
		// ORDERBY
		if ($this->state->get ( 'order' )) {
			$orderString = "\n ORDER BY " . $this->state->get ( 'order' ) . " ";
		}
		
		// ORDERDIR
		if ($this->state->get ( 'order_dir' )) {
			$orderString .= $this->state->get ( 'order_dir' );
		}
		
		$query = "SELECT s.*, u.name AS editor" .
				"\n FROM #__jmap_aigenerator AS s" .
				"\n LEFT JOIN #__users AS u" .
				"\n ON s.checked_out = u.id" .
				$whereString . 
				$orderString;
				return $query;
	}
	
	/**
	 * Function to purify common contents pitfalls and contents
	 * 
	 * @acceess private
	 * @param string $contents
	 * @return string 
	 */
	private function purifyContents($contents) {
		// Decode all HTML entities to UTF-8
		$contents = html_entity_decode($contents, ENT_QUOTES, 'UTF-8');
		
		// Convert all UTF-8 unicode characters to symbols 
		$contents = preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/imu', function ($match) {
			return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE');
		}, $contents);
		
		// Remove carriage returns
		$contents = preg_replace( "/\r|\n|\\\\n|src=\"\"/imu", "", $contents );
		
		// Remove escaped slashes
		$contents = preg_replace('#\\\/#imu', '/',  $contents );
		
		// Rmove <noscript> tags
		$contents = preg_replace( "/<noscript>(.*)<\/noscript>/imuU", "", $contents );
		
		// Remove all links referencing original contents, keep inner text or remove unclosed tags
		if($this->componentParams->get('aigenerator_remove_links', 1)) {
			$contents = preg_replace('#(<a.*?>)(.*?)(</a>)#imu', '$2', $contents);
			$contents = preg_replace('#(<a.*?>)#imu', '', $contents);
			$contents = preg_replace('#(<button.*?>)(.*?)(</button>)#imu', '', $contents);
		}
		
		// Images should be removed?
		if($this->getState('removeimgs')) {
			$contents = preg_replace('#(<img.*?\/?>)#i', '', $contents);
		}
		
		// Replace headers with plain paragraphs
		$contents = preg_replace('/<h\d.*>/imuU', '<p>', $contents);
		$contents = preg_replace('/<\/h\d>/imu', '</p>', $contents);
		
		// Reduce extra spaces and normalize them
		$contents = preg_replace('/\s{2,}/imu', ' ', $contents);
		$contents = preg_replace('/\t{2,}/imu', ' ', $contents);
		
		// Remove onevents
		$contents = preg_replace( "/oncontextmenu=\"|onmousedown=\"|onload=\"|onclick=\"|onerror=\"|onmouseover=\"|onmouseout=\"/im", "data-onevent=\"", $contents );
		
		// Replace lazy loaded sources with standard ones
		if($this->componentParams->get('aigenerator_turn_datasrc', 1)) {
			$contents = preg_replace( '/src=\s*["\']([^"]*)(data:image)/imu', 'data-lazyoldsrc="$1$2', $contents );
			
			$contents = preg_replace( "/data-src/imu", "src", $contents );
			$contents = preg_replace( "/data-original/imu", "src", $contents );
			$contents = preg_replace( "/data-lazyload/imu", "src", $contents );
			$contents = preg_replace( "/data-dt-lazy-src/imu", "src", $contents );
			$contents = preg_replace( "/data-lazy-src/imu", "src", $contents );
			
			if($this->componentParams->get('aigenerator_remove_srcset', 1)) {
				$contents = preg_replace( '/srcset=\s*(["\'])/imu', 'data-noloadsrcset=$1', $contents );
			}
		}
		
		return $contents;
	}
	
	/**
	 * Get the state of the language filter plugin and Joomla multilanguage
	 *
	 * @access public
	 * @return bool
	 */
	public function getLanguagePluginEnabled() {
		$languageFilterPluginEnabled = false;
		// Check if multilanguage dropdown is always active
		if($this->getComponentParams()->get('showalways_language_dropdown', false)) {
			$languageFilterPluginEnabled = true;
		} else {
			// Detect Joomla Language Filter plugin enabled
			$query = "SELECT " . $this->dbInstance->quoteName('enabled') .
					 "\n FROM #__extensions" .
					 "\n WHERE " . $this->dbInstance->quoteName('element') . " = " . $this->dbInstance->quote('languagefilter') .
					 "\n OR " . $this->dbInstance->quoteName('element') . " = " . $this->dbInstance->quote('jfdatabase');
			$this->dbInstance->setQuery($query);
			$languageFilterPluginEnabled = $this->dbInstance->loadResult();
		}
		
		return $languageFilterPluginEnabled;
	}
	
	/**
	 * Return select lists used as filter for listEntities
	 *
	 * @access public
	 * @return array
	 */
	public function getFilters(): array {
		$filters = [];
		
		$languageOptions = Languages::getAvailableLanguageOptions(true);
		$filters ['languages'] = HTMLHelper::_ ( 'select.genericlist', $languageOptions, 'language', 'onchange="Joomla.submitform();" class="form-select"', 'value', 'text', $this->getState ( 'language' ) );
		
		// Selection of the AI API
		$chosenGeneratorAPI = array();
		$chosenGeneratorAPI[] = HTMLHelper::_('select.option', '', Text::_('COM_JMAP_AIGENERATOR_ALL_API'));
		$chosenGeneratorAPI[] = HTMLHelper::_('select.option', 'google', Text::_('COM_JMAP_AIGENERATOR_GOOGLE_API'));
		$chosenGeneratorAPI[] = HTMLHelper::_('select.option', 'bing', Text::_('COM_JMAP_AIGENERATOR_BING_API'));
		$filters ['contentsapi'] = HTMLHelper::_ ( 'select.genericlist', $chosenGeneratorAPI, 'contentsapi', 'onchange="Joomla.submitform();" class="form-select"', 'value', 'text', $this->getState ( 'contentsapi' ) );
		
		return $filters;
	}
	
	/**
	 * Return select lists used as filter for editEntity
	 *
	 * @access public
	 * @param Object $record
	 * @return array
	 */
	public function getLists($record = null): array {
		$lists = [];
		
		// Selection of the AI API
		$chosenGeneratorAPI = array();
		$chosenGeneratorAPI[] = HTMLHelper::_('select.option', 'google', Text::_('COM_JMAP_AIGENERATOR_GOOGLE_API'));
		$chosenGeneratorAPI[] = HTMLHelper::_('select.option', 'bing', Text::_('COM_JMAP_AIGENERATOR_BING_API'));
		$lists ['ai_generator_contents_api'] = JMapHelpersHtml::radiolist( $chosenGeneratorAPI, 'api', '', 'value', 'text', $record->api, 'api_');

			// Priority select list
		$options = array ();
		$options [] = HTMLHelper::_ ( 'select.option', null, Text::_ ( 'COM_JMAP_SELECT_MAX_AI_RESULTS' ) );
		$arrayMaxResults = array (
				'5' => Text::_('COM_JMAP_SELECT_MAX_AI_RESULTS_5_ITEMS'),
				'10' => Text::_('COM_JMAP_SELECT_MAX_AI_RESULTS_10_ITEMS'),
				'15' => Text::_('COM_JMAP_SELECT_MAX_AI_RESULTS_15_ITEMS'),
				'20' => Text::_('COM_JMAP_SELECT_MAX_AI_RESULTS_20_ITEMS'),
				'25' => Text::_('COM_JMAP_SELECT_MAX_AI_RESULTS_25_ITEMS'),
				'30' => Text::_('COM_JMAP_SELECT_MAX_AI_RESULTS_30_ITEMS'),
				'35' => Text::_('COM_JMAP_SELECT_MAX_AI_RESULTS_35_ITEMS'),
				'40' => Text::_('COM_JMAP_SELECT_MAX_AI_RESULTS_40_ITEMS'),
				'45' => Text::_('COM_JMAP_SELECT_MAX_AI_RESULTS_45_ITEMS'),
				'50' => Text::_('COM_JMAP_SELECT_MAX_AI_RESULTS_50_ITEMS'),
		);
		foreach ( $arrayMaxResults as $value => $text ) {
			$options [] = HTMLHelper::_ ( 'select.option', $value, $text );
		}
		$lists ['ai_generator_max_results'] = HTMLHelper::_ ( 'select.genericlist', $options, 'maxresults', 'class="form-select"', 'value', 'text', $record->maxresults );
		
		// Language dropdown
		$languageFilterPluginEnabled = $this->getLanguagePluginEnabled();
		$languageOptions = Languages::getAvailableLanguageOptions(false);
		if(count($languageOptions) >= 2 && $languageFilterPluginEnabled) {
			$lists['languages']	= HTMLHelper::_('select.genericlist',   $languageOptions, 'language', 'class="form-select"', 'value', 'text', $record->language, 'aigenerator_language' );
			// Append a flag button image if the language is a specific one
			if($record->language && $record->language != '*') {
				$lists['languages'] .= '<img id="language_flag_image" src="' . Uri::root(false) . 'media/mod_languages/images/' . StringHelper::str_ireplace('-', '_', $record->language) . '.gif" alt="language_flag" />';
			}
		}
		
		return $lists;
	}
	
	/**
	 * Main get data methods
	 *
	 * @access public
	 * @return Object[]
	 */
	public function getData(): array {
		// Build query
		$query = $this->buildListQuery ();
		try {
			$dbQuery = $this->dbInstance->getQuery ( true )->setQuery ( $query )->setLimit ( $this->getState ( 'limit' ), $this->getState ( 'limitstart' ) );
			$this->dbInstance->setQuery ( $dbQuery );
			$result = $this->dbInstance->loadObjectList ();
		} catch (JMapException $e) {
			$this->app->enqueueMessage($e->getMessage(), $e->getErrorLevel());
			$result = array();
		} catch (\Exception $e) {
			$jmapException = new JMapException($e->getMessage(), 'error');
			$this->app->enqueueMessage($jmapException->getMessage(), $jmapException->getErrorLevel());
			$result = array();
		}
		return $result;
	}
	
	/**
	 * Generate data method for the AI contents API
	 *
	 * @access public
	 * @param string $keywordPhrase
	 * @param string $selectedApi
	 * @return mixed Contents on success, false on failure exceptions
	 */
	public function generateAIContentData($keywordPhrase, $selectedApi) {
		$cParams = $this->getComponentParams ();
		
		// Hold the final concatenated contents to be returned and stored in the DB
		$serviceAIContents = '';
		
		// BING API endpoint
		if($selectedApi == 'bing') {
			$url = "https://www.bing.com/news/search?q=%s&setlang=%s&format=RSS";
		} else {
			// GOOGLE API endpoint
			$url = "https://news.google.com/rss/search?q=%s&hl=%s";
		}
		
		// Format the API endpoint based on user's settings
		$keywordPhraseEncoded = urlencode($keywordPhrase);
		$apiUrl = sprintf($url, $keywordPhraseEncoded, $this->getState('contentlanguage'));
		
		try {
			// Fetch remote data to scrape
			$httpTransport = $cParams->get ( 'aigenerator_service_http_transport', 'curl' ) == 'socket' ? 'file_get_contents' : new Curl (true);
			
			// CURL lib
			if (is_object ( $httpTransport )) {
				$connectionAdapter = new Http ( $httpTransport, $cParams );
				
				// Init headers
				$headers = array (
						'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
						'Accept-Encoding' => 'text/plain',
						'Accept-Language' => 'en,it;q=0.9,en-US;q=0.8,de;q=0.7,es;q=0.6,fr;q=0.5,ru;q=0.4,ja;q=0.3,el;q=0.2,sk;q=0.1,nl;q=0.1,ar;q=0.1,sv;q=0.1,da;q=0.1',
						'Cache-Control' => 'no-cache',
						'Connection' => 'keep-alive',
						'Pragma' => 'no-cache',
						'User-Agent' => 'Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/106.0.4664.93 Safari/537.36'
				);
				
				$httpResponse = $connectionAdapter->get ( $apiUrl, $headers );
			} else {
				// file_get_contents case 
				$opts = array (
						'http' => array (
								'method' => "GET",
								'header' => "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9\r\n" .
								"Accept-Encoding: text/plain\r\n" .
								"Accept-Language: en,it;q=0.9,en-US;q=0.8,de;q=0.7,es;q=0.6,fr;q=0.5,ru;q=0.4,ja;q=0.3,el;q=0.2,sk;q=0.1,nl;q=0.1,ar;q=0.1,sv;q=0.1,da;q=0.1\r\n" .
								"Cache-Control: no-cache\r\n" .
								"Connection: keep-alive\r\n" .
								"Pragma: no-cache\r\n" .
								"User-Agent: Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/106.0.4664.93 Safari/537.36",
								'timeout' => 5
						)
				);
				$context = stream_context_create ( $opts );
				$response = file_get_contents ( $apiUrl, false, $context );
				
				if ($response) {
					$httpResponse = new Response ();
					$httpResponse->code = 200;
					$httpResponse->body = $response;
				} else {
					throw new \RuntimeException ( Text::sprintf ( 'COM_JMAP_AIGENERATOR_ERROR_GENERATING_CONTENT', 409 ) );
				}
			}
			
			// Check if HTTP status code is 200 OK
			if ($httpResponse->code != 200 || !$httpResponse->body) {
				throw new \RuntimeException ( Text::sprintf( 'COM_JMAP_AIGENERATOR_ERROR_GENERATING_CONTENT', $httpResponse->code) );
			}
			
			// Process result as XML document. Extract all single feed URLs for further AI scraping analyzing
			libxml_use_internal_errors(true);
			
			$objXmlDocument = simplexml_load_string($httpResponse->body);
			
			if ($objXmlDocument === false) {
				$errors = [];
				foreach(libxml_get_errors() as $error) {
					$errors[] = $error->message;
				}
				throw new \RuntimeException ( Text::sprintf( 'COM_JMAP_AIGENERATOR_ERROR_XML', implode('. ', $errors)) );
			}
			
			$objJsonDocument = json_encode($objXmlDocument);
			$feedArrayStructure = json_decode($objJsonDocument, true);
			
			if(!is_array($feedArrayStructure)) {
				throw new \RuntimeException ( Text::sprintf( 'COM_JMAP_AIGENERATOR_ERROR_DATA' ));
			}
			
			if(!isset($feedArrayStructure['channel']['item'])) {
				throw new \RuntimeException ( Text::sprintf( 'COM_JMAP_AIGENERATOR_ERROR_DATA_ITEM' ));
			}
			
			if(count($feedArrayStructure['channel']['item']) == 0) {
				throw new \RuntimeException ( Text::sprintf( 'COM_JMAP_AIGENERATOR_ERROR_DATA_EMPTY_ITEM' ));
			}
			
			// Start crawling and elaborating remote resources
			$counter = 0;
			$limitSnippets = $this->getState('maxresults');
			foreach ($feedArrayStructure['channel']['item'] as $itemLinkToAnalyze) {
				// Hold the response for a given page
				$serviceAIResponse = '';
				$queryVars = [];
				
				// CURL lib
				if (is_object ( $httpTransport )) {
					// Parse the full URL
					
					if($selectedApi == 'bing') {
						$parsedUrl = parse_url($itemLinkToAnalyze['link']);
						parse_str($parsedUrl['query'], $queryVars);
					} else {
						$parsedUrl = parse_url($itemLinkToAnalyze['link']);
						$parts = explode('/', $parsedUrl['path']);
						$bas64FunctionNameDecode = 'base'. 64 . '_decode';
						$decodedUrl = $bas64FunctionNameDecode(array_pop($parts));
						preg_match('/(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i', $decodedUrl, $matches);
						if($matches[0]) {
							$queryVars['url'] = $matches[0];
						} else {
							$queryVars['url'] = $itemLinkToAnalyze['link'];
						}
					}
					
					$httpResponse = $connectionAdapter->get ( $queryVars['url'] );
					
					// Check if HTTP status code is 200 OK
					if ($httpResponse->code != 200 || !$httpResponse->body) {
						continue;
					}
					
					// Set the response contents
					$serviceAIResponse = $httpResponse->body;
					
					// Put the case to lower
					$httpResponse->headers = array_change_key_case($httpResponse->headers, CASE_LOWER);
					
					// Detect if contents are utf8 encoded otherwise convert them
					if(isset($httpResponse->headers['content-type']) && !preg_match('/utf-8/i', $httpResponse->headers['content-type'])) {
						/** 
						 * Note: PHP Readability expects UTF-8 encoded content.
						 * If your content is not UTF-8 encoded, convert it
						 * first before passing it to PHP Readability.
						 */
						if(preg_match('/ISO-8859-1/i', $httpResponse->headers['content-type'])) {
							$fromEncoding = 'ISO-8859-1';
						} else {
							$fromEncoding = mb_detect_encoding($serviceAIResponse);
						}
						if($fromEncoding) {
							$serviceAIResponse = mb_convert_encoding($serviceAIResponse, 'UTF-8', $fromEncoding);
						} else {
							continue;
						}
					}
				} else {
					$queryVars['url'] = $itemLinkToAnalyze['link'];
					$serviceAIResponse = file_get_contents ( $itemLinkToAnalyze['link'], false, $context );
					if (!$serviceAIResponse) {
						continue;
					}
				}
				
				/** 
				 * If we've got Tidy, let's clean up input.
				 * This step is highly recommended - PHP's default HTML parser
				 * often doesn't do a great job and results in strange output. 
				 */
				if (function_exists('tidy_parse_string')) {
					$tidy = tidy_parse_string($serviceAIResponse, array(), 'UTF8');
					$tidy->cleanRepair();
					$serviceAIResponse = $tidy->value;
				}
				
				$readability = new Readability($serviceAIResponse, $queryVars['url']);
				$result = $readability->init();
				$readability->removeScripts($readability->getContent());
				if ($result) {
					$readabilityTitle = StringHelper::ucfirst($readability->getTitle()->textContent);
					$readabilityContents = $readability->getContent()->innerHTML;
					
					// If we've got Tidy, let's clean it up for output
					if (function_exists('tidy_parse_string')) {
						$tidy = tidy_parse_string($readabilityContents, array('indent'=>true, 'show-body-only' => true), 'UTF8');
						$tidy->cleanRepair();
						$serviceAIContents .= '{title}' . $readabilityTitle . '{/title}{content}' . $tidy->value . '{/content}{contentdivider}';
					} else {
						$readabilityContents = HtmLawed::executeHtmlLawed($readabilityContents);
						$serviceAIContents .= '{title}' . $readabilityTitle . '{/title}{content}' . $readabilityContents . '{/content}{contentdivider}';
					}
				}
				
				// Respect pagination max snippets limit
				if($counter == ($limitSnippets - 1)) {
					break;
				}
				$counter++;
			}
		} catch ( \RuntimeException $e ) {
			$jmapException = new JMapException ( $e->getMessage (), 'error' );
			$this->setError ( $jmapException );
			return false;
		} catch ( \Exception $e ) {
			$jmapException = new JMapException ( $e->getMessage (), 'error' );
			$this->setError ( $jmapException );
			return false;
		}
		
		// Clean the content from common pitfalls 
		$serviceAIContents = $this->purifyContents($serviceAIContents);
		
		return $serviceAIContents;
	}
}