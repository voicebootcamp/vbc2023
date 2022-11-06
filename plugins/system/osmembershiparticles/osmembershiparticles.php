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
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;

class plgSystemOSMembershipArticles extends CMSPlugin
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
		if (!file_exists(JPATH_ADMINISTRATOR . '/components/com_osmembership/osmembership.php'))
		{
			return;
		}

		parent::__construct($subject, $config);

		require_once JPATH_ADMINISTRATOR . '/components/com_osmembership/loader.php';
	}

	/**
	 * Render articles restriction setting form
	 *
	 * @param $row
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

		return ['title' => Text::_('PLG_OSMEMBERSHIP_ARTICLES_RESTRICTION_SETTINGS'),
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

		$db         = $this->db;
		$query      = $db->getQuery(true);
		$planId     = $row->id;
		$articleIds = $data['article_ids'];

		if (!$isNew)
		{
			$query->delete('#__osmembership_articles')->where('plan_id=' . (int) $planId);
			$db->setQuery($query);
			$db->execute();
		}

		if (!empty($articleIds))
		{
			$articleIds = explode(',', $articleIds);

			for ($i = 0; $i < count($articleIds); $i++)
			{
				$articleId = $articleIds[$i];
				$query->clear();
				$query->insert('#__osmembership_articles')
					->columns('plan_id,article_id')
					->values("$row->id,$articleId");
				$db->setQuery($query);
				$db->execute();
			}
		}

		if (isset($data['article_categories']))
		{
			$selectedCategories = $data['article_categories'];
		}
		else
		{
			$selectedCategories = [];
		}

		$params = new Registry($row->params);
		$params->set('article_categories', implode(',', $selectedCategories));
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
		//Get categories
		$categoryIds = $this->params->get('category_ids', []);
		$db          = $this->db;
		$query       = $db->getQuery(true);
		$query->select('id, title')
			->from('#__categories')
			->where('extension = "com_content"')
			->where('published = 1');

		if (count($categoryIds) && !in_array(0, $categoryIds))
		{
			$query->where('id IN (' . implode(',', $categoryIds) . ')');
		}

		$db->setQuery($query);
		$categories = $db->loadObjectList('id');

		if (!count($categories))
		{
			return;
		}

		$categoryIds = array_keys($categories);
		$query->clear()
			->select('id, title, catid')
			->from('#__content')
			->where('`state` = 1')
			->where('catid IN (' . implode(',', $categoryIds) . ')');
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

		//Get plans articles
		$query->clear()
			->select('article_id')
			->from('#__osmembership_articles')
			->where('plan_id=' . (int) $row->id);
		$db->setQuery($query);
		$planArticles = $db->loadColumn();

		$params             = new Registry($row->params);
		$selectedCategories = explode(',', $params->get('article_categories', ''));

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

		if ($this->params->get('protection_method', 0) == 1)
		{
			return true;
		}

		if ($this->params->get('allow_search_engine', 1) == 0 && $this->app->client->robot)
		{
			return true;
		}

		$option = $this->app->input->getCmd('option');
		$view   = $this->app->input->getCmd('view');

		if ($option != 'com_content' || $view != 'article')
		{
			return true;
		}

		$articleId = $this->app->input->getInt('id');

		if ($this->isArticleReleased($articleId))
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

				$msg = Text::_('OS_MEMBERSHIP_ARTICLE_ACCESS_RESITRICTED');
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
			return true;
		}

		if ($this->app->isClient('administrator'))
		{
			return true;
		}

		if ($this->params->get('protection_method', 0) == 0)
		{
			return;
		}

		$option = $this->app->input->getCmd('option');

		if ($option != 'com_content')
		{
			return true;
		}

		$articleFields = [
			'introtext',
			'fulltext',
			'catid',
		];

		foreach ($articleFields as $articleField)
		{
			if (!property_exists($row, $articleField))
			{
				return;
			}
		}

		if ($this->params->get('allow_search_engine', 0) == 1 && $this->app->client->robot)
		{
			return;
		}

		if (!is_object($row))
		{
			return;
		}

		if ($this->isArticleReleased($row->id))
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

				$redirectUrl = OSMembershipHelper::callOverridableHelperMethod('Helper', 'getPluginRestrictionRedirectUrl', [$this->params, $planIds]);

				// Store URL of this page to redirect user back after user logged in if they have active subscription of this plan
				$session = Factory::getSession();
				$session->set('osm_return_url', Uri::getInstance()->toString());
				$session->set('required_plan_ids', $planIds);

				$loginUrl = Route::_('index.php?option=com_users&view=login&return=' . base64_encode(Uri::getInstance()->toString()), false);

				$msg = str_replace('[SUBSCRIPTION_URL]', $redirectUrl, $msg);
				$msg = str_replace('[LOGIN_URL]', $loginUrl, $msg);
				$msg = str_replace('[PLAN_IDS]', implode(',', $planIds), $msg);
				$msg = str_replace('[PLAN_TITLES]', $this->getPlanTitles($planIds), $msg);
				$msg = HTMLHelper::_('content.prepare', $msg);

				$layoutData = [
					'row'       => $row,
					'introText' => $row->introtext,
					'msg'       => $msg,
					'context'   => 'plgSystemOSMembershipArticles.onContentPrepare',
				];

				$row->text = OSMembershipHelperHtml::loadCommonLayout('common/tmpl/restrictionmsg.php', $layoutData);

				if ($row->params instanceof Registry)
				{
					$row->params->set('show_readmore', 0);
				}
			}
		}

		return true;
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
		if (!$this->params->get('display_articles_in_profile'))
		{
			return;
		}

		ob_start();
		$this->displayArticles($row);

		$form = ob_get_clean();

		return ['title' => Text::_('OSM_MY_ARTICLES'),
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
	private function isArticleReleased($articleId)
	{
		if (!$this->params->get('release_article_older_than_x_days', 0))
		{
			return false;
		}

		$db    = $this->db;
		$query = $db->getQuery(true)
			->select('*')
			->from('#__content')
			->where('id = ' . (int) $articleId);
		$db->setQuery($query);
		$article = $db->loadObject();

		if ($article->publish_up && $article->publish_up != $db->getNullDate())
		{
			$publishedDate = $article->publish_up;
		}
		else
		{
			$publishedDate = $article->created;
		}

		$today         = Factory::getDate();
		$publishedDate = Factory::getDate($publishedDate);
		$numberDays    = $publishedDate->diff($today)->days;

		// This article is older than configured number of days, it can be accessed for free
		if ($today >= $publishedDate && $numberDays >= $this->params->get('release_article_older_than_x_days'))
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
		$query = $db->getQuery(true)
			->select('DISTINCT plan_id')
			->from('#__osmembership_articles')
			->where('article_id = ' . (int) $articleId);
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
			->from('#__content')
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

			if ($articleCategories = $params->get('article_categories'))
			{
				$articleCategories = explode(',', $articleCategories);

				if (in_array($catId, $articleCategories))
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
	private function displayArticles($row)
	{
		$db            = $this->db;
		$query         = $db->getQuery(true);
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

			if ($articleCategories = $params->get('article_categories'))
			{
				$catIds = array_merge($catIds, explode(',', $articleCategories));
			}
		}

		$items = [];

		if (count($activePlanIds) > 1)
		{
			$query->clear()
				->select('a.id, a.catid, a.title, a.alias, a.hits, c.title AS category_title')
				->from('#__content AS a')
				->innerJoin('#__categories AS c ON a.catid = c.id')
				->innerJoin('#__osmembership_articles AS b ON a.id = b.article_id')
				->where('b.plan_id IN (' . implode(',', $activePlanIds) . ')')
				->where('a.state = 1')
				->order('plan_id')
				->order('a.ordering');

			if (Multilanguage::isEnabled())
			{
				$query->where('a.language IN (' . $db->quote(Factory::getLanguage()->getTag()) . ',' . $db->quote('*') . ', "")');
			}

			$db->setQuery($query);

			$items = array_merge($items, $db->loadObjectList());
		}

		if (count($catIds) > 0)
		{
			$query->clear()
				->select('a.id, a.catid, a.title, a.alias, a.hits, c.title AS category_title')
				->from('#__content AS a')
				->innerJoin('#__categories AS c ON a.catid = c.id')
				->where('a.catid IN (' . implode(',', $catIds) . ')')
				->where('a.state = 1')
				->order('a.ordering');

			$items = array_merge($items, $db->loadObjectList());
		}

		if (empty($items))
		{
			return;
		}

		echo OSMembershipHelperHtml::loadCommonLayout('plugins/tmpl/osmembershiparticles.php', ['items' => $items]);
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
