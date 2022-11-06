<?php
/**
 * Plugin table
 */

use Joomla\CMS\Table\Table;

class OSMembershipTablePlugin extends Table
{
	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver  $db  Database connector object
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__osmembership_plugins', 'id', $db);
	}
}
