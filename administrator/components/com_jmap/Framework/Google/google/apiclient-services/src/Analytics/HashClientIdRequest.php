<?php

namespace Google\Service\Analytics;

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
class HashClientIdRequest extends \Google\Model {
	public $clientId;
	public $kind;
	public $webPropertyId;
	public function setClientId($clientId) {
		$this->clientId = $clientId;
	}
	public function getClientId() {
		return $this->clientId;
	}
	public function setKind($kind) {
		$this->kind = $kind;
	}
	public function getKind() {
		return $this->kind;
	}
	public function setWebPropertyId($webPropertyId) {
		$this->webPropertyId = $webPropertyId;
	}
	public function getWebPropertyId() {
		return $this->webPropertyId;
	}
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias ( HashClientIdRequest::class, 'Google_Service_Analytics_HashClientIdRequest' );
