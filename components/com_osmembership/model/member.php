<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2022 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die;

class OSMembershipModelMember extends MPFModel
{
	/**
	 * Model constructor.
	 *
	 * @param   array  $config
	 */
	public function __construct(array $config)
	{
		parent::__construct($config);

		$this->state->insert('id', 'int', 0);
	}

	/**
	 * Get profile data
	 *
	 * @return mixed
	 */
	public function getData()
	{
		$db    = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*')
			->from('#__osmembership_subscribers')
			->where('id = ' . (int) $this->state->get('id'));
		$db->setQuery($query);

		$row = $db->loadObject();

		if ($row->state)
		{
			$row->state = OSMembershipHelper::getStateName($row->country, $row->state);
		}

		return $row;
	}
}
