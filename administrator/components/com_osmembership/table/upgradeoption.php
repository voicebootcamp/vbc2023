<?php
/**
 * Upgraderule table
 */

use Joomla\CMS\Table\Table;

class OSMembershipTableUpgradeoption extends Table
{
	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver  $db  Database connector object
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__osmembership_upgraderules', 'id', $db);
	}
}
