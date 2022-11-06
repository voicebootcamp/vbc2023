<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2022 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

$isJoomla4 = EventbookingHelper::isJoomla4();

if (!$isJoomla4)
{
	HTMLHelper::_('formbehavior.chosen', 'select');
}

$saveOrder = $this->state->filter_order == 'tbl.ordering';

if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_eventbooking&task=country.save_order_ajax';

	if (EventbookingHelper::isJoomla4())
	{
		HTMLHelper::_('draggablelist.draggable');
	}
	else
	{
		HTMLHelper::_('sortablelist.sortable', 'countryList', 'adminForm', strtolower($this->state->filter_order_Dir), $saveOrderingUrl);
	}
}

$customOptions = array(
	'filtersHidden'       => true,
	'defaultLimit'        => Factory::getApplication()->get('list_limit', 20),
	'searchFieldSelector' => '#filter_search',
	'orderFieldSelector'  => '#filter_full_ordering',
);

HTMLHelper::_('searchtools.form', '#adminForm', $customOptions);
?>
<form action="index.php?option=com_eventbooking&view=countries" method="post" name="adminForm" id="adminForm">
	<div id="j-main-container"<?php if ($isJoomla4) echo ' class="eb-joomla4-container"'; ?>>
		<div id="filter-bar" class="btn-toolbar<?php if ($isJoomla4) echo ' js-stools-container-filters-visible'; ?>">
			<div class="filter-search btn-group pull-left">
				<label for="filter_search" class="element-invisible"><?php echo Text::_('EB_FILTER_SEARCH_COUNTRIES_DESC');?></label>
				<input type="text" name="filter_search" id="filter_search" inputmode="search" placeholder="<?php echo Text::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->escape($this->state->filter_search); ?>" class="hasTooltip form-control" title="<?php echo HTMLHelper::tooltipText('EB_SEARCH_COUNTRIES_DESC'); ?>" />
			</div>
			<div class="btn-group pull-left">
				<button type="submit" class="btn <?php if ($isJoomla4) echo ' btn-primary'; ?> hasTooltip" title="<?php echo HTMLHelper::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>"><span class="icon-search"></span></button>
				<button type="button" class="btn <?php if ($isJoomla4) echo ' btn-primary'; ?> hasTooltip" title="<?php echo HTMLHelper::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('filter_search').value='';this.form.submit();"><span class="icon-remove"></span></button>
			</div>
			<div class="btn-group pull-right">
				<?php
				echo $this->lists['filter_state'];
				echo $this->pagination->getLimitBox();
				?>
			</div>
		</div>
		<div class="clearfix"></div>
		<table class="adminlist table table-striped" id="countryList">
			<thead>
			<tr>
                <th width="1%" class="nowrap center hidden-phone">
					<?php echo HTMLHelper::_('searchtools.sort', '', 'tbl.ordering', $this->state->filter_order_Dir, $this->state->filter_order, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
                </th>
				<th width="20">
					<?php echo HTMLHelper::_('grid.checkall'); ?>
				</th>
				<th class="title" style="text-align: left;">
					<?php echo HTMLHelper::_('searchtools.sort', Text::_('EB_COUNTRY_NAME'), 'tbl.name', $this->state->filter_order_Dir, $this->state->filter_order); ?>
				</th>
				<th class="center title" width="15%">
					<?php echo HTMLHelper::_('searchtools.sort', Text::_('EB_COUNTRY_CODE_3'), 'tbl.country_3_code', $this->state->filter_order_Dir, $this->state->filter_order); ?>
				</th>
				<th class="center title" width="15%">
					<?php echo HTMLHelper::_('searchtools.sort', Text::_('EB_COUNTRY_CODE_2'), 'tbl.country_2_code', $this->state->filter_order_Dir, $this->state->filter_order); ?>
				</th>
				<th class="center" width="10%">
					<?php echo HTMLHelper::_('searchtools.sort', Text::_('EB_PUBLISHED'), 'tbl.published', $this->state->filter_order_Dir, $this->state->filter_order); ?>
				</th>
				<th class="center" width="5%">
					<?php echo HTMLHelper::_('searchtools.sort', Text::_('EB_ID'), 'tbl.id', $this->state->filter_order_Dir, $this->state->filter_order); ?>
				</th>
			</tr>
			</thead>
			<tfoot>
			<tr>
				<td colspan="7">
					<?php echo $this->pagination->getPaginationLinks(); ?>
				</td>
			</tr>
			</tfoot>
            <tbody <?php if ($saveOrder) :?> class="js-draggable" data-url="<?php echo $saveOrderingUrl; ?>" data-direction="<?php echo strtolower($this->state->filter_order_Dir); ?>" <?php endif; ?>>
			<?php
			$k = 0;

			for ($i = 0, $n = count($this->items); $i < $n; $i++)
			{
				$row       = $this->items[$i];
				$link      = Route::_('index.php?option=com_eventbooking&view=country&id=' . $row->id);
				$checked   = HTMLHelper::_('grid.id', $i, $row->id);
				$published = HTMLHelper::_('jgrid.published', $row->published, $i);
				?>
				<tr class="<?php echo "row$k"; ?>">
                    <td class="order nowrap center hidden-phone">
						<?php
						$iconClass = '';

						if(!$saveOrder)
						{
							$iconClass = ' inactive tip-top hasTooltip"';
						}
						?>
                        <span class="sortable-handler<?php echo $iconClass ?>">
						<i class="icon-menu"></i>
						</span>
						<?php if ($saveOrder) : ?>
                            <input type="text" style="display:none" name="order[]" size="5" value="<?php echo $row->ordering ?>" class="width-20 text-area-order "/>
						<?php endif; ?>
                    </td>
					<td>
						<?php echo $checked; ?>
					</td>
					<td>
						<a href="<?php echo $link; ?>">
							<?php echo $row->name; ?>
						</a>
					</td>
					<td class="center">
						<?php echo $row->country_3_code; ?>
					</td>
					<td class="center">
						<?php echo $row->country_2_code; ?>
					</td>
					<td class="center">
						<?php echo $published; ?>
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
	</div>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->state->filter_order; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->state->filter_order_Dir; ?>" />
    <input type="hidden" id="filter_full_ordering" name="filter_full_ordering" value="" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>