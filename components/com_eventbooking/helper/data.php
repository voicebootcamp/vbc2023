<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2022 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;

class EventbookingHelperData
{
	/**
	 * Get day name from given day number
	 *
	 * @param $dayNumber
	 *
	 * @return mixed
	 */
	public static function getDayName($dayNumber)
	{
		static $days;

		if ($days == null)
		{
			$days = [
				Text::_('SUNDAY'),
				Text::_('MONDAY'),
				Text::_('TUESDAY'),
				Text::_('WEDNESDAY'),
				Text::_('THURSDAY'),
				Text::_('FRIDAY'),
				Text::_('SATURDAY'),
			];
		}

		$i = $dayNumber % 7;

		return $days[$i];
	}

	/**
	 * Get day name from day number in mini calendar
	 *
	 * @param $dayNumber
	 *
	 * @return mixed
	 */
	public static function getDayNameMini($dayNumber)
	{
		static $daysMini = null;

		if ($daysMini === null)
		{
			$daysMini    = [];
			$daysMini[0] = Text::_('EB_MINICAL_SUNDAY');
			$daysMini[1] = Text::_('EB_MINICAL_MONDAY');
			$daysMini[2] = Text::_('EB_MINICAL_TUESDAY');
			$daysMini[3] = Text::_('EB_MINICAL_WEDNESDAY');
			$daysMini[4] = Text::_('EB_MINICAL_THURSDAY');
			$daysMini[5] = Text::_('EB_MINICAL_FRIDAY');
			$daysMini[6] = Text::_('EB_MINICAL_SATURDAY');
		}

		$i = $dayNumber % 7; //

		return $daysMini[$i];
	}

	/**
	 * Get day name HTML code for a given day
	 *
	 * @param   int   $dayNumber
	 * @param   bool  $colored
	 *
	 * @return string
	 */
	public static function getDayNameHtml($dayNumber, $colored = false)
	{
		$i = $dayNumber % 7; // modulo 7

		if ($i == '0' && $colored === true)
		{
			$dayName = '<span class="sunday">' . self::getDayName($i) . '</span>';
		}
		elseif ($i == '6' && $colored === true)
		{
			$dayName = '<span class="saturday">' . self::getDayName($i) . '</span>';
		}
		else
		{
			$dayName = self::getDayName($i);
		}

		return $dayName;
	}

	/**
	 * Get day name HTML code for a given day
	 *
	 * @param   int   $dayNumber
	 * @param   bool  $colored
	 *
	 * @return string
	 */
	public static function getDayNameHtmlMini($dayNumber, $colored = false)
	{
		$i = $dayNumber % 7; // modulo 7

		if ($i == '0' && $colored === true)
		{
			$dayName = '<span class="sunday">' . self::getDayNameMini($i) . '</span>';
		}
		elseif ($i == '6' && $colored === true)
		{
			$dayName = '<span class="saturday">' . self::getDayNameMini($i) . '</span>';
		}
		else
		{
			$dayName = self::getDayNameMini($i);
		}

		return $dayName;
	}

	/**
	 * Build the data used for rendering calendar
	 *
	 * @param   array  $rows
	 * @param   int    $year
	 * @param   int    $month
	 * @param   bool   $mini
	 *
	 * @return array
	 */
	public static function getCalendarData($rows, $year, $month, $mini = false)
	{
		$config           = EventbookingHelper::getConfig();
		$rowCount         = count($rows);
		$data             = [];
		$data['startday'] = $startDay = (int) $config->get('calendar_start_date');
		$data['year']     = $year;
		$data['month']    = $month;
		$data["daynames"] = [];
		$data["dates"]    = [];
		$month            = intval($month);

		if ($month <= '9')
		{
			$month = '0' . $month;
		}

		// Get days in week
		for ($i = 0; $i < 7; $i++)
		{
			if ($mini)
			{
				$data["daynames"][$i] = self::getDayNameMini(($i + $startDay) % 7);
			}
			else
			{
				$data["daynames"][$i] = self::getDayName(($i + $startDay) % 7);
			}
		}

		// Today date data
		$date       = new DateTime('now', new DateTimeZone(Factory::getApplication()->get('offset')));
		$todayDay   = $date->format('d');
		$todayMonth = $date->format('m');
		$todayYear  = $date->format('Y');

		// Start days in month
		$date->setDate($year, $month, 1);
		$start = ($date->format('w') - $startDay + 7) % 7;

		//Previous month
		$preMonth = clone $date;
		$preMonth->modify('-1 month');
		$priorMonth = $preMonth->format('m');
		$priorYear  = $preMonth->format('Y');

		$dayCount = 0;

		for ($a = $start; $a > 0; $a--)
		{
			$data["dates"][$dayCount]              = [];
			$data["dates"][$dayCount]["monthType"] = "prior";
			$data["dates"][$dayCount]["month"]     = $priorMonth;
			$data["dates"][$dayCount]["year"]      = $priorYear;
			$dayCount++;
		}

		sort($data["dates"]);

		// Current month
		$end = $date->format('t');

		for ($d = 1; $d <= $end; $d++)
		{
			$data["dates"][$dayCount]              = [];
			$data["dates"][$dayCount]["monthType"] = "current";
			$data["dates"][$dayCount]["month"]     = $month;
			$data["dates"][$dayCount]["year"]      = $year;

			if ($month == $todayMonth && $year == $todayYear && $d == $todayDay)
			{
				$data["dates"][$dayCount]["today"] = true;
			}
			else
			{
				$data["dates"][$dayCount]["today"] = false;
			}

			$data["dates"][$dayCount]['d']      = $d;
			$data["dates"][$dayCount]['events'] = [];

			if ($rowCount > 0)
			{
				foreach ($rows as $row)
				{
					$date_of_event = explode('-', $row->event_date);
					$date_of_event = (int) $date_of_event[2];

					if ($d == $date_of_event)
					{
						$i                                      = count($data["dates"][$dayCount]['events']);
						$data["dates"][$dayCount]['events'][$i] = $row;
					}
				}
			}

			$dayCount++;
		}

		// Following month
		$date->modify('+1 month');
		$days        = (7 - $date->format('w') + $startDay) % 7;
		$followMonth = $date->format('m');
		$followYear  = $date->format('Y');

		for ($d = 1; $d <= $days; $d++)
		{
			$data["dates"][$dayCount]              = [];
			$data["dates"][$dayCount]["monthType"] = "following";
			$data["dates"][$dayCount]["month"]     = $followMonth;
			$data["dates"][$dayCount]["year"]      = $followYear;
			$dayCount++;
		}

		return $data;
	}

