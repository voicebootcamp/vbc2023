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
use Joomla\CMS\Router\Route;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Uri\Uri;

class plgSystemScheduleSPPageBuilder extends CMSPlugin
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
		if (!file_exists(JPATH_ADMINISTRATOR . '/components/com_osmembership/osmembership.php')
			|| !file_exists(JPATH_ADMINISTRATOR . '/components/com_sppagebuilder/sppagebuilder.php'))
		{
			return;
		}

		parent::__construct($subject, $config);
	}

	/**
	 * Render setting form
	 *
	 * @param   PlanOSMembership  $row
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

		return ['title' => Text::_('OSM_SCHEDULE_SP_PAGE_BUILDER_MANAGER'),
		        'form'  => ob_get_clean(),
		];
	}

	/**
	 * Store setting into database, in this case, use params field of plans table
	 *
	 * @param   OSMembershipTablePlan  $row
	 * @param   bool                   $isNew  true if create new plan, false if edit
	 */
	public function onAfterSaveSubscriptionPlan($context, $row, $data, $isNew)
	{
		if (!$this->isExecutable())
		{
			return;
		}

		$scheduleSPPages   = isset($data['schedule_sp_page_builder_pages']) ? $data['schedule_sp_page_builder_pages'] : [];
		$scheduleSPPageIds = [];
		$ordering          = 1;

		foreach ($scheduleSPPages as $scheduleSPPage)
		{
			if (empty($scheduleSPPage['page_id']))
			{
				continue;
			}

			/* @var OSMembershipTableScheduleSPPageBuilder $rowScheduleSPPageBuilder */
			$rowScheduleSPPageBuilder = Table::getInstance('ScheduleSPPageBuilder', 'OSMembershipTable');

			$rowScheduleSPPageBuilder->bind($scheduleSPPage);

			// Prevent item being moved to new plan on save as copy
			if ($isNew)
			{
				$rowScheduleSPPageBuilder->id = 0;
			}

			$rowScheduleSPPageBuilder->plan_id  = $row->id;
			$rowScheduleSPPageBuilder->ordering = $ordering++;
			$rowScheduleSPPageBuilder->store();
			$scheduleSPPageIds[] = $rowScheduleSPPageBuilder->id;
		}

		if (!$isNew)
		{
			$db    = $this->db;
			$query = $db->getQuery(true);
			$query->delete('#__osmembership_schedule_sppagebuilder_pages')
				->where('plan_id = ' . $row->id);

			if (count($scheduleSPPageIds))
			{
				$query->where('id NOT IN (' . implode(',', $scheduleSPPageIds) . ')');
			}

			$db->setQuery($query)
				->execute();
		}
	}

	/**
	 * Render setting form
	 *
	 * @param   JTable  $row
	 *
	 * @return array
	 */
	public function onProfileDisplay($row)
	{
		if (!$this->app)
		{
			return [];
		}

		ob_start();
		$this->drawSchedulePages($row);

		return ['title' => Text::_('OSM_MY_SCHEDULE_SP_PAGE_BUILDER_PAGES'),
		        'form'  => ob_get_clean(),
		];
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

		$db     = $this->db;
		$query  = $db->getQuery(true);
		$pageId = $this->app->input->getInt('id', 0);

		$query->select('*')
			->from('#__osmembership_schedule_sppagebuilder_pages')
			->where('page_id = ' . $pageId);
		$db->setQuery($query);
		$rows = $db->loadObjectList();

		if (empty($rows))
		{
			return;
		}

		$releasePageOlderThanXDays = (int) $this->params->get('release_pages_older_than_x_days', 0);

		if ($releasePageOlderThanXDays > 0)
		{
			$query->select('*')
				->from('#__sppagebuilder')
				->where('id = ' . $pageId);
			$db->setQuery($query);
			$rowPage = $db->loadObject();

			if ($rowPage->created_on && $rowPage->created_on != $db->getNullDate())
			{
				$publishedDate = $rowPage->created_on;

				$today         = Factory::getDate();
				$publishedDate = Factory::getDate($publishedDate);
				$numberDays    = $publishedDate->diff($today)->days;

				// This article is older than configured number of days, it can be accessed for free
				if ($today >= $publishedDate && $numberDays >= $releasePageOlderThanXDays)
				{
					return true;
				}
			}
		}

		// Require library + register autoloader
		require_once JPATH_ADMINISTRATOR . '/components/com_osmembership/loader.php';

		$canAccess     = false;
		$subscriptions = OSMembershipHelperSubscription::getUserSubscriptionsInfo();

		foreach ($rows as $row)
		{
			if (isset($subscriptions[$row->plan_id]))
			{
				$subscription = $subscriptions[$row->plan_id];

				if ($subscription->active_in_number_days >= $row->number_days)
				{
					$canAccess = true;
					break;
				}
			}
		}

		if (!$canAccess)
		{
			if (!$user->id)
			{
				// Redirect user to login page
				$this->app->redirect(Route::_('index.php?option=com_users&view=login&return=' . base64_encode(Uri::getInstance()->toString())));
			}
			else
			{
				OSMembershipHelper::loadLanguage();

				$this->app->enqueueMessage(Text::_('OSM_SCHEDULE_PAGE_LOCKED'), 'error');
				$this->app->redirect(Uri::root(), 403);
			}
		}
	}

	/**
	 * Display form allows users to change settings on subscription plan add/edit screen
	 *
	 * @param   object  $row
	 */
	private function drawSettingForm($row)
	{
		$numberPagesEachTime                        = $this->params->get('number_new_pages_each_time', 10);
		$form                                       = JForm::getInstance('scheduleSPPageBuilder', JPATH_ROOT . '/plugins/system/schedulesppagebuilder/form/schedulesppagebuilder.xml');
		$formData['schedule_sp_page_builder_pages'] = [];

		if ($row->id)
		{
			$db    = $this->db;
			$query = $db->getQuery(true)
				->select('*')
				->from('#__osmembership_schedule_sppagebuilder_pages')
				->where('plan_id = ' . $row->id)
				->order('ordering');
			$db->setQuery($query);

			foreach ($db->loadObjectList() as $schedulePage)
			{
				$formData['schedule_sp_page_builder_pages'][] = [
					'id'          => $schedulePage->id,
					'page_id'     => $schedulePage->page_id,
					'number_days' => $schedulePage->number_days,
				];
			}
		}

		for ($i = 0; $i < $numberPagesEachTime; $i++)
		{
			$formData['schedule_sp_page_builder_pages'][] = [
				'id '         => 0,
				'page_id'     => 0,
				'number_days' => '',
			];
		}

		$form->bind($formData);

		foreach ($form->getFieldset() as $field)
		{
			echo $field->input;
		}
	}

	/**
	 * Display Display List of Documents which the current subscriber can download from his subscription
	 *
	 * @param   object  $row
	 */
	private function drawSchedulePages($row)
	{
		$config = OSMembershipHelper::getConfig();

		$subscriptions = OSMembershipHelperSubscription::getUserSubscriptionsInfo();

		if (empty($subscriptions))
		{
			return;
		}

		$accessiblePlanIds = array_keys($subscriptions);

		$db    = $this->db;
		$query = $db->getQuery(true)
			->select('a.id, a.title, a.hits, a.created_on, b.plan_id, b.number_days')
			->from('#__sppagebuilder AS a')
			->innerJoin('#__osmembership_schedule_sppagebuilder_pages AS b ON a.id = b.page_id')
			->where('b.plan_id IN (' . implode(',', $accessiblePlanIds) . ')')
			->order('b.plan_id')
			->order('b.number_days');
		$db->setQuery($query);
		$items = $db->loadObjectList();

		if (empty($items))
		{
			return;
		}

		foreach ($items as $item)
		{
			$item->isReleased = $this->isItemReleased($item);
		}

		echo OSMembershipHelperHtml::loadCommonLayout('plugins/tmpl/schedulesppagebuilder.php', ['items' => $items, 'subscriptions' => $subscriptions, 'params' => $this->params]);
	}

	/**
	 * Check if the K2 items released
	 *
	 * @param   stdClass  $item
	 *
	 * @return bool
	 */
	private function isItemReleased($item)
	{
		if (!$this->params->get('release_pages_older_than_x_days', 0))
		{
			return false;
		}

		$db = $this->db;

		if ($item->created_on && $item->created_on != $db->getNullDate())
		{
			$publishedDate = $item->created_on;
			$today         = Factory::getDate();
			$publishedDate = Factory::getDate($publishedDate);
			$numberDays    = $publishedDate->diff($today)->days;

			// This article is older than configured number of days, it can be accessed for free
			if ($today >= $publishedDate
				&& $numberDays >= $this->params->get('release_pages_older_than_x_days', 0))
			{
				return true;
			}
		}

		return false;
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
