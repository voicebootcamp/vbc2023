<?php
/*------------------------------------------------------------------------
# default.php - OSB cart
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2022 joomdonation.com. All Rights Reserved.
# @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

?>
<div class="<?php echo $mapClass['row-fluid']; ?> modosbcart<?php echo $moduleclass_sfx; ?>" id="cartbox">
	<div class="<?php echo $mapClass['span12']; ?>" id="cartdiv">
		<?php
		$sid					= $jinput->getInt('sid',0);
		$eid					= $jinput->getInt('eid',0);
		$category_id 			= $jinput->getInt('category_id',0);
		$date_from				= $jinput->getInt('date_from','');
		$date_to				= $jinput->getInt('date_to','');
		$vid		 			= $jinput->getInt('vid',0);
		$userdata				= $_COOKIE['userdata'];
		OsAppscheduleAjax::cart($userdata,$vid, $category_id,$eid,$date_from,$date_to);
		?>
	</div>
</div>
<script type="text/javascript">
function removeItem(itemid,sid,start_time,end_time,eid)
{
	<?php if($configClass['use_js_popup'] == 1){?>
	var answer = confirm("<?php  echo JText::_('OS_ARE_YOU_SURE_YOU_WANT_TO_REMOVE_BOOKING')?>");
	<?php }else{ ?>
	var answer = 1;
	<?php } ?>
	if(answer == 1)
	{
		var category_id		= document.getElementById('category_id');
		if(category_id != null)
		{
			catid			= category_id.value;
		}
		else
		{
			catid			= 0;
		}
		var employee_id     = document.getElementById('employee_id');
		if(employee_id != null)
		{
			emid			= employee_id.value;
		}
		else
		{
			emid			= 0;
		}
		var vid				= document.getElementById('vid');
		if(vid != null)
		{
			vidv			= vid.value;
		}
		else
		{
			vidv			= 0;
		}
		var live_site		= document.getElementById('live_site');
		if(live_site != null)
		{
			live_sitev		= live_site.value;
		}
		else
		{
			live_sitev		= "<?php echo JUri::root(); ?>";
		}
		var count_services  = document.getElementById('count_services');
		if(count_services != null)
		{
			var cservices   = count_services.value;
		}
		else
		{
			var cservices	= 0;
		}
		removeItemAjax(itemid,live_sitev,sid,start_time,end_time,eid,catid,emid,vidv,cservices);
	}
}

function removeAllItem(sid)
{
	<?php if($configClass['use_js_popup'] == 1){?>
	var answer = confirm("<?php  echo JText::_('OS_ARE_YOU_SURE_YOU_WANT_TO_REMOVE_BOOKING')?>");
	<?php }else{ ?>
	var answer = 1;
	<?php } ?>
	if(answer == 1)
	{
		var category_id		= document.getElementById('category_id');
		var employee_id     = document.getElementById('employee_id');
		var vid				= document.getElementById('vid');
		var live_site		= document.getElementById('live_site');
		var count_services  = document.getElementById('count_services');
		var category_id		= document.getElementById('category_id');
		if(category_id != null)
		{
			catid			= category_id.value;
		}
		else
		{
			catid			= 0;
		}
		var employee_id     = document.getElementById('employee_id');
		if(employee_id != null)
		{
			emid			= employee_id.value;
		}
		else
		{
			emid			= 0;
		}
		var vid				= document.getElementById('vid');
		if(vid != null)
		{
			vidv			= vid.value;
		}
		else
		{
			vidv			= 0;
		}
		var live_site		= document.getElementById('live_site');
		if(live_site != null)
		{
			live_sitev		= live_site.value;
		}
		else
		{
			live_sitev		= "<?php echo JUri::root(); ?>";
		}
		if(count_services != null)
		{
			var cservices   = count_services.value;
		}
		else
		{
			var cservices	= 0;
		}
		removeAllItemAjax(live_sitev,sid,catid,emid,vidv,cservices);
	}
}
</script>