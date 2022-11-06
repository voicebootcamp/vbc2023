<?php
/**
 * ScheduleContent table
 *
 * @property $id
 * @property $plan_id
 * @property $article_id
 * @property $number_days
 * @property $release_date
 * @property $ordering
 */

use Joomla\CMS\Table\Table;

class OSMembershipTableScheduleContent extends Table
{
	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver  $db  Database connector object
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__osmembership_schedulecontent', 'id', $db);
	}
}
