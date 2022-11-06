<?php
/**
 * Category table
 */

use Joomla\CMS\Table\Table;

class OSMembershipTableCategory extends Table
{
	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver  $db  Database connector object
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__osmembership_categories', 'id', $db);
	}
}
