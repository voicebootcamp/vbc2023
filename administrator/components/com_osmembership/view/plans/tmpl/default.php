<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2022 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die ;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

$isJoomla4 =  OSMembershipHelper::isJoomla4();

if (!$isJoomla4)
{
	HTMLHelper::_('formbehavior.chosen', 'select');
}

$user      = Factory::getUser();
$userId    = $user->get('id');
$saveOrder = ($this->state->filter_order == 'tbl.ordering');
$rootUri   = Uri::root(true);

if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_osmembership&task=plan.save_order_ajax';

	if (OSMembershipHelper::isJoomla4())
	{
		HTMLHelper::_('draggablelist.draggable');
	}
	else
	{
		HTMLHelper::_('sortablelist.sortable', 'planList', 'adminForm', strtolower($this->state->filter_order_Dir), $saveOrderingUrl);
	}
}

$customOptions = array(
	'filtersHidden'       => true,
	'defaultLimit'        => Factory::getApplication()->get('list_limit', 20),
	'searchFieldSelector' => '#filter_search',
	'orderFieldSelector'  => '#filter_full_ordering',
);

HTMLHelper::_('searchtools.form', '#adminForm', $customOptions);

$config = OSMembershipHelper::getConfig();
$cols   = 11;

if ($this->showThumbnail)
{
	$cols++;
}