	/**
	 * Calculate the discounted prices for events
	 *
	 * @param $rows
	 */
	public static function calculateDiscount($rows)
	{
		if (EventbookingHelper::isMethodOverridden('EventbookingHelperOverrideData', 'calculateDiscount'))
		{
			return EventbookingHelperOverrideData::calculateDiscount($rows);
		}

		$db     = Factory::getDbo();
		$query  = $db->getQuery(true);
		$user   = Factory::getUser();
		$config = EventbookingHelper::getConfig();
		$userId = $user->get('id');

		// Calculate discounted price if configured
		if ($config->show_discounted_price)
		{
			EventbookingHelper::callOverridableHelperMethod('Data', 'calculateEventsDiscountedPrice', [$rows]);
		}

		foreach ($rows as $row)
		{
			if ($userId > 0)
			{
				$query->clear()
					->select('COUNT(id)')
					->from('#__eb_registrants')
					->where('user_id = ' . $userId)
					->where('event_id = ' . $row->id)
					->where('(published=1 OR (published = 0 AND payment_method LIKE "os_offline%"))');
				$db->setQuery($query);
				$row->user_registered = $db->loadResult();
			}

			$lateFee = 0;

			if ((int) $row->late_fee_date && $row->late_fee_date_diff >= 0 && $row->late_fee_amount > 0)
			{
				if ($row->late_fee_type == 1)
				{
					$lateFee = $row->individual_price * $row->late_fee_amount / 100;
				}
				else
				{

					$lateFee = $row->late_fee_amount;
				}
			}

			$row->late_fee = $lateFee;
		}
	}

	/**
	 * Method to calculate discounted price for events
	 *
	 * @param   array  $rows
	 */
	public static function calculateEventsDiscountedPrice($rows)
	{
		$userId = Factory::getUser()->id;

		foreach ($rows as $row)
		{
			$discount = 0;

			if ((int) $row->early_bird_discount_date && $row->date_diff >= 0)
			{
				if ($row->early_bird_discount_type == 1)
				{
					$discount = $row->individual_price * $row->early_bird_discount_amount / 100;
				}
				else
				{
					$discount = $row->early_bird_discount_amount;
				}
			}

			if ($userId > 0)
			{
				$discountRate = EventbookingHelper::callOverridableHelperMethod('Registration', 'calculateMemberDiscount', [$row->discount_amounts, $row->discount_groups]);

				if ($discountRate > 0)
				{
					if ($row->discount_type == 1)
					{
						$discount += $row->individual_price * $discountRate / 100;
					}
					else
					{
						$discount += $discountRate;
					}
				}
			}

			$row->discounted_price = $row->individual_price - $discount;
		}
	}

	/**
	 * Get all children categories of a given category
	 *
	 * @param   int   $id
	 * @param   bool  $checkSubmitEventAccess
	 *
	 * @return array
	 */
	public static function getAllChildrenCategories($id, $checkSubmitEventAccess = false)
	{
		$user                   = Factory::getUser();
		$db                     = Factory::getDbo();
		$query                  = $db->getQuery(true);
		$userHasAdminPermission = $user->authorise('core.admin', 'com_eventbooking');

		$queue       = [$id];
		$categoryIds = [$id];

		while (count($queue))
		{
			$categoryId = array_pop($queue);

			//Get list of children categories of the current category
			$query->clear()
				->select('id')
				->from('#__eb_categories')
				->where('parent = ' . $categoryId)
				->where('published = 1');

			if ($checkSubmitEventAccess && !$userHasAdminPermission)
			{
				$query->where('submit_event_access IN (' . implode(',', $user->getAuthorisedViewLevels()) . ')');
			}

			$db->setQuery($query);
			$children = $db->loadColumn();

			if (count($children))
			{
				$queue       = array_merge($queue, $children);
				$categoryIds = array_merge($categoryIds, $children);
			}
		}

		return $categoryIds;
	}

