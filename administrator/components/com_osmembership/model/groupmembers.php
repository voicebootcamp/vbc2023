<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2022 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

class OSMembershipModelGroupmembers extends MPFModelList
{
	/**
	 * Instantiate the model.
	 *
	 * @param   array  $config  configuration data for the model
	 */
	public function __construct($config = [])
	{
		$config['table']         = '#__osmembership_subscribers';
		$config['search_fields'] = ['tbl.first_name', 'tbl.last_name', 'tbl.email'];

		parent::__construct($config);

		$this->state->insert('filter_plan_id', 'int', 0)
			->insert('filter_group_admin_id', 0)
			->insert('filter_published', 0)
			->setDefault('filter_order', 'tbl.created_date')
			->setDefault('filter_order_Dir', 'DESC');

		// Dynamic searchable fields
		$db    = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('name')
			->from('#__osmembership_fields')
			->where('published = 1')
			->where('is_searchable = 1');
		$db->setQuery($query);
		$searchableFields = $db->loadColumn();

		foreach ($searchableFields as $field)
		{
			$field = 'tbl.' . $field;

			if (!in_array($field, $this->searchFields))
			{
				$this->searchFields[] = $field;
			}
		}
	}

	protected function beforeReturnData($rows)
	{
		if (count($rows))
		{
			$groupAdminIds = [];

			foreach ($rows as $row)
			{
				$groupAdminIds[] = $row->group_admin_id;
			}

			$db    = $this->getDbo();
			$query = $db->getQuery(true);
			$query->select('id, username, name')
				->from('#__users')
				->where('id IN (' . implode(',', $groupAdminIds) . ')');
			$db->setQuery($query);
			$groupAdmins = $db->loadObjectList('id');

			for ($i = 0, $n = count($rows); $i < $n; $i++)
			{
				$row = $rows[$i];

				if (isset($groupAdmins[$row->group_admin_id]))
				{
					$row->group_admin = $groupAdmins[$row->group_admin_id]->username;
				}
				else
				{
					$row->group_admin = '';
				}
			}
		}
	}

	/**
	 * Builds SELECT columns list for the query
	 *
	 * @param   JDatabaseQuery  $query
	 *
	 * @return $this
	 */
	protected function buildQueryColumns(JDatabaseQuery $query)
	{
		$fieldSuffix = OSMembershipHelper::getFieldSuffix();

		$query->select('tbl.*')
			->select('b.title' . $fieldSuffix . ' AS plan_title, b.lifetime_membership')
			->select('c.username AS username');

		return $this;
	}

	/**
	 * Builds JOINS clauses for the query
	 *
	 * @param   JDatabaseQuery  $query
	 *
	 * @return $this
	 */
	protected function buildQueryJoins(JDatabaseQuery $query)
	{
		$query->leftJoin('#__osmembership_plans AS b  ON tbl.plan_id = b.id')
			->leftJoin('#__users AS c ON tbl.user_id = c.id');

		return $this;
	}

	/**
	 * Builds a WHERE clause for the query
	 *
	 * @param   JDatabaseQuery  $query
	 *
	 * @return $this
	 */
	protected function buildQueryWhere(JDatabaseQuery $query)
	{
		parent::buildQueryWhere($query);

		$config = OSMembershipHelper::getConfig();

		$query->where('tbl.group_admin_id > 0');

		if ($this->state->filter_plan_id)
		{
			$query->where('tbl.plan_id = ' . $this->state->filter_plan_id);
		}

		if ($this->state->filter_group_admin_id)
		{
			$query->where('tbl.group_admin_id = ' . $this->state->filter_group_admin_id);
		}

		if ($this->state->filter_published)
		{
			$query->where('tbl.published = ' . $this->state->filter_published);
		}

		if (!$config->get('show_incomplete_payment_subscriptions', 1))
		{
			$query->where('(tbl.published != 0 OR tbl.payment_method LIKE "os_offline%")');
		}

		return $this;
	}

	/**
	 * Apply search filter for the query
	 *
	 * @param   JDatabaseQuery  $query
	 *
	 * @return MPFModelList|void
	 */
	protected function applySearchFilter(JDatabaseQuery $query)
	{
		// Special case for searching for group admin
		if (stripos($this->state->filter_search, 'group_admin_id:') === 0)
		{
			$query->where('tbl.id = ' . (int) substr($this->state->filter_search, 15));

			return;
		}

		parent::applySearchFilter($query);
	}

	/**
	 * Get registrants custom fields data
	 *
	 * @param   array  $fields
	 *
	 * @return array
	 */
	public function getFieldsData($fields)
	{
		$fieldsData = [];
		$rows       = $this->data;

		if (count($rows) && count($fields))
		{
			$db    = $this->getDbo();
			$query = $db->getQuery(true);

			$subscriptionIds = [];

			foreach ($rows as $row)
			{
				$subscriptionIds[] = $row->id;
			}

			$query->select('subscriber_id, field_id, field_value')
				->from('#__osmembership_field_value')
				->where('subscriber_id IN (' . implode(',', $subscriptionIds) . ')')
				->where('field_id IN (' . implode(',', $fields) . ')');
			$db->setQuery($query);
			$rowFieldValues = $db->loadObjectList();

			foreach ($rowFieldValues as $rowFieldValue)
			{
				$fieldValue = $rowFieldValue->field_value;

				if (is_string($fieldValue) && is_array(json_decode($fieldValue)))
				{
					$fieldValue = implode(', ', json_decode($fieldValue));
				}

				$fieldsData[$rowFieldValue->subscriber_id][$rowFieldValue->field_id] = $fieldValue;
			}
		}

		return $fieldsData;
	}
}
