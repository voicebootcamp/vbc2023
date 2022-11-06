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

class OSMembershipViewCouponsHtml extends MPFViewList
{
	protected function prepareView()
	{
		parent::prepareView();

		$db            = Factory::getDbo();
		$config        = OSMembershipHelper::getConfig();
		$discountTypes = [0 => '%', 1 => $config->currency_symbol];
		$query         = $db->getQuery(true);
		$query->select('id, title')
			->from('#__osmembership_plans')
			->where('published = 1')
			->order('ordering');
		$db->setQuery($query);
		$options                       = [];
		$options[]                     = HTMLHelper::_('select.option', 0, Text::_('OSM_PLAN'), 'id', 'title');
		$options                       = array_merge($options, $db->loadObjectList());
		$this->lists['filter_plan_id'] = HTMLHelper::_(
			'select.genericlist',
			$options,
			'filter_plan_id',
			'class="form-select" onchange="submit();" ',
			'id',
			'title',
			$this->state->filter_plan_id
		);

		$this->lists['filter_plan_id'] = OSMembershipHelperHtml::getChoicesJsSelect($this->lists['filter_plan_id'], Text::_('OSM_TYPE_OR_SELECT_ONE_PLAN'));

		$this->dateFormat    = $config->date_format;
		$this->nullDate      = $db->getNullDate();
		$this->discountTypes = $discountTypes;
		$this->config        = $config;
	}
}