	/**
	 * Get parent categories of the given category
	 *
	 * @param $categoryId
	 *
	 * @return array
	 */
	public static function getParentCategories($categoryId)
	{
		$db          = Factory::getDbo();
		$query       = $db->getQuery(true);
		$parents     = [];
		$fieldSuffix = EventbookingHelper::getFieldSuffix();

		while (true)
		{
			$query->clear()
				->select('id, parent')
				->select($db->quoteName('name' . $fieldSuffix, 'name'))
				->where('id = ' . $categoryId)
				->where('published = 1');
			$db->setQuery($query);
			$row = $db->loadObject();

			if ($row)
			{
				$parents[]  = $row;
				$categoryId = $row->parent;
			}
			else
			{
				break;
			}
		}

		return $parents;
	}

	/**
	 * Get all ticket types of this event
	 *
	 * @param   int   $eventId
	 * @param   bool  $checkPublishUpDown
	 *
	 * @return array
	 */
	public static function getTicketTypes($eventId, $checkPublishUpDown = false)
	{
		static $ticketTypes;

		$cacheKey = $eventId . (int) $checkPublishUpDown;

		if (!isset($ticketTypes[$cacheKey]))
		{
			$db    = Factory::getDbo();
			$query = $db->getQuery(true);
			$query->select('*, 0 AS registered')
				->from('#__eb_ticket_types')
				->where('event_id = ' . $eventId)
				->order('ordering');

			if ($checkPublishUpDown)
			{
				$nullDate = $db->quote($db->getNullDate());
				$nowDate  = $db->quote(EventbookingHelper::getServerTimeFromGMTTime());
				$query->where('(publish_up = ' . $nullDate . ' OR publish_up <= ' . $nowDate . ')')
					->where('(publish_down = ' . $nullDate . ' OR publish_down >= ' . $nowDate . ')')
					->where($db->quoteName('access') . ' IN (' . implode(',', Factory::getUser()->getAuthorisedViewLevels()) . ')');
			}

			$db->setQuery($query);
			$rows = $db->loadObjectList();

			$query->clear()
				->select('a.ticket_type_id')
				->select('IFNULL(SUM(a.quantity), 0) AS registered')
				->from('#__eb_registrant_tickets AS a')
				->innerJoin('#__eb_registrants AS b ON a.registrant_id = b.id')
				->where('b.event_id = ' . $eventId)
				->where('b.group_id = 0')
				->where('(b.published = 1 OR (b.published = 0 AND b.payment_method LIKE "os_offline%"))')
				->group('a.ticket_type_id');
			$db->setQuery($query);
			$rowTickets = $db->loadObjectList('ticket_type_id');

			if (count($rowTickets))
			{
				foreach ($rows as $row)
				{
					if (isset($rowTickets[$row->id]))
					{
						$row->registered = $rowTickets[$row->id]->registered;
					}
				}
			}

			$ticketTypes[$cacheKey] = $rows;
		}

		return $ticketTypes[$cacheKey];

	}

	/***
	 * Get categories used to generate breadcrump
	 *
	 * @param $id
	 * @param $parentId
	 *
	 * @return array
	 */
	public static function getCategoriesBreadcrumb($id, $parentId)
	{
		$db          = Factory::getDbo();
		$query       = $db->getQuery(true);
		$fieldSuffix = EventbookingHelper::getFieldSuffix();
		$query->select('id, parent')
			->select($db->quoteName('name' . $fieldSuffix, 'name'))
			->from('#__eb_categories')
			->where('published = 1');
		$db->setQuery($query);
		$categories = $db->loadObjectList('id');
		$paths      = [];

		while ($id != $parentId)
		{
			if (isset($categories[$id]))
			{
				$paths[] = $categories[$id];
				$id      = $categories[$id]->parent;
			}
			else
			{
				break;
			}
		}

		return $paths;
	}

