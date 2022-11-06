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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Registry\Registry;

class OSMembershipViewSchedulecontentHtml extends MPFViewHtml
{
	/**
	 * Schedule articles
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
	 * Contains select lists use on the view
	 *
	 * @var array
	 */
	protected $lists = [];

	/**
	 * The current user's subscriptions
	 *
	 * @var array
	 */
	protected $subscriptions;

	/**
	 * Component config
	 *
	 * @var MPFConfig
	 */
	protected $config;

	/**
	 * Number days articles will be released for access
	 *
	 * @var int
	 */
	protected $releaseArticleOlderThanXDays;

	/**
	 * How article link to article will be opened
	 *
	 * @var int
	 */
	protected $openArticle;

	/**
	 * Active menu item parameters
	 *
	 * @var \Joomla\Registry\Registry
	 */
	protected $params;

	/**
	 * Display the view
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	public function display()
	{
		if (!PluginHelper::isEnabled('system', 'schedulecontent'))
		{
			Factory::getApplication()->enqueueMessage(Text::_('Schedule Content feature is not enabled. Please contact super administrator'));

			return;
		}

		$this->requestLogin();

		$plugin = PluginHelper::getPlugin('system', 'schedulecontent');

		$params = new Registry($plugin->params);

		$activePlanIds = array_unique(array_keys(OSMembershipHelperSubscription::getUserSubscriptionsInfo()));

		/* @var $model OSMembershipModelSchedulecontent */
		$model = $this->getModel();

		if (count($activePlanIds) > 1)
		{
			$db    = $model->getDbo();
			$query = $db->getQuery(true)
				->select('DISTINCT plan_id')
				->from('#__osmembership_schedulecontent')
				->where('plan_id IN (' . implode(',', $activePlanIds) . ')');
			$db->setQuery($query);
			$planIds = $db->loadColumn();

			if (count($planIds) > 1)
			{
				$query->clear()
					->select('id, title')
					->from('#__osmembership_plans')
					->where(' id IN (' . implode(',', $planIds) . ')')
					->order('ordering');
				$db->setQuery($query);

				$options   = [];
				$options[] = HTMLHelper::_('select.option', 0, Text::_('OSM_ALL_PLANS'), 'id', 'title');
				$options   = array_merge($options, $db->loadObjectList());

				$this->lists['id'] = HTMLHelper::_('select.genericlist', $options, 'id', 'class="form-select" onchange="submit();"', 'id', 'title', $model->getState()->id);
			}
		}

		$this->items                        = $model->getData();
		$this->config                       = OSMembershipHelper::getConfig();
		$this->pagination                   = $model->getPagination();
		$this->subscriptions                = OSMembershipHelperSubscription::getUserSubscriptionsInfo();
		$this->releaseArticleOlderThanXDays = (int) $params->get('release_article_older_than_x_days', 0);
		$this->openArticle                  = $params->get('open_article');
		$this->params                       = $this->getParams();

		parent::display();
	}
}
