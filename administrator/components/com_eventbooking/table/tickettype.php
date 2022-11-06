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
 * Class EventbookingTableTickettype
 *
 * @property $id
 * @property $event_id
 * @property $time
 * @property $title
 * @property $description
 * @property $ordering
 */
class EventbookingTableTickettype extends Table
{
	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver  $db  Database connector object
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__eb_ticket_types', 'id', $db);
	}
}
