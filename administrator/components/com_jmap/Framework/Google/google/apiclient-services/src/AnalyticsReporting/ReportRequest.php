<?php

namespace Google\Service\AnalyticsReporting;

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
class ReportRequest extends \Google\Collection {
	protected $collection_key = 'segments';
	protected $cohortGroupType = CohortGroup::class;
	protected $cohortGroupDataType = '';
	protected $dateRangesType = DateRange::class;
	protected $dateRangesDataType = 'array';
	protected $dimensionFilterClausesType = DimensionFilterClause::class;
	protected $dimensionFilterClausesDataType = 'array';
	protected $dimensionsType = Dimension::class;
	protected $dimensionsDataType = 'array';
	public $filtersExpression;
	public $hideTotals;
	public $hideValueRanges;
	public $includeEmptyRows;
	protected $metricFilterClausesType = MetricFilterClause::class;
	protected $metricFilterClausesDataType = 'array';
	protected $metricsType = Metric::class;
	protected $metricsDataType = 'array';
	protected $orderBysType = OrderBy::class;
	protected $orderBysDataType = 'array';
	public $pageSize;
	public $pageToken;
	protected $pivotsType = Pivot::class;
	protected $pivotsDataType = 'array';
	public $samplingLevel;
	protected $segmentsType = Segment::class;
	protected $segmentsDataType = 'array';
	public $viewId;

	/**
	 *
	 * @param
	 *        	CohortGroup
	 */
	public function setCohortGroup(CohortGroup $cohortGroup) {
		$this->cohortGroup = $cohortGroup;
	}
	/**
	 *
	 * @return CohortGroup
	 */
	public function getCohortGroup() {
		return $this->cohortGroup;
	}
	/**
	 *
	 * @param
	 *        	DateRange[]
	 */
	public function setDateRanges($dateRanges) {
		$this->dateRanges = $dateRanges;
	}
	/**
	 *
	 * @return DateRange[]
	 */
	public function getDateRanges() {
		return $this->dateRanges;
	}
	/**
	 *
	 * @param
	 *        	DimensionFilterClause[]
	 */
	public function setDimensionFilterClauses($dimensionFilterClauses) {
		$this->dimensionFilterClauses = $dimensionFilterClauses;
	}
	/**
	 *
	 * @return DimensionFilterClause[]
	 */
	public function getDimensionFilterClauses() {
		return $this->dimensionFilterClauses;
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
	public function setFiltersExpression($filtersExpression) {
		$this->filtersExpression = $filtersExpression;
	}
	public function getFiltersExpression() {
		return $this->filtersExpression;
	}
	public function setHideTotals($hideTotals) {
		$this->hideTotals = $hideTotals;
	}
	public function getHideTotals() {
		return $this->hideTotals;
	}
	public function setHideValueRanges($hideValueRanges) {
		$this->hideValueRanges = $hideValueRanges;
	}
	public function getHideValueRanges() {
		return $this->hideValueRanges;
	}
	public function setIncludeEmptyRows($includeEmptyRows) {
		$this->includeEmptyRows = $includeEmptyRows;
	}
	public function getIncludeEmptyRows() {
		return $this->includeEmptyRows;
	}
	/**
	 *
	 * @param
	 *        	MetricFilterClause[]
	 */
	public function setMetricFilterClauses($metricFilterClauses) {
		$this->metricFilterClauses = $metricFilterClauses;
	}
	/**
	 *
	 * @return MetricFilterClause[]
	 */
	public function getMetricFilterClauses() {
		return $this->metricFilterClauses;
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
	public function setPageSize($pageSize) {
		$this->pageSize = $pageSize;
	}
	public function getPageSize() {
		return $this->pageSize;
	}
	public function setPageToken($pageToken) {
		$this->pageToken = $pageToken;
	}
	public function getPageToken() {
		return $this->pageToken;
	}
	/**
	 *
	 * @param
	 *        	Pivot[]
	 */
	public function setPivots($pivots) {
		$this->pivots = $pivots;
	}
	/**
	 *
	 * @return Pivot[]
	 */
	public function getPivots() {
		return $this->pivots;
	}
	public function setSamplingLevel($samplingLevel) {
		$this->samplingLevel = $samplingLevel;
	}
	public function getSamplingLevel() {
		return $this->samplingLevel;
	}
	/**
	 *
	 * @param
	 *        	Segment[]
	 */
	public function setSegments($segments) {
		$this->segments = $segments;
	}
	/**
	 *
	 * @return Segment[]
	 */
	public function getSegments() {
		return $this->segments;
	}
	public function setViewId($viewId) {
		$this->viewId = $viewId;
	}
	public function getViewId() {
		return $this->viewId;
	}
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias ( ReportRequest::class, 'Google_Service_AnalyticsReporting_ReportRequest' );
