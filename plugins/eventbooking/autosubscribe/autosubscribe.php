<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2021 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Registry\Registry;


class plgEventBookingAutoSubscribe extends CMSPlugin
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

	public function __construct(&$subject, $config = [])
	{
		parent::__construct($subject, $config);

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

		return [
			'title' => Text::_('Auto Subscribe'),
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
		// The plugin will only be available in the backend
		if (!$this->canRun($row))
		{
			return;
		}

		$params = new Registry($row->params);
		$params->set('auto_subscribe_plan_id', trim($data['auto_subscribe_plan_id']));
		$row->params = $params->toString();
		$row->store();
	}


	/**
	 * Run when a membership activated
	 *
	 * @param   EventbookingTableRegistrant  $row
	 */
	public function onAfterPaymentSuccess($row)
	{
		if (!$row->user_id)
		{
			return;
		}

		$registrantParams = new Registry($row->params);

		// This record was processed, stop
		if ($registrantParams->get('auto_subscribe_processed'))
		{
			return;
		}

		$event  = EventbookingHelperDatabase::getEvent($row->event_id);
		$params = new Registry($event->params);

		$planId = (int) $params->get('auto_subscribe_plan_id', '');

		if (empty($planId))
		{
			return;
		}

		$db    = $this->db;
		$query = $db->getQuery(true);

		if ($this->params->get('ignore_if_user_has_existing_subscription'))
		{
			// Stop process in case user already subscribed for the plan
			$query->select('COUNT(*)')
				->from('#__osmembership_subscribers')
				->where('user_id = ' . $row->user_id)
				->where('(published >= 1 OR payment_method LIKE "os_offline%")');
			$db->setQuery($query);

			if ($db->loadResult())
			{
				return;
			}
		}

		// Membership Pro auto-loader class
		require_once JPATH_ADMINISTRATOR . '/components/com_osmembership/loader.php';

		/* @var OSMembershipModelApi $model */
		$model = MPFModel::getInstance('Api', 'OSMembershipModel',
			['ignore_request' => true, 'remember_states' => false]);

		$data = $this->getSubscriptionDataFromRegistrant($row);

		$data['plan_id'] = $planId;

		try
		{
			$model->store($data);
			$registrantParams->set('auto_subscribe_processed', 1);
			$row->params = $registrantParams->toString();
			$row->store();
		}
		catch (Exception $e)
		{
			EventbookingHelper::logData(__DIR__.'/logs.txt', $data, 'Create Subscription Error');
		}
	}

	/**
	 * Get registrant data
	 *
	 * @param   EventbookingTableRegistrant  $row
	 *
	 * @return
	 */
	private function getSubscriptionDataFromRegistrant($row)
	{
		$db    = $this->db;
		$query = $db->getQuery(true);
		$data  = ['user_id' => $row->user_id, 'email' => $row->email];

		// Reset amount data, set it to 0  for the auto-subscribed subscription
		$data['amount'] = $data['discount_amount'] = $data['tax_amount'] = $data['payment_processing_fee'] = $data['tax_rate'] = 0;

		// None core fields
		$query->select('a.*, b.field_value')
			->from('#__eb_fields AS a')
			->innerJoin('#__eb_field_values AS b ON a.id = b.field_id')
			->where('b.registrant_id = ' . $row->id);
		$db->setQuery($query);

		foreach ($db->loadObjectList() as $rowFieldValue)
		{
			if ($rowFieldValue->field_mapping)
			{
				$data[$rowFieldValue->field_mapping] = $rowFieldValue->field_value;
			}
		}

		// core fields
		$query->clear()
			->select('*')
			->from('#__eb_fields')
			->where('is_core = 1')
			->where('published = 1');
		$db->setQuery($query);
		$coreFields = $db->loadObjectList();

		foreach ($coreFields as $coreField)
		{
			if ($coreField->field_mapping)
			{
				$data[$coreField->field_mapping] = $row->{$coreField->name};
			}
			else
			{
				$data[$coreField->name] = $row->{$coreField->name};
			}
		}

		return $data;
	}


	/**
	 * Get Joomla groups from custom fields which subscriber select for their subscription
	 *
	 * @param   EventbookingTableRegistrant  $row
	 *
	 * @return array
	 */

	/**
	 * Display form allows users to change settings on subscription plan add/edit screen
	 *
	 * @param   EventbookingTableEvent  $row
	 */
	private function drawSettingForm($row)
	{
		?>
		<div class="control-group">
			<div class="control-label">
				<?php echo EventbookingHelperHtml::getFieldLabel('auto_subscribe_plan_id', Text::_('Select Plan')); ?>
			</div>
			<div class="control-group">
				<?php
				$params = new Registry($row->params);
				$planId = (int) $params->get('auto_subscribe_plan_id', '');

				$db    = $this->db;
				$query = $db->getQuery(true)
					->select('id, title')
					->from('#__osmembership_plans')
					->where('published = 1')
					->order('ordering');
				$db->setQuery($query);
				$options                         = [];
				$options[]                       = HTMLHelper::_('select.option', '', Text::_('Select Plan'), 'id', 'title');
				$options                         = array_merge($options, $db->loadObjectList());
				$lists['auto_subscribe_plan_id'] = HTMLHelper::_('select.genericlist', $options, 'auto_subscribe_plan_id',
					'form-select class="validate[required]"', 'id', 'title', $planId);

				echo EventbookingHelperHtml::getChoicesJsSelect($lists['auto_subscribe_plan_id']);
				?>
			</div>
		</div>
		<?php
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
}