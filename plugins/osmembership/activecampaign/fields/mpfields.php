<?php
defined('_JEXEC') or die('Restricted access');

JFormHelper::loadFieldClass('list');

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

class JFormFieldMpfields extends JFormFieldList
{
	protected $type = 'Mpfields';

	protected function getOptions()
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true)
			->select('name, title')
			->from('#__osmembership_fields')
			->where('published = 1')
			->order('ordering');
		$db->setQuery($query);
		$fields = $db->loadObjectList();

		$options   = [];
		$options[] = HTMLHelper::_('select.option', '', Text::_('Select Field'));

		foreach ($fields as $field)
		{
			$options[] = HTMLHelper::_('select.option', $field->name, $field->title);
		}

		// Some basic fields
		$options[] = HTMLHelper::_('select.option', 'id', Text::_('Subscription ID'));
		$options[] = HTMLHelper::_('select.option', 'username', Text::_('Username'));
		$options[] = HTMLHelper::_('select.option', 'email', Text::_('Email'));
		$options[] = HTMLHelper::_('select.option', 'user_id', Text::_('User ID'));
		$options[] = HTMLHelper::_('select.option', 'username', Text::_('User Name'));
		$options[] = HTMLHelper::_('select.option', 'created_date', Text::_('Created Date'));
		$options[] = HTMLHelper::_('select.option', 'from_date', Text::_('Subscription Start Date'));
		$options[] = HTMLHelper::_('select.option', 'to_date', Text::_('Subscription End Date'));

		return $options;
	}
}
