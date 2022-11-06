<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2021 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

$i = 1;
?>
<p style="padding-bottom: 20px; text-align: center;">
<h1><?php echo JText::_('OS_ORDERS_LIST'); ?></h1>
</p>
<table border="1" width="100%" cellspacing="0" cellpadding="2" style="margin-top: 100px;">
	<thead>
	<tr>
		<th width="3%" height="20" style="text-align: center;">
			No
		</th>
		<th height="20" width="15%">
			<?php echo JText::_('OS_CUSTOMER_DETAILS'); ?>
		</th>
		<th height="20" width="30%">
			<?php echo JText::_('OS_BOOKING_INFORMATION'); ?>
		</th>
		<th height="20" width="20%">
			<?php echo JText::_('OS_OTHER_INFORMATION'); ?>
		</th>
		<?php
		if($configClass['disable_payment'] == 0)
		{
		?>
			<th width="15%">
				<?php echo JText::_('OS_ORDER_PAYMENT');?>
			</th>
		<?php
		} 
		?>
		<th height="20" width="7%">
			<?php echo JText::_('OS_STATUS'); ?>
		</th>
		<th height="20" width="10%" style="text-align: center">
			<?php echo JText::_('OS_DATE'); ?>
		</th>
		<th width="3%" height="20" style="text-align: center;">
			<?php echo JText::_('OS_ID'); ?>
		</th>
	</tr>
	</thead>
	<tbody>
	<?php
	
	foreach ($rows as $row)
	{
		?>
		<tr>
			<td width="3%" style="text-align: center;" valign="top"><?php echo $row->order_id; ?></td>
			<td width="15%" valign="top">
				<?php echo $row->order_name; ?>
				<BR />
				<a href="mailto:<?php echo $row->order_email?>" target="_blank"><?php echo $row->order_email?></a>
			</td>
			<td width="30%" valign="top">
				<?php echo JText::_('OS_SERVICE');?>: <?php echo $row->service_name; ?>
				<BR />
				<?php echo JText::_('OS_EMPLOYEE');?>: <?php echo $row->employee_name; ?>
				<?php
				if($row->address != "")
				{
					?>
					<BR />
					<?php echo JText::_('OS_VENUE');?>: <?php echo $row->address; ?>
					<?php
				}
				?>
				<BR />
				<?php echo JText::_('OS_BOOKING_DATE');?>: <?php echo date($configClass['date_format'],$row->start_time); ?>
				<BR />
				<?php echo JText::_('OS_START_TIME');?>: <?php echo date($configClass['time_format'],$row->start_time); ?>
				<BR />
				<?php echo JText::_('OS_END_TIME');?>: <?php echo date($configClass['time_format'],$row->end_time); ?>
				<?php
				if($row->service_time_type == 1)
				{
					?>
					<BR />
					<?php echo JText::_('OS_NUMBER_SLOT');?>: <?php echo $row->nslots ?>
					<?php
				}
				
				if(count($extraFields))
				{
					foreach($extraFields as $field)
					{
						if($row->{'field_'.$field->id} != "")
						{
							?>
							<BR />
							<?php echo $field->field_label;?>: <?php echo $row->{'field_'.$field->id} ;?>
							<?php
						}
					}
				}
				?>
			</td>
			<td width="20%;" valign="top">
				<?php
				if(count($checkoutFields))
				{
					foreach($checkoutFields as $f)
					{
						$field_value = OsAppscheduleDefault::orderFieldData($f, $row->order_id);
						echo $field_value;
						echo "<BR />";
					}
				}	
				?>
			</td>
			<?php
			if($configClass['disable_payment'] == 0)
			{
			?>
				<td width="15%" valign="top">
					<?php
						echo JText::_('OS_TOTAL').": ".OSBHelper::showMoney($row->order_total,1);
					?>
					<br />
					<?php
						echo JText::_('OS_DISCOUNT').": ".OSBHelper::showMoney($row->order_discount,1);
					?>
					<br />
					<?php
					echo JText::_('OS_GROSS_AMOUNT').": ".OSBHelper::showMoney($row->order_final_cost,1);
					?>
					<br />
					<?php
						echo JText::_('OS_DEPOSIT').": ".OSBHelper::showMoney($row->order_upfront,1);
					?>
					<br />
					<?php 
					$order_payment = $row->order_payment;
					if($order_payment != "")
					{
						echo Jtext::_('OS_PAYMENT')." <strong>".JText::_(os_payments::loadPaymentMethod($order_payment)->title)."</strong>";
						if($row->refunded == 1)
						{
							?>
							<BR />
							<span style="color:red;font-weight: bold;"><?php echo JText::_('OS_REFUNDED');?></span>
							<?php
						}
					}
				?></td>
			<?php
			}
			?>
			<td width="7%" valign="top"><?php echo OSBHelper::orderStatus(0,$row->order_status); ?></td>
			<td width="10%" style="text-align: center;" valign="top"><?php echo date($configClass["date_time_format"],strtotime($row->order_date)); ?></td>
			<td width="3%" style="text-align: center;" valign="top"><?php echo $row->id; ?></td>
		</tr>
		<?php
	}
	?>
	</tbody>
</table>