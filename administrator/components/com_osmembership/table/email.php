<?php
/**
 * Email table
 */

use Joomla\CMS\Table\Table;

class OSMembershipTableEmail extends Table
{
	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver  $db  Database connector object
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__osmembership_emails', 'id', $db);
	}
}
