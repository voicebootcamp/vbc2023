<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2022 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die ;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

$isJoomla4 = OSMembershipHelper::isJoomla4();

if (!$isJoomla4)
{
	HTMLHelper::_('formbehavior.chosen', 'select');
}

$cols = 7 + count($this->fields);
?>
<form action="index.php?option=com_osmembership&view=groupmembers" method="post" name="adminForm" id="adminForm">
    <div id="j-main-container"<?php if ($isJoomla4) echo ' class="mp-joomla4-container"'; ?>>
        <div id="filter-bar" class="btn-toolbar<?php if ($isJoomla4) echo ' js-stools-container-filters-visible'; ?>">
			<div class="filter-search btn-group pull-left">
				<label for="filter_search" class="element-invisible"><?php echo Text::_('OSM_FILTER_SEARCH_SUBSCRIPTIONS_DESC');?></label>
				<input type="text" name="filter_search" inputmode="search" id="filter_search" placeholder="<?php echo Text::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->escape($this->state->filter_search); ?>" class="hasTooltip form-control" title="<?php echo HTMLHelper::tooltipText('OSM_SEARCH_SUBSCRIPTIONS_DESC'); ?>" />
			</div>
			<div class="btn-group pull-left">
				<button type="submit" class="btn<?php if ($isJoomla4) echo ' btn-primary'; ?> hasTooltip" title="<?php echo HTMLHelper::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>"><span class="icon-search"></span></button>
				<button type="button" class="btn<?php if ($isJoomla4) echo ' btn-primary'; ?> hasTooltip" title="<?php echo HTMLHelper::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('filter_search').value='';this.form.submit();"><span class="icon-remove"></span></button>
			</div>
            <div class="btn-group pull-right">
		        <?php echo $this->lists['filter_plan_id']; ?>
            </div>
			<div class="btn-group pull-right">
				<?php
					if (isset($this->lists['filter_group_admin_id']))
					{
						echo $this->lists['filter_group_admin_id'];
					}

					echo $this->lists['filter_published'];
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
						<?php echo HTMLHelper::_('grid.sort', Text::_('OSM_PLAN'), 'b.title', $this->state->filter_order_Dir, $this->state->filter_order); ?>
                    </th>
                    <?php
                    foreach($this->fields as $field)
                    {
                    ?>
                        <th>
	                        <?php
	                        if ($field->is_core || $field->is_searchable)
	                        {
                            ?>
		                        <?php echo HTMLHelper::_('grid.sort', $field->title, 'tbl.' . $field->name, $this->state->filter_order_Dir, $this->state->filter_order); ?>
                            <?php
	                        }
	                        else
	                        {
                                echo $field->title;
	                        }
	                        ?>
                        </th>
	                <?php
                    }
                    ?>
					<th class="title">
						<?php echo HTMLHelper::_('grid.sort', Text::_('OSM_GROUP'), 'tbl.group_admin_id', $this->state->filter_order_Dir, $this->state->filter_order); ?>
					</th>
					<th class="title center">
						<?php echo HTMLHelper::_('grid.sort', Text::_('OSM_START_DATE'), 'tbl.from_date', $this->state->filter_order_Dir, $this->state->filter_order); ?>
						/
						<?php echo HTMLHelper::_('grid.sort', Text::_('OSM_END_DATE'), 'tbl.to_date', $this->state->filter_order_Dir, $this->state->filter_order); ?>
					</th>
					<th class="title center">
						<?php echo HTMLHelper::_('grid.sort', Text::_('OSM_CREATED_DATE'), 'tbl.created_date', $this->state->filter_order_Dir, $this->state->filter_order); ?>
					</th>
					<th width="8%" class="center">
						<?php echo HTMLHelper::_('grid.sort', Text::_('OSM_SUBSCRIPTION_STATUS'), 'tbl.published', $this->state->filter_order_Dir, $this->state->filter_order); ?>
					</th>
					<?php
					if ($this->config->auto_generate_membership_id)
					{
						$cols++ ;
					?>
						<th width="8%" class="center">
							<?php echo HTMLHelper::_('grid.sort', Text::_('OSM_MEMBERSHIP_ID'), 'tbl.membership_id', $this->state->filter_order_Dir, $this->state->filter_order); ?>
						</th>
					<?php
					}
					?>
					<th width="2%">
						<?php echo HTMLHelper::_('grid.sort', Text::_('OSM_ID'), 'tbl.id', $this->state->filter_order_Dir, $this->state->filter_order); ?>
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
			<tbody>
			<?php
			$k = 0;
			for ($i=0, $n=count($this->items); $i < $n; $i++)
			{
				$row         = $this->items[$i];
				$link        = Route::_('index.php?option=com_osmembership&task=groupmember.edit&cid[]=' . $row->id);
				$checked     = HTMLHelper::_('grid.id', $i, $row->id);
				$accountLink = 'index.php?option=com_users&task=user.edit&id=' . $row->user_id;
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td>
						<?php echo $checked; ?>
					</td>
                    <td>
                        <a href="<?php echo Route::_('index.php?option=com_osmembership&task=plan.edit&cid[]=' . $row->plan_id); ?>" target="_blank"><?php echo $row->plan_title; ?></a>
                    </td>

                    <?php
                        $count = 0;

                        foreach ($this->fields as $field)
                        {
                        ?>
                        <td>
                            <?php
                            if ($field->is_core)
                            {
	                            if ($field->name == 'email')
	                            {
		                        ?>
                                    <a href="mailto:<?php echo $row->email; ?>"><?php echo $row->email; ?></a>
		                        <?php
	                            }
	                            else
	                            {
		                            if ($field->name == 'first_name')
		                            {
			                        ?>
                                        <a href="<?php echo $link ?>"><?php echo $row->{$field->name}; ?></a>
			                        <?php
		                            }
		                            else
		                            {
			                            echo $row->{$field->name};
		                            }
	                            }
                            }
                            elseif (isset($this->fieldsData[$row->id][$field->id]))
                            {
	                            echo $this->fieldsData[$row->id][$field->id];
                            }

                            if ($count == 0 && $row->username)
                            {
                            ?>
                                <a href="<?php echo $accountLink; ?>" title="View Profile">&nbsp;(<strong><?php echo $row->username ; ?>)</strong></a>
                            <?php
                            }
                            ?>
                        </td>
                        <?php
                            $count++;
                        }
                        ?>
					<td>
						<?php
						if ($row->group_admin)
						{
						?>
							<a href="<?php echo 'index.php?option=com_users&task=user.edit&id=' . $row->group_admin_id; ?>" title="View Profile">&nbsp;<?php echo $row->group_admin; ?></a>
						<?php
						}
						?>
					</td>
					<td class="center">
						<strong><?php echo HTMLHelper::_('date', $row->from_date, $this->config->date_format); ?></strong> <?php echo Text::_('OSM_TO'); ?>
						<strong>
							<?php
								if ($row->lifetime_membership || $row->to_date == '2099-12-31 23:59:59')
								{
									echo Text::_('OSM_LIFETIME');
								}
								else
								{
									echo HTMLHelper::_('date', $row->to_date, $this->config->date_format);
								}
							?>
						</strong>
					</td>
					<td class="center">
						<?php echo HTMLHelper::_('date', $row->created_date, $this->config->date_format . ' H:i:s'); ?>
					</td>
					<td class="center">
						<?php
		                    switch ($row->published)
		                    {
		                        case 0 :
		                            echo Text::_('OSM_PENDING');
		                            break ;
		                        case 1 :
		                            echo Text::_('OSM_ACTIVE');
		                            break ;
		                        case 2 :
		                            echo Text::_('OSM_EXPIRED');
		                            break ;
		                        case 3 :
		                            echo Text::_('OSM_CANCELLED_PENDING');
		                            break ;
		                        case 4 :
		                            echo Text::_('OSM_CANCELLED_REFUNDED');
		                            break ;
		                    }
						?>
					</td>
					<?php
					if ($this->config->auto_generate_membership_id)
					{
					?>
					<td class="center">
						<?php echo $row->membership_id ? OSMembershipHelper::formatMembershipId($row, $this->config) : ''; ?>
					</td>
					<?php
					}
					?>
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