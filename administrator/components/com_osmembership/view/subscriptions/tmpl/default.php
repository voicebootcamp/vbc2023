<?php
/**
 * @package		   Joomla
 * @subpackage	   Membership Pro
 * @author		   Tuan Pham Ngoc
 * @copyright	   Copyright (C) 2012 - 2022 Ossolution Team
 * @license		   GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die ;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

$isJoomla4 =  OSMembershipHelper::isJoomla4();

if (!$isJoomla4)
{
	HTMLHelper::_('formbehavior.chosen', 'select');
}

HTMLHelper::_('behavior.core');
Text::script('OSM_SELECT_PLAN_TO_ADD_SUBSCRIPTION', true);

$document = Factory::getDocument();
$document->addScript(Uri::root(true) . '/media/com_osmembership/js/admin-subscriptions-default.min.js');
$document->addScriptOptions('force_select_plan', (int) $this->config->force_select_plan);
$cols = 8 ;
?>
<form action="index.php?option=com_osmembership&view=subscriptions" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
	<div id="j-main-container"<?php if ($isJoomla4) echo ' class="mp-joomla4-container"'; ?>>
		<div id="filter-bar" class="btn-toolbar<?php if ($isJoomla4) echo ' js-stools-container-filters-visible'; ?>">
			<div class="filter-search btn-group pull-left">
				<label for="filter_search" class="element-invisible"><?php echo Text::_('OSM_FILTER_SEARCH_SUBSCRIPTIONS_DESC');?></label>
				<input type="text" name="filter_search" inputmode="search" id="filter_search" placeholder="<?php echo Text::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->escape($this->state->filter_search); ?>" class="hasTooltip form-control" title="<?php echo HTMLHelper::tooltipText('OSM_SEARCH_SUBSCRIPTIONS_DESC'); ?>" onchange="submit();" />
			</div>
			<div class="btn-group pull-left">
				<?php echo $this->lists['filter_date_field']; ?>
			</div>
			<div class="btn-group pull-left osm-filter-date">
				<div class="pull-left"><?php echo HTMLHelper::_('calendar', (int) $this->state->filter_from_date ? $this->state->filter_from_date : '', 'filter_from_date', 'filter_from_date', $this->datePickerFormat . ' %H:%M:%S', array('class' => 'input-medium', 'showTime' => true, 'placeholder' => Text::_('OSM_FROM'))); ?></div>
				<div class="pull-left"><?php echo HTMLHelper::_('calendar', (int) $this->state->filter_to_date ? $this->state->filter_to_date : '', 'filter_to_date', 'filter_to_date', $this->datePickerFormat . ' %H:%M:%S', ['class' => 'input-medium', 'showTime' => true, 'placeholder' => Text::_('OSM_TO')]); ?></div>
			</div>
			<div class="btn-group pull-left">
				<button type="submit" class="btn<?php if ($isJoomla4) echo ' btn-primary'; ?> hasTooltip" title="<?php echo HTMLHelper::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>"><span class="icon-search"></span></button>
				<button type="button" class="btn<?php if ($isJoomla4) echo ' btn-primary'; ?> hasTooltip" title="<?php echo HTMLHelper::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('filter_search').value='';document.getElementById('filter_from_date').value='';document.getElementById('filter_to_date').value='';this.form.submit();"><span class="icon-remove"></span></button>
				<?php
					echo $this->pagination->getLimitBox();
				?>
			</div>
			<div class="btn-group pull-right btn-subscriptions-filter-second-row">
				<?php
					if (isset($this->lists['filter_category_id']))
					{
						echo $this->lists['filter_category_id'];
					}

					echo $this->lists['plan_id'];
					echo $this->lists['subscription_type'];
					echo $this->lists['published'];
					echo $this->lists['filter_subscription_duration'];

					foreach ($this->filters as $filter)
					{
						echo $filter;
					}
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
						<?php echo HTMLHelper::_('grid.sort', Text::_('OSM_FIRSTNAME'), 'tbl.first_name', $this->state->filter_order_Dir, $this->state->filter_order); ?>
					</th>
					<?php
						if ($this->showLastName)
						{
							$cols++;
						?>
							<th class="title" style="text-align: left;">
								<?php echo HTMLHelper::_('grid.sort', Text::_('OSM_LASTNAME'), 'tbl.last_name', $this->state->filter_order_Dir, $this->state->filter_order); ?>
							</th>
						<?php
						}

						foreach ($this->fields as $field)
						{
							$cols++;
							if ($field->is_core || $field->is_searchable)
							{
							?>
								<th class="title" nowrap="nowrap">
									<?php echo HTMLHelper::_('grid.sort', Text::_($field->title), 'tbl.' . $field->name, $this->state->filter_order_Dir, $this->state->filter_order); ?>
								</th>
							<?php
							}
							else
							{
							?>
								<th class="title" nowrap="nowrap"><?php echo $field->title; ?></th>
							<?php
							}
						}
					?>
					<th class="title" style="text-align: left;">
						<?php echo HTMLHelper::_('grid.sort', Text::_('OSM_PLAN'), 'b.title', $this->state->filter_order_Dir, $this->state->filter_order); ?>
					</th>
					<th class="title center">
						<?php echo HTMLHelper::_('grid.sort', Text::_('OSM_START_DATE'), 'tbl.from_date', $this->state->filter_order_Dir, $this->state->filter_order); ?>
						/
						<?php echo HTMLHelper::_('grid.sort', Text::_('OSM_END_DATE'), 'tbl.to_date', $this->state->filter_order_Dir, $this->state->filter_order); ?>
					</th>
					<th class="title center">
						<?php echo HTMLHelper::_('grid.sort', Text::_('OSM_CREATED_DATE'), 'tbl.created_date', $this->state->filter_order_Dir, $this->state->filter_order); ?>
					</th>
					<th width="10%">
						<?php echo HTMLHelper::_('grid.sort', Text::_('OSM_GROSS_AMOUNT'), 'tbl.gross_amount', $this->state->filter_order_Dir, $this->state->filter_order); ?>
					</th>
					<?php
						if ($this->config->enable_coupon)
						{
							$cols++;
						?>
							<th>
								<?php echo HTMLHelper::_('grid.sort', Text::_('OSM_COUPON'), 'd.code', $this->state->filter_order_Dir, $this->state->filter_order); ?>
							</th>
						<?php
						}
					?>
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
						if ($this->config->activate_invoice_feature)
						{
							$cols++ ;
						?>
							<th width="8%" class="center">
								<?php echo HTMLHelper::_('grid.sort', Text::_('OSM_INVOICE_NUMBER'), 'tbl.invoice_number', $this->state->filter_order_Dir, $this->state->filter_order); ?>
							</th>
						<?php
						}

						if ($this->config->show_download_member_card)
						{
							$cols++;
						?>
							<th class="center">
								<?php echo Text::_('OSM_MEMBER_CARD'); ?>
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
					<td colspan="<?php echo $cols ; ?>">
						<?php echo $this->pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
			<tbody>
			<?php
			$k = 0;
			$statusCssClasses = [
				0 => 'osm-pending-subscription',
				1 => 'osm-active-subscription',
				2 => 'osm-expired-subscription',
				3 => 'osm-cancelled-pending-subscription',
				5 => 'osm-cancelled-refunded-subscription',
			];

			for ($i=0, $n=count($this->items); $i < $n; $i++)
			{
				$row         = $this->items[$i];
				$link        = Route::_('index.php?option=com_osmembership&view=subscription&id=' . $row->id);
				$checked     = HTMLHelper::_('grid.id', $i, $row->id);
				$accountLink = 'index.php?option=com_users&task=user.edit&id=' . $row->user_id;
				$symbol      = $row->currency_symbol ? $row->currency_symbol : $row->currency;
				?>
				<tr class="<?php echo "row$k"; if (isset($statusCssClasses[$row->published])) echo ' ' . $statusCssClasses[$row->published]; ?>">
					<td>
						<?php echo $checked; ?>
					</td>
					<td>
						<a href="<?php echo $link; ?>"><?php echo $row->first_name ?: $row->username ; ?></a>
                        <?php
                            if ($row->username)
                            {
                            ?>
                                <a href="<?php echo $accountLink; ?>" title="View Profile">(<strong><?php echo $row->username ; ?></strong>)</a>
                            <?php
                            }
                        ?>
					</td>
					<?php
						if ($this->showLastName)
						{
						?>
							<td>
								<?php echo $row->last_name ; ?>
							</td>
						<?php
						}

						foreach ($this->fields as $field)
						{
							if ($field->is_core)
							{
								$fieldValue = $row->{$field->name};
							}
							else
							{
								$fieldValue = isset($this->fieldsData[$row->id][$field->id]) ? $this->fieldsData[$row->id][$field->id] : '';
							}
						?>
							<td>
								<?php echo $fieldValue; ?>
							</td>
						<?php
						}
					?>
					<td>
						<a href="<?php echo Route::_('index.php?option=com_osmembership&task=plan.edit&cid[]=' . $row->plan_id); ?>" target="_blank"><?php echo $row->plan_title ; ?></a>
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
						<?php echo OSMembershipHelper::formatCurrency($row->gross_amount, $this->config, $symbol)?>
					</td>
					<?php
						if ($this->config->enable_coupon)
						{
						?>
							<td>
								<?php
									if ($row->coupon_id)
									{
									?>
										<a href="index.php?option=com_osmembership&view=coupon&id=<?php echo $row->coupon_id; ?>" target="_blank"><?php	 echo $row->coupon_code; ?></a>
									<?php
									}
								?>
							</td>
						<?php
						}
					?>
					<td class="center">
						<?php
							switch ($row->published)
							{
								case 0 :
								case 1 :
									echo HTMLHelper::_('jgrid.published', $row->published, $i, 'subscription.');
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
							if ($row->recurring_subscription_cancelled)
							{
								echo '<br /><span class="text-error">' . Text::_('OSM_RECURRING_CANCELLED') . '</span>';
							}
						?>
					</td>
					<?php
						if ($this->config->auto_generate_membership_id)
						{
						?>
							<td class="center">
								<?php echo OSMembershipHelper::formatMembershipId($row, $this->config); ?>
							</td>
						<?php
						}

						if ($this->config->activate_invoice_feature)
						{
						?>
							<td class="center">
								<?php
									if ($row->invoice_number)
									{
									?>
										<a href="<?php echo Route::_('index.php?option=com_osmembership&task=download_invoice&id=' . $row->id); ?>" title="<?php echo Text::_('OSM_DOWNLOAD'); ?>"><?php echo OSMembershipHelper::formatInvoiceNumber($row, $this->config) ; ?></a>
									<?php
									}
								?>
							</td>
						<?php
						}

						if ($this->config->show_download_member_card)
						{
						?>
							<td class="center">
								<?php
									if ($row->activate_member_card_feature)
									{
									?>
										<a href="<?php echo Route::_('index.php?option=com_osmembership&task=subscription.download_member_card&id=' . $row->id); ?>" title="<?php echo Text::_('OSM_DOWNLOAD'); ?>"><i class="icon icon-download"></i></a>
									<?php
									}
								?>
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

		<?php
            echo HTMLHelper::_(
                'bootstrap.renderModal',
                'collapseModal',
                [
                    'title'  => Text::_('OSM_MASS_MAIL'),
                    'footer' => $this->loadTemplate('batch_footer'),
                ],
                $this->loadTemplate('batch_body')
            );

			echo HTMLHelper::_(
				'bootstrap.renderModal',
				'collapseModal_Subscriptions',
				[
					'title'  => Text::_('OSM_BATCH_SUBSCRIPTIONS'),
					'footer' => $this->loadTemplate('batch_subscriptions_footer'),
				],
				$this->loadTemplate('batch_subscriptions_body')
			);

            if (PluginHelper::isEnabled('system', 'membershippro'))
            {
                echo HTMLHelper::_(
                    'bootstrap.renderModal',
                    'collapseModal_Sms',
                    [
                        'title'  => Text::_('OSM_BATCH_SMS'),
                        'footer' => $this->loadTemplate('batch_sms_footer'),
                    ],
                    $this->loadTemplate('batch_sms_body')
                );
            }
		?>

        <input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $this->state->filter_order; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $this->state->filter_order_Dir; ?>" />
		<?php echo HTMLHelper::_('form.token'); ?>
	</div>
</form>
<?php
if (!count($this->filters))
{
?>
	<form action="index.php?option=com_osmembership&view=subscriptions" method="post" name="subscriptionsExportForm" id="subscriptionsExportForm">
		<input type="hidden" name="task" value=""/>
		<input type="hidden" id="export_filter_search" name="filter_search"/>
		<input type="hidden" id="export_filter_date_field" name="filter_date_field" />
		<input type="hidden" id="export_filter_from_date" name="filter_from_date" value="">
		<input type="hidden" id="export_filter_to_date" name="filter_to_date" value="">
		<input type="hidden" id="export_filter_plan_id" name="plan_id" value="">
		<input type="hidden" id="export_filter_category_id" name="filter_category_id" value="">
		<input type="hidden" id="export_subscription_type" name="subscription_type" value="">
		<input type="hidden" id="export_published" name="published" value="">
		<input type="hidden" id="export_cid" name="cid" value="">
		<input type="hidden" name="filter_order" value="<?php echo $this->state->filter_order; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $this->state->filter_order_Dir; ?>" />
		<?php echo HTMLHelper::_('form.token'); ?>
	</form>
<?php
}
