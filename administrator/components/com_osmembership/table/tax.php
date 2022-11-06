<?php
/**
 * Tax table
 */

use Joomla\CMS\Table\Table;

class OSMembershipTableTax extends Table
{
	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver  $db  Database connector object
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__osmembership_taxes', 'id', $db);
	}
}
