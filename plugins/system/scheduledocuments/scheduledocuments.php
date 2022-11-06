<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2022 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Table\Table;

class plgSystemScheduleDocuments extends CMSPlugin
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
		$form = ob_get_contents();
		ob_end_clean();

		return ['title' => Text::_('OSM_SCHEDULE_DOCUMENTS'),
		        'form'  => $form,
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

		$scheduleDocuments   = isset($data['schedule_documents']) ? $data['schedule_documents'] : [];
		$scheduleDocumentIds = [];
		$ordering            = 1;

		foreach ($scheduleDocuments as $scheduleDocument)
		{
			if (empty($scheduleDocument['document']))
			{
				continue;
			}

			/* @var OSMembershipTableScheduleContent $rowScheduleDocument */
			$rowScheduleDocument = Table::getInstance('ScheduleDocument', 'OSMembershipTable');

			$rowScheduleDocument->bind($scheduleDocument);

			// Prevent item being moved to new plan on save as copy

			if ($isNew)
			{
				$rowScheduleDocument->id = 0;
			}

			$rowScheduleDocument->plan_id  = $row->id;
			$rowScheduleDocument->ordering = $ordering++;
			$rowScheduleDocument->store();
			$scheduleDocumentIds[] = $rowScheduleDocument->id;
		}

		if (!$isNew)
		{
			$db    = $this->db;
			$query = $db->getQuery(true);
			$query->delete('#__osmembership_scheduledocuments')
				->where('plan_id = ' . $row->id);

			if (count($scheduleDocumentIds))
			{
				$query->where('id NOT IN (' . implode(',', $scheduleDocumentIds) . ')');
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
		ob_start();
		$this->drawScheduleContent($row);

		return ['title' => Text::_('OSM_MY_SCHEDULE_DOCUMENTS'),
		        'form'  => ob_get_clean(),
		];
	}

	/**
	 * Display form allows users to change settings on subscription plan add/edit screen
	 *
	 * @param   object  $row
	 */
	private function drawSettingForm($row)
	{
		$numberArticlesEachTime         = $this->params->get('number_new_documents_each_time', 10);
		$form                           = JForm::getInstance('schedule_documents', JPATH_ROOT . '/plugins/system/scheduledocuments/form/scheduledocuments.xml');
		$formData['schedule_documents'] = [];

		// Load existing schedule documents for this plan
		if ($row->id)
		{
			$db    = $this->db;
			$query = $db->getQuery(true)
				->select('*')
				->from('#__osmembership_scheduledocuments')
				->where('plan_id = ' . $row->id)
				->order('ordering');
			$db->setQuery($query);

			foreach ($db->loadObjectList() as $scheduleContent)
			{
				$formData['schedule_documents'][] = [
					'id'          => $scheduleContent->id,
					'document'    => $scheduleContent->document,
					'number_days' => $scheduleContent->number_days,
				];
			}
		}

		for ($i = 0; $i < $numberArticlesEachTime; $i++)
		{
			$formData['schedule_documents'][] = [
				'id '         => 0,
				'document'    => '',
				'number_days' => 0,
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
	private function drawScheduleContent($row)
	{
		$config = OSMembershipHelper::getConfig();

		$subscriptions = OSMembershipHelperSubscription::getUserSubscriptionsInfo();

		if (empty($subscriptions))
		{
			return;
		}

		$accessiblePlanIds = array_keys($subscriptions);

		$db    = $this->db;
		$query = $db->getQuery(true);
		$query->select('a.*')
			->from('#__osmembership_scheduledocuments AS a')
			->where('a.plan_id IN (' . implode(',', $accessiblePlanIds) . ')')
			->order('a.plan_id')
			->order('a.number_days');
		$db->setQuery($query);
		$items = $db->loadObjectList();

		if (empty($items))
		{
			return;
		}

		echo OSMembershipHelperHtml::loadCommonLayout('plugins/tmpl/scheduledocuments.php', ['items' => $items, 'subscriptions' => $subscriptions]);
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
