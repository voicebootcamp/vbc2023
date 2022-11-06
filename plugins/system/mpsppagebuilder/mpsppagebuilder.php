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
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;

class plgSystemMPSPPageBuilder extends CMSPlugin
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
		$canRun = file_exists(JPATH_ADMINISTRATOR . '/components/com_osmembership/osmembership.php') &&
			file_exists(JPATH_ADMINISTRATOR . '/components/com_sppagebuilder/sppagebuilder.php');

		if (!$canRun)
		{
			return;
		}

		parent::__construct($subject, $config);

		require_once JPATH_ADMINISTRATOR . '/components/com_osmembership/loader.php';
	}

	/**
	 * Render articles restriction setting form
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
		$form = ob_get_contents();
		ob_end_clean();

		return ['title' => Text::_('OSM_SPPAGEBUILDER_RESTRICTION_SETTINGS'),
		        'form'  => $form,
		];
	}

	/**
	 * Store setting into database
	 *
	 * @param   OSMembershipTablePlan  $row
	 * @param   Boolean                $isNew  true if create new plan, false if edit
	 */
	public function onAfterSaveSubscriptionPlan($context, $row, $data, $isNew)
	{
		if (!$this->isExecutable())
		{
			return;
		}

		$db     = $this->db;
		$query  = $db->getQuery(true);
		$planId = $row->id;

		if (!$isNew)
		{
			$query->delete('#__osmembership_sppagebuilder_pages')->where('plan_id=' . (int) $planId);
			$db->setQuery($query);
			$db->execute();
		}

		if (!empty($data['sppb_page_ids']))
		{
			$pageIds = explode(',', $data['sppb_page_ids']);

			$query->clear()
				->insert('#__osmembership_sppagebuilder_pages')
				->columns('plan_id, page_id');

			for ($i = 0; $i < count($pageIds); $i++)
			{
				$pageId = $pageIds[$i];
				$query->values(implode(',', $db->quote([$row->id, $pageId])));
			}

			$db->setQuery($query);
			$db->execute();
		}

		if (isset($data['sppb_category_ids']))
		{
			$selectedCategories = $data['sppb_category_ids'];
		}
		else
		{
			$selectedCategories = [];
		}

		$params = new Registry($row->params);
		$params->set('sppagebuilder_category_ids', implode(',', $selectedCategories));
		$row->params = $params->toString();
		$row->store();
	}

	/**
	 * Display form allows users to change setting for this subscription plan
	 *
	 * @param   OSMembershipTablePlan  $row
	 */
	private function drawSettingForm($row)
	{
		$db    = $this->db;
		$query = $db->getQuery(true)
			->select('id, title')
			->from('#__categories')
			->where('extension = "com_sppagebuilder"')
			->where('published = 1');
		$db->setQuery($query);
		$categories = $db->loadObjectList();
		$query->clear()
			->select('id, title, catid')
			->from('#__sppagebuilder')
			->where('`published` = 1');
		$db->setQuery($query);
		$pages = $db->loadObjectList();

		if (!count($pages))
		{
			return;
		}

		$listPages    = [];
		$listPages[0] = [];

		foreach ($pages as $page)
		{
			$listPages[$page->catid][] = $page;
		}

		// Remove categories which don't have any articles
		for ($i = 0, $n = count($categories); $i < $n; $i++)
		{
			$category = $categories[$i];

			if (!isset($listPages[$category->id]))
			{
				unset($categories[$i]);
			}
		}

		reset($categories);

		//Get plan pages
		$query->clear()
			->select('page_id')
			->from('#__osmembership_sppagebuilder_pages')
			->where('plan_id = ' . (int) $row->id);
		$db->setQuery($query);
		$planPagebuilders = $db->loadColumn();

		$params             = new Registry($row->params);
		$selectedCategories = explode(',', $params->get('sppagebuilder_category_ids', ''));

		require PluginHelper::getLayoutPath($this->_type, $this->_name, 'form');
	}

	/**
	 * Protect access to articles
	 *
	 * @return bool
	 * @throws Exception
	 */
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

		$option = $this->app->input->getCmd('option');
		$view   = $this->app->input->getCmd('view');

		if ($option != 'com_sppagebuilder' || $view != 'page')
		{
			return true;
		}

		$pageId = $this->app->input->getInt('id');

		if ($this->isPageReleased($pageId))
		{
			return true;
		}

		if ($this->isOwner($pageId))
		{
			return true;
		}

		$planIds = $this->getRequiredPlanIds($pageId);

		if (count($planIds))
		{
			//Check to see the current user has an active subscription plans
			$activePlans = OSMembershipHelperSubscription::getActiveMembershipPlans();

			if (!count(array_intersect($planIds, $activePlans)))
			{
				OSMembershipHelper::loadLanguage();

				$msg = Text::_('OSM_SPPAGEBUILDER_PAGE_ACCESS_RESITRICTED');
				$msg = str_replace('[PLAN_TITLES]', $this->getPlanTitles($planIds), $msg);

				// Try to find the best redirect URL
				$redirectUrl = OSMembershipHelper::callOverridableHelperMethod('Helper', 'getPluginRestrictionRedirectUrl', [$this->params, $planIds]);

				// Store URL of this page to redirect user back after user logged in if they have active subscription of this plan
				$session = Factory::getSession();
				$session->set('osm_return_url', Uri::getInstance()->toString());
				$session->set('required_plan_ids', $planIds);

				// Redirect to subscription page to allow users to subscribe or logging in
				$this->app->enqueueMessage($msg);
				$this->app->redirect($redirectUrl);
			}
		}
	}

	/**
	 * Display list of articles on profile page
	 *
	 * @param   OSMembershipTableSubscriber  $row
	 *
	 * @return array
	 */
	public function onProfileDisplay($row)
	{
		if (!$this->app)
		{
			return;
		}

		if (!$this->params->get('display_pages_in_profile'))
		{
			return;
		}

		ob_start();
		$this->displayPage($row);

		$form = ob_get_clean();

		return ['title' => Text::_('OSM_MY_PAGES'),
		        'form'  => $form,
		];
	}

	/**
	 * Check if article released
	 *
	 * @param   int  $articleId
	 *
	 * @return bool
	 */
	private function isPageReleased($pageId)
	{
		$db    = $this->db;
		$query = $db->getQuery(true)
			->select('created_on')
			->from('#__sppagebuilder')
			->where('id = ' . (int) $pageId);
		$db->setQuery($query);
		$createdOn = $db->loadResult();

		$today         = Factory::getDate();
		$publishedDate = Factory::getDate($createdOn);
		$numberDays    = $publishedDate->diff($today)->days;

		if (!$this->params->get('release_pages_older_than_x_days'))
		{
			return false;
		}

		// This page is older than configured number of days, it can be accessed for free
		if ($today >= $publishedDate && $numberDays >= $this->params->get('release_pages_older_than_x_days'))
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
	private function getRequiredPlanIds($pageId)
	{
		$db    = $this->db;
		$query = $db->getQuery(true);
		$query->select('DISTINCT plan_id')
			->from('#__osmembership_sppagebuilder_pages')
			->where('page_id = ' . (int) $pageId);
		$db->setQuery($query);

		try
		{
			$planIds = $db->loadColumn();
		}
		catch (Exception $e)
		{
			$planIds = [];
		}

		$query->clear()
			->select('catid')
			->from('#__sppagebuilder')
			->where('id = ' . (int) $pageId);
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

			if ($pageCategories = $params->get('sppagebuilder_category_ids'))
			{
				$pageCategories = explode(',', $pageCategories);

				if (in_array($catId, $pageCategories))
				{
					$planIds[] = $plan->id;
				}
			}
		}

		$query->clear()
			->select('id')
			->from('#__osmembership_plans')
			->where('published = 0');
		$db->setQuery($query);

		return array_diff($planIds, $db->loadColumn());
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
	 * Display articles which subscriber can access to
	 *
	 * @param   OSMembershipTableSubscriber  $row
	 *
	 * @throws Exception
	 */
	private function displayPage($row)
	{
		$activePlanIds = OSMembershipHelperSubscription::getActiveMembershipPlans();
		$items         = [];

		if (count($activePlanIds) > 1)
		{
			$db    = $this->db;
			$query = $db->getQuery(true)
				->select('params')
				->from('#__osmembership_plans')
				->where('id IN (' . implode(',', $activePlanIds) . ')');
			$db->setQuery($query);
			$rowPlans           = $db->loadObjectList();
			$selectedCategories = [];

			foreach ($rowPlans as $rowPlan)
			{
				$params             = new Registry($rowPlan->params);
				$selectedCategories = array_merge($selectedCategories, array_filter(explode(',', $params->get('sppagebuilder_category_ids', ''))));
			}

			if (!count($selectedCategories))
			{
				$selectedCategories = [0];
			}

			$query->clear()
				->select('a.id, a.catid, a.title, a.hits, c.title AS category_title')
				->from('#__sppagebuilder AS a')
				->leftJoin('#__osmembership_sppagebuilder_pages AS b ON a.id = b.page_id')
				->leftJoin('#__categories AS c ON a.catid = c.id')
				->where('(b.plan_id IN (' . implode(',', $activePlanIds) . ') OR a.catid IN (' . implode(',', $selectedCategories) . '))')
				->where('a.published = 1')
				->order('plan_id')
				->order('a.title');
			$db->setQuery($query);
			$items = $db->loadObjectList();
		}

		if (empty($items))
		{
			return;
		}

		echo OSMembershipHelperHtml::loadCommonLayout('plugins/tmpl/mpsppagebuilder.php', ['items' => $items]);
	}

	/**
	 * Method to check if the current user is the page author
	 *
	 * @param   int  $pageId
	 *
	 * @return bool
	 */
	private function isOwner($pageId = 0)
	{
		if (!$pageId)
		{
			return false;
		}

		$db    = $this->db;
		$query = $db->getQuery(true)
			->select('COUNT(id)')
			->from('#__sppagebuilder')
			->where('created_by = ' . Factory::getUser()->id)
			->where('id = ' . $pageId);
		$db->setQuery($query);

		return $db->loadResult() > 0;
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
