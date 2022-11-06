<?php
/**
 * State Table Class
 */

use Joomla\CMS\Table\Table;

class OSMembershipTableState extends Table
{
	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver  $db  Database connector object
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__osmembership_states', 'id', $db);
	}
}
