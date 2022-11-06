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
use Joomla\CMS\Mail\MailHelper;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Table\Table;
use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;

class plgOSMembershipAcym extends CMSPlugin
{
	/**
	 * Application object.
	 *
	 * @var    JApplicationCms
	 */
	protected $app;

	/**
	 * Database object.
	 *
	 * @var    JDatabaseDriver
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
		if (!file_exists(JPATH_ROOT . '/components/com_acym/acym.php'))
		{
			return;
		}

		parent::__construct($subject, $config);
	}

	/**
	 * Return list of custom fields in ACYMailing which will be used to map with fields in Events Booking
	 *
	 * @return array
	 */
	public function onGetNewsletterFields()
	{
		if (!$this->app)
		{
			return [];
		}

		$db    = $this->db;
		$query = $db->getQuery(true)
			->select($db->quoteName(['name', 'name'], ['value', 'text']))
			->from('#__acym_field')
			->where('name NOT IN ("ACYM_NAME", "ACYM_EMAIL")');
		$db->setQuery($query);

		return $db->loadObjectList();
	}

	/**
	 * Render setting form
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

		return ['title' => Text::_('PLG_OSMEMBERSHIP_ACYMAILING_LIST_SETTINGS'),
		        'form'  => $form,
		];
	}

	/**
	 * Store setting into database, in this case, use params field of plans table
	 *
	 * @param   OSMembershipTablePlan  $row
	 * @param   bool                   $isNew  true if create new plan, false if edit
	 *
	 * @return void
	 */
	public function onAfterSaveSubscriptionPlan($context, $row, $data, $isNew)
	{
		if (!$this->isExecutable())
		{
			return;
		}

		$params = new Registry($row->params);

		$params->set('acymailing_list_ids', implode(',', $data['acymailing_list_ids'] ?? []));
		$params->set('acymailing_active_remove_list_ids', implode(',', $data['acymailing_active_remove_list_ids'] ?? []));
		$params->set('subscription_expired_acymailing_list_ids', implode(',', $data['subscription_expired_acymailing_list_ids'] ?? []));
		$params->set('acymailing_expired_assign_list_ids', implode(',', $data['acymailing_expired_assign_list_ids'] ?? []));
		$params->set('mailing_list_custom_field', $data['mailing_list_custom_field'] ?? 0);
		$row->params = $params->toString();

		$row->store();
	}

	/**
	 * Run when a membership activated
	 *
	 * @param   OSMembershipTableSubscriber  $row
	 */
	public function onMembershipActive($row)
	{
		if (!$this->app)
		{
			return;
		}

		if ($row->group_admin_id > 0 && $this->params->get('subscribe_group_members', '1') == '0')
		{
			return;
		}

		if (!MailHelper::isEmailAddress($row->email))
		{
			return;
		}

		$config = OSMembershipHelper::getConfig();

		// In case subscriber doesn't want to subscribe to newsleter, stop
		if ($config->show_subscribe_newsletter_checkbox && empty($row->subscribe_newsletter))
		{
			return;
		}

		/* @var OSMembershipTablePlan $plan */
		$plan = Table::getInstance('Plan', 'OSMembershipTable');
		$plan->load($row->plan_id);
		$params = new Registry($plan->params);

		$db    = $this->db;
		$query = $db->getQuery(true);

		if ($fieldId = (int) $params->get('mailing_list_custom_field'))
		{
			$query->select('field_value')
				->from('#__osmembership_field_value')
				->where('subscriber_id = ' . $row->id)
				->where('field_id = ' . $fieldId);
			$db->setQuery($query);
			$fieldValue = $db->loadResult();

			if ($fieldValue && is_array(json_decode($fieldValue)))
			{
				$listNames = array_map('trim', json_decode($fieldValue));
			}
			elseif (is_string($fieldValue) && strpos($fieldValue, ', ') !== false)
			{
				$listNames = explode(', ', $fieldValue);
			}
			elseif (is_string($fieldValue))
			{
				$listNames = [$fieldValue];
			}
			else
			{
				$listNames = [];
			}

			if (!empty($listNames))
			{
				$listNames = array_map([$db, 'quote'], $listNames);

				$fields = array_keys($db->getTableColumns('#__acym_list'));

				// Workaround causes by ACYMailing changes their database field names :(
				if (in_array('id', $fields))
				{
					$idField = 'id';
				}
				else
				{
					$idField = 'listid';
				}

				if (in_array('active', $fields))
				{
					$publishedField = 'active';
				}
				else
				{
					$publishedField = 'published';
				}

				$query->clear()
					->select($db->quoteName($idField))
					->from('#__acym_list')
					->where($db->quoteName($publishedField) . ' = 1')
					->where('(name = ' . implode(' OR name = ', $listNames) . ')');
				$db->setQuery($query);
				$listIds = implode(',', $db->loadColumn());
			}
			else
			{
				$listIds = '';
			}
		}
		else
		{
			$listIds = trim($params->get('acymailing_list_ids', ''));
		}

		$removeListIds = trim($params->get('acymailing_active_remove_list_ids', ''));

		if ($listIds || $removeListIds)
		{
			require_once JPATH_ADMINISTRATOR . '/components/com_acym/helpers/helper.php';

			/* @var acymUserClass $userClass */
			$userClass               = acym_get('class.user');
			$userClass->checkVisitor = false;

			if (method_exists($userClass, 'getOneByEmail'))
			{
				$subId = $userClass->getOneByEmail($row->email);
			}
			else
			{
				$subId = $userClass->getUserIdByEmail($row->email);
			}

			if (is_object($subId))
			{
				$subId = $subId->id;
			}

			if (!$subId)
			{
				$myUser         = new stdClass;
				$myUser->email  = $row->email;
				$myUser->name   = trim($row->first_name . ' ' . $row->last_name);
				$myUser->cms_id = $row->user_id;
				$subId          = $userClass->save($myUser);
			}

			$this->updateAcyMailingFieldsData($row, $subId);

			if ($listIds)
			{
				$listIds = explode(',', $listIds);
				$listIds = ArrayHelper::toInteger($listIds);
				$listIds = array_filter($listIds);

				if (count($listIds))
				{
					try
					{
						$userClass->subscribe($subId, $listIds);
					}
					catch (Exception $e)
					{

					}
				}
			}

			if ($removeListIds)
			{
				$removeListIds = explode(',', $removeListIds);
				$removeListIds = ArrayHelper::toInteger($removeListIds);
				$removeListIds = array_filter($removeListIds);

				if (count($removeListIds))
				{
					try
					{
						$userClass->unsubscribe($subId, $removeListIds);
					}
					catch (Exception $e)
					{

					}
				}
			}
		}
	}