	/**
	 * Pre-process event's data before passing to the view for displaying
	 *
	 * @param   array   $rows
	 * @param   string  $context
	 */
	public static function preProcessEventData($rows, $context = 'list')
	{
		// Calculate discounted price
		self::calculateDiscount($rows);

		$config = EventbookingHelper::getConfig();

		// Get categories data for each events
		$db          = Factory::getDbo();
		$query       = $db->getQuery(true);
		$fieldSuffix = EventbookingHelper::getFieldSuffix();

		$query->select('*')
			->from('#__eb_locations');

		if ($fieldSuffix)
		{
			EventbookingHelperDatabase::getMultilingualFields($query, ['name', 'alias', 'description'], $fieldSuffix);
		}

		$db->setQuery($query);
		$locations = $db->loadObjectList('id');

		$ids = [];

		foreach ($rows as $row)
		{
			$ids[] = $row->id;
		}

		if (count($ids) == 0)
		{
			$ids = [0];
		}

		$query->clear()
			->select('a.*, b.event_id')
			->from('#__eb_categories AS a')
			->innerJoin('#__eb_event_categories AS b ON a.id = b.category_id')
			->where('b.event_id IN (' . implode(',', $ids) . ')')
			->order('b.id');

		if ($fieldSuffix)
		{
			EventbookingHelperDatabase::getMultilingualFields($query, ['a.name', 'a.alias'], $fieldSuffix);
		}

		$db->setQuery($query);
		$categories      = $db->loadObjectList();
		$eventCategories = [];

		foreach ($categories as $category)
		{
			$eventCategories[$category->event_id][] = $category;
		}

		foreach ($rows as $row)
		{
			$row->categories     = $eventCategories[$row->id];
			$row->category       = $row->categories[0];
			$row->category_id    = $row->categories[0]->id;
			$row->category_name  = $row->categories[0]->name;
			$row->category_alias = $row->categories[0]->alias;

			if ($row->currency_code && !$row->currency_symbol)
			{
				$row->currency_symbol = $row->currency_code;
			}

			// Location data
			if ($row->location_id)
			{
				$row->location = $locations[$row->location_id];
			}
			else
			{
				$row->location = null;
			}

			// Process content plugin
			$row->short_description = HTMLHelper::_('content.prepare', $row->short_description);

			if ($context == 'item')
			{
				$row->description = HTMLHelper::_('content.prepare', $row->description);
			}

			if ($config->show_price_including_tax && !$config->get('setup_price'))
			{
				$taxRate                = $row->tax_rate;
				$row->individual_price  = round($row->individual_price * (1 + $taxRate / 100), 2);
				$row->fixed_group_price = round($row->fixed_group_price * (1 + $taxRate / 100), 2);
				$row->late_fee          = round($row->late_fee * (1 + $taxRate / 100), 2);

				if ($config->show_discounted_price)
				{
					$row->discounted_price = round($row->discounted_price * (1 + $taxRate / 100), 2);
				}
			}

			if ($row->has_multiple_ticket_types)
			{
				$row->ticketTypes = self::getTicketTypes($row->id, true);

				if (empty($row->ticketTypes))
				{
					$row->has_multiple_ticket_types = 0;
				}
			}
		}

		// Process event custom fields data
		if ($config->event_custom_field)
		{
			EventbookingHelperData::prepareCustomFieldsData($rows);
		}
	}

	/**
	 * Decode custom fields data and store it for each event record
	 *
	 * @param $items
	 */
	public static function prepareCustomFieldsData($items)
	{
		$xml = simplexml_load_file(JPATH_ROOT . '/components/com_eventbooking/fields.xml');

		if ($xml === false)
		{
			foreach ($items as $item)
			{
				$item->paramData = [];
			}

			return;
		}

		$user        = Factory::getUser();
		$fields      = $xml->fields->fieldset->children();
		$languageTag = Factory::getLanguage()->getTag();
		$fieldsAssoc = [];

		foreach ($fields as $field)
		{
			$name     = (string) $field->attributes()->name;
			$language = (string) $field->attributes()->language;
			$access   = (int) $field->attributes()->access;

			if ($language && !in_array($language, ['*', $languageTag]))
			{
				$showField = false;
			}
			elseif ($access && !in_array($access, $user->getAuthorisedViewLevels()))
			{
				$showField = false;
			}
			else
			{
				$showField = true;
			}

			if ($showField)
			{
				$fieldsAssoc[$name] = $field;
			}
		}

		for ($i = 0, $n = count($items); $i < $n; $i++)
		{
			$item   = $items[$i];
			$params = new Registry();
			$params->loadString($item->custom_fields, 'JSON');
			$paramData = [];

			foreach ($fieldsAssoc as $name => $field)
			{
				$paramData[$name]['field'] = $field;
				$paramData[$name]['title'] = Text::_($field->attributes()->label);
				$paramData[$name]['type']  = (string) $field->attributes()->type;

				$show = (string) $field->attributes()->show;

				if ($show === '0' || $show === 'false')
				{
					$paramData[$name]['hide'] = true;
				}
				else
				{
					$paramData[$name]['hide'] = false;
				}

				$fieldValue = $params->get($name);

				if (is_array($fieldValue))
				{
					$fieldValue = implode(', ', $fieldValue);
				}

				if (!property_exists($item, $name))
				{
					$item->{$name} = $fieldValue;
				}

				// Workaround for subform field type, $fieldValue is stdclass and will causes fatal error on string function
				if (is_object($fieldValue))
				{
					$fieldValue = '';
				}

				$paramData[$name]['value'] = $fieldValue;

				$key                     = strtoupper($name);
				$item->short_description = str_ireplace("[$key]", (string) $fieldValue, $item->short_description);
				$item->description       = str_ireplace("[$key]", (string) $fieldValue, $item->description);
			}

			$item->paramData = $paramData;
		}
	}

