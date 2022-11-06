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

class JFormFieldOSMCategory extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var string
	 */
	public $type = 'osmcategory';

	protected function getOptions()
	{
		if (version_compare(JVERSION, '4.0.0-dev', 'ge'))
		{
			$this->layout = 'joomla.form.field.list-fancy-select';
		}

		$db    = Factory::getDbo();
		$query = $db->getQuery(true);

		$query->select($db->quoteName('id', 'value'))
			->select($db->quoteName('title', 'text'))
			->from('#__osmembership_categories')
			->where('published = 1')
			->order('title');
		$db->setQuery($query);
		$options   = [];
		$options[] = HTMLHelper::_('select.option', '0', Text::_('Select Category'));

		return array_merge($options, $db->loadObjectList());
	}
}
