<?php

use Joomla\CMS\Factory;

/**
 * @package     MPF
 * @subpackage  Synchronizer
 *
 * @copyright   Copyright (C) 2016 Ossolution Team, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
class MPFSynchronizerJoomla
{
	public function getData($userId, $mappings)
	{
		$data  = [];
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('profile_key, profile_value')
			->from('#__user_profiles')
			->where('user_id=' . $userId);
		$db->setQuery($query);
		$rows = $db->loadObjectList('profile_key');

		foreach ($mappings as $fieldName => $mappingFieldName)
		{
			$key = 'profile.' . $mappingFieldName;

			if ($mappingFieldName && isset($rows[$key]))
			{
				$data[$fieldName] = json_decode($rows[$key]->profile_value, true);
			}
		}

		$fields = OSMembershipHelper::getUserFields();

		if (count($fields))
		{
			JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_fields/models', 'FieldsModel');

			/* @var FieldsModelField $model */
			$model = JModelLegacy::getInstance('Field', 'FieldsModel', ['ignore_request' => true]);

			$fieldIds = [];

			foreach ($mappings as $fieldName => $mappingFieldName)
			{
				if ($mappingFieldName && isset($fields[$mappingFieldName]))
				{
					$fieldIds[] = $fields[$mappingFieldName]->id;
				}
			}

			$fieldValues = $model->getFieldValues($fieldIds, $userId);

			foreach ($mappings as $fieldName => $mappingFieldName)
			{
				if ($mappingFieldName && isset($fields[$mappingFieldName]))
				{
					$fieldId = $fields[$mappingFieldName]->id;

					if (isset($fieldValues[$fieldId]))
					{
						$data[$fieldName] = $fieldValues[$fieldId];
					}
				}
			}
		}

		return $data;
	}
}
