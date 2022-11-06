<?php
/**
 * Renewaldiscount table
 */

use Joomla\CMS\Table\Table;

class OSMembershipTableRenewaldiscount extends Table
{
	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver  $db  Database connector object
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__osmembership_renewaldiscounts', 'id', $db);
	}
}
