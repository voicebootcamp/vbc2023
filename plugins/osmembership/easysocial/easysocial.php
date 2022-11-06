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
use Joomla\CMS\Table\Table;
use Joomla\Registry\Registry;

class plgOSMembershipEasySocial extends CMSPlugin
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
		if (!file_exists(JPATH_ROOT . '/components/com_easysocial/easysocial.php'))
		{
			return;
		}

		parent::__construct($subject, $config);
	}

	/**
	 * Method to get list of custom fields in Jomsocial used to map with fields in Membership Pro
	 *
	 * Method is called on custom field add / edit page from backend of Membership Pro
	 *
	 * @return mixed
	 */
	public function onGetFields()
	{
		if (!$this->app)
		{
			return [];
		}

		$db  = $this->db;
		$sql = 'SELECT title AS `value`, title AS `text` FROM #__social_fields WHERE state=1 AND title != ""';
		$db->setQuery($sql);

		return $db->loadObjectList();
	}

	/**
	 * Method to get data stored in Jomsocial profile of the given user
	 *
	 * @param   int    $userId
	 * @param   array  $mappings
	 *
	 * @return array
	 */
	public function onGetProfileData($userId, $mappings = [])
	{
		if (!$this->app)
		{
			return [];
		}

		$synchronizer = new MPFSynchronizerEasysocial();

		return $synchronizer->getData($userId, $mappings);
	}

	/**
	 * Render settings form allows admin to choose what Jomsocial groups subscribers will be assigned to when they sign up for this plan
	 *
	 * Method is called on plan add/edit page
	 *
	 * @param   OSMembershipTablePlan  $row  The plan record
	 *
	 * @return array
	 */
	public function onEditSubscriptionPlan($row)
	{
		if (!$this->app)
		{
			return [];
		}

		ob_start();
		$this->drawSettingForm($row);
		$form = ob_get_contents();
		ob_end_clean();

		return ['title' => Text::_('PLG_OSMEMBERSHIP_EASYSOCIAL_SETTINGS'),
		        'form'  => $form,
		];
	}

	/**
	 * Method to store settings into database
	 *
	 * @param   OSMembershipTablePlan  $row    The plan record
	 * @param   array                  $data   The form post data
	 * @param   bool                   $isNew  True if new plan is created, false if updating the plan
	 */
	public function onAfterSaveSubscriptionPlan($context, $row, $data, $isNew)
	{
		if (!$this->app)
		{
			return;
		}

		$params = new Registry($row->params);

		$params->set('easysocial_group_ids', implode(',', $data['easysocial_group_ids'] ?? []));
		$params->set('easysocial_expried_group_ids', implode(',', $data['easysocial_expried_group_ids'] ?? []));
		$row->params = $params->toString();

		$row->store();
	}

	/**
	 * Method to create Jomsocial account for subscriber and assign him to selected Jomsocial groups when subscription is active
	 *
	 * @param   OSMembershipTableSubscriber  $row
	 *
	 * @return bool
	 */
	public function onMembershipActive($row)
	{
		if (!$this->app)
		{
			return;
		}

		$db  = $this->db;
		$sql = 'SELECT COUNT(*) FROM #__social_users WHERE user_id=' . $row->user_id;
		$db->setQuery($sql);
		$count = $db->loadResult();

		if (!$count)
		{
			$sql = 'INSERT INTO #__social_users(user_id) VALUES(' . $row->user_id . ')';
			$db->setQuery($sql);
			$db->execute();
		}

		$sql = 'SELECT id, title FROM #__social_fields WHERE state=1 AND title != ""';
		$db->setQuery($sql);
		$rowFields = $db->loadObjectList();
		$fieldList = [];

		foreach ($rowFields as $rowField)
		{
			$fieldList[$rowField->fieldcode] = $rowField->id;
		}

		$sql = 'SELECT name, field_mapping FROM #__osmembership_fields WHERE field_mapping != "" AND field_mapping IS NOT NULL AND is_core = 1';
		$db->setQuery($sql);
		$fields      = $db->loadObjectList();
		$fieldValues = [];

		if (count($fields))
		{
			foreach ($fields as $field)
			{
				$fieldName = $field->field_mapping;

				if ($fieldName)
				{
					$fieldValues[$fieldName] = $row->{$field->name};
				}
			}
		}

		$sql = 'SELECT a.field_mapping, b.field_value FROM #__osmembership_fields AS a '
			. ' INNER JOIN #__osmembership_field_value AS b '
			. ' ON a.id = b.field_id '
			. ' WHERE b.subscriber_id=' . $row->id;
		$db->setQuery($sql);
		$fields = $db->loadObjectList();

		if (count($fields))
		{
			foreach ($fields as $field)
			{
				if ($field->field_mapping)
				{
					$fieldValues[$field->field_mapping] = $field->field_value;
				}
			}
		}

		$db    = $this->db;
		$query = $db->getQuery(true);

		if (count($fieldValues))
		{
			foreach ($fieldValues as $fieldCode => $fieldValue)
			{
				if (isset($fieldList[$fieldCode]))
				{
					$fieldId = $fieldList[$fieldCode];

					if ($fieldId)
					{
						$query->clear()
							->delete('#__social_fields_data')
							->where($db->quoteName('uid') . ' = ' . $row->user_id)
							->where('field_id = ' . (int) $fieldId);
						$db->setQuery($query)
							->execute();
						$fieldValue = $db->quote($fieldValue);
						$sql        = "INSERT INTO #__social_fields_data(uid, field_id, `data`) VALUES($row->user_id, $fieldId, $fieldValue)";
						$db->setQuery($sql);
						$db->execute();
					}
				}
			}
		}

		$plan = Table::getInstance('Plan', 'OSMembershipTable');
		$plan->load($row->plan_id);
		$params = new Registry($plan->params);
		$groups = explode(',', $params->get('easysocial_group_ids'));

		if (count($groups))
		{
			$sql = 'REPLACE INTO `#__social_clusters_nodes` (`cluster_id`,`uid`,`type`,`created`,`state`,`owner`,`admin`,`invited_by`) VALUES ';

			$values = [];

			foreach ($groups as $group)
			{
				$values[] = '(' . $db->quote($group) . ', ' . $db->quote($row->user_id) . ',' . $db->quote('user') . ',' . $db->quote(Factory::getDate()) . ', 1, 0, 0, 0)';
			}

			$sql .= implode(', ', $values);

			$db->setQuery($sql);
			$db->execute();
		}

		return true;
	}

	/**
	 * Update EasySocial profile data when user update his profile data in Membership Pro
	 *
	 * @param   OSMembershipTableSubscriber  $row
	 *
	 * @return bool|void
	 */
	public function onProfileUpdate($row)
	{
		if (!$this->app)
		{
			return;
		}

		if (!$row->user_id)
		{
			return;
		}

		$db  = $this->db;
		$sql = 'SELECT COUNT(*) FROM #__social_users WHERE user_id=' . $row->user_id;
		$db->setQuery($sql);
		$count = $db->loadResult();

		if (!$count)
		{
			$sql = 'INSERT INTO #__social_users(user_id) VALUES(' . $row->user_id . ')';
			$db->setQuery($sql);
			$db->execute();
		}

		$sql = 'SELECT id, title FROM #__social_fields WHERE state=1 AND title != ""';
		$db->setQuery($sql);
		$rowFields = $db->loadObjectList();
		$fieldList = [];

		foreach ($rowFields as $rowField)
		{
			$fieldList[$rowField->title] = $rowField->id;
		}

		$sql = 'SELECT name, field_mapping FROM #__osmembership_fields WHERE field_mapping != "" AND field_mapping IS NOT NULL AND is_core = 1';
		$db->setQuery($sql);
		$fields      = $db->loadObjectList();
		$fieldValues = [];

		if (count($fields))
		{
			foreach ($fields as $field)
			{
				$fieldName = $field->field_mapping;

				if ($fieldName)
				{
					$fieldValues[$fieldName] = $row->{$field->name};
				}
			}
		}

		$sql = 'SELECT a.field_mapping, b.field_value FROM #__osmembership_fields AS a '
			. ' INNER JOIN #__osmembership_field_value AS b '
			. ' ON a.id = b.field_id '
			. ' WHERE b.subscriber_id=' . $row->id;
		$db->setQuery($sql);
		$fields = $db->loadObjectList();

		if (count($fields))
		{
			foreach ($fields as $field)
			{
				if ($field->field_mapping)
				{
					$fieldValues[$field->field_mapping] = $field->field_value;
				}
			}
		}

		$db    = $this->db;
		$query = $db->getQuery(true);

		if (count($fieldValues))
		{
			foreach ($fieldValues as $fieldCode => $fieldValue)
			{
				if (isset($fieldList[$fieldCode]))
				{
					$fieldId = $fieldList[$fieldCode];

					if ($fieldId)
					{
						$query->clear()
							->delete('#__social_fields_data')
							->where($db->quoteName('uid') . ' = ' . $row->user_id)
							->where('field_id = ' . (int) $fieldId);
						$db->setQuery($query)
							->execute();

						$fieldValue = $db->quote($fieldValue);
						$sql        = "REPLACE INTO #__social_fields_data(field_id, uid, `data`) VALUES($fieldId, $row->user_id, $fieldValue)";
						$db->setQuery($sql);
						$db->execute();
					}
				}
			}
		}

		$plan = Table::getInstance('Plan', 'OSMembershipTable');
		$plan->load($row->plan_id);
		$params = new Registry($plan->params);
		$groups = explode(',', $params->get('easysocial_group_ids'));

		if (count($groups))
		{
			$sql = 'REPLACE INTO `#__social_clusters_nodes` (`cluster_id`,`uid`,`type`,`created`,`state`,`owner`,`admin`,`invited_by`) VALUES ';

			$values = [];

			foreach ($groups as $group)
			{
				$values[] = '(' . $db->quote($group) . ', ' . $db->quote($row->user_id) . ',' . $db->quote('user') . ',' . $db->quote(Factory::getDate()) . ', 1, 1, 0, 0)';
			}

			$sql .= implode(', ', $values);

			$db->setQuery($sql);
			$db->execute();
		}

		return true;
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

		if (!$row->user_id)
		{
			return;
		}

		$plan = Table::getInstance('Plan', 'OSMembershipTable');
		$plan->load($row->plan_id);
		$params = new Registry($plan->params);
		$groups = explode(',', $params->get('easysocial_expried_group_ids'));

		if (count($groups))
		{
			foreach ($groups as $group)
			{
				$group = (int) $group;

				if ($group)
				{
					$db  = $this->db;
					$sql = 'DELETE FROM #__social_clusters_nodes WHERE id=' . $group . ' AND uid=' . $row->user_id;
					$db->setQuery($sql);
					$db->execute();
				}
			}
		}
	}

	/**
	 * Display form allows users to change setting for this subscription plan
	 *
	 * @param   OSMembershipTablePlan  $row
	 */
	private function drawSettingForm($row)
	{
		$sql = 'SELECT id, title AS name FROM #__social_clusters WHERE state = 1 ORDER BY title ';
		$this->db->setQuery($sql);
		$options   = [];
		$options[] = JHTML::_('select.option', 0, Text::_('OSM_CHOOSE_GROUPS'), 'id', 'name');
		$options   = array_merge($options, $this->db->loadObjectList());

		require PluginHelper::getLayoutPath($this->_type, $this->_name, 'form');
	}
}
