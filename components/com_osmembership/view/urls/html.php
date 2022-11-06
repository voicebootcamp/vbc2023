<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2022 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

class OSMembershipViewUrlsHtml extends MPFViewList
{
	/**
	 * Active menu item parameters
	 *
	 * @var \Joomla\Registry\Registry
	 */
	protected $params;

	/**
	 * Prepare view data before displaying
	 *
	 * @throws Exception
	 */
	public function prepareView()
	{
		$this->requestLogin();

		parent::prepareView();

		$this->params = $this->getParams();
	}
}
