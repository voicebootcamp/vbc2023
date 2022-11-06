<?php 
/** 
 * @package JMAP::CPANEL::administrator::components::com_jmap
 * @subpackage views
 * @subpackage cpanel
 * @subpackage tmpl
 * @author Joomla! Extensions Store
 * @copyright (C) 2021 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html  
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );
use Joomla\CMS\Language\Text;
?>
<!-- HYPESTAT SEOSTATS -->
<div class="single_stat_container">
	<div class="statcircle">
		<span class="fas fa-chart-line fa-icon-large" aria-hidden="true"></span>
	</div>
	<ul class="subdescription_stats">
		<li data-bind="{websiteinformer_rank}" class="es-stat-no"></li>
		<li class="es-stat-title"><?php echo Text::_('COM_JMAP_WEBSITEINFORMER_PAGE_RANK');?></li>
	</ul>
</div>

<div class="single_stat_container">
	<div class="statcircle">
		<span class="fas fa-user fa-icon-large" aria-hidden="true"></span>
	</div>
	<ul class="subdescription_stats">
		<li data-bind="{daily_visitors}" class="es-stat-no"></li>
		<li class="es-stat-title"><?php echo Text::_('COM_JMAP_DAILY_VISITORS');?></li>
	</ul>
</div>

<div class="single_stat_container">
	<div class="statcircle">
		<span class="fas fa-copy fa-icon-large" aria-hidden="true"></span>
	</div>
	<ul class="subdescription_stats">
		<li data-bind="{daily_pageviews}" class="es-stat-no"></li>
		<li class="es-stat-title"><?php echo Text::_('COM_JMAP_WEBSITEINFORMER_DAILY_PAGEVIEWS');?></li>
	</ul>
</div>

<div class="single_stat_container">
	<ul class="subdescription_stats">
		<li data-bind="{website_screen}" class="es-stat-no fancybox-image"></li>
	</ul>
</div>

<div class="single_stat_rowseparator"></div>

<div class="card card-body card-stats card-hidden" data-bind="{website_report_text}"></div>