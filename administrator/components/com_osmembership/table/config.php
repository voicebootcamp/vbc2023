<?php
/**
 * Config table
 */

use Joomla\CMS\Table\Table;

class OSMembershipTableConfig extends Table
{
	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver  $db  Database connector object
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__osmembership_configs', 'id', $db);
	}
}
