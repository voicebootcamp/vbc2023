<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2022 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die;

JFormHelper::loadFieldClass('list');

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

class JFormFieldOSMPlan extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var string
	 */
	protected $type = 'osmplan';

	protected function getOptions()
	{
		if (version_compare(JVERSION, '4.0.0-dev', 'ge'))
		{
			$this->layout = 'joomla.form.field.list-fancy-select';
		}

		$db    = Factory::getDbo();
		$query = $db->getQuery(true)
			->select($db->quoteName(['id', 'title'], ['value', 'text']))
			->from('#__osmembership_plans')
			->where('published = 1')
			->order('title');
		$db->setQuery($query);
		$options = [];

		if (!$this->multiple)
		{
			$options[] = HTMLHelper::_('select.option', '', Text::_('Select Plan'));
		}

		return array_merge($options, $db->loadObjectList());
	}
}
