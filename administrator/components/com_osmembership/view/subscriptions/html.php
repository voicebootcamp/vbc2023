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
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\Utilities\ArrayHelper;

class OSMembershipViewSubscriptionsHtml extends MPFViewList
{
	protected function prepareView()
	{
		parent::prepareView();

		$db    = Factory::getDbo();
		$query = $db->getQuery(true)
			->select('id, title')
			->from('#__osmembership_plans')
			->where('published = 1')
			->order('ordering');

		if ($this->state->filter_category_id > 0)
		{
			$query->where('category_id = ' . $this->state->filter_category_id);
		}

		$db->setQuery($query);

		$options                = [];
		$options[]              = HTMLHelper::_('select.option', 0, Text::_('OSM_SELECT_PLAN'), 'id', 'title');
		$options                = array_merge($options, $db->loadObjectList());
		$this->lists['plan_id'] = HTMLHelper::_('select.genericlist', $options, 'plan_id', 'class="form-select" onchange="submit();" ', 'id', 'title',
			$this->state->plan_id);
		$this->lists['plan_id'] = OSMembershipHelperHtml::getChoicesJsSelect($this->lists['plan_id'], Text::_('OSM_TYPE_OR_SELECT_ONE_PLAN'));

		$options   = [];
		$options[] = HTMLHelper::_('select.option', 'H', Text::_('OSM_HOURS'));
		$options[] = HTMLHelper::_('select.option', 'D', Text::_('OSM_DAYS'));
		$options[] = HTMLHelper::_('select.option', 'W', Text::_('OSM_WEEKS'));
		$options[] = HTMLHelper::_('select.option', 'M', Text::_('OSM_MONTHS'));
		$options[] = HTMLHelper::_('select.option', 'Y', Text::_('OSM_YEARS'));

		$this->lists['extend_subscription_duration_unit'] = HTMLHelper::_(
			'select.genericlist',
			$options,
			'extend_subscription_duration_unit',
			'class="form-select input-medium d-inline-block"',
			'value',
			'text',
			'D'
		);

		$query->clear()
			->select('id, title')
			->from('#__osmembership_categories')
			->where('published = 1')
			->order('title');
		$db->setQuery($query);

		$categories = $db->loadObjectList();

		if (count($categories) > 0)
		{
			$options                           = [];
			$options[]                         = HTMLHelper::_('select.option', 0, Text::_('OSM_SELECT_CATEGORY'), 'id', 'title');
			$options                           = array_merge($options, $categories);
			$this->lists['filter_category_id'] = HTMLHelper::_('select.genericlist', $options, 'filter_category_id',
				'class="form-select" onchange="submit();" ', 'id', 'title', $this->state->filter_category_id);
			$this->lists['filter_category_id'] = OSMembershipHelperHtml::getChoicesJsSelect($this->lists['filter_category_id'],
				Text::_('OSM_TYPE_OR_SELECT_ONE_CATEGORY'));
		}

		$options                          = [];
		$options[]                        = HTMLHelper::_('select.option', 0, Text::_('OSM_ALL_SUBSCRIPTIONS'));
		$options[]                        = HTMLHelper::_('select.option', 1, Text::_('OSM_NEW_SUBSCRIPTION'));
		$options[]                        = HTMLHelper::_('select.option', 2, Text::_('OSM_SUBSCRIPTION_RENEWAL'));
		$options[]                        = HTMLHelper::_('select.option', 3, Text::_('OSM_SUBSCRIPTION_UPGRADE'));
		$this->lists['subscription_type'] = HTMLHelper::_('select.genericlist', $options, 'subscription_type',
			' class="form-select input-medium" onchange="submit();" ', 'value', 'text', $this->state->subscription_type);

		$options                  = [];
		$options[]                = HTMLHelper::_('select.option', -1, Text::_('OSM_ALL'));
		$options[]                = HTMLHelper::_('select.option', 0, Text::_('OSM_PENDING'));
		$options[]                = HTMLHelper::_('select.option', 1, Text::_('OSM_ACTIVE'));
		$options[]                = HTMLHelper::_('select.option', 2, Text::_('OSM_EXPIRED'));
		$options[]                = HTMLHelper::_('select.option', 3, Text::_('OSM_CANCELLED_PENDING'));
		$options[]                = HTMLHelper::_('select.option', 4, Text::_('OSM_CANCELLED_REFUNDED'));
		$this->lists['published'] = HTMLHelper::_('select.genericlist', $options, 'published',
			' class="form-select input-medium" onchange="submit();" ', 'value', 'text', $this->state->published);

		$options                          = [];
		$options[]                        = HTMLHelper::_('select.option', 'tbl.created_date', Text::_('OSM_CREATED_DATE'));
		$options[]                        = HTMLHelper::_('select.option', 'tbl.from_date', Text::_('OSM_START_DATE'));
		$options[]                        = HTMLHelper::_('select.option', 'tbl.to_date', Text::_('OSM_END_DATE'));
		$this->lists['filter_date_field'] = HTMLHelper::_('select.genericlist', $options, 'filter_date_field', ' class="form-select input-medium" ',
			'value', 'text', $this->state->filter_date_field);

		$options   = [];
		$options[] = HTMLHelper::_('select.option', '', Text::_('OSM_DURATION'));
		$options[] = HTMLHelper::_('select.option', 'today', Text::_('OSM_TODAY'));
		$options[] = HTMLHelper::_('select.option', 'yesterday', Text::_('OSM_YESTERDAY'));
		$options[] = HTMLHelper::_('select.option', 'this_week', Text::_('OSM_THIS_WEEK'));
		$options[] = HTMLHelper::_('select.option', 'last_week', Text::_('OSM_LAST_WEEK'));
		$options[] = HTMLHelper::_('select.option', 'this_month', Text::_('OSM_THIS_MONTH'));
		$options[] = HTMLHelper::_('select.option', 'last_month', Text::_('OSM_LAST_MONTH'));
		$options[] = HTMLHelper::_('select.option', 'this_year', Text::_('OSM_THIS_YEAR'));
		$options[] = HTMLHelper::_('select.option', 'last_year', Text::_('OSM_LAST_YEAR'));
		$options[] = HTMLHelper::_('select.option', 'last_7_days', Text::_('OSM_LAST_7_DAYS'));
		$options[] = HTMLHelper::_('select.option', 'last_30_days', Text::_('OSM_LAST_30_DAYS'));

		$this->lists['filter_subscription_duration'] = HTMLHelper::_('select.genericlist', $options, 'filter_subscription_duration',
			' class="form-select input-medium" onchange="submit()" ', 'value', 'text', $this->state->filter_subscription_duration);

		$rowFields    = OSMembershipHelper::getProfileFields($this->state->plan_id, true);
		$fields       = [];
		$filters      = [];
		$showLastName = false;

		$filterFieldsValues = $this->state->get('filter_fields', []);

		foreach ($rowFields as $rowField)
		{
			if ($rowField->name == 'last_name')
			{
				$showLastName = true;
			}

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
					' class="form-select input-medium" onchange="submit();" ', 'value', 'text',
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

		$config                 = OSMembershipHelper::getConfig();
		$this->datePickerFormat = $config->get('date_field_format', '%Y-%m-%d');
		$this->config           = $config;
		$this->fields           = $fields;
		$this->filters          = $filters;
		$this->showLastName     = $showLastName;
	}

	/**
	 * Custom Toolbar buttons
	 *
	 * @return void
	 */
	protected function addCustomToolbarButtons()
	{
		$config = OSMembershipHelper::getConfig();
		$bar    = Toolbar::getInstance('toolbar');

		ToolbarHelper::custom('renew', 'plus', 'plus', 'OSM_RENEW_SUBSCRIPTION', true);
		ToolbarHelper::custom('request_payment', 'envelope', 'envelope', 'OSM_REQUEST_PAYMENT', true);

		if (OSMembershipHelper::isJoomla4())
		{
			/* @var \Joomla\CMS\Toolbar\Button\DropdownButton $dropdown */
			$dropdown = $bar->dropdownButton('status-group')
				->text('OSM_EXPORT')
				->toggleSplit(false)
				->icon('icon-ellipsis-h')
				->buttonClass('btn btn-action');

			$childBar = $dropdown->getChildToolbar();

			$childBar->standardButton('export')
				->text('OSM_EXPORT_EXCEL')
				->icon('icon-download')
				->task('export');

			$childBar->standardButton('export_pdf')
				->text('OSM_EXPORT_PDF')
				->icon('icon-download')
				->task('export_pdf');

			if ($config->activate_invoice_feature)
			{
				$childBar->standardButton('export_invoices')
					->text('OSM_EXPORT_INVOICES')
					->icon('icon-download')
					->task('export_invoices');
			}
		}
		else
		{
			ToolbarHelper::custom('export', 'download', 'download', 'OSM_EXPORT_EXCEL', false);
			ToolbarHelper::custom('export_pdf', 'download', 'download', 'OSM_EXPORT_PDF', false);

			if ($config->activate_invoice_feature)
			{
				ToolbarHelper::custom('export_invoices', 'download', 'download', 'OSM_EXPORT_INVOICES', false);
			}
		}

		// Batch subscriptions
		if (OSMembershipHelper::isJoomla4())
		{
			$bar->popupButton('batch')
				->text('JTOOLBAR_BATCH')
				->selector('collapseModal_Subscriptions')
				->listCheck(true);
		}
		else
		{
			$dhtml = OSMembershipHelperHtml::loadCommonLayout('common/tmpl/batch.php',
				['title' => Text::_('JTOOLBAR_BATCH'), 'selector' => 'collapseModal_Subscriptions']);
			$bar->appendButton('Custom', $dhtml, 'batch');
		}
		
		ToolbarHelper::custom('resend_email', 'envelope', 'envelope', 'OSM_RESEND_EMAIL', true);
		ToolbarHelper::custom('disable_reminders', 'delete', 'delete', 'OSM_DISABLE_REMINDERS', true);

		// Mass Mail
		$layout = new JLayoutFile('joomla.toolbar.batch');
		$dhtml  = $layout->render(['title' => Text::_('OSM_MASS_MAIL')]);
		$bar->appendButton('Custom', $dhtml, 'batch');

		// Batch SMS
		if (PluginHelper::isEnabled('system', 'membershippro'))
		{
			if (OSMembershipHelper::isJoomla4())
			{
				$bar->popupButton('batch')
					->text('OSM_BATCH_SMS')
					->selector('collapseModal_Sms')
					->listCheck(true);
			}
			else
			{
				$dhtml = OSMembershipHelperHtml::loadCommonLayout('common/tmpl/batch.php',
					['title' => Text::_('OSM_BATCH_SMS'), 'selector' => 'collapseModal_Sms']);
				$bar->appendButton('Custom', $dhtml, 'batch');
			}
		}
	}
}
