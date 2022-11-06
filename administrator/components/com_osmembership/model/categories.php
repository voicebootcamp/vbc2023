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

class OSMembershipModelCategories extends MPFModelList
{
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
			$db    = $this->getDbo();
			$query = $db->getQuery(true);
			$this->buildQueryColumns($query)
				->buildQueryFrom($query)
				->buildQueryJoins($query)
				->buildQueryWhere($query)
				->buildQueryGroup($query)
				->buildQueryOrder($query);
			$db->setQuery($query);
			$rows = $db->loadObjectList();

			$children = [];

			// first pass - collect children
			if (count($rows))
			{
				foreach ($rows as $v)
				{
					$pt   = $v->parent_id;
					$list = @$children[$pt] ? $children[$pt] : [];
					array_push($list, $v);
					$children[$pt] = $list;
				}
			}
			$list             = HTMLHelper::_('menu.treerecurse', 0, '', [], $children, 9999);
			$total            = count($list);
			$this->pagination = new JPagination($total, $this->state->limitstart, $this->state->limit);
			// slice out elements based on limits
			$list       = array_slice($list, $this->pagination->limitstart, $this->pagination->limit);
			$this->data = $list;
		}

		return $this->data;
	}

	/**
	 * Builds SELECT columns list for the query
	 */
	protected function buildQueryColumns(JDatabaseQuery $query)
	{
		$query->select('tbl.*, vl.title AS access_level');

		return $this;
	}

	/**
	 * Builds LEFT JOINS clauses for the query
	 */
	protected function buildQueryJoins(JDatabaseQuery $query)
	{
		$query->leftJoin('#__viewlevels AS vl ON vl.id = tbl.access');

		return $this;
	}
}
