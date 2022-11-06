<?php
/**
 * Renewoption table
 */

use Joomla\CMS\Table\Table;

class OSMembershipTableRenewoption extends Table
{
	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver  $db  Database connector object
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__osmembership_renewrates', 'id', $db);
	}
}