	/**
	 * Plugin triggered when user update his profile
	 *
	 * @param   OSMembershipTableSubscriber  $row  The subscription record
	 */
	public function onProfileUpdate($row)
	{
		if (!$this->app)
		{
			return;
		}

		if ($row->group_admin_id > 0 && $this->params->get('subscribe_group_members', '1') == '0')
		{
			return;
		}

		if (!MailHelper::isEmailAddress($row->email))
		{
			return;
		}

		$db    = $this->db;
		$query = $db->getQuery(true);
		$user  = Factory::getUser($row->user_id);
		$query->update('#__acym_user')
			->set('email = ' . $db->quote($row->email))
			->where('email = ' . $db->quote($user->email));
		$db->setQuery($query);

		try
		{
			$db->execute();
		}
		catch (Exception $e)
		{
			// There is another ACYMailing user uses this email, ignore
			return;
		}

		require_once JPATH_ADMINISTRATOR . '/components/com_acym/helpers/helper.php';

		/* @var acymUserClass $userClass */
		$userClass               = acym_get('class.user');
		$userClass->checkVisitor = false;

		if (method_exists($userClass, 'getOneByEmail'))
		{
			$subId = $userClass->getOneByEmail($row->email);
		}
		else
		{
			$subId = $userClass->getUserIdByEmail($row->email);
		}

		if (is_object($subId))
		{
			$subId = $subId->id;
		}

		$this->updateAcyMailingFieldsData($row, $subId);
	}

