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

class OSMembershipViewFieldRaw extends MPFViewHtml
{
	public function display()
	{
		$this->setLayout('options');
		$db      = Factory::getDbo();
		$query   = $db->getQuery(true);
		$fieldId = Factory::getApplication()->input->getInt('field_id');
		$query->select('`values`')
			->from('#__osmembership_fields')
			->where('id=' . $fieldId);
		$db->setQuery($query);
		$options       = explode("\r\n", $db->loadResult());
		$this->options = $options;

		parent::display();
	}
}
