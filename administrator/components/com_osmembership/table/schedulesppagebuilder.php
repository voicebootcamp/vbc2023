<?php
/**
 * ScheduleContent table
 *
 * @property $id
 * @property $plan_id
 * @property $page_id
 * @property $number_days
 * @property $release_date
 * @property $ordering
 */

use Joomla\CMS\Table\Table;

class OSMembershipTableScheduleSPPageBuilder extends Table
{
	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver  $db  Database connector object
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__osmembership_schedule_sppagebuilder_pages', 'id', $db);
	}
}
