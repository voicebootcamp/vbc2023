<?php
/**
 * Renewal Discount Table
 */

use Joomla\CMS\Table\Table;

class OSMembershipTableDiscount extends Table
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
