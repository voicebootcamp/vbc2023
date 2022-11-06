<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2010 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

$rootUri = Uri::root(true);

Factory::getDocument()->addScript($rootUri . '/media/com_osmembership/assets/js/chartjs/Chart.min.js')
	->addScript($rootUri . '/media/com_osmembership/assets/js/chartjs/chartjs-plugin-datalabels.min.js')
	->addScript($rootUri . '/media/com_osmembership/js/admin-dashboard-chart.min.js')
	->addScriptOptions('labels', $this->sales['labels'])
	->addScriptOptions('sales', $this->sales['income'])
	->addScriptOptions('subscriptionsCount', $this->sales['count'])
	->addScriptOptions('ajaxUrl', Uri::base(true) . '/index.php?option=com_osmembership&task=subscription.get_sales_chart_data');

Text::script('OSM_SALES_INCOME');
?>
<canvas id="osm-sales-chart"></canvas>