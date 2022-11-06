<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2022 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\Utilities\ArrayHelper;

class OSMembershipViewSubscribersHtml extends MPFViewList
{
	/**
	 * The subscriptions custom fields data
	 *
	 * @var array
	 */
	protected $fieldsData;

	/**
	 * Custom Fields which will be displayed on subscriptions management
	 *
	 * @var array
	 */
	protected $fields;

	/**
	 * Contains filter selects use to filter data
	 *
	 * @var array
	 */
	protected $filters;

	/**
	 * Component config
	 *
	 * @var MPFConfig
	 */
	protected $config;

	/**
	 * Bootstrap Helper
	 *
	 * @var OSMembershipHelperBootstrap
	 */
	protected $bootstrapHelper;

	/**
	 * Active menu item parameters
	 *
	 * @var \Joomla\Registry\Registry
	 */
	protected $params;

	/**
	 * Constructor
	 *
	 * @param   array  $config
	 */
	public function __construct($config = [])
	{
		parent::__construct($config);

		$this->params      = $this->getParams(['subscribers']);
		$this->hideButtons = $this->params->get('hide_buttons', []);
	}

	/**
	 * Prepare view data before displaying
	 *
	 * @return void
	 */
	protected function prepareView()
	{
		parent::prepareView();

		$user        = Factory::getUser();
		$fieldSuffix = OSMembershipHelper::getFieldSuffix();
		$db          = Factory::getDbo();
		$query       = $db->getQuery(true)
			->select('id')
			->select($db->quoteName('title' . $fieldSuffix, 'title'))
			->from('#__osmembership_plans')
			->where('published = 1')
			->order('ordering');

		// If user does not have super-user permisson, only allow them to manage subscriptions from the plans which they created
		if (!$user->authorise('core.admin', 'com_osmembership'))
		{
			$query->where('subscriptions_manage_user_id IN (0, ' . $user->id . ')');
		}

		if ($this->params->get('plan_ids'))
		{
			$query->where('id IN (' . implode(',', ArrayHelper::toInteger($this->params->get('plan_ids'))) . ')');
		}

		if ($this->params->get('exclude_plan_ids'))
		{
			$query->where('id NOT IN (' . implode(',', ArrayHelper::toInteger($this->params->get('exclude_plan_ids'))) . ')');
		}

		$db->setQuery($query);

		$options   = [];
		$options[] = HTMLHelper::_('select.option', 0, Text::_('OSM_ALL_PLANS'), 'id', 'title');
		$options   = array_merge($options, $db->loadObjectList());

		$this->lists['plan_id'] = HTMLHelper::_('select.genericlist', $options, 'plan_id', 'class="form-select" onchange="submit();"', 'id', 'title',
			$this->state->plan_id);

		$rowFields = OSMembershipHelper::getProfileFields($this->state->plan_id, true);

		$fields             = [];
		$filters            = [];
		$filterFieldsValues = $this->state->get('filter_fields', []);

		foreach ($rowFields as $rowField)
		{
			if ($rowField->filterable)
			{
				$fieldOptions = explode("\r\n", $rowField->values);

				$options   = [];
				$options[] = HTMLHelper::_('select.option', '', $rowField->title);

				foreach ($fieldOptions as $option)
				{
					$options[] = HTMLHelper::_('select.option', $option, $option);
				}

				$filters['field_' . $rowField->id] = HTMLHelper::_('select.genericlist', $options, 'filter_fields[field_' . $rowField->id . ']',
					'class="form-select input-medium" onchange="submit();"', 'value', 'text',
					ArrayHelper::getValue($filterFieldsValues, 'field_' . $rowField->id));
			}

			if ($rowField->show_on_subscriptions != 1 || in_array($rowField->name, ['first_name', 'last_name']))
			{
				continue;
			}

			$fields[$rowField->id] = $rowField;
		}

		if (count($fields))
		{
			$this->fieldsData = $this->model->getFieldsData(array_keys($fields));
		}

		$options   = [];
		$options[] = HTMLHelper::_('select.option', 0, Text::_('OSM_ALL_SUBSCRIPTIONS'));
		$options[] = HTMLHelper::_('select.option', 1, Text::_('OSM_NEW_SUBSCRIPTION'));
		$options[] = HTMLHelper::_('select.option', 2, Text::_('OSM_SUBSCRIPTION_RENEWAL'));
		$options[] = HTMLHelper::_('select.option', 3, Text::_('OSM_SUBSCRIPTION_UPGRADE'));

		$this->lists['subscription_type'] = HTMLHelper::_('select.genericlist', $options, 'subscription_type',
			'class="form-select" onchange="submit();"', 'value', 'text', $this->state->subscription_type);

		$options   = [];
		$options[] = HTMLHelper::_('select.option', -1, Text::_('OSM_ALL'));
		$options[] = HTMLHelper::_('select.option', 0, Text::_('OSM_PENDING'));
		$options[] = HTMLHelper::_('select.option', 1, Text::_('OSM_ACTIVE'));
		$options[] = HTMLHelper::_('select.option', 2, Text::_('OSM_EXPIRED'));
		$options[] = HTMLHelper::_('select.option', 3, Text::_('OSM_CANCELLED_PENDING'));
		$options[] = HTMLHelper::_('select.option', 4, Text::_('OSM_CANCELLED_REFUNDED'));

		$this->lists['published'] = HTMLHelper::_('select.genericlist', $options, 'published',
			'class="input-medium form-select" onchange="submit();"', 'value', 'text', $this->state->published);

		$this->config          = OSMembershipHelper::getConfig();
		$this->fields          = $fields;
		$this->filters         = $filters;
		$this->bootstrapHelper = OSMembershipHelperBootstrap::getInstance();

		$this->addToolbar();
	}

	/**
	 * Add Toolbar
	 */
	protected function addToolbar()
	{
		if ($this->getLayout() == 'import')
		{
			ToolbarHelper::title(Text::_('OSM_IMPORT_SUBSCRIPTIONS'));
			ToolbarHelper::save('import_subscriptions', 'OSM_IMPORT');
			ToolbarHelper::cancel('cancel');
		}
		else
		{
			parent::addToolbar();

			if (!in_array('renew', $this->hideButtons))
			{
				ToolbarHelper::custom('renew', 'plus', 'plus', 'OSM_RENEW_SUBSCRIPTION', true);
			}

			if (!in_array('export', $this->hideButtons))
			{
				ToolbarHelper::custom('export', 'download', 'download', 'OSM_EXPORT', false);
			}

			if (!in_array('batch_mail', $this->hideButtons))
			{
				// Instantiate a new JLayoutFile instance and render the batch button
				$layout = new JLayoutFile('joomla.toolbar.batch');

				$bar   = JToolbar::getInstance('toolbar');
				$dhtml = $layout->render(['title' => Text::_('OSM_MASS_MAIL')]);
				$bar->appendButton('Custom', $dhtml, 'batch');
			}

			$config = OSMembershipHelper::getConfig();

			if ($config->enable_subscription_payment && !in_array('request_payment', $this->hideButtons))
			{
				ToolbarHelper::custom('request_payment', 'envelope', 'envelope', 'OSM_REQUEST_PAYMENT', true);
			}
		}
	}
}
