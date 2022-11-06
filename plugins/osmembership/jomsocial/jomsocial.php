<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2022 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Table\Table;
use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;

class plgOSMembershipJomSocial extends CMSPlugin
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
		if (!file_exists(JPATH_ROOT . '/components/com_community/community.php'))
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
		$sql = 'SELECT fieldcode AS `value`, fieldcode AS `text` FROM #__community_fields WHERE published=1 AND fieldcode != ""';
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

		$synchronizer = new MPFSynchronizerJomsocial();

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
		if (!$this->isExecutable())
		{
			return [];
		}

		ob_start();
		$this->drawSettingForm($row);
		$form = ob_get_contents();
		ob_end_clean();

		return ['title' => Text::_('PLG_OSMEMBERSHIP_JOMSOCIAL_SETTINGS'),
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
		if (!$this->isExecutable())
		{
			return;
		}

		$params = new Registry($row->params);

		$params->set('jomsocial_group_ids', implode(',', $data['jomsocial_group_ids'] ?? []));
		$params->set('jomsocial_expried_group_ids', implode(',', $data['jomsocial_expried_group_ids'] ?? []));
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

		if (!$row->user_id)
		{
			return;
		}

		$db  = $this->db;
		$sql = 'SELECT COUNT(*) FROM #__community_users WHERE userid = ' . $row->user_id;
		$db->setQuery($sql);
		$count = $db->loadResult();

		if (!$count)
		{
			$sql = 'INSERT INTO #__community_users(userid) VALUES(' . $row->user_id . ')';
			$db->setQuery($sql);
			$db->execute();
		}

		$sql = 'SELECT id, fieldcode FROM #__community_fields WHERE published=1 AND fieldcode != ""';
		$db->setQuery($sql);
		$rowFields = $db->loadObjectList();
		$fieldList = [];

		foreach ($rowFields as $rowField)
		{
			$fieldList[$rowField->fieldcode] = $rowField->id;
		}

		$sql = 'SELECT name, field_mapping FROM #__osmembership_fields WHERE field_mapping != "" AND field_mapping IS NOT NULL AND is_core = 1';
		$db->setQuery($sql);
		$fields = $db->loadObjectList();

		$fieldValues = [];

		foreach ($fields as $field)
		{
			$fieldName = $field->field_mapping;

			if ($fieldName)
			{
				$fieldValues[$fieldName] = $row->{$field->name};
			}
		}

		$sql = 'SELECT a.field_mapping, b.field_value FROM #__osmembership_fields AS a '
			. ' INNER JOIN #__osmembership_field_value AS b '
			. ' ON a.id = b.field_id '
			. ' WHERE b.subscriber_id=' . $row->id;
		$db->setQuery($sql);
		$fields = $db->loadObjectList();

		foreach ($fields as $field)
		{
			if ($field->field_mapping)
			{
				$fieldValues[$field->field_mapping] = $field->field_value;
			}
		}

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
						// Delete old data of exists
						$query->clear()
							->delete('#__community_fields_values')
							->where('user_id = ' . $row->user_id)
							->where('field_id = ' . (int) $fieldId);
						$db->setQuery($query)
							->execute();

						if (is_string($fieldValue) && is_array(json_decode($fieldValue)))
						{
							$fieldValue = implode(',', json_decode($fieldValue));
						}

						$fieldValue = $db->quote($fieldValue);

						$sql = "INSERT INTO #__community_fields_values(user_id, field_id, `value`, `access`) VALUES($row->user_id, $fieldId, $fieldValue, 1)";
						$db->setQuery($sql);
						$db->execute();
					}
				}
			}
		}

		$plan = Table::getInstance('Plan', 'OSMembershipTable');
		$plan->load($row->plan_id);
		$params = new Registry($plan->params);
		$groups = explode(',', $params->get('jomsocial_group_ids'));
		$groups = array_filter(ArrayHelper::toInteger($groups));

		if (count($groups))
		{
			$sql = 'REPLACE INTO `#__community_groups_members` (`memberid`,`groupid`,`approved`,`permissions`) VALUES ';

			$values = [];

			foreach ($groups as $group)
			{
				$values[] = '(' . $db->Quote($row->user_id) . ', ' . $db->Quote($group) . ', 1, 0)';
			}

			$sql .= implode(', ', $values);

			$db->setQuery($sql);
			$db->execute();
		}

		return true;
	}

	/**
	 * Run when a membership activated
	 *
	 * @param   OSMembershipTableSubscriber  $row
	 *
	 * @return boolean
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
		$sql = 'SELECT COUNT(*) FROM #__community_users WHERE userid=' . $row->user_id;
		$db->setQuery($sql);
		$count = $db->loadResult();

		if (!$count)
		{
			$sql = 'INSERT INTO #__community_users(userid) VALUES(' . $row->user_id . ')';
			$db->setQuery($sql);
			$db->execute();
		}

		$sql = 'SELECT id, fieldcode FROM #__community_fields WHERE published=1 AND fieldcode != ""';
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

		foreach ($fields as $field)
		{
			$fieldName = $field->field_mapping;

			if ($fieldName)
			{
				$fieldValues[$fieldName] = $row->{$field->name};
			}
		}

		$sql = 'SELECT a.field_mapping, b.field_value FROM #__osmembership_fields AS a '
			. ' INNER JOIN #__osmembership_field_value AS b '
			. ' ON a.id = b.field_id '
			. ' WHERE b.subscriber_id=' . $row->id;
		$db->setQuery($sql);
		$fields = $db->loadObjectList();

		foreach ($fields as $field)
		{
			if ($field->field_mapping)
			{
				$fieldValues[$field->field_mapping] = $field->field_value;
			}
		}

		if (count($fieldValues))
		{
			$query = $db->getQuery(true);

			foreach ($fieldValues as $fieldCode => $fieldValue)
			{
				if (isset($fieldList[$fieldCode]))
				{
					$fieldId = $fieldList[$fieldCode];

					if ($fieldId)
					{
						// Delete old data of exists
						$query->clear()
							->delete('#__community_fields_values')
							->where('user_id = ' . $row->user_id)
							->where('field_id = ' . (int) $fieldId);
						$db->setQuery($query)
							->execute();

						if (is_string($fieldValue) && is_array(json_decode($fieldValue)))
						{
							$fieldValue = implode(',', json_decode($fieldValue));
						}

						$fieldValue = $db->quote($fieldValue);
						$sql        = "REPLACE INTO #__community_fields_values(user_id, field_id, `value`, `access`) VALUES($row->user_id, $fieldId, $fieldValue, 1)";
						$db->setQuery($sql);
						$db->execute();
					}
				}
			}
		}

		$plan = Table::getInstance('Plan', 'OSMembershipTable');
		$plan->load($row->plan_id);
		$params = new Registry($plan->params);
		$groups = explode(',', $params->get('jomsocial_group_ids'));
		$groups = array_filter(ArrayHelper::toInteger($groups));

		if (count($groups))
		{
			$sql = 'REPLACE INTO `#__community_groups_members` (`memberid`,`groupid`,`approved`,`permissions`) VALUES ';

			$values = [];

			foreach ($groups as $group)
			{
				$values[] = '(' . $db->Quote($row->user_id) . ', ' . $db->Quote($group) . ', 1, 0)';
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
		$groups = explode(',', $params->get('jomsocial_expried_group_ids'));
		$groups = array_filter(ArrayHelper::toInteger($groups));

		$db = $this->db;

		foreach ($groups as $group)
		{
			$sql = 'DELETE FROM #__community_groups_members WHERE groupid=' . $group . ' AND memberid=' . $row->user_id;
			$db->setQuery($sql);
			$db->execute();
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
	 * Display form allows users to change setting for this subscription plan
	 *
	 * @param   OSMembershipTablePlan  $row
	 */
	private function drawSettingForm($row)
	{
		$sql = 'SELECT id, name FROM #__community_groups WHERE published = 1 ORDER BY name ';
		$this->db->setQuery($sql);

		$options   = [];
		$options[] = HTMLHelper::_('select.option', 0, Text::_('Choose Groups'), 'id', 'name');
		$options   = array_merge($options, $this->db->loadObjectList());

		require PluginHelper::getLayoutPath($this->_type, $this->_name, 'form');
	}
}
