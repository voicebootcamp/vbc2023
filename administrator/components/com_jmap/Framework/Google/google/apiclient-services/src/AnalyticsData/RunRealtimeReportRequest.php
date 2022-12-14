<?php

namespace Google\Service\AnalyticsData;

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
class RunRealtimeReportRequest extends \Google\Collection {
	protected $collection_key = 'orderBys';
	protected $dimensionFilterType = FilterExpression::class;
	protected $dimensionFilterDataType = '';
	protected $dimensionsType = Dimension::class;
	protected $dimensionsDataType = 'array';
	public $limit;
	public $metricAggregations;
	protected $metricFilterType = FilterExpression::class;
	protected $metricFilterDataType = '';
	protected $metricsType = Metric::class;
	protected $metricsDataType = 'array';
	protected $orderBysType = OrderBy::class;
	protected $orderBysDataType = 'array';
	public $returnPropertyQuota;

	/**
	 *
	 * @param
	 *        	FilterExpression
	 */
	public function setDimensionFilter(FilterExpression $dimensionFilter) {
		$this->dimensionFilter = $dimensionFilter;
	}
	/**
	 *
	 * @return FilterExpression
	 */
	public function getDimensionFilter() {
		return $this->dimensionFilter;
	}
	/**
	 *
	 * @param
	 *        	Dimension[]
	 */
	public function setDimensions($dimensions) {
		$this->dimensions = $dimensions;
	}
	/**
	 *
	 * @return Dimension[]
	 */
	public function getDimensions() {
		return $this->dimensions;
	}
	public function setLimit($limit) {
		$this->limit = $limit;
	}
	public function getLimit() {
		return $this->limit;
	}
	public function setMetricAggregations($metricAggregations) {
		$this->metricAggregations = $metricAggregations;
	}
	public function getMetricAggregations() {
		return $this->metricAggregations;
	}
	/**
	 *
	 * @param
	 *        	FilterExpression
	 */
	public function setMetricFilter(FilterExpression $metricFilter) {
		$this->metricFilter = $metricFilter;
	}
	/**
	 *
	 * @return FilterExpression
	 */
	public function getMetricFilter() {
		return $this->metricFilter;
	}
	/**
	 *
	 * @param
	 *        	Metric[]
	 */
	public function setMetrics($metrics) {
		$this->metrics = $metrics;
	}
	/**
	 *
	 * @return Metric[]
	 */
	public function getMetrics() {
		return $this->metrics;
	}
	/**
	 *
	 * @param
	 *        	OrderBy[]
	 */
	public function setOrderBys($orderBys) {
		$this->orderBys = $orderBys;
	}
	/**
	 *
	 * @return OrderBy[]
	 */
	public function getOrderBys() {
		return $this->orderBys;
	}
	public function setReturnPropertyQuota($returnPropertyQuota) {
		$this->returnPropertyQuota = $returnPropertyQuota;
	}
	public function getReturnPropertyQuota() {
		return $this->returnPropertyQuota;
	}
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias ( RunRealtimeReportRequest::class, 'Google_Service_AnalyticsData_RunRealtimeReportRequest' );
