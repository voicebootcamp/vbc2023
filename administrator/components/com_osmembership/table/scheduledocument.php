<?php
/**
 * OSMembershipTableScheduleDocument table
 *
 * @property $id
 * @property $plan_id
 * @property $number_days
 * @property $ordering
 */

use Joomla\CMS\Table\Table;

class OSMembershipTableScheduleDocument extends Table
{
	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver  $db  Database connector object
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__osmembership_scheduledocuments', 'id', $db);
	}
}
