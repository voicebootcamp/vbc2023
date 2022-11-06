<?php
/**
 * Coupon table
 */

use Joomla\CMS\Table\Table;

class OSMembershipTableCoupon extends Table
{
	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver  $db  Database connector object
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__osmembership_coupons', 'id', $db);
	}

	/**
	 * Sanitize data before storing into database
	 *
	 * @return bool|void
	 */
	public function check()
	{
		$this->times = (int) $this->times;
		$this->used  = (int) $this->used;

		if (!(int) $this->valid_from)
		{
			$this->valid_from = $this->getDbo()->getNullDate();
		}

		if (!(int) $this->valid_to)
		{
			$this->valid_to = $this->getDbo()->getNullDate();
		}

		return parent::check();
	}
}