	/**
	 * Run when a membership expiried die
	 *
	 * @param   OSMembershipTableSubscriber  $row
	 */
	public function onMembershipExpire($row)
	{
		if (!$this->app)
		{
			return;
		}

		if ($row->group_admin_id > 0 && $this->params->get('subscribe_group_members', '1') == '0')
		{
			return;
		}

		if (!MailHelper::isEmailAddress($row->email))
		{
			return;
		}

		$config = OSMembershipHelper::getConfig();

		// In case subscriber doesn't want to subscribe to newsleter, stop
		if ($config->show_subscribe_newsletter_checkbox && empty($row->subscribe_newsletter))
		{
			return;
		}

		/* @var OSMembershipTablePlan $plan */
		$plan = Table::getInstance('Plan', 'OSMembershipTable');
		$plan->load($row->plan_id);
		$params        = new Registry($plan->params);
		$listIds       = trim($params->get('subscription_expired_acymailing_list_ids', ''));
		$assignListIds = trim($params->get('acymailing_expired_assign_list_ids', ''));

		if ($row->user_id)
		{
			$activePlans = OSMembershipHelperSubscription::getActiveMembershipPlans($row->user_id, [$row->id]);

			// He renewed his subscription before, so don't remove him from the lists
			if (in_array($row->plan_id, $activePlans))
			{
				return;
			}
		}

		if ($listIds != '' || $assignListIds != '')
		{
			require_once JPATH_ADMINISTRATOR . '/components/com_acym/helpers/helper.php';

			/* @var acymuserClass $userClass */
			$userClass               = acym_get('class.user');
			$userClass->checkVisitor = false;

			if (method_exists($userClass, 'getOneByEmail'))
			{
				$subId = $userClass->getOneByEmail($row->email);
			}
			else
			{
				$subId = $userClass->getUserIdByEmail($row->email);
			}

			if (is_object($subId))
			{
				$subId = $subId->id;
			}

			if (!$subId && $assignListIds)
			{
				// Create new subscriber as it is needed to assign user to the lists
				$myUser         = new stdClass();
				$myUser->email  = $row->email;
				$myUser->name   = $row->first_name . ' ' . $row->last_name;
				$myUser->cms_id = $row->user_id;
				$subId          = $userClass->save($myUser); //this
			}

			if ($subId)
			{
				if ($listIds)
				{
					try
					{
						$userClass->unsubscribe($subId, $listIds);
					}
					catch (Exception $e)
					{

					}
				}

				if ($assignListIds)
				{
					try
					{
						$userClass->subscribe($subId, $assignListIds);
					}
					catch (Exception $e)
					{

					}
				}
			}
		}
	}

	/**
	 * Method to synchronize custom fields data from Membership Pro to ACYMailing
	 *
	 * @param   OSMembershipTableSubscriber  $row
	 * @param   int                          $subId
	 */
	private function updateAcyMailingFieldsData($row, $subId)
	{
		if (!$subId)
		{
			return;
		}

		$db    = $this->db;
		$query = $db->getQuery(true);

		// Map fields
		$rowFields = OSMembershipHelper::getProfileFields($row->plan_id, true, null, $row->act);
		$data      = OSMembershipHelper::getProfileData($row, $row->plan_id, $rowFields);

		foreach ($rowFields as $rowField)
		{
			if (!$rowField->newsletter_field_mapping)
			{
				continue;
			}

			// Get ID of field
			$query->clear()
				->select('id')
				->from('#__acym_field')
				->where('name = ' . $db->quote($rowField->newsletter_field_mapping));
			$db->setQuery($query);
			$fieldId = $db->loadResult();

			if (!$fieldId)
			{
				continue;
			}

			$fieldValue = $data[$rowField->name] ?? '';

			$query->clear()
				->select('COUNT(*)')
				->from('#__acym_user_has_field')
				->where('user_id = ' . $subId)
				->where('field_id = ' . (int) $fieldId);
			$db->setQuery($query);
			$count = (int) $db->loadResult();

			if (!$count)
			{
				$query->clear()
					->insert('#__acym_user_has_field')
					->columns($db->quoteName(['user_id', 'field_id', 'value']))
					->values(implode(',', $db->quote([$subId, $fieldId, $fieldValue])));
			}
			else
			{
				$query->update('#__acym_user_has_field')
					->set($db->quoteName('value') . '=' . $db->quote($fieldValue))
					->where('user_id = ' . (int) $subId)
					->where('field_id = ' . (int) $fieldId);
			}

			try
			{
				$db->setQuery($query)
					->execute();
			}
			catch (Exception $e)
			{
				// Ignore the error for now
			}
		}
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

	/**
	 * Display form allows users to change settings on subscription plan add/edit screen
	 *
	 * @param   OSMembershipTablePlan  $row
	 */
	private function drawSettingForm($row)
	{
		require_once JPATH_ADMINISTRATOR . '/components/com_acym/helpers/helper.php';

		/* @var acymlistClass $listClass */
		$listClass = acym_get('class.list');
		$allLists  = $listClass->getAllWithIdName();

		$db    = $this->db;
		$query = $db->getQuery(true);
		$query->select('id, name')
			->from('#__osmembership_fields')
			->where('published = 1')
			->where('fieldtype = "Checkboxes"')
			->order('name');
		$db->setQuery($query);
		$mailingListFields = $db->loadObjectList();

		require PluginHelper::getLayoutPath($this->_type, $this->_name, 'form');
	}
}
