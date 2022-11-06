<?php
/**
 * Country table
 */

use Joomla\CMS\Table\Table;

class OSMembershipTableCountry extends Table
{
	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver  $db  Database connector object
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__osmembership_countries', 'id', $db);
	}
}
