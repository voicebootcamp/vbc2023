<?php
/**
 * Message table
 */

use Joomla\CMS\Table\Table;

class OSMembershipTableMessage extends Table
{
	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver  $db  Database connector object
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__osmembership_messages', 'id', $db);
	}
}