if ($this->showCategory)
{
	$cols++;
}
?>
<form action="index.php?option=com_osmembership&view=plans" method="post" name="adminForm" id="adminForm">
    <div id="j-main-container"<?php if ($isJoomla4) echo ' class="mp-joomla4-container"'; ?>>
        <div id="filter-bar" class="btn-toolbar js-stools<?php if ($isJoomla4) echo ' js-stools-container-filters-visible'; ?>">
			<div class="filter-search btn-group pull-left">
				<label for="filter_search" class="element-invisible"><?php echo Text::_('OSM_FILTER_SEARCH_PLANS_DESC');?></label>
				<input type="text" name="filter_search" inputmode="search" id="filter_search" placeholder="<?php echo Text::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->escape($this->state->filter_search); ?>" class="hasTooltip form-control" title="<?php echo HTMLHelper::tooltipText('OSM_SEARCH_PLANS_DESC'); ?>" />
			</div>
			<div class="btn-group pull-left">
				<button type="submit" class="btn<?php if ($isJoomla4) echo ' btn-primary'; ?> hasTooltip" title="<?php echo HTMLHelper::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>"><span class="icon-search"></span></button>
				<button type="button" class="btn<?php if ($isJoomla4) echo ' btn-primary'; ?> hasTooltip" title="<?php echo HTMLHelper::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('filter_search').value='';this.form.submit();"><span class="icon-remove"></span></button>
			</div>
            <?php
                if (isset($this->lists['filter_category_id']))
                {
                ?>
                    <div class="btn-group pull-right">
                        <?php echo $this->lists['filter_category_id']; ?>
                    </div>
                <?php
                }
            ?>
			<div class="btn-group pull-right">
				<?php
					echo $this->lists['filter_state'];
					echo $this->pagination->getLimitBox();
				?>
			</div>
		</div>
		<div class="clearfix"> </div>
		<table class="adminlist table table-striped" id="planList">
			<thead>
				<tr>
					<th width="1%" class="nowrap center hidden-phone">
						<?php echo HTMLHelper::_('searchtools.sort', '', 'tbl.ordering', $this->state->filter_order_Dir, $this->state->filter_order, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
					</th>
					<th width="20">
						<?php echo HTMLHelper::_('grid.checkall'); ?>
					</th>
					<th class="title">
						<?php echo HTMLHelper::_('searchtools.sort', Text::_('OSM_TITLE'), 'tbl.title', $this->state->filter_order_Dir, $this->state->filter_order); ?>
					</th>
					<?php
						if ($this->showCategory)
						{
						?>
							<th class="title">
								<?php echo HTMLHelper::_('searchtools.sort', Text::_('OSM_CATEGORY'), 'b.title', $this->state->filter_order_Dir, $this->state->filter_order); ?>
							</th>
						<?php
						}
						if ($this->showThumbnail)
						{
						?>
							<th class="title" width="10%">
								<?php echo Text::_('OSM_THUMB'); ?>
							</th>
						<?php
						}
					?>
					<th class="title" width="8%">
						<?php echo Text::_('OSM_LENGTH'); ?>
					</th>
					<th class="center" width="8%">
						<?php echo HTMLHelper::_('searchtools.sort', Text::_('OSM_RECURRING'), 'tbl.recurring_subscription', $this->state->filter_order_Dir, $this->state->filter_order); ?>
					</th>
					<th class="title" width="8%">
						<?php echo HTMLHelper::_('searchtools.sort', Text::_('OSM_PRICE'), 'tbl.price', $this->state->filter_order_Dir, $this->state->filter_order); ?>
					</th>
					<th class="title center" width="12%">
						<?php echo Text::_('OSM_TOTAL_SUBSCRIBERS'); ?>
					</th>
					<th class="title center" width="12%">
						<?php echo Text::_('OSM_ACTIVE_SUBSCRIBERS'); ?>
					</th>
					<th width="5%">
						<?php echo HTMLHelper::_('searchtools.sort', Text::_('JGRID_HEADING_ACCESS'), 'tbl.access', $this->state->filter_order_Dir, $this->state->filter_order); ?>
					</th>
					<th width="5%">
						<?php echo HTMLHelper::_('searchtools.sort', Text::_('OSM_PUBLISHED'), 'tbl.published', $this->state->filter_order_Dir, $this->state->filter_order); ?>
					</th>
					<th width="2%">
						<?php echo HTMLHelper::_('searchtools.sort', Text::_('OSM_ID'), 'tbl.id', $this->state->filter_order_Dir, $this->state->filter_order); ?>
					</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="<?php echo $cols; ?>">
						<?php echo $this->pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
            <tbody <?php if ($saveOrder) :?> class="js-draggable" data-url="<?php echo $saveOrderingUrl; ?>" data-direction="<?php echo strtolower($this->state->filter_order_Dir); ?>" <?php endif; ?>>
			<?php
			$k = 0;
			$bootstrapHelper = OSMembershipHelperBootstrap::getInstance();
			$iconPublish = $bootstrapHelper->getClassMapping('icon-publish');
			$iconUnPublish = $bootstrapHelper->getClassMapping('icon-unpublish');

			for ($i=0, $n=count($this->items); $i < $n; $i++)
			{
				$row       = $this->items[$i];
				$link      = Route::_('index.php?option=com_osmembership&task=plan.edit&cid[]=' . $row->id);
				$checked   = HTMLHelper::_('grid.id', $i, $row->id);
				$published = HTMLHelper::_('jgrid.published', $row->published, $i, 'plan.');
				$symbol    = $row->currency_symbol ? $row->currency_symbol : $row->currency;
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td class="order nowrap center hidden-phone">
						<?php
						$iconClass = '';

						if (!$saveOrder)
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
						<a href="<?php echo $link; ?>"><?php echo $row->title ; ?></a>
					</td>
					<?php
						if ($this->showCategory)
						{
						?>
							<td><?php echo $row->category_title; ?></td>
						<?php
						}
						if ($this->showThumbnail)
						{
						?>
							<td class="center">
								<?php
								if ($row->thumb)
								{
								?>
									<a href="<?php echo $rootUri . '/media/com_osmembership/' . $row->thumb ; ?>" target="_blank"><img src="<?php echo $rootUri . '/media/com_osmembership/' . $row->thumb ; ?>" class="osm-plan-thumb" /></a>
								<?php
								}
								?>
							</td>
						<?php
						}
					?>
					<td>
						<?php
						if ($row->lifetime_membership)
						{
							echo Text::_('OSM_LIFETIME');
						}
						else
						{
							echo OSMembershipHelperSubscription::getDurationText($row->subscription_length, $row->subscription_length_unit);
						}
						?>
					</td>
					<td class="center">
                        <a class="tbody-icon"><span class="<?php echo $row->recurring_subscription ? $iconPublish : $iconUnPublish; ?>"></span></a>
					</td>
					<td>
						<?php
						if ($row->price > 0)
						{
							echo OSMembershipHelper::formatCurrency($row->price, $config, $symbol);
						}
						else
						{
							echo Text::_('OSM_FREE');
						}
						?>
					</td>
					<td class="center">
                        <a href="index.php?option=com_osmembership&view=subscriptions&plan_id=<?php echo $row->id ?>"><?php echo OSMembershipHelper::countSubscribers($row->id); ?></a>
					</td>
					<td class="center">
                        <a href="index.php?option=com_osmembership&view=subscriptions&plan_id=<?php echo $row->id ?>&published=1"><?php echo OSMembershipHelper::countSubscribers($row->id, 1); ?></a>
					</td>
					<td>
						<?php echo $row->access_level; ?>
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
	<?php $this->renderFormHiddenVariables(); ?>
</form>