	/**
	 * Prepare data for events before it's being displayed
	 *
	 * @param   array      $events
	 * @param   int        $activeCategoryId
	 * @param   RADConfig  $config
	 * @param   int        $Itemid
	 */
	public static function prepareDisplayData($events, $activeCategoryId, $config, $Itemid)
	{
		if (EventbookingHelper::isMethodOverridden('EventbookingHelperOverrideData', 'prepareDisplayData'))
		{
			EventbookingHelperOverrideData::prepareDisplayData($events, $activeCategoryId, $config, $Itemid);

			return;
		}

		$rootUrl    = Uri::root(true);
		$viewLevels = Factory::getUser()->getAuthorisedViewLevels();

		foreach ($events as $event)
		{
			$event->can_register = EventbookingHelper::callOverridableHelperMethod('Registration', 'acceptRegistration', [$event]);

			if ((int) $event->cut_off_date)
			{
				$registrationOpen = ($event->cut_off_minutes < 0);
			}
			elseif (isset($event->event_start_minutes))
			{
				$registrationOpen = ($event->event_start_minutes < 0);
			}
			else
			{
				$registrationOpen = ($event->number_event_dates > 0);
			}

			$event->registration_open = $registrationOpen;

			if (empty($activeCategoryId))
			{
				$catId = @$event->main_category_id;
			}
			else
			{
				$catId = $activeCategoryId;
			}

			$event->url = Route::_(EventbookingHelperRoute::getEventRoute($event->id, $catId, $Itemid));

			if ($event->activate_waiting_list == 2)
			{
				$activateWaitingList = $config->activate_waitinglist_feature;
			}
			else
			{
				$activateWaitingList = $event->activate_waiting_list;
			}

			$waitingList = false;

			if ($event->event_capacity > 0
				&& $event->event_capacity <= $event->total_registrants
				&& $activateWaitingList
				&& !@$event->user_registered
				&& $registrationOpen
				&& in_array($event->registration_access, $viewLevels))
			{
				$waitingList = true;
			}

			$event->waiting_list = $waitingList;

			$isMultipleDate = false;

			if ($config->show_children_events_under_parent_event && $event->event_type == 1)
			{
				$isMultipleDate = true;
			}

			$event->is_multiple_date = $isMultipleDate;

			if ($event->thumb && file_exists(JPATH_ROOT . '/media/com_eventbooking/images/thumbs/' . $event->thumb))
			{
				$event->thumb_url = $rootUrl . '/media/com_eventbooking/images/thumbs/' . $event->thumb;

				if ($event->image && file_exists(JPATH_ROOT . '/' . $event->image))
				{
					$imageUrl = $rootUrl . '/' . $event->image;
				}
				elseif (file_exists(JPATH_ROOT . '/media/com_eventbooking/images/' . $event->thumb))
				{
					$imageUrl = $rootUrl . '/media/com_eventbooking/images/' . $event->thumb;
				}
				else
				{
					$imageUrl = $rootUrl . '/media/com_eventbooking/images/thumbs/' . $event->thumb;
				}

				$event->image_url = $imageUrl;
			}

			// Display Price
			if ($config->show_discounted_price)
			{
				$price = $event->discounted_price;
			}
			else
			{
				$price = $event->individual_price;
			}

			if ($event->price_text)
			{
				$priceDisplay = $event->price_text;
			}
			elseif ($price > 0)
			{
				$symbol       = $event->currency_symbol ?: $config->currency_symbol;
				$priceDisplay = EventbookingHelper::formatCurrency($price, $config, $symbol);
			}
			elseif ($config->show_price_for_free_event)
			{
				$priceDisplay = Text::_('EB_FREE');
			}
			else
			{
				$priceDisplay = '';
			}

			$event->priceDisplay = $priceDisplay;
		}
	}

	/**
	 * Get data from excel file using PHPExcel library
	 *
	 * @param $file
	 * @param $filename
	 *
	 * @return array
	 */
	public static function getDataFromFile($file, $filename = '')
	{
		// Allow using a custom library to parse the file
		PluginHelper::importPlugin('eventbooking');

		$results = Factory::getApplication()->triggerEvent('onBeforeGettingDataFromFile', [$file, $filename]);

		foreach ($results as $result)
		{
			if (is_array($result))
			{
				return $result;
			}
		}

		// Use spout to get data
		try
		{
			$reader = ReaderEntityFactory::createReaderFromFile($filename);

			if ($reader instanceof Box\Spout\Reader\CSV\Reader)
			{
				$config = EventbookingHelper::getConfig();
				$reader->setFieldDelimiter($config->get('csv_delimiter', ','));
			}

			$reader->open($file);
			$headers = [];
			$rows    = [];
			$count   = 0;

			foreach ($reader->getSheetIterator() as $sheet)
			{
				foreach ($sheet->getRowIterator() as $row)
				{
					$cells = $row->getCells();

					if ($count === 0)
					{
						foreach ($cells as $cell)
						{
							$headers[] = $cell->getValue();
						}

						$count++;
					}
					else
					{
						$cellIndex = 0;
						$row       = [];

						foreach ($cells as $cell)
						{
							$row[$headers[$cellIndex++]] = $cell->getValue();
						}

						$rows[] = $row;
					}
				}
			}

			$reader->close();

			return $rows;
		}
		catch (Exception $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');

			return [];
		}
	}

