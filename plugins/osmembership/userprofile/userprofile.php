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
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Plugin\PluginHelper;

class plgOSMembershipUserprofile extends CMSPlugin
{
	/**
	 * Database object.
	 *
	 * @var    JDatabaseDriver
	 */
	protected $db;

	/**
	 * Run when a membership activated
	 *
	 * @param   OSMembershipTableSubscriber  $row
	 */
	public function onAfterStoreSubscription($row)
	{
		if (!$row->user_id)
		{
			return;
		}

		// Require library + register autoloader
		require_once JPATH_ADMINISTRATOR . '/components/com_osmembership/loader.php';

		$db    = $this->db;
		$query = $db->getQuery(true);

		$userId = $row->user_id;

		// Update Name of users based on first name and last name from profile
		$user = Factory::getUser($userId);
		$user->set('name', rtrim($row->first_name . ' ' . $row->last_name));
		$user->save(true);

		// Get subscribers data
		$rowFields      = OSMembershipHelper::getProfileFields($row->plan_id, true, null, $row->act);
		$subscriberData = OSMembershipHelper::getProfileData($row, $row->plan_id, $rowFields);

		if (!empty($subscriberData['country']) && !empty($subscriberData['state']))
		{
			$subscriberData['state'] = OSMembershipHelper::getStateName($subscriberData['country'], $subscriberData['state']);
		}

		$userProfilePluginEnabled = PluginHelper::isEnabled('user', 'profile');
		$profileFields            = ['address1', 'address2', 'city', 'region', 'country', 'postal_code', 'phone', 'website', 'favoritebook', 'aboutme', 'dob'];
		$userFields               = OSMembershipHelper::getUserFields();
		$userFieldsName           = array_keys($userFields);
		$profileFieldsMapping     = [];
		$userFieldsMapping        = [];

		foreach ($rowFields as $rowField)
		{
			if (!$rowField->profile_field_mapping)
			{
				continue;
			}

			if ($userProfilePluginEnabled && in_array($rowField->profile_field_mapping, $profileFields))
			{
				$profileFieldsMapping[$rowField->profile_field_mapping] = $rowField->name;

				continue;
			}

			if (in_array($rowField->profile_field_mapping, $userFieldsName))
			{
				$userFieldsMapping[$rowField->profile_field_mapping] = $rowField->name;
			}
		}

		// Store user profile data
		if (count($profileFieldsMapping) > 0)
		{
			//Delete old profile data
			$fields = $keys = array_keys($profileFieldsMapping);

			for ($i = 0, $n = count($keys); $i < $n; $i++)
			{
				$keys[$i] = 'profile.' . $keys[$i];
			}

			$query->delete('#__user_profiles')
				->where('user_id = ' . $userId)
				->where('profile_key IN (' . implode(',', $db->quote($keys)) . ')');
			$db->setQuery($query);
			$db->execute();

			$order = 1;

			$query->clear()
				->insert('#__user_profiles');

			foreach ($fields as $field)
			{
				$fieldMapping = $profileFieldsMapping[$field];

				if (isset($subscriberData[$fieldMapping]))
				{
					$value = $subscriberData[$fieldMapping];
				}
				else
				{
					$value = '';
				}

				$query->values(implode(',', $db->quote([$userId, 'profile.' . $field, json_encode($value), $order++])));
			}

			$db->setQuery($query);
			$db->execute();
		}

		if (count($userFields) > 0)
		{
			JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_fields/models', 'FieldsModel');

			/* @var FieldsModelField $model */
			$model = JModelLegacy::getInstance('Field', 'FieldsModel', ['ignore_request' => true]);

			foreach ($userFields as $field)
			{
				$fieldName = $field->name;

				if (isset($userFieldsMapping[$fieldName]))
				{
					$fieldMapping = $userFieldsMapping[$fieldName];

					if (isset($subscriberData[$fieldMapping]))
					{
						$fieldValue = $subscriberData[$fieldMapping];
					}
					else
					{
						$fieldValue = '';
					}

					$model->setFieldValue($field->id, $userId, $fieldValue);
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
		$this->onAfterStoreSubscription($row);
	}

	/**
	 * Plugin triggered when membership active
	 *
	 * @param   OSMembershipTableSubscriber  $row  The subscription record
	 */
	public function onMembershipActive($row)
	{
		$config = OSMembershipHelper::getConfig();

		if ($config->create_account_when_membership_active === '1')
		{
			$this->onAfterStoreSubscription($row);
		}
	}

	/**
	 * Plugin triggered when user update his profile
	 *
	 * @param   OSMembershipTableSubscriber  $row  The subscription record
	 */
	public function onMembershipUpdate($row)
	{
		if ($this->params->get('update_profile_data_when_admin_update_subscription'))
		{
			$this->onAfterStoreSubscription($row);
		}
	}
}
