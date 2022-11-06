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

$config = OSMembershipHelper::getConfig();
?>
<form action="index.php?option=com_osmembership&view=emails" method="post" name="adminForm" id="adminForm">
    <div id="j-main-container"<?php if ($isJoomla4) echo ' class="mp-joomla4-container"'; ?>>
        <div id="filter-bar" class="btn-toolbar<?php if ($isJoomla4) echo ' js-stools-container-filters-visible'; ?>">
			<div class="filter-search btn-group pull-left">
				<label for="filter_search" class="element-invisible"><?php echo Text::_('OSM_FILTER_SEARCH_EMAILS_DESC');?></label>
				<input type="text" name="filter_search" inputmode="search" id="filter_search" placeholder="<?php echo Text::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->escape($this->state->filter_search); ?>" class="hasTooltip form-control" title="<?php echo HTMLHelper::tooltipText('OSM_SEARCH_EMAILS_DESC'); ?>" />
			</div>
			<div class="btn-group pull-left">
				<button type="submit" class="btn<?php if ($isJoomla4) echo ' btn-primary'; ?> hasTooltip" title="<?php echo HTMLHelper::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>"><span class="icon-search"></span></button>
				<button type="button" class="btn<?php if ($isJoomla4) echo ' btn-primary'; ?> hasTooltip" title="<?php echo HTMLHelper::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('filter_search').value='';this.form.submit();"><span class="icon-remove"></span></button>
			</div>
			<div class="btn-group pull-right">
				<?php
					echo $this->lists['filter_sent_to'];
					echo $this->lists['filter_email_type'];
					echo $this->pagination->getLimitBox();
				?>
			</div>
		</div>
		<div class="clearfix"></div>
		<table class="adminlist table table-striped">
			<thead>
				<tr>
					<th width="20">
						<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this)" />
					</th>
					<th class="title" style="text-align: left;">
						<?php echo HTMLHelper::_('grid.sort', Text::_('OSM_SUBJECT'), 'tbl.subject', $this->state->filter_order_Dir, $this->state->filter_order); ?>
					</th>
					<th class="title">
						<?php echo HTMLHelper::_('grid.sort', Text::_('OSM_EMAIL'), 'tbl.email', $this->state->filter_order_Dir, $this->state->filter_order); ?>
					</th>
					<th class="center title" width="15%">
						<?php echo HTMLHelper::_('grid.sort', Text::_('OSM_SENT_TO'), 'tbl.sent_to', $this->state->filter_order_Dir, $this->state->filter_order); ?>
					</th>			
					<th class="center title" width="15%">
						<?php echo HTMLHelper::_('grid.sort', Text::_('OSM_SENT_AT_TIME'), 'tbl.sent_at', $this->state->filter_order_Dir, $this->state->filter_order); ?>
					</th>
					<th class="center title" width="15%">
						<?php echo HTMLHelper::_('grid.sort', Text::_('OSM_TYPE'), 'tbl.email_type', $this->state->filter_order_Dir, $this->state->filter_order); ?>
					</th>
					<th class="center" width="5%">
						<?php echo HTMLHelper::_('grid.sort', Text::_('OSM_ID'), 'tbl.id', $this->state->filter_order_Dir, $this->state->filter_order); ?>
					</th>													
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="7">
						<?php echo $this->pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
			<tbody>
			<?php
			$k = 0;

			$sentTos = array(
				1 => Text::_('OSM_ADMIN'),
				2 => Text::_('OSM_SUBSCRIBERS'),
			);

			for ($i=0, $n=count($this->items); $i < $n; $i++)
			{
				$row = &$this->items[$i];
				$link 	= Route::_('index.php?option=com_osmembership&view=email&id=' . $row->id);
				$checked 	= HTMLHelper::_('grid.id', $i, $row->id);
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td>
						<?php echo $checked; ?>
					</td>
					<td>
						<a href="<?php echo $link; ?>">
							<?php echo $row->subject; ?>
						</a>
					</td>
					<td>
						<a href="mailto:<?php echo $row->email; ?>"><?php echo $row->email; ?></a>
					</td>
					<td class="center">
						<?php
							if (isset($sentTos[$row->sent_to]))
							{
								echo $sentTos[$row->sent_to];
							}
						?>
					</td>	
					<td class="center">
						<?php echo HTMLHelper::_('date', $row->sent_at, $config->date_format . ' H:i'); ?>
					</td>											
					<td class="center">
						<?php
						if (isset($this->emailTypes[$row->email_type]))
						{
							echo $this->emailTypes[$row->email_type];
						}
						?>
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