	/**
	 * Prepare registrants data before exporting to excel
	 *
	 * @param   array      $rows
	 * @param   RADConfig  $config
	 * @param   array      $rowFields
	 * @param   array      $fieldValues
	 * @param   int        $eventId
	 * @param   bool       $forImport
	 *
	 * @return array
	 */
	public static function prepareRegistrantsExportData($rows, $config, $rowFields, $fieldValues, $eventId = 0, $forImport = false)
	{
		$showGroup = false;

		foreach ($rows as $row)
		{
			if ($row->is_group_billing || $row->group_id > 0)
			{
				$showGroup = true;
				break;
			}
		}

		// Determine whether we need to show payment method column
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('name, title')
			->from('#__eb_payment_plugins')
			->where('published=1');
		$db->setQuery($query);
		$plugins = $db->loadObjectList('name');

		$showPaymentMethodColumn = false;

		if (count($plugins) > 1)
		{
			$showPaymentMethodColumn = true;
		}

		if ($eventId)
		{
			$event = EventbookingHelperDatabase::getEvent($eventId);

			if ($event->has_multiple_ticket_types)
			{
				$ticketTypes = EventbookingHelperData::getTicketTypes($eventId);

				$ticketTypeIds = [];

				foreach ($ticketTypes as $ticketType)
				{
					$ticketTypeIds[] = $ticketType->id;
				}

				$db    = Factory::getDbo();
				$query = $db->getQuery(true);
				$query->select('registrant_id, ticket_type_id, quantity')
					->from('#__eb_registrant_tickets')
					->where('ticket_type_id IN (' . implode(',', $ticketTypeIds) . ')');
				$db->setQuery($query);

				$registrantTickets = $db->loadObjectList();

				$tickets = [];

				foreach ($registrantTickets as $registrantTicket)
				{
					$tickets[$registrantTicket->registrant_id][$registrantTicket->ticket_type_id] = $registrantTicket->quantity;
				}
			}
		}

		$headers = [Text::_('EB_ID'), Text::_('EB_EVENT')];
		$fields  = ['id'];

		if ($forImport)
		{
			$fields[] = 'event_id';
		}
		else
		{
			$fields[] = 'title';
		}

		if ($config->get('export_event_date', 1))
		{
			$headers[] = Text::_('EB_EVENT_DATE');
			$fields[]  = 'event_date';
		}

		if ($config->get('export_event_end_date'))
		{
			$headers[] = Text::_('EB_EVENT_END_DATE');
			$fields[]  = 'event_end_date';
		}

		if ($config->get('export_category'))
		{
			$headers[] = Text::_('EB_CATEGORY');
			$fields[]  = 'category_name';
		}

		if ($config->get('export_user_id', 1))
		{
			$headers[] = Text::_('EB_USER_ID');
			$fields[]  = 'user_id';
		}

		if ($config->get('export_username', 0))
		{
			$headers[] = Text::_('EB_USERNAME');
			$fields[]  = 'username';
		}

		if ($showGroup)
		{
			$headers[] = Text::_('EB_GROUP');
			$fields[]  = 'registration_group_name';
		}

		if (count($rowFields))
		{
			foreach ($rowFields as $rowField)
			{
				// Don't show message and heading fields
				if ($rowField->title && !$rowField->hide_on_export && !in_array($rowField->fieldtype, ['Heading', 'Message']))
				{
					$headers[] = $rowField->title;
					$fields[]  = $rowField->name;
				}
			}
		}

		if (!empty($ticketTypes))
		{
			foreach ($ticketTypes as $ticketType)
			{
				$headers[] = $ticketType->title;
				$fields[]  = 'event_ticket_type_' . $ticketType->id;
			}
		}

		if ($config->get('export_number_registrants', 1))
		{
			$headers[] = Text::_('EB_NUMBER_REGISTRANTS');
			$fields[]  = 'number_registrants';
		}

		if ($config->get('export_amount', 1))
		{
			$headers[] = Text::_('EB_AMOUNT');
			$fields[]  = 'total_amount';
		}

		if ($config->get('export_discount_amount', 1))
		{
			$headers[] = Text::_('EB_DISCOUNT_AMOUNT');
			$fields[]  = 'discount_amount';
		}

		if ($config->get('export_late_fee', 1))
		{
			$headers[] = Text::_('EB_LATE_FEE');
			$fields[]  = 'late_fee';
		}

		if ($config->get('export_tax_amount', 1))
		{
			$headers[] = Text::_('EB_TAX_AMOUNT');
			$fields[]  = 'tax_amount';
		}

		if ($config->get('export_payment_processing_fee', 0))
		{
			$headers[] = Text::_('EB_PAYMENT_FEE');
			$fields[]  = 'payment_processing_fee';
		}

		if ($config->get('export_gross_amount', 1))
		{
			$headers[] = Text::_('EB_GROSS_AMOUNT');
			$fields[]  = 'amount';
		}

		if ($config->activate_deposit_feature)
		{
			if ($config->get('export_deposit_amount', 1))
			{
				$headers[] = Text::_('EB_DEPOSIT_AMOUNT');
				$fields[]  = 'deposit_amount';
			}

			if ($config->get('export_due_amount', 1))
			{
				$headers[] = Text::_('EB_DUE_AMOUNT');
				$fields[]  = 'due_amount';
			}
		}

		if ($config->show_coupon_code_in_registrant_list)
		{
			$headers[] = Text::_('EB_COUPON');
			$fields[]  = 'coupon_code';
		}

		if ($config->get('export_registration_date', 1))
		{
			$headers[] = Text::_('EB_REGISTRATION_DATE');
			$fields[]  = 'register_date';
		}

		if (($showPaymentMethodColumn && $config->get('export_payment_method', 1)) || $forImport)
		{
			$headers[] = Text::_('EB_PAYMENT_METHOD');
			$fields[]  = 'payment_method';
		}

		if ($config->activate_tickets_pdf)
		{
			$headers[] = Text::_('EB_TICKET_NUMBER');
			$headers[] = Text::_('EB_TICKET_CODE');
			$fields[]  = 'ticket_number';
			$fields[]  = 'ticket_code';
		}

		if ($config->get('export_transaction_id', 1))
		{
			$headers[] = Text::_('EB_TRANSACTION_ID');
			$fields[]  = 'transaction_id';
		}

		if ($config->get('export_payment_date'))
		{
			$headers[] = Text::_('EB_PAYMENT_DATE');
			$fields[]  = 'payment_date';
		}

		if ($config->activate_deposit_feature && $config->get('export_deposit_payment_transaction_id', 1))
		{
			$headers[] = Text::_('EB_DEPOSIT_PAYMENT_TRANSACTION_ID');
			$fields[]  = 'deposit_payment_transaction_id';
		}

		if ($config->get('export_payment_status', 1))
		{
			$headers[] = Text::_('EB_PAYMENT_STATUS');

			if ($forImport)
			{
				$fields[] = 'published';
			}
			else
			{
				$fields[] = 'payment_status';
			}
		}

		if ($config->activate_checkin_registrants)
		{
			if ($config->get('export_checked_in', 1))
			{
				$headers[] = Text::_('EB_CHECKED_IN');
				$fields[]  = 'checked_in';
			}

			if ($config->get('export_checked_in_at', 1))
			{
				$headers[] = Text::_('EB_CHECKED_IN_TIME');
				$fields[]  = 'checked_in_at';
			}

			if ($config->get('export_checked_out_at', 1))
			{
				$headers[] = Text::_('EB_CHECKED_OUT_TIME');
				$fields[]  = 'checked_out_at';
			}
		}

		if ($config->activate_invoice_feature)
		{
			$headers[] = Text::_('EB_INVOICE_NUMBER');
			$fields[]  = 'invoice_number';
		}

		if ($config->export_subscribe_to_newsletter)
		{
			$headers[] = Text::_('EB_SUBSCRIBE_TO_NEWSLETTER');
			$fields[]  = 'subscribe_newsletter';
		}

		foreach ($rows as $row)
		{
			if ($config->show_event_date)
			{
				$row->event_date = HTMLHelper::_('date', $row->event_date, $config->event_date_format, null);
			}

			if ($row->ticket_qrcode)
			{
				$row->ticket_code = $row->ticket_qrcode;
			}

			if ($showGroup)
			{
				if ($row->is_group_billing)
				{
					$row->registration_group_name = $row->first_name . ' ' . $row->last_name;
				}
				elseif ($row->group_id > 0)
				{
					$row->registration_group_name = $row->group_name;
				}
				else
				{
					$row->registration_group_name = '';
				}
			}

			foreach ($rowFields as $rowField)
			{
				if (!$rowField->is_core)
				{
					$fieldValue = @$fieldValues[$row->id][$rowField->id];

					if (is_string($fieldValue) && is_array(json_decode($fieldValue)))
					{
						$fieldValue = implode(', ', json_decode($fieldValue));
					}

					$row->{$rowField->name} = $fieldValue;
				}
			}

			if (!empty($ticketTypes))
			{
				foreach ($ticketTypes as $ticketType)
				{
					if (!empty($tickets[$row->id][$ticketType->id]))
					{
						$row->{'event_ticket_type_' . $ticketType->id} = $tickets[$row->id][$ticketType->id];
					}
					else
					{
						$row->{'event_ticket_type_' . $ticketType->id} = 0;
					}
				}
			}

			$amountFields = [
				'total_amount',
				'discount_amount',
				'late_fee',
				'tax_amount',
				'payment_processing_fee',
				'amount',
				'deposit_amount',
			];

			foreach ($amountFields as $amountField)
			{
				$row->{$amountField} = (float) $row->{$amountField};
			}

			$dueAmount = $row->amount - $row->deposit_amount;

			$row->total_amount           = EventbookingHelper::formatAmount($row->total_amount, $config);
			$row->discount_amount        = EventbookingHelper::formatAmount($row->discount_amount, $config);
			$row->late_fee               = EventbookingHelper::formatAmount($row->late_fee, $config);
			$row->tax_amount             = EventbookingHelper::formatAmount($row->tax_amount, $config);
			$row->payment_processing_fee = EventbookingHelper::formatAmount($row->payment_processing_fee, $config);
			$row->amount                 = EventbookingHelper::formatAmount($row->amount, $config);

			if ($config->activate_deposit_feature)
			{
				if ($row->deposit_amount > 0)
				{
					$row->deposit_amount = EventbookingHelper::formatAmount($row->deposit_amount, $config);
				}
				else
				{
					$row->deposit_amount = '';
				}

				if ($row->payment_status == 0)
				{

					$row->due_amount = EventbookingHelper::formatAmount($dueAmount, $config);
				}
				else
				{
					$row->due_amount = '';
				}
			}

			if ($config->activate_tickets_pdf)
			{
				if ($row->ticket_number)
				{
					$row->ticket_number = EventbookingHelperTicket::formatTicketNumber($row->ticket_prefix, $row->ticket_number, $config);
				}
				else
				{
					$row->ticket_number = '';
				}
			}

			if (!$forImport)
			{
				switch ($row->published)
				{
					case 0:
						$row->payment_status = Text::_('EB_PENDING');
						break;
					case 1:
						$row->payment_status = Text::_('EB_PAID');
						break;
					case 2:
						$row->payment_status = Text::_('EB_CANCELLED');
						break;
					case 3:
						$row->payment_status = Text::_('EB_WAITING_LIST');
						break;
					case 4:
						$row->payment_status = Text::_('EB_WAITING_LIST_CANCELLED');
						break;
					default:
						break;
				}

				if ($row->checked_in)
				{
					$row->checked_in = Text::_('JYES');
				}
				else
				{
					$row->checked_in = Text::_('JNO');
				}

				if ($row->checked_in && (int) $row->checked_in_at)
				{
					$row->checked_in_at = HTMLHelper::_('date', $row->checked_in_at, $config->date_format . ' H:i:s');
				}
				else
				{
					$row->checked_in_at = '';
				}

				if ((int) $row->checked_out_at)
				{
					$row->checked_out_at = HTMLHelper::_('date', $row->checked_out_at, $config->date_format . ' H:i:s');
				}
				else
				{
					$row->checked_out_at = '';
				}

				if ($config->activate_invoice_feature)
				{

					if ($row->invoice_number)
					{
						$row->invoice_number = EventbookingHelper::callOverridableHelperMethod('Helper', 'formatInvoiceNumber', [$row->invoice_number, $config, $row]);
					}
					else
					{
						$row->invoice_number = '';
					}
				}

				if ($row->payment_method && isset($plugins[$row->payment_method]))
				{
					$row->payment_method = Text::_($plugins[$row->payment_method]->title);
				}
				else
				{
					$row->payment_method = '';
				}
			}

			$row->register_date = HTMLHelper::_('date', $row->register_date, $config->date_format . ' H:i:s');

			if ((int) $row->payment_date)
			{
				$row->payment_date = HTMLHelper::_('date', $row->payment_date, $config->date_format . ' H:i:s');
			}
			else
			{
				$row->payment_date = '';
			}

			$row->subscribe_newsletter = $row->subscribe_newsletter ? Text::_('JYES') : Text::_('JNO');
		}

		return [$fields, $headers];
	}

