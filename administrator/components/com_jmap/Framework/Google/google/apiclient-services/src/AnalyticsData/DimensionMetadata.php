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
class DimensionMetadata extends \Google\Collection {
	protected $collection_key = 'deprecatedApiNames';
	public $apiName;
	public $customDefinition;
	public $deprecatedApiNames;
	public $description;
	public $uiName;
	public function setApiName($apiName) {
		$this->apiName = $apiName;
	}
	public function getApiName() {
		return $this->apiName;
	}
	public function setCustomDefinition($customDefinition) {
		$this->customDefinition = $customDefinition;
	}
	public function getCustomDefinition() {
		return $this->customDefinition;
	}
	public function setDeprecatedApiNames($deprecatedApiNames) {
		$this->deprecatedApiNames = $deprecatedApiNames;
	}
	public function getDeprecatedApiNames() {
		return $this->deprecatedApiNames;
	}
	public function setDescription($description) {
		$this->description = $description;
	}
	public function getDescription() {
		return $this->description;
	}
	public function setUiName($uiName) {
		$this->uiName = $uiName;
	}
	public function getUiName() {
		return $this->uiName;
	}
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias ( DimensionMetadata::class, 'Google_Service_AnalyticsData_DimensionMetadata' );
