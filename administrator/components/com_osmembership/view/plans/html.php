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
use Joomla\CMS\Language\Text;

class OSMembershipViewPlansHtml extends MPFViewList
{
	protected function prepareView()
	{
		parent::prepareView();

		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->clear();
		$query->select('COUNT(*)')
			->from('#__osmembership_categories')
			->where('published = 1');
		$db->setQuery($query);
		$categories = $db->loadObjectList();

		if (count($categories))
		{
			$this->lists['filter_category_id'] = OSMembershipHelperHtml::buildCategoryDropdown($this->state->filter_category_id, 'filter_category_id', 'class="form-select input-xlarge" onchange="submit();"');
			$this->lists['filter_category_id'] = OSMembershipHelperHtml::getChoicesJsSelect($this->lists['filter_category_id'], Text::_('OSM_TYPE_OR_SELECT_ONE_CATEGORY'));
		}

		// Check to see whether we will show thumbnail column
		$query->clear()
			->select('COUNT(*)')
			->from('#__osmembership_plans')
			->where('thumb != ""');
		$db->setQuery($query);
		$this->showThumbnail = (int) $db->loadResult();

		// Check to see whether we should show category column
		$query->clear()
			->select('COUNT(*)')
			->from('#__osmembership_plans')
			->where('category_id > 0');
		$db->setQuery($query);
		$this->showCategory = (int) $db->loadResult();
	}
}
