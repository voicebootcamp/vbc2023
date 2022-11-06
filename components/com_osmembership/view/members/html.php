<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2022 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

/**
 * Class OSMembershipViewMembersHtml
 *
 * @property OSMembershipHelperBootstrap $bootstrapHelper
 * @property JRegistry                   $params
 */
class OSMembershipViewMembersHtml extends MPFViewHtml
{
	/**
	 * Members data
	 *
	 * @var array
	 */
	protected $items;

	/**
	 * Model state
	 *
	 * @var MPFModelState
	 */
	protected $state;

	/**
	 * Pagination object
	 *
	 * @var \Joomla\CMS\Pagination\Pagination
	 */
	protected $pagination;

	/**
	 * Custom Fields which will be shown on Members page
	 *
	 * @var array
	 */
	protected $fields;

	/**
	 * Custom Fields Data
	 *
	 * @var array
	 */
	protected $fieldsData;

	/**
	 * Active menu item parameters
	 *
	 * @var \Joomla\Registry\Registry
	 */
	protected $params;

	/**
	 * Component config
	 *
	 * @var MPFConfig
	 */
	protected $config;

	/**
	 * Bootstrap Helper
	 *
	 * @var OSMembershipHelperBootstrap
	 */
	protected $bootstrapHelper;

	/**
	 * Display members list
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	public function display()
	{
		$user = Factory::getUser();

		if (!$user->authorise('core.viewmembers', 'com_osmembership'))
		{
			if (!$user->id)
			{
				$this->requestLogin();
			}
			else
			{
				$app = Factory::getApplication();
				$app->enqueueMessage(Text::_('OSM_NOT_ALLOW_TO_VIEW_MEMBERS'));
				$app->redirect(Uri::root(), 403);
			}
		}

		/* @var OSMembershipModelMembers $model */
		$model  = $this->getModel();
		$state  = $model->getState();
		$fields = OSMembershipHelper::getProfileFields($state->id, true);

		for ($i = 0, $n = count($fields); $i < $n; $i++)
		{
			if (!$fields[$i]->show_on_members_list)
			{
				unset($fields[$i]);
			}
		}

		$fields = array_values($fields);

		$this->fields          = $fields;
		$this->state           = $state;
		$this->items           = $model->getData();
		$this->pagination      = $model->getPagination();
		$this->fieldsData      = $model->getFieldsData();
		$this->config          = OSMembershipHelper::getConfig();
		$this->params          = $this->getParams();
		$this->bootstrapHelper = OSMembershipHelperBootstrap::getInstance();

		parent::display();
	}
}
