<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2022 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

class OSMembershipModelDownloadids extends MPFModelList
{
	public function __construct($config = [])
	{
		$config['search_fields'] = ['tbl.download_id', 'tbl.domain', 'u.username'];

		parent::__construct($config);

		$this->state->setDefault('filter_order_Dir', 'DESC');
	}

	/**
	 * Override buildQueryColumns to get required fields
	 *
	 * @param   JDatabaseQuery  $query
	 *
	 * @return $this
	 */
	protected function buildQueryColumns(JDatabaseQuery $query)
	{
		$query->select('u.username');

		return parent::buildQueryColumns($query);
	}

	/**
	 * @param   JDatabaseQuery  $query
	 *
	 * @return $this
	 */
	protected function buildQueryJoins(JDatabaseQuery $query)
	{
		$query->leftJoin('#__users AS u ON tbl.user_id = u.id');

		return parent::buildQueryJoins($query);
	}

	/**
	 * @param   JDatabaseQuery  $query
	 *
	 * @return $this
	 */
	protected function buildQueryWhere(JDatabaseQuery $query)
	{
		if ($this->state->filter_state == 'P')
		{
			$query->where('tbl.published = 1');
		}
		elseif ($this->state->filter_state == 'U')
		{
			$query->where('tbl.published = 0');
		}

		return parent::buildQueryWhere($query);
	}
}
