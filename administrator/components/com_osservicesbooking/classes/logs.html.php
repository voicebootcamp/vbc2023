<?php
/*------------------------------------------------------------------------
# logs.html.php - Ossolution Services Booking
# ------------------------------------------------------------------------
# author    Ossolution team
# copyright Copyright (C) 2022 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/
// no direct access
defined('_JEXEC') or die;

class HTML_OSappscheduleLogs
{
	static function logsList($option,$rows, $pageNav, $lists)
	{
		global $mainframe, $configClass;
		JToolBarHelper::title(JText::_('OS_EMAIL_LOGS'),'envelope');
		JToolBarHelper::cancel('goto_index');
		JToolbarHelper::custom('cpanel_list','featured.png', 'featured_f2.png',JText::_('OS_DASHBOARD'),false);
		?>
		<form method="POST" action="index.php" name="adminForm" id="adminForm">
		<table width="100%">
			<tr>
				<td style="text-align:left;">
					<?php echo $lists['email_key'];?>
				</td>
			</tr>
		</table>
		<table width="100%" class="adminlist table table-striped">
			<thead>
				<tr>
                    <th width="5%">#</th>
					<th width="5%">
						<?php echo JText::_('OS_ORDERID');?>
					</th>
					<th width="15%">
						<?php echo JText::_('OS_EMAIL_KEY');?>
					</th>
					<th width="15%">
						<?php echo JText::_('OS_RECEIVED_ADDRESS');?>
					</th>
					<th width="40%">
						<?php echo JText::_('OS_EMAIL_SUBJECT');?>
					</th>
                    <th width="15%" style="text-align:center;">
                        <?php echo JText::_('OS_SENT_FROM'); ?>
                    </th>
				</tr>
			</thead>
			<tbody>
				<?php
				$k = 0;
				for ($i=0, $n=count($rows); $i < $n; $i++) 
				{
					$row = $rows[$i];
					$link = JRoute::_('index.php?option=com_osservicesbooking&task=log_details&id='.$row->id);
					?>
					<tr class="<?php echo "row$k"; ?>">
                        <td align="center"><?php echo $i + 1; ?></td>
						<td align="center"><?php echo $row->order_id; ?></td>
						<td align="left">
							<a href="<?php echo $link; ?>">
								<?php echo $row->email_key; ?>
							</a>
						</td>
						<td align="left">
							<?php echo $row->received_email_address; ?>
						</td>
						<td align="left">
							<?php echo $row->subject; ?>
						</td>
                        <td align="center" style="text-align:center;">
							<?php 
							$date = JFactory::getDate($row->sent_from);
							echo $date->format($configClass['date_time_format']);
							?>
						</td>
					</tr>
					<?php
					$k = 1 - $k;	
				}
				?>
			</tbody>
			<tfoot>
				<tr>
					<td width="100%" colspan="6" style="text-align:center;">
						<?php
							echo $pageNav->getListFooter();
						?>
					</td>
				</tr>
			</tfoot>
		</table>
		<input type="hidden" name="option" value="com_osservicesbooking" />
		<input type="hidden" name="task" value="log_list" />
		<input type="hidden" name="boxchecked" value="0" />
		</form>
		<?php
	}
	
	static function logDetailsForm($option,$row)
	{
		global $mainframe,$configClass;
		JToolBarHelper::title(JText::_('OS_EMAIL_DETAILS') ,'envelope');
		JToolBarHelper::cancel('log_gotolist');
		?>
		<form method="POST" action="index.php" name="adminForm" id="adminForm">
		
		<table cellpadding="0" cellspacing="0" width="100%" class="admintable">
			<tr>
				<td class="key">
					<?php echo JText::_('OS_RECEIVED_ADDRESS')?>
				</td>
				<td class="value">
					<?php echo $row->received_email_address; ?>
				</td>
			</tr>
			<tr>
				<td class="key">
					<?php echo JText::_('OS_EMAIL_SUBJECT')?>
				</td>
				<td class="value">
					<?php echo $row->subject; ?>
				</td>
			</tr>
			<tr>
				<td class="key" valign="top" style="padding-top:5px;">
					<?php echo JText::_('OS_EMAIL_CONTENT')?>
				</td>
				<td class="value">
					<?php echo $row->body; ?>
				</td>
			</tr>
			<tr>
				<td class="key" valign="top" style="padding-top:5px;">
					<?php echo JText::_('OS_SENT_FROM')?>
				</td>
				<td class="value">
					<?php 
					$date = JFactory::getDate($row->sent_from);
					echo $date->format($configClass['date_time_format']);
					?>
				</td>
			</tr>
		</table>
		<input type="hidden" name="option" value="<?php echo $option?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="id" value="<?php echo (int)$row->id?>" />
		</form>		
		<?php
	}
}
?>