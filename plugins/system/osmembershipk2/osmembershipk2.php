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
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;

class plgSystemOSMembershipk2 extends CMSPlugin
{
	/**
	 * Application object.
	 *
	 * @var    JApplicationCms
	 */
	protected $app;
	/**
	 * Database object
	 *
	 * @var JDatabaseDriver
	 */
	protected $db;

	/**
	 * Make language files will be loaded automatically.
	 *
	 * @var bool
	 */
	protected $autoloadLanguage = true;

	/**
	 * Constructor
	 *
	 * @param   object &$subject  The object to observe
	 * @param   array   $config   An optional associative array of configuration settings.
	 */
	public function __construct($subject, array $config = [])
	{
		$canRun = file_exists(JPATH_ADMINISTRATOR . '/components/com_osmembership/osmembership.php')
			&& file_exists(JPATH_ROOT . '/components/com_k2/k2.php');

		if (!$canRun)
		{
			return;
		}

		parent::__construct($subject, $config);

		require_once JPATH_ADMINISTRATOR . '/components/com_osmembership/loader.php';
	}

	/**
	 * Render settings form
	 *
	 * @param   OSMembershipTablePlan  $row
	 *
	 * @return array
	 */
	public function onEditSubscriptionPlan($row)
	{
		if (!$this->isExecutable())
		{
			return [];
		}

		ob_start();
		$this->drawSettingForm($row);
		$form = ob_get_clean();

		return ['title' => Text::_('PLG_OSMEMBERSHIP_K2_ITEMS_RESTRICTION_SETTINGS'),
		        'form'  => $form,
		];
	}

	/**
	 * Store setting into database
	 *
	 * @param   PlanOsMembership  $row
	 * @param   Boolean           $isNew  true if create new plan, false if edit
	 */
	public function onAfterSaveSubscriptionPlan($context, $row, $data, $isNew)
	{
		if (!$this->isExecutable())
		{
			return;
		}

		$db         = $this->db;
		$query      = $db->getQuery(true);
		$planId     = $row->id;
		$articleIds = $data['k2_item_ids'];

		if (!$isNew)
		{
			$query->delete('#__osmembership_k2items')
				->where('plan_id=' . (int) $planId);
			$db->setQuery($query)
				->execute();
		}

		if (!empty($articleIds))
		{
			$articleIds = explode(',', $articleIds);

			for ($i = 0; $i < count($articleIds); $i++)
			{
				$articleId = $articleIds[$i];
				$query->clear()
					->insert('#__osmembership_k2items')
					->columns('plan_id, article_id')
					->values("$row->id,$articleId");
				$db->setQuery($query);
				$db->execute();
			}
		}

		if (isset($data['k2_item_categories']))
		{
			$selectedCategories = $data['k2_item_categories'];
		}
		else
		{
			$selectedCategories = [];
		}

		$params = new Registry($row->params);
		$params->set('k2_item_categories', implode(',', $selectedCategories));
		$row->params = $params->toString();
		$row->store();
	}

	/**
	 * Display form allows users to change setting for this subscription plan
	 *
	 * @param   object  $row
	 */
	private function drawSettingForm($row)
	{
		//Get categories
		$db    = $this->db;
		$query = $db->getQuery(true);
		$query->select('id, name')
			->from('#__k2_categories')
			->where('published = 1')
			->order('ordering');
		$db->setQuery($query);
		$categories = $db->loadObjectList('id');

		if (!count($categories))
		{
			return;
		}

		$categoryIds = array_keys($categories);
		$query->clear()
			->select('id, title, catid')
			->from('#__k2_items')
			->where('`published` = 1')
			->where('catid IN (' . implode(',', $categoryIds) . ')')
			->order('ordering');
		$db->setQuery($query);
		$rowArticles = $db->loadObjectList();

		if (!count($rowArticles))
		{
			return;
		}

		$articles = [];

		foreach ($rowArticles as $rowArticle)
		{
			$articles[$rowArticle->catid][] = $rowArticle;
		}

		$categories = array_values($categories);

		if (!$this->params->get('display_empty_categories'))
		{
			for ($i = 0, $n = count($categories); $i < $n; $i++)
			{
				$category = $categories[$i];

				if (!isset($articles[$category->id]))
				{
					unset($categories[$i]);
				}
			}

			reset($categories);
		}

		//Get plans articles
		$query->clear()
			->select('article_id')
			->from('#__osmembership_k2items')
			->where('plan_id=' . (int) $row->id);
		$db->setQuery($query);
		$planArticles = $db->loadColumn();

		$params             = new Registry($row->params);
		$selectedCategories = explode(',', $params->get('k2_item_categories', ''));

		require PluginHelper::getLayoutPath($this->_type, $this->_name, 'form');
	}

