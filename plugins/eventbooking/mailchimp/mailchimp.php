<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2022 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use DrewM\MailChimp\MailChimp;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Mail\MailHelper;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Table\Table;
use Joomla\Registry\Registry;

class plgEventBookingMailchimp extends CMSPlugin
{
	/**
	 * Application object.
	 *
	 * @var    JApplicationCms
	 */
	protected $app;

	/**
	 * Database object.
	 *
	 * @var    JDatabaseDriver
	 */
	protected $db;

	/**
	 * Constructor.
	 *
	 * @param   object    $subject
	 * @param   Registry  $config
	 */
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);

		Factory::getLanguage()->load('plg_eventbooking_mailchimp', JPATH_ADMINISTRATOR);
	}

	/**
	 * Render settings form
	 *
	 * @param   EventbookingTableEvent  $row
	 *
	 * @return array
	 */
	public function onEditEvent($row)
	{
		if (!$this->canRun($row))
		{
			return;
		}

		ob_start();
		$this->drawSettingForm($row);

		return ['title' => Text::_('PLG_EB_MAILCHIMP_SETTINGS'),
		        'form'  => ob_get_clean(),
		];
	}

	/**
	 * Store setting into database, in this case, use params field of plans table
	 *
	 * @param   EventbookingTableEvent  $row
	 * @param   Boolean                 $isNew  true if create new plan, false if edit
	 */
	public function onAfterSaveEvent($row, $data, $isNew)
	{
		if (!$this->canRun($row))
		{
			return;
		}

		$params = new Registry($row->params);

		$params->set('mailchimp_list_ids', implode(',', $data['mailchimp_list_ids'] ?? []));
		$params->set('mailchimp_group_ids', implode(',', $data['mailchimp_group_ids'] ?? []));

		$row->params = $params->toString();

		$row->store();
	}

	/**
	 * Add registrant to Mailchimp when they perform registration uses offline payment
	 *
	 * @param   EventbookingTableRegistrant  $row
	 *
	 * @return void
	 */
	public function onAfterStoreRegistrant($row)
	{
		if (strpos($row->payment_method, 'os_offline') !== false)
		{
			$this->addRegistrantToMailchimp($row);
		}
	}

	/**
	 * Add registrants to Mailchimp when payment for registration completed or registration is approved
	 *
	 * @param   EventbookingTableRegistrant  $row
	 */
	public function onAfterPaymentSuccess($row)
	{
		if (strpos($row->payment_method, 'os_offline') === false)
		{
			$this->addRegistrantToMailchimp($row);
		}
	}

	/**
	 * Add registrant to mailchimp
	 *
	 * @param   EventbookingTableRegistrant  $row
	 */
	private function addRegistrantToMailchimp($row)
	{
	    // Do not process further if no API Key provided
		if (!$this->params->get('api_key'))
        {
            return;
        }

		$config = EventbookingHelper::getConfig();

		// In case subscriber doesn't want to subscribe to newsleter, stop
		if ($config->show_subscribe_newsletter_checkbox && empty($row->subscribe_newsletter))
		{
			return;
		}

		$db       = $this->db;
		$query    = $db->getQuery(true);
		$listIds  = [];
		$groupIds = [];
		$eventIds = [];
		$config   = EventbookingHelper::getConfig();
		$event    = Table::getInstance('Event', 'EventbookingTable');

		if ($config->multiple_booking)
		{
			$query->clear()
				->select('event_id')
				->from('#__eb_registrants')
				->where('id = ' . $row->id . ' OR cart_id = ' . $row->id);
			$db->setQuery($query);
			$eventIds = $db->loadColumn();
		}
		else
		{
			$eventIds[] = $row->event_id;
		}

		foreach ($eventIds as $eventId)
		{
			$event->load($eventId);
			$params            = new Registry($event->params);
			$mailingListIds    = $params->get('mailchimp_list_ids', '');
			$mailchimpGroupIds = $params->get('mailchimp_group_ids', '');

			if (empty($mailingListIds))
			{
				$mailingListIds = $this->params->get('default_list_ids', '');
			}

			if ($mailingListIds)
			{
				$listIds = array_merge($listIds, explode(',', $mailingListIds));
			}

			if ($mailchimpGroupIds)
			{
				$groupIds = array_merge($groupIds, explode(',', $mailchimpGroupIds));
			}
		}

		$listIds  = array_filter($listIds);
		$groupIds = array_filter($groupIds);

		if (empty($listIds) && empty($groupIds))
		{
			return;
		}

		$this->subscribeToMailchimpMailingLists($row, $listIds, $groupIds);

		if ($row->is_group_billing && $this->params->get('add_group_members_to_newsletter'))
		{
			$query->clear()
				->select('user_id, first_name, last_name, email')
				->from('#__eb_registrants')
				->where('group_id = ' . (int) $row->id);
			$db->setQuery($query);
			$groupMembers = $db->loadObjectList();

			foreach ($groupMembers as $groupMember)
			{
				$this->subscribeToMailchimpMailingLists($groupMember, $listIds, $groupIds);
			}
		}
	}

	/**
	 * Subscribe registrant to mailchimp lists and groups
	 *
	 * @param   EventbookingTableRegistrant  $row
	 * @param   array                        $listIds
	 * @param   array                        $groupIds
	 */
	private function subscribeToMailchimpMailingLists($row, $listIds, $groupIds)
	{
		if (!MailHelper::isEmailAddress($row->email))
		{
			return;
		}

		require_once dirname(__FILE__) . '/api/MailChimp.php';

		try
		{
			$mailchimp = new MailChimp($this->params->get('api_key', ''));
		}
		catch (Exception $e)
		{
			$this->logError([], $e->getMessage());

			return;
		}

		if ($this->params->get('double_optin'))
		{
			$status = 'pending';
		}
		else
		{
			$status = 'subscribed';
		}

		$config = EventbookingHelper::getConfig();

		foreach ($groupIds as $groupId)
		{
			list($groupListId, $id) = explode('-', $groupId);
			$listGroupMap[$groupListId][] = $id;
		}

		foreach ($listIds as $listId)
		{
			$data = [
				'skip_merge_validation' => true,
				'id'              => $listId,
				'email_address'   => $row->email,
				'merge_fields'    => [],
				'status'          => $status,
				'update_existing' => true,
			];

			if ($row->first_name)
			{
				$data['merge_fields']['FNAME'] = $row->first_name;
			}

			if ($row->last_name)
			{
				$data['merge_fields']['LNAME'] = $row->last_name;
			}

			if ($row->address && $row->address2 && $row->city && $row->state && $row->zip)
			{
				$data['merge_fields']['ADDRESS'] = [
					'addr1'   => $row->address,
					'addr2'   => $row->address2,
					'city'    => $row->city,
					'state'   => $row->state,
					'zip'     => $row->zip,
					'country' => $row->country ?: $config->get('default_country'),
				];
			}

			if ($row->phone)
			{
				$data['merge_fields']['PHONE'] = $row->phone;
			}

			if (!empty($listGroupMap[$listId]))
			{
				$data['interests'] = [];

				foreach ($listGroupMap[$listId] as $interestId)
				{
					$data['interests'][$interestId] = true;
				}
			}

			$result = $mailchimp->post("lists/$listId/members", $data);

			if ($result === false)
			{
				$this->logError($data, $mailchimp->getLastError());
			}
		}
	}

	/**
	 * Display form allows users to change settings on event add/edit screen
	 *
	 * @param   EventbookingTableEvent  $row
	 */
	private function drawSettingForm($row)
	{
		require_once dirname(__FILE__) . '/api/MailChimp.php';

		try
		{
			$mailchimp = new MailChimp($this->params->get('api_key', ''));
		}
		catch (Exception $e)
		{
			$this->logError([], $e->getMessage());

			return;
		}

		$lists = $mailchimp->get('lists', ['count' => 1000]);

		if ($lists === false)
		{
			return;
		}

		$params = new Registry($row->params);

		if ($row->id)
		{
			$listIds = explode(',', $params->get('mailchimp_list_ids', ''));
		}
		else
		{
			$lists = explode(',', $this->params->get('default_list_ids', ''));
		}

		$options    = [];
		$allListIds = [];

		foreach ($lists['lists'] as $list)
		{
			$options[]    = HTMLHelper::_('select.option', $list['id'], $list['name']);
			$allListIds[] = $list['id'];
		}
		?>
			<div class="control-group">
				<div class="control-label">
	                <?php echo EventbookingHelperHtml::getFieldLabel('mailchimp_list_ids', Text::_('PLG_EB_MAILCHIMP_ASSIGN_TO_LISTS'), Text::_('PLG_EB_ACYMAILING_ASSIGN_TO_LISTS_EXPLAIN')); ?>
				</div>
				<div class="controls">
	                <?php echo EventbookingHelperHtml::getChoicesJsSelect(HTMLHelper::_('select.genericlist', $options, 'mailchimp_list_ids[]', 'class="inputbox" multiple="multiple" size="10"', 'value', 'text', $listIds)); ?>
				</div>
			</div>
		<?php

		$groupOptions = [];

		foreach ($allListIds as $listId)
		{
			$interestCategoriesResponse = $mailchimp->get('lists/' . $listId . '/interest-categories', ['count' => 1000]);

			foreach ($interestCategoriesResponse['categories'] as $category)
			{
				$interestsResponse = $mailchimp->get('lists/' . $listId . '/interest-categories/' . $category['id'] . '/interests', ['count' => 1000]);

				foreach ($interestsResponse['interests'] as $interest)
				{
					$groupOptions[] = HTMLHelper::_('select.option', $listId . '-' . $interest['id'], $category['title'] . '-' . $interest['name']);
				}
			}
		}

		if (count($groupOptions))
		{
		?>
			<div class="control-group">
				<div class="control-label">
					<?php echo EventbookingHelperHtml::getFieldLabel('mailchimp_group_ids', Text::_('PLG_EB_MAILCHIMP_ADD_TO_GROUPS'), Text::_('PLG_EB_MAILCHIMP_ADD_TO_GROUPS_EXPLAIN')); ?>
				</div>
				<div class="controls">
					<?php echo EventbookingHelperHtml::getChoicesJsSelect(HTMLHelper::_('select.genericlist', $groupOptions, 'mailchimp_group_ids[]', 'class="form-select advSelect" multiple="multiple" size="10"', 'value', 'text', explode(',', $params->get('mailchimp_group_ids', '')))); ?>
				</div>
			</div>
		<?php
		}
	}

	/**
	 * Method to check to see whether the plugin should run
	 *
	 * @param   EventbookingTableEvent  $row
	 *
	 * @return bool
	 */
	private function canRun($row)
	{
		if ($this->app->isClient('site') && !$this->params->get('show_on_frontend'))
		{
			return false;
		}

		return true;
	}

	/**
	 * Log the error from API call
	 *
	 * @param   array   $data
	 * @param   string  $error
	 */
	private function logError($data, $error)
	{
		$text = '[' . date('m/d/Y g:i A') . '] - ';

		foreach ($data as $key => $value)
		{
			if (is_array($value))
			{
				foreach ($value as $keyValue => $valueValue)
				{
					$text .= "$keyValue=$valueValue, ";
				}
			}
			else
			{
				$text .= "$key=$value, ";
			}
		}

		$text .= $error;

		$ipnLogFile = JPATH_ROOT . '/components/com_eventbooking/mailchimp_api_errors.txt';
		$fp         = fopen($ipnLogFile, 'a');
		fwrite($fp, $text . "\n\n");
		fclose($fp);
	}
}
