<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2022 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

class OSMembershipViewSubscriptionsHtml extends MPFViewHtml
{
	/**
	 * Subscriptions history data
	 *
	 * @var array
	 */
	protected $items;

	/**
	 * The pagination object
	 *
	 * @var \Joomla\CMS\Pagination\Pagination
	 */
	protected $pagination;

	/**
	 * Component config
	 *
	 * @var MPFConfig
	 */
	protected $config;

	/**
	 * Active menu item parameters
	 *
	 * @var \Joomla\Registry\Registry
	 */
	protected $params;

	/**
	 * Bootstrap Helper
	 *
	 * @var OSMembershipHelperBootstrap
	 */
	protected $bootstrapHelper;

	/**
	 * Display the view
	 *
	 * @throws Exception
	 */
	public function display()
	{
		$this->requestLogin();

		/* @var OSMembershipModelSubscriptions $model */
		$model                 = $this->getModel();
		$this->items           = $model->getData();
		$this->config          = OSMembershipHelper::getConfig();
		$this->pagination      = $model->getPagination();
		$this->params          = $this->getParams();
		$this->bootstrapHelper = OSMembershipHelperBootstrap::getInstance();

		parent::display();
	}
}
