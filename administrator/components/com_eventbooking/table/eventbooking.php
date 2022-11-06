<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2022 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Table\Table;

/**
 * Event Table Class
 */
class EventEventBooking extends Table
{
	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver  $db  Database connector object
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__eb_events', 'id', $db);
	}
}

/**
 * Field Table Class
 */
class FieldEventBooking extends Table
{
	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver  $db  Database connector object
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__eb_fields', 'id', $db);
	}
}

/**
 * Registrant Event Booking
 */
class RegistrantEventBooking extends Table
{
	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver  $db  Database connector object
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__eb_registrants', 'id', $db);
	}
}

/**
 * Category Table Class
 */
class CategoryEventBooking extends Table
{
	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver  $db  Database connector object
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__eb_categories', 'id', $db);
	}
}

/**
 * Location Table Class
 */
class LocationEventBooking extends Table
{
	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver  $db  Database connector object
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__eb_locations', 'id', $db);
	}
}

/**
 * Plugin table class
 */
class PluginEventBooking extends Table
{
	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver  $db  Database connector object
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__eb_payment_plugins', 'id', $db);
	}
}

/**
 * Coupon Table
 */
class CouponEventBooking extends Table
{
	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver  $db  Database connector object
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__eb_coupons', 'id', $db);
	}
}

/**
 * Event Table Class
 */
class ConfigEventBooking extends Table
{
	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver  $db  Database connector object
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__eb_configs', 'id', $db);
	}
}

/**
 * Waiting list table class
 */
class WaitingListEventBooking extends Table
{
	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver  $db  Database connector object
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__eb_waiting_lists', 'id', $db);
	}
}

/**
 * Waiting list table class
 */
class FieldvalueEventBooking extends Table
{
	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver  $db  Database connector object
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__eb_field_values', 'id', $db);
	}
}

/**
 * State Table Class
 */
class StateEventBooking extends Table
{
	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver  $db  Database connector object
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__eb_states', 'id', $db);
	}
}

/**
 * State Table Class
 */
class CountryEventBooking extends Table
{
	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver  $db  Database connector object
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__eb_countries', 'id', $db);
	}
}
