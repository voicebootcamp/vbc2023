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
use Joomla\CMS\Form\Form;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Registry\Registry;

trait OSMembershipViewPlan
{
	protected function prepareView()
	{
		parent::prepareView();

		PluginHelper::importPlugin('osmembership');
		$app    = Factory::getApplication();
		$config = OSMembershipHelper::getConfig();
		$db     = Factory::getDbo();
		$query  = $db->getQuery(true);

		$item  = $this->item;
		$lists = &$this->lists;

		//Trigger plugins
		$results                         = array_filter($app->triggerEvent('onEditSubscriptionPlan', [$item]));
		$lists['subscribe_access']       = HTMLHelper::_('access.level', 'subscribe_access', $this->item->subscribe_access, 'class="form-select"',
			false);
		$lists['enable_renewal']         = OSMembershipHelperHtml::getBooleanInput('enable_renewal', $item->enable_renewal);
		$lists['lifetime_membership']    = OSMembershipHelperHtml::getBooleanInput('lifetime_membership', $item->lifetime_membership);
		$lists['recurring_subscription'] = OSMembershipHelperHtml::getBooleanInput('recurring_subscription', $item->recurring_subscription);
		$lists['thumb']                  = HTMLHelper::_('list.images', 'thumb', $item->thumb, ' ', '/media/com_osmembership/');

		$lists['category_id'] = OSMembershipHelperHtml::buildCategoryDropdown($item->category_id, 'category_id');

		$options                      = [];
		$options[]                    = HTMLHelper::_('select.option', 'D', Text::_('OSM_DAYS'));
		$options[]                    = HTMLHelper::_('select.option', 'W', Text::_('OSM_WEEKS'));
		$options[]                    = HTMLHelper::_('select.option', 'M', Text::_('OSM_MONTHS'));
		$options[]                    = HTMLHelper::_('select.option', 'Y', Text::_('OSM_YEARS'));
		$lists['trial_duration_unit'] = HTMLHelper::_(
			'select.genericlist',
			$options,
			'trial_duration_unit',
			'class="form-select input-medium d-inline-block"',
			'value',
			'text',
			$item->trial_duration_unit
		);

		$options   = [];
		$options[] = HTMLHelper::_('select.option', 'm', Text::_('OSM_MINUTES'));
		$options[] = HTMLHelper::_('select.option', 'H', Text::_('OSM_HOURS'));
		$options[] = HTMLHelper::_('select.option', 'D', Text::_('OSM_DAYS'));
		$options[] = HTMLHelper::_('select.option', 'W', Text::_('OSM_WEEKS'));
		$options[] = HTMLHelper::_('select.option', 'M', Text::_('OSM_MONTHS'));
		$options[] = HTMLHelper::_('select.option', 'Y', Text::_('OSM_YEARS'));

		$lists['subscription_length_unit'] = HTMLHelper::_(
			'select.genericlist',
			$options,
			'subscription_length_unit',
			'class="form-select input-medium d-inline-block"',
			'value',
			'text',
			$item->subscription_length_unit
		);

		$lists['extend_duration_unit'] = HTMLHelper::_(
			'select.genericlist',
			$options,
			'extend_duration_unit',
			'class="form-select input-medium d-inline-block"',
			'value',
			'text',
			$item->extend_duration_unit
		);

		$query->clear()
			->select('id, title')
			->from('#__osmembership_plans')
			->where('published = 1')
			->where('id != ' . (int) $item->id)
			->order('ordering');
		$db->setQuery($query);
		$this->plans = $db->loadObjectList();

		//Get list of renew and upgrade options
		if ($item->id > 0)
		{
			$query->clear()
				->select('*')
				->from('#__osmembership_renewrates')
				->where('plan_id = ' . $item->id)
				->order('id');
			$db->setQuery($query);
			$prices = $db->loadObjectList();

			$query->clear()
				->select('*')
				->from('#__osmembership_upgraderules')
				->where('from_plan_id = ' . $item->id)
				->order('id');
			$db->setQuery($query);
			$upgradeRules = $db->loadObjectList();

			$query->clear()
				->select('*')
				->from('#__osmembership_renewaldiscounts')
				->where('plan_id = ' . $item->id)
				->order('id');
			$db->setQuery($query);
			$renewalDiscounts = $db->loadObjectList();
		}
		else
		{
			$prices           = [];
			$upgradeRules     = [];
			$renewalDiscounts = [];
		}

		// Payment methods
		$options   = [];
		$options[] = HTMLHelper::_('select.option', '', Text::_('OSM_ALL_PAYMENT_METHODS'), 'id', 'title');
		$query->clear()
			->select('id, title')
			->from('#__osmembership_plugins')
			->where('published=1');
		$db->setQuery($query);
		$lists['payment_methods'] = HTMLHelper::_('select.genericlist', array_merge($options, $db->loadObjectList()), 'payment_methods[]',
			' class="form-select" multiple="multiple" ', 'id', 'title', explode(',', (string) $item->payment_methods));

		$options   = [];
		$options[] = HTMLHelper::_('select.option', 0, Text::_('OSM_PENDING'));
		$options[] = HTMLHelper::_('select.option', 1, Text::_('OSM_ACTIVE'));

		$lists['free_plan_subscription_status'] = HTMLHelper::_('select.genericlist', $options, 'free_plan_subscription_status',
			'class="form-select"', 'value', 'text', $item->id ? $item->free_plan_subscription_status : 1);

		// Login redirect
		JLoader::register('MenusHelper', JPATH_ADMINISTRATOR . '/components/com_menus/helpers/menus.php');

		$groups = [];
		$items  = MenusHelper::getMenuLinks();

		foreach ($items as $menu)
		{
			$groups[$menu->menutype] = [];

			foreach ($menu->links as $link)
			{
				$groups[$menu->menutype][] = HTMLHelper::_('select.option', $link->value, $link->text);
			}
		}

		array_unshift($groups, [HTMLHelper::_('select.option', 0, Text::_('OSM_SELECT_MENU_ITEM'))]);

		$lists['login_redirect_menu_id'] = HTMLHelper::_(
			'select.groupedlist',
			$groups,
			'login_redirect_menu_id',
			[
				'id'                 => 'menu_item',
				'list.select'        => $item->login_redirect_menu_id,
				'group.items'        => null,
				'option.key.toHtml'  => false,
				'option.text.toHtml' => false,
				'list.attr'          => 'class="form-select"',
			]
		);

		// Currency code
		$currencies = require_once JPATH_ROOT . '/components/com_osmembership/helper/currencies.php';
		$options    = [];
		$options[]  = HTMLHelper::_('select.option', '', Text::_('OSM_DEFAULT_CURRENCY'));

		foreach ($currencies as $code => $title)
		{
			$options[] = HTMLHelper::_('select.option', $code, $title);
		}

		$lists['currency'] = HTMLHelper::_('select.genericlist', $options, 'currency', ' class="form-select" ', 'value', 'text', $item->currency);

		$options   = [];
		$options[] = HTMLHelper::_('select.option', '0', Text::_('OSM_NO'));
		$options[] = HTMLHelper::_('select.option', '1', Text::_('OSM_BY_DAYS'));
		$options[] = HTMLHelper::_('select.option', '2', Text::_('OSM_BY_MONTHS'));

		$lists['prorated_signup_cost'] = HTMLHelper::_(
			'select.genericlist',
			$options,
			'prorated_signup_cost',
			' class="form-select input-large" ',
			'value',
			'text',
			$item->prorated_signup_cost
		);

		$options   = [];
		$options[] = HTMLHelper::_('select.option', '1', Text::_('OSM_BEFORE'));
		$options[] = HTMLHelper::_('select.option', '-1', Text::_('OSM_AFTER'));

		$lists['send_first_reminder_time'] = HTMLHelper::_(
			'select.genericlist',
			$options,
			'send_first_reminder_time',
			'class="form-select input-small d-inline-block"',
			'value',
			'text',
			$item->send_first_reminder >= 0 ? 1 : -1
		);

		$lists['send_second_reminder_time'] = HTMLHelper::_(
			'select.genericlist',
			$options,
			'send_second_reminder_time',
			'class="form-select input-small d-inline-block"',
			'value',
			'text',
			$item->send_second_reminder >= 0 ? 1 : -1
		);

		$lists['send_third_reminder_time'] = HTMLHelper::_(
			'select.genericlist',
			$options,
			'send_third_reminder_time',
			'class="form-select input-small d-inline-block"',
			'value',
			'text',
			$item->send_third_reminder >= 0 ? 1 : -1
		);

		$lists['send_subscription_end_time'] = HTMLHelper::_(
			'select.genericlist',
			$options,
			'send_subscription_end_time',
			'class="form-select input-small d-inline-block"',
			'value',
			'text',
			$item->send_subscription_end >= 0 ? 1 : -1
		);

		$item->send_first_reminder  = abs($item->send_first_reminder);
		$item->send_second_reminder = abs($item->send_second_reminder);
		$item->send_third_reminder  = abs($item->send_third_reminder);

		$this->datePickerFormat = $config->get('date_field_format', '%Y-%m-%d');
		$this->prices           = $prices;
		$this->upgradeRules     = $upgradeRules;
		$this->renewalDiscounts = $renewalDiscounts;
		$this->plugins          = $results;
		$this->nullDate         = $db->getNullDate();
		$this->config           = $config;

		$dateFields = ['expired_date', 'publish_up', 'publish_down'];

		foreach ($dateFields as $dateField)
		{
			if ($this->item->{$dateField} == $this->nullDate)
			{
				$this->item->{$dateField} = '';
			}
		}

		if (Factory::getApplication()->isClient('site'))
		{
			$this->addToolbar();
		}

		if ($app->isClient('administrator'))
		{
			$lists['category_id'] = OSMembershipHelperHtml::getChoicesJsSelect($lists['category_id'], Text::_('OSM_TYPE_OR_SELECT_ONE_CATEGORY'));

			$keys = [
				'login_redirect_menu_id',
				'payment_methods',
				'currency',
			];

			foreach ($keys as $key)
			{
				$lists[$key] = OSMembershipHelperHtml::getChoicesJsSelect($lists[$key]);
			}
		}

		$options   = [];
		$options[] = HTMLHelper::_('select.option', '0', Text::_('OSM_NO_CHANGE'));
		$options[] = HTMLHelper::_('select.option', '1', Text::_('OSM_SET_LIFETIME'));
		$options[] = HTMLHelper::_('select.option', '2', Text::_('OSM_EXTEND_SUBSCRIPTION'));

		$lists['last_payment_action'] = HTMLHelper::_(
			'select.genericlist',
			$options,
			'last_payment_action',
			' class="form-select input-large" ',
			'value',
			'text',
			$item->last_payment_action
		);

		$params = new Registry($item->params);

		$options   = [];
		$options[] = HTMLHelper::_('select.option', '0', Text::_('OSM_REGISTER_DATE'));
		$options[] = HTMLHelper::_('select.option', '1', Text::_('OSM_FIXED_DATE'));
		$options[] = HTMLHelper::_('select.option', '2', Text::_('OSM_DETERMINE_BY_CUSTOMER'));

		$lists['subscription_start_date_option'] = HTMLHelper::_(
			'select.genericlist',
			$options,
			'subscription_start_date_option',
			' class="form-select input-large" ',
			'value',
			'text',
			$params->get('subscription_start_date_option', '0')
		);

		// Get list of date fields for subscription_start_date selection
		$query->clear()
			->select('name, title')
			->from('#__osmembership_fields')
			->where('fieldtype = "Date"')
			->where('published = 1')
			->order('title');
		$db->setQuery($query);

		$options   = [];
		$options[] = HTMLHelper::_('select.option', '', Text::_('OSM_SELECT'), 'name', 'title');
		$options   = array_merge($options, $db->loadObjectList());

		$lists['subscription_start_date_field'] = HTMLHelper::_(
			'select.genericlist',
			$options,
			'subscription_start_date_field',
			' class="form-select input-large" ',
			'name',
			'title',
			$params->get('subscription_start_date_field')
		);

		if (file_exists(JPATH_ROOT . '/components/com_osmembership/fields.xml')
			&& filesize(JPATH_ROOT . '/components/com_osmembership/fields.xml') > 0)
		{
			$registry = new Registry;
			$registry->loadString($item->custom_fields);
			$data         = new stdClass;
			$data->fields = $registry->toArray();

			try
			{
				$form = Form::getInstance('plan_fields', JPATH_ROOT . '/components/com_osmembership/fields.xml', [], false, '//config');
				$form->bind($data);
				$this->planFieldsForm = $form;
			}
			catch (Exception $e)
			{
				$this->planFieldsForm = false;
			}
		}

		$lists['subscription_start_date_field'] = OSMembershipHelperHtml::getChoicesJsSelect($lists['subscription_start_date_field']);

		$this->planParams = $params;

		return true;
	}
}