	public function onAfterRoute()
	{
		if (!$this->app)
		{
			return true;
		}

		if ($this->app->isClient('administrator'))
		{
			return true;
		}

		$user = Factory::getUser();

		if ($user->authorise('core.admin'))
		{
			return true;
		}

		if ($this->params->get('protection_method', 0) == 1)
		{
			return true;
		}

		if ($this->params->get('allow_search_engine', 0) == 1 && $this->app->client->robot)
		{
			return true;
		}

		$option    = $this->app->input->getCmd('option');
		$view      = $this->app->input->getCmd('view');
		$task      = $this->app->input->getCmd('task');
		$articleId = $this->app->input->getInt('id', 0);

		if ($option != 'com_k2' || ($view != 'item' && $task != 'download') || !$articleId)
		{
			return true;
		}

		if ($this->isItemReleased($articleId))
		{
			return true;
		}

		$planIds = $this->getRequiredPlanIds($articleId);

		if (count($planIds))
		{
			//Check to see the current user has an active subscription plans
			$activePlans = OSMembershipHelperSubscription::getActiveMembershipPlans();

			if (!count(array_intersect($planIds, $activePlans)))
			{
				OSMembershipHelper::loadLanguage();

				$msg = Text::_('OS_MEMBERSHIP_K2_ARTICLE_ACCESS_RESITRICTED');
				$msg = str_replace('[PLAN_TITLES]', $this->getPlanTitles($planIds), $msg);
				$msg = HTMLHelper::_('content.prepare', $msg);

				// Try to find the best redirect URL
				$redirectUrl = OSMembershipHelper::callOverridableHelperMethod('Helper', 'getPluginRestrictionRedirectUrl', [$this->params, $planIds]);

				// Store URL of this page to redirect user back after user logged in if they have active subscription of this plan
				$session = Factory::getSession();
				$session->set('osm_return_url', Uri::getInstance()->toString());
				$session->set('required_plan_ids', $planIds);

				$this->app->enqueueMessage($msg);
				$this->app->redirect($redirectUrl);
			}
		}
	}

	/**
	 * Hide fulltext of article to none-subscribers
	 *
	 * @param        $context
	 * @param        $row
	 * @param        $params
	 * @param   int  $page
	 *
	 * @return bool|void
	 */
	public function onContentPrepare($context, &$row, &$params, $page = 0)
	{
		if (!$this->app)
		{
			return;
		}

		if ($this->params->get('protection_method', 0) == 0)
		{
			return;
		}

		if ($this->params->get('allow_search_engine', 0) == 1 & $this->app->client->robot)
		{
			return;
		}

		if (!is_object($row))
		{
			return;
		}

		if ($context != 'com_k2.item')
		{
			return;
		}

		if ($this->isItemReleased($row->id))
		{
			return;
		}

		$planIds = $this->getRequiredPlanIds($row->id);

		if (count($planIds))
		{
			//Check to see the current user has an active subscription plans
			$activePlans = OSMembershipHelperSubscription::getActiveMembershipPlans();

			if (!count(array_intersect($planIds, $activePlans)))
			{
				$msg = OSMembershipHelper::callOverridableHelperMethod('Helper', 'getContentRestrictedMessages', [$planIds]);

				// Try to find the best redirect URL
				$redirectUrl = OSMembershipHelper::callOverridableHelperMethod('Helper', 'getPluginRestrictionRedirectUrl', [$this->params, $planIds]);

				// Store URL of this page to redirect user back after user logged in if they have active subscription of this plan
				$session = Factory::getSession();
				$session->set('osm_return_url', Uri::getInstance()->toString());
				$session->set('required_plan_ids', $planIds);

				$msg = str_replace('[SUBSCRIPTION_URL]', $redirectUrl, $msg);
				$msg = str_replace('[PLAN_IDS]', implode(',', $planIds), $msg);
				$msg = HTMLHelper::_('content.prepare', $msg);

				$layoutData = [
					'row'       => $row,
					'introText' => $row->introtext,
					'msg'       => $msg,
					'context'   => 'plgSystemOSMembershipK2.onContentPrepare',
				];

				$row->text = OSMembershipHelperHtml::loadCommonLayout('common/tmpl/restrictionmsg.php', $layoutData);
			}
		}

		return true;
	}