	/**
	 * Export the given data to Excel
	 *
	 * @param   array   $fields
	 * @param   array   $rows
	 * @param   string  $filename
	 * @param   array   $headers
	 *
	 * @return string
	 */
	public static function excelExport($fields, $rows, $filename, $headers = [])
	{
		if (empty($headers))
		{
			$headers = $fields;
		}

		$filename = File::stripExt($filename);

		$config = EventbookingHelper::getConfig();

		if ($config->get('export_data_format') == 'csv')
		{
			$writer = WriterEntityFactory::createCSVWriter();
			$writer->setFieldDelimiter($config->get('csv_delimiter', ','));

			$filePath = JPATH_ROOT . '/media/com_eventbooking/invoices/' . $filename . '.csv';
		}
		else
		{
			$writer = WriterEntityFactory::createXLSXWriter();

			$filePath = JPATH_ROOT . '/media/com_eventbooking/invoices/' . $filename . '.xlsx';

			// Set temp path for writer
			$tmpPath = Factory::getApplication()->get('tmp_path');

			if (!Folder::exists($tmpPath))
			{
				$tmpPath = JPATH_ROOT . '/tmp';
			}

			if (Folder::exists($tmpPath) && is_writable($tmpPath))
			{
				$writer->setTempFolder($tmpPath);
			}
		}

		//Delete the file if exist
		if (File::exists($filePath))
		{
			File::delete($filePath);
		}

		$writer->openToFile($filePath);

		if (empty($headers))
		{
			$headers = $fields;
		}

		$style = (new StyleBuilder())
			->setShouldWrapText(false)
			->build();

		// Write header columns
		$writer->addRow(WriterEntityFactory::createRowFromArray($headers, $style));

		foreach ($rows as $row)
		{
			$data = [];

			foreach ($fields as $field)
			{
				if (property_exists($row, $field))
				{
					$data[] = $row->{$field};
				}
				else
				{
					$data[] = '';
				}
			}

			$writer->addRow(WriterEntityFactory::createRowFromArray($data, $style));
		}

		$writer->close();

		return $filePath;
	}
}
