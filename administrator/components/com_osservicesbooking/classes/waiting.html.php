<?php
/*------------------------------------------------------------------------
# worktime_custom.html.php - Ossolution Services Booking
# ------------------------------------------------------------------------
# author    Ossolution team
# copyright Copyright (C) 2016 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/
// no direct access
defined('_JEXEC') or die;


class HTML_OSappscheduleWaiting{
	/**
	 * Extra field list HTML
	 *
	 * @param unknown_type $option
	 * @param unknown_type $rows
	 * @param unknown_type $pageNav
	 * @param unknown_type $lists
	 */
	static function waiting_list($option,$rows,$pageNav,$lists)
	{
		global $mainframe,$_jversion,$configClass;
		
		JHtml::_('behavior.multiselect');
		
		JToolBarHelper::title(JText::_('OS_MANAGE_WAITING_LIST'),'clock');
		JToolBarHelper::addNew('waiting_add');
		if(count($rows) > 0)
		{
			JToolBarHelper::deleteList(JText::_('OS_ARE_YOU_SURE_TO_REMOVE_ITEMS'),'waiting_remove');
		}
		JToolbarHelper::custom('cpanel_list','featured.png', 'featured_f2.png',JText::_('OS_DASHBOARD'),false);
	?>
		<form method="POST" action="index.php?option=<?php echo $option; ?>&task=waiting_list" name="adminForm" id="adminForm">
			<table cellpadding="0" cellspacing="0" width="100%" border="0">
				<tr>
					<td align="left">
						<?php echo JText::_("OS_FILTER")?>: &nbsp;
						<?php echo $lists['filter_service'];?>
						<?php echo $lists['filter_employee'];?>
						<input type="submit" class="btn btn-warning" value="Go">
						<input type="reset"  class="btn btn-info" value="Reset" onclick="document.adminForm.filter_service.value='0';document.adminForm.filter_employee.value='0';this.form.submit();">
					</td>
				</tr>
			</table>
			<table class="adminlist table table-striped" width="100%">
				<thead>
					<tr>
						<th width="2%" style="text-align:center;">#</th>
						<th width="3%" style="text-align:center;">
							<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
						</th>
						<th width="20%">
							<?php echo JText::_('EMAIL');?>
						</th>
						<th width="15%" style="text-align:left;">
							<?php echo JHTML::_('grid.sort',   JText::_('OS_SERVICE'), 'b.service_name', @$lists['order_Dir'], @$lists['order'] ); ?>
						</th>
						<th width="15%" style="text-align:left;">
							<?php echo JHTML::_('grid.sort',   JText::_('OS_EMPLOYEE'), 'c.employee_name', @$lists['order_Dir'], @$lists['order'] ); ?>
						</th>
						<th width="15%">
							<?php echo JHTML::_('grid.sort',   JText::_('OS_WORKTIME_START_TIME'), 'start_time', @$lists['order_Dir'], @$lists['order'] ); ?>
						</th>
						<th width="15%">
							<?php echo JHTML::_('grid.sort',   JText::_('OS_WORKTIME_END_TIME'), 'end_time', @$lists['order_Dir'], @$lists['order'] ); ?>
						</th>
						<th width="5%" style="text-align:center;">
							<?php echo JHTML::_('grid.sort',   JText::_('OS_ID'), 'id', @$lists['order_Dir'], @$lists['order'] ); ?>
						</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td width="100%" colspan="9" style="text-align:center;">
							<?php
								echo $pageNav->getListFooter();
							?>
						</td>
					</tr>
				</tfoot>
				<tbody>
				<?php
				$k = 0;
				for ($i=0, $n=count($rows); $i < $n; $i++) 
				{
					$row = $rows[$i];
					$checked = JHtml::_('grid.id', $i, $row->id);
					?>
					<tr class="<?php echo "row$k";?>">
						<td align="center" style="text-align:center;"><?php echo $pageNav->getRowOffset( $i ); ?></td>
						<td align="center" style="text-align:center;"><?php echo $checked; ?></td>
						<td align="left">
							<a href="mailto:<?php echo $row->email; ?>">
								<?php echo $row->email; ?>
							</a>
						</td>
						<td align="center" style="text-align:left;">
							<?php echo $row->service_name; ?>
						</td>
						<td align="center" style="text-align:left;">
							<?php echo $row->employee_name; ?>
						</td>
						<td align="center" style="text-align:left;"><?php echo date($configClass['date_time_format'],$row->start_time); ?> </td>
						<td align="center" style="text-align:left;"><?php echo date($configClass['date_time_format'],$row->end_time); ?></td>
						<td align="center" style="text-align:center;"><?php echo $row->id; ?></td>
					</tr>
				<?php
					$k = 1 - $k;	
				}
				?>
				</tbody>
			</table>
			<input type="hidden" name="option" value="<?php echo $option; ?>">
			<input type="hidden" name="task" value="waiting_list">
			<input type="hidden" name="boxchecked" value="0">
			<input type="hidden" name="filter_order" value="<?php echo $lists['order'];?>">
			<input type="hidden" name="filter_order_Dir" value="<?php echo $lists['order_Dir'];?>">
		</form>
		<?php
	}
}
?>