	/**
	 * Check if the K2 items released
	 *
	 * @param   int  $id
	 *
	 * @return bool
	 */
	private function isItemReleased($id)
	{
		if (!$this->params->get('release_article_older_than_x_days', 0) &&
			!$this->params->get('make_new_item_free_for_x_days', 0))
		{
			return false;
		}

		$db    = $this->db;
		$query = $db->getQuery(true)
			->select('*')
			->from('#__k2_items')
			->where('id = ' . (int) $id);
		$db->setQuery($query);
		$item = $db->loadObject();

		if ($item->publish_up && $item->publish_up != $db->getNullDate())
		{
			$publishedDate = $item->publish_up;
		}
		else
		{
			$publishedDate = $item->created;
		}

		$today         = Factory::getDate();
		$publishedDate = Factory::getDate($publishedDate);
		$numberDays    = $publishedDate->diff($today)->days;

		// This article is older than configured number of days, it can be accessed for free
		if ($today >= $publishedDate
			&& $this->params->get('release_item_older_than_x_days') > 0 &&
			$numberDays >= $this->params->get('release_item_older_than_x_days'))
		{
			return true;
		}

		// This article is just published and it's still free for access for the first X-days
		if ($today >= $publishedDate
			&& $this->params->get('make_new_item_free_for_x_days') > 0 &&
			$numberDays <= $this->params->get('make_new_item_free_for_x_days'))
		{
			return true;
		}

		return false;
	}

	/**
	 * The the Ids of the plans which users can subscribe for to access to the given article
	 *
	 * @param   int  $articleId
	 *
	 * @return array
	 */
	private function getRequiredPlanIds($articleId)
	{
		$db    = $this->db;
		$query = $db->getQuery(true);
		$query->select('DISTINCT plan_id')
			->from('#__osmembership_k2items')
			->where('article_id = ' . $articleId)
			->where('plan_id IN (SELECT id FROM #__osmembership_plans WHERE published = 1)');
		$db->setQuery($query);

		try
		{
			$planIds = $db->loadColumn();
		}
		catch (Exception $e)
		{
			$planIds = [];
		}

		// Check categories
		$query->clear()
			->select('catid')
			->from('#__k2_items')
			->where('id = ' . (int) $articleId);
		$db->setQuery($query);
		$catId = $db->loadResult();

		$query->clear()
			->select('id, params')
			->from('#__osmembership_plans')
			->where('published = 1');
		$db->setQuery($query);
		$plans = $db->loadObjectList();

		foreach ($plans as $plan)
		{
			$params = new Registry($plan->params);

			if ($articleCategories = $params->get('k2_item_categories'))
			{
				$articleCategories = ArrayHelper::toInteger(explode(',', $articleCategories));

				if ($this->params->get('restrict_children_categories'))
				{
					$articleCategories = $this->getAllChildrenCategories($articleCategories);
				}

				if (in_array($catId, $articleCategories))
				{
					$planIds[] = $plan->id;
				}
			}
		}

		return $planIds;
	}

