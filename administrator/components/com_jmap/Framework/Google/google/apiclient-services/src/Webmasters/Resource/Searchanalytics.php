<?php

namespace Google\Service\Webmasters\Resource;

/**
 *
 * @package JMAP::FRAMEWORK::administrator::components::com_jmap
 * @subpackage framework
 * @subpackage google
 * @author Joomla! Extensions Store
 * @copyright (C) 2021 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined ( '_JEXEC' ) or die ();

use Google\Service\Webmasters\SearchAnalyticsQueryRequest;
use Google\Service\Webmasters\SearchAnalyticsQueryResponse;

/**
 * The "searchanalytics" collection of methods.
 * Typical usage is:
 * <code>
 * $webmastersService = new Google\Service\Webmasters(...);
 * $searchanalytics = $webmastersService->searchanalytics;
 * </code>
 */
class Searchanalytics extends \Google\Service\Resource {
	/**
	 * Query your data with filters and parameters that you define.
	 * Returns zero or
	 * more rows grouped by the row keys that you define. You must define a date
	 * range of one or more days.
	 *
	 * When date is one of the group by values, any days without data are omitted
	 * from the result list. If you need to know which days have data, issue a broad
	 * date range query grouped by date for any metric, and see which day rows are
	 * returned. (searchanalytics.query)
	 *
	 * @param string $siteUrl
	 *        	The site's URL, including protocol. For example:
	 *        	http://www.example.com/
	 * @param SearchAnalyticsQueryRequest $postBody
	 * @param array $optParams
	 *        	Optional parameters.
	 * @return SearchAnalyticsQueryResponse
	 */
	public function query($siteUrl, SearchAnalyticsQueryRequest $postBody, $optParams = [ ]) {
		$params = [ 
				'siteUrl' => $siteUrl,
				'postBody' => $postBody
		];
		$params = array_merge ( $params, $optParams );
		return $this->call ( 'query', [ 
				$params
		], SearchAnalyticsQueryResponse::class );
	}
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias ( Searchanalytics::class, 'Google_Service_Webmasters_Resource_Searchanalytics' );
