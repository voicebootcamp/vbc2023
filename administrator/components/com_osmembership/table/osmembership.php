<?php
/**
 * Plan table
 */

use Joomla\CMS\Table\Table;

class PlanOsMembership extends Table
{
	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver  $db  Database connector object
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__osmembership_plans', 'id', $db);
	}
}

/**
 * Subscriber table
 */
class SubscriberOSMembership extends Table
{
	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver  $db  Database connector object
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__osmembership_subscribers', 'id', $db);
	}
}

/**
 * Fieldvalue table
 */
class FieldValueOsMembership extends Table
{
	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver  $db  Database connector object
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__osmembership_field_value', 'id', $db);
	}
}
