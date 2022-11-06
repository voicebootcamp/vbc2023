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

class OSMembershipModelCategories extends MPFModelList
{
	/**
	 * Instantiate the model.
	 *
	 * @param   array  $config  configuration data for the model
	 */
	public function __construct($config = [])
	{
		parent::__construct($config);

		$this->state->insert('id', 'int', 0);
	}

	/**
	 * Method to get categories data
	 *
	 * @param   boolean  $returnIterator
	 *
	 * @access public
	 * @return array
	 */
	public function getData($returnIterator = false)
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->data))
		{
			$rows = parent::getData();

			for ($i = 0, $n = count($rows); $i < $n; $i++)
			{
				$row              = $rows[$i];
				$row->total_plans = OSMembershipHelper::countPlans($row->id);
			}

			$this->data = $rows;
		}

		return $this->data;
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

		$query->select('tbl.*');

		if ($fieldSuffix)
		{
			OSMembershipHelperDatabase::getMultilingualFields($query, ['tbl.title', 'tbl.description'], $fieldSuffix);
		}

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
		$query->where('tbl.published = 1')
			->where('tbl.parent_id = ' . $this->state->id)
			->where('tbl.access IN (' . implode(',', Factory::getUser()->getAuthorisedViewLevels()) . ')');

		return $this;
	}
}
