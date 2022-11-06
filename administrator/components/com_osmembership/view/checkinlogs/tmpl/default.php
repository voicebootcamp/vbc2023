<?php
/**
 * @package		   Joomla
 * @subpackage	   Membership Pro
 * @author		   Tuan Pham Ngoc
 * @copyright	   Copyright (C) 2012 - 2022 Ossolution Team
 * @license		   GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die ;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

$isJoomla4 =  OSMembershipHelper::isJoomla4();
?>
<form action="index.php?option=com_osmembership&view=checkinlogs" method="post" name="adminForm" id="adminForm">
	<div id="j-main-container"<?php if ($isJoomla4) echo ' class="mp-joomla4-container"'; ?>>
		<div id="filter-bar" class="btn-toolbar<?php if ($isJoomla4) echo ' js-stools-container-filters-visible'; ?>">
			<div class="filter-search btn-group pull-left">
				<label for="filter_search" class="element-invisible"><?php echo Text::_('OSM_FILTER_SEARCH_CHECKIN_LOGS_DESC');?></label>
				<input type="text" name="filter_search" inputmode="search" id="filter_search" placeholder="<?php echo Text::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->escape($this->state->filter_search); ?>" class="hasTooltip form-control" title="<?php echo HTMLHelper::tooltipText('OSM_SEARCH_CHECKIN_LOGS_DESC'); ?>" onchange="submit();" />
			</div>
			<div class="btn-group pull-left">
				<button type="submit" class="btn<?php if ($isJoomla4) echo ' btn-primary'; ?> hasTooltip" title="<?php echo HTMLHelper::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>"><span class="icon-search"></span></button>
				<button type="button" class="btn<?php if ($isJoomla4) echo ' btn-primary'; ?> hasTooltip" title="<?php echo HTMLHelper::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('filter_search').value='';document.getElementById('filter_from_date').value='';document.getElementById('filter_to_date').value='';this.form.submit();"><span class="icon-remove"></span></button>
			</div>
            <div class="btn-group pull-right">
				<?php
                    echo $this->lists['filter_plan_id'];
                    echo $this->pagination->getLimitBox();
				?>
            </div>
		</div>
		<div class="clearfix"></div>
		<table class="adminlist table table-striped">
			<thead>
				<tr>
					<th width="20">
						<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
					</th>
					<th class="title" style="text-align: left;">
						<?php echo HTMLHelper::_('grid.sort', Text::_('OSM_USERNAME'), 'u.username', $this->state->filter_order_Dir, $this->state->filter_order); ?>
					</th>
                    <th class="title" style="text-align: left;">
						<?php echo HTMLHelper::_('grid.sort', Text::_('OSM_NAME'), 'u.name', $this->state->filter_order_Dir, $this->state->filter_order); ?>
                    </th>
					<th class="title" style="text-align: left;">
						<?php echo HTMLHelper::_('grid.sort', Text::_('OSM_PLAN'), 'p.title', $this->state->filter_order_Dir, $this->state->filter_order); ?>
					</th>
					<th class="title center">
						<?php echo HTMLHelper::_('grid.sort', Text::_('OSM_CHECKIN_DATE'), 'tbl.checkin_date', $this->state->filter_order_Dir, $this->state->filter_order); ?>
					</th>
					<th width="2%">
						<?php echo HTMLHelper::_('grid.sort', Text::_('OSM_ID'), 'tbl.id', $this->state->filter_order_Dir, $this->state->filter_order); ?>
					</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="6">
						<?php echo $this->pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
			<tbody>
			<?php
			$k = 0;

			for ($i=0, $n=count($this->items); $i < $n; $i++)
			{
				$row         = $this->items[$i];
				$checked     = HTMLHelper::_('grid.id', $i, $row->id);
				$accountLink = 'index.php?option=com_users&task=user.edit&id=' . $row->user_id;
				$symbol      = $row->currency_symbol ? $row->currency_symbol : $row->currency;
				?>
				<tr class="<?php echo "row$k"; if (isset($statusCssClasses[$row->published])) echo ' ' . $statusCssClasses[$row->published]; ?>">
					<td>
						<?php echo $checked; ?>
					</td>
					<td>
						<a href="<?php echo $accountLink; ?>"><?php echo $row->username; ?></a>
					</td>
                    <td>
                        <?php echo $row->name; ?>
                    </td>
					<td>
						<a href="<?php echo Route::_('index.php?option=com_osmembership&task=plan.edit&cid[]=' . $row->plan_id); ?>" target="_blank"><?php echo $row->plan_title ; ?></a>
					</td>
					<td class="center">
						<?php echo HTMLHelper::_('date', $row->checkin_date, $this->config->date_format . ' H:i:s'); ?>
					</td>
					<td class="center">
						<?php echo $row->id; ?>
					</td>
				</tr>
				<?php
				$k = 1 - $k;
			}
			?>
			</tbody>
		</table>

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $this->state->filter_order; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $this->state->filter_order_Dir; ?>" />
		<?php echo HTMLHelper::_('form.token'); ?>
	</div>
</form>
