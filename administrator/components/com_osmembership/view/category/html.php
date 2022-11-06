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
use Joomla\CMS\Plugin\PluginHelper;

class OSMembershipViewCategoryHtml extends MPFViewItem
{
	protected function prepareView()
	{
		parent::prepareView();

		PluginHelper::importPlugin('osmembership');

		$this->plugins = Factory::getApplication()->triggerEvent('onEditSubscriptionCategory', [$this->item]);
	}
}
