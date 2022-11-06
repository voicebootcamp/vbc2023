<?php
/**
 * Downloadid table
 */

use Joomla\CMS\Table\Table;

class OSMembershipTableDownloadid extends Table
{
	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver  $db  Database connector object
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__osmembership_downloadids', 'id', $db);
	}
}
