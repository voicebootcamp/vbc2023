<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2022 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Filesystem\Folder;

class EventbookingViewFieldHtml extends RADViewItem
{
	protected function prepareView()
	{
		parent::prepareView();

		$db     = Factory::getDbo();
		$query  = $db->getQuery(true);
		$config = EventbookingHelper::getConfig();

		// Set default display_in property for new field
		if (!$this->item->id)
		{
			$this->item->display_in = 5;
		}

		$options    = [];
		$fieldTypes = ['Text', 'Url', 'Email', 'Number', 'Tel', 'Textarea', 'List', 'Checkboxes', 'Radio', 'Date', 'Heading', 'Message', 'File', 'Countries', 'State', 'SQL', 'Range', 'Hidden', 'Password'];

		foreach ($fieldTypes as $fieldType)
		{
			$options[] = HTMLHelper::_('select.option', $fieldType, $fieldType);
		}

		// Allow adding custom field type
		$files     = Folder::files(JPATH_ADMINISTRATOR . '/components/com_eventbooking/libraries/rad/form/field', 'php$');
		$coreFiles = [];

		foreach ($fieldTypes as $fieldType)
		{
			$coreFiles[] = strtolower($fieldType . '.php');
		}

		foreach ($files as $file)
		{
			if (!in_array($file, $coreFiles))
			{
				$fieldType = ucfirst(File::stripExt($file));
				$options[] = HTMLHelper::_('select.option', $fieldType, $fieldType);
			}
		}

		if ($this->item->is_core)
		{
			$readOnly = ' readonly="true" ';
		}
		else
		{
			$readOnly = '';
		}

		$this->lists['fieldtype'] = HTMLHelper::_(
			'select.genericlist',
			$options,
			'fieldtype',
			' class="form-select" ' . $readOnly,
			'value',
			'text',
			$this->item->fieldtype
		);

		if ($config->custom_field_by_category)
		{
			if (empty($this->item->id) || $this->item->category_id == -1)
			{
				$selectedCategoryIds[] = -1;
			}
			else
			{
				$query->select('category_id')
					->from('#__eb_field_categories')
					->where('field_id=' . $this->item->id);
				$db->setQuery($query);
				$selectedCategoryIds = $db->loadColumn();
			}

			$this->lists['category_id'] = EventbookingHelperHtml::getCategoryListDropdown(
				'category_id[]',
				$selectedCategoryIds,
				'class="input-xlarge form-select" multiple="multiple"',
				null,
				[],
				-1,
				'EB_ALL_CATEGORIES'
			);
		}
		else
		{

			if (empty($this->item->id) || $this->item->event_id == -1)
			{
				$selectedEventIds[] = -1;
				$assignment         = 0;
			}
			else
			{
				$query->select('event_id')
					->from('#__eb_field_events')
					->where('field_id=' . $this->item->id);
				$db->setQuery($query);
				$selectedEventIds = $db->loadColumn();

				if (count($selectedEventIds) && $selectedEventIds[0] < 0)
				{
					$assignment = -1;
				}
				else
				{
					$assignment = 1;
				}

				$selectedEventIds = array_map('abs', $selectedEventIds);
			}

			$filters = [];

			if ($config->hide_disable_registration_events)
			{
				$filters[] = 'registration_type != 3';
			}

			$rows                    = EventbookingHelperDatabase::getAllEvents($config->sort_events_dropdown, $config->hide_past_events_from_events_dropdown, $filters);
			$this->lists['event_id'] = EventbookingHelperHtml::getEventsDropdown($rows, 'event_id[]', 'class="input-xlarge" multiple="multiple" size="5" ', $selectedEventIds);

			$options   = [];
			$options[] = HTMLHelper::_('select.option', 0, Text::_('EB_ALL_EVENTS'));
			$options[] = HTMLHelper::_('select.option', 1, Text::_('EB_ALL_SELECTED_EVENTS'));

			if (!$config->multiple_booking)
			{
				$options[] = HTMLHelper::_('select.option', -1, Text::_('EB_ALL_EXCEPT_SELECTED_EVENTS'));
			}

			$this->lists['assignment'] = HTMLHelper::_('select.genericlist', $options, 'assignment', 'class="form-select"', 'value', 'text', $assignment);
			$this->assignment          = $assignment;
		}

		// Trigger plugins to get list of fields for mapping
		PluginHelper::importPlugin('eventbooking');

		$results = Factory::getApplication()->triggerEvent('onGetFields', []);
		$fields  = [];

		foreach ($results as $res)
		{
			if (is_array($res) && count($res))
			{
				$fields = $res;
				break;
			}
		}

		if (count($fields))
		{
			$options                      = [];
			$options[]                    = HTMLHelper::_('select.option', '', Text::_('Select Field'));
			$options                      = array_merge($options, $fields);
			$this->lists['field_mapping'] = HTMLHelper::_(
				'select.genericlist',
				$options,
				'field_mapping',
				' class="form-select" ',
				'value',
				'text',
				$this->item->field_mapping
			);
		}

		$results = Factory::getApplication()->triggerEvent('onGetNewsletterFields', []);
		$fields  = [];

		foreach ($results as $res)
		{
			if (is_array($res) && count($res))
			{
				$fields = $res;
				break;
			}
		}

		if (count($fields))
		{
			$options   = [];
			$options[] = HTMLHelper::_('select.option', '', Text::_('Select Field'));

			$options                                 = array_merge($options, $fields);
			$this->lists['newsletter_field_mapping'] = HTMLHelper::_(
				'select.genericlist',
				$options,
				'newsletter_field_mapping',
				' class="form-select" ',
				'value',
				'text',
				$this->item->newsletter_field_mapping
			);
		}

		$options   = [];
		$options[] = HTMLHelper::_('select.option', 0, Text::_('EB_ALL'));
		$options[] = HTMLHelper::_('select.option', 1, Text::_('EB_INDIVIDUAL_BILLING'));
		$options[] = HTMLHelper::_('select.option', 2, Text::_('EB_GROUP_BILLING_FORM'));
		$options[] = HTMLHelper::_('select.option', 3, Text::_('EB_INDIVIDUAL_GROUP_BILLING'));
		$options[] = HTMLHelper::_('select.option', 4, Text::_('EB_GROUP_MEMBER_FORM'));
		$options[] = HTMLHelper::_('select.option', 5, Text::_('EB_GROUP_MEMBER_INDIVIDUAL'));

		$this->lists['display_in'] = HTMLHelper::_(
			'select.genericlist',
			$options,
			'display_in',
			' class="form-select" ',
			'value',
			'text',
			$this->item->display_in
		);

		$options   = [];
		$options[] = HTMLHelper::_('select.option', 0, Text::_('EB_ALL'));
		$options[] = HTMLHelper::_('select.option', 1, Text::_('EB_STANDARD_REGISTRATION'));
		$options[] = HTMLHelper::_('select.option', 2, Text::_('EB_WAITING_LIST'));

		$this->lists['show_on_registration_type'] = HTMLHelper::_(
			'select.genericlist',
			$options,
			'show_on_registration_type',
			' class="form-select" ',
			'value',
			'text',
			$this->item->show_on_registration_type
		);

		$options   = [];
		$options[] = HTMLHelper::_('select.option', 0, Text::_('None'));
		$options[] = HTMLHelper::_('select.option', 1, Text::_('Integer Number'));
		$options[] = HTMLHelper::_('select.option', 2, Text::_('Number'));
		$options[] = HTMLHelper::_('select.option', 3, Text::_('Email'));
		$options[] = HTMLHelper::_('select.option', 4, Text::_('Url'));
		$options[] = HTMLHelper::_('select.option', 5, Text::_('Phone'));
		$options[] = HTMLHelper::_('select.option', 6, Text::_('Past Date'));
		$options[] = HTMLHelper::_('select.option', 7, Text::_('Ip'));
		$options[] = HTMLHelper::_('select.option', 8, Text::_('Min size'));
		$options[] = HTMLHelper::_('select.option', 9, Text::_('Max size'));
		$options[] = HTMLHelper::_('select.option', 10, Text::_('Min integer'));
		$options[] = HTMLHelper::_('select.option', 11, Text::_('Max integer'));

		$this->lists['datatype_validation'] = HTMLHelper::_(
			'select.genericlist',
			$options,
			'datatype_validation',
			'class="form-select"',
			'value',
			'text',
			$this->item->datatype_validation
		);

		$query->clear()
			->select('id, title')
			->from('#__eb_fields')
			->where('fieldtype IN ("List", "Radio", "Checkboxes")')
			->where('published=1');

		if ($this->item->id)
		{
			$query->where('id != ' . $this->item->id);
		}

		$db->setQuery($query);
		$options                           = [];
		$options[]                         = HTMLHelper::_('select.option', 0, Text::_('Select'), 'id', 'title');
		$options                           = array_merge($options, $db->loadObjectList());
		$this->lists['depend_on_field_id'] = HTMLHelper::_(
			'select.genericlist',
			$options,
			'depend_on_field_id',
			'class="form-select"',
			'id',
			'title',
			$this->item->depend_on_field_id
		);

		$this->dependOptions = [];

		if ($this->item->depend_on_field_id)
		{
			//Get the selected options
			$this->dependOnOptions = json_decode($this->item->depend_on_options);
			$query->clear()
				->select('`values`')
				->from('#__eb_fields')
				->where('id=' . $this->item->depend_on_field_id);
			$db->setQuery($query);
			$this->dependOptions = explode("\r\n", $db->loadResult());
		}

		$options   = [];
		$options[] = HTMLHelper::_('select.option', 0, Text::_('EB_ABOVE_PAYMENT_INFORMATION'));
		$options[] = HTMLHelper::_('select.option', 1, Text::_('EB_BELOW_PAYMENT_INFORMATION'));
		$options[] = HTMLHelper::_('select.option', 2, Text::_('EB_BELOW_PAYMENT_METHODS'));

		$this->lists['position'] = HTMLHelper::_(
			'select.genericlist',
			$options,
			'position',
			'class="form-select"',
			'value',
			'text',
			$this->item->position
		);

		$options   = [];
		$options[] = HTMLHelper::_('select.option', '', Text::_('EB_FULL_WIDTH'));
		$options[] = HTMLHelper::_('select.option', 'eb-one-half', Text::_('1/2'));
		$options[] = HTMLHelper::_('select.option', 'eb-one-third', Text::_('1/3'));
		$options[] = HTMLHelper::_('select.option', 'eb-two-thirds', Text::_('2/3'));
		$options[] = HTMLHelper::_('select.option', 'eb-one-quarter', Text::_('1/4'));
		$options[] = HTMLHelper::_('select.option', 'eb-two-quarters', Text::_('2/4'));
		$options[] = HTMLHelper::_('select.option', 'eb-three-quarters', Text::_('3/4'));

		$this->lists['container_size'] = HTMLHelper::_(
			'select.genericlist',
			$options,
			'container_size',
			'class="form-select"',
			'value',
			'text',
			$this->item->container_size
		);

		$options   = [];
		$options[] = HTMLHelper::_('select.option', '', Text::_('EB_DEFAULT'));
		$options[] = HTMLHelper::_('select.option', 'input-mini', Text::_('EB_EXTRA_SMALL'));
		$options[] = HTMLHelper::_('select.option', 'input-small', Text::_('EB_SMALL'));
		$options[] = HTMLHelper::_('select.option', 'input-medium', Text::_('EB_MEDIUM'));
		$options[] = HTMLHelper::_('select.option', 'input-large', Text::_('EB_LARGE'));
		$options[] = HTMLHelper::_('select.option', 'input-xlarge', Text::_('EB_EXTRA_LARGE'));
		$options[] = HTMLHelper::_('select.option', 'input-xxlarge', Text::_('EB_ULTRA_LARGE'));

		$this->lists['input_size'] = HTMLHelper::_(
			'select.genericlist',
			$options,
			'input_size',
			'class="form-select"',
			'value',
			'text',
			$this->item->input_size
		);

		// Payment methods
		$options   = [];
		$options[] = HTMLHelper::_('select.option', '', Text::_('EB_ALL'), 'name', 'title');
		$query->clear()
			->select('name, title')
			->from('#__eb_payment_plugins')
			->where('published = 1')
			->order('ordering');
		$db->setQuery($query);
		$methods = $db->loadObjectList();

		$options                       = array_merge($options, $methods);
		$this->lists['payment_method'] = HTMLHelper::_(
			'select.genericlist',
			$options,
			'payment_method',
			' class="form-select" ',
			'name',
			'title',
			$this->item->payment_method ?: ''
		);

		$this->config  = $config;
		$this->plugins = Factory::getApplication()->triggerEvent('onEditField', [$this->item]);
	}
}
