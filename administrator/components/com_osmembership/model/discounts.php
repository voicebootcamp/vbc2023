<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2022 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

class OSMembershipModelDiscounts extends MPFModelList
{
	protected $clearJoin = false;

	/**
	 * Instantiate the model.
	 *
	 * @param   array  $config  configuration data for the model
	 */
	public function __construct($config = [])
	{
		$config['table'] = '#__osmembership_renewaldiscounts';

		$config['search_fields'] = ['tbl.title', 'b.title'];

		parent::__construct($config);

		$this->state->insert('filter_plan_id', 'int', 0);
	}

	protected function buildQueryColumns(JDatabaseQuery $query)
	{
		$query->select(['tbl.*'])
			->select('b.title AS plan_title');

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
		$query->leftJoin('#__osmembership_plans AS b ON tbl.plan_id = b.id');

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

		if ($this->state->filter_plan_id)
		{
			$query->where('tbl.plan_id = ' . $this->state->filter_plan_id);
		}

		return $this;
	}
}