	/**
	 * Get imploded titles of the given plans
	 *
	 * @param   array  $planIds
	 *
	 * @return string
	 */
	private function getPlanTitles($planIds)
	{
		$db    = $this->db;
		$query = $db->getQuery(true);
		$query->select('title')
			->from('#__osmembership_plans')
			->where('id IN (' . implode(',', $planIds) . ')')
			->where('published = 1')
			->order('ordering');
		$db->setQuery($query);

		return implode(' ' . Text::_('OSM_OR') . ' ', $db->loadColumn());
	}

	/**
	 * Get all childrent categories of the given categories
	 *
	 * @param   array  $catIds
	 *
	 * @return array
	 */
	private function getAllChildrenCategories($catIds)
	{
		$db    = $this->db;
		$query = $db->getQuery(true);

		$queue       = $catIds;
		$categoryIds = $catIds;

		while (count($queue))
		{
			$categoryId = array_pop($queue);

			//Get list of children categories of the current category
			$query->clear()
				->select('id')
				->from('#__k2_categories')
				->where('parent = ' . $categoryId)
				->where('published = 1');
			$db->setQuery($query);
			$db->setQuery($query);
			$children = $db->loadColumn();

			if (count($children))
			{
				$queue       = array_merge($queue, $children);
				$categoryIds = array_merge($categoryIds, $children);
			}
		}

		return $categoryIds;
	}

	/**
	 * Display k2 items which subscriber can access to in his profile
	 *
	 * @param $row
	 *
	 * @return array|void
	 */
	public function onProfileDisplay($row)
	{
		if (!$this->app)
		{
			return;
		}

		if (!$this->params->get('display_k2_items_in_profile'))
		{
			return;
		}

		ob_start();
		$this->displayK2Items();
		$form = ob_get_clean();

		return ['title' => Text::_('OSM_MY_K2_ITMES'),
		        'form'  => $form,
		];
	}

	/**
	 * Display list of accessible k2 items
	 */
	private function displayK2Items()
	{
		$db    = $this->db;
		$query = $db->getQuery(true);

		$items         = [];
		$activePlanIds = OSMembershipHelperSubscription::getActiveMembershipPlans();

		// Get categories
		$query->select('id, params')
			->from('#__osmembership_plans')
			->where('id IN (' . implode(',', $activePlanIds) . ')');
		$db->setQuery($query);
		$plans  = $db->loadObjectList();
		$catIds = [];

		foreach ($plans as $plan)
		{
			$params = new Registry($plan->params);

			if ($articleCategories = $params->get('k2_item_categories'))
			{
				$catIds = array_merge($catIds, explode(',', $articleCategories));
			}
		}

		if (count($activePlanIds) > 1)
		{
			$query->clear()
				->select('a.id, a.catid, a.title, a.alias, a.hits, c.name AS category_name')
				->from('#__k2_items AS a')
				->innerJoin('#__k2_categories AS c ON a.catid = c.id')
				->innerJoin('#__osmembership_k2items AS b ON a.id = b.article_id')
				->where('b.plan_id IN (' . implode(',', $activePlanIds) . ')')
				->where('a.published = 1')
				->order('plan_id')
				->order('a.ordering');
			$db->setQuery($query);

			$items = array_merge($items, $db->loadObjectList());
		}

		if (count($catIds))
		{
			$query->clear()
				->select('a.id, a.catid, a.title, a.alias, a.hits, c.name AS category_name')
				->from('#__k2_items AS a')
				->innerJoin('#__k2_categories AS c ON a.catid = c.id')
				->where('a.catid IN (' . implode(',', $catIds) . ')')
				->where('a.published = 1')
				->order('a.ordering');
			$db->setQuery($query);

			$items = array_merge($items, $db->loadObjectList());
		}

		if (empty($items))
		{
			return;
		}

		echo OSMembershipHelperHtml::loadCommonLayout('plugins/tmpl/osmembershipk2.php', ['items' => $items]);
	}

	/**
	 * Method to check if the plugin is executable
	 *
	 * @return bool
	 */
	private function isExecutable()
	{
		if (!$this->app)
		{
			return false;
		}

		if ($this->app->isClient('site') && !$this->params->get('show_on_frontend'))
		{
			return false;
		}

		return true;
	}
}
