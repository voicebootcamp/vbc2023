<?php
/*------------------------------------------------------------------------
# default.php - OSB Search
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2021 joomdonation.com. All Rights Reserved.
# @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');
?>
<div class="<?php echo $mapClass['row-fluid']; ?> modosbsearch<?php echo $moduleclass_sfx; ?>">
	<div class="<?php echo $mapClass['span12']; ?>">
		<form method="post" name="osbsearchform" id="osbsearchform" action="<?php echo JRoute::_('index.php?option=com_osservicesbooking&task=default_layout&Itemid='.$itemid);?>" class="form-horizontal">
			<div class="<?php echo $mapClass['row-fluid']; ?>" id="osbsearchmodule">
				<div class="<?php echo $mapClass['span12']; ?>">
					<?php 
					if($show_venue == 1){
					?>
						<div class="<?php echo $mapClass['row-fluid']; ?>">
							<div class="<?php echo $mapClass['span12']; ?>">
								<?php echo JText::_('OS_VENUE');?>
							</div>
						</div>
						<div class="<?php echo $mapClass['row-fluid']; ?>">
							<div class="<?php echo $mapClass['span12']; ?>">
								<?php echo $lists['venue'];?>
							</div>
						</div>
					<?php } ?>
					<?php 
					if($show_category == 1){
					?>
						<div class="<?php echo $mapClass['row-fluid']; ?>">
							<div class="<?php echo $mapClass['span12']; ?>">
								<?php echo JText::_('OS_CATEGORY');?>
							</div>
						</div>
						<div class="<?php echo $mapClass['row-fluid']; ?>">
							<div class="<?php echo $mapClass['span12']; ?>">
								<?php echo $lists['category'];?>
							</div>
						</div>
					<?php } ?>
					<?php
					if($show_service == 1){
						?>
						<div class="<?php echo $mapClass['row-fluid']; ?>">
							<div class="<?php echo $mapClass['span12']; ?>">
								<?php echo JText::_('OS_SERVICE');?>
							</div>
						</div>
						<div class="<?php echo $mapClass['row-fluid']; ?>">
							<div class="<?php echo $mapClass['span12']; ?>">
							<?php echo $lists['service'];?>
							</div>
						</div>
					<?php } ?>
					<?php 
					if($show_employee == 1){
					?>
						<div class="<?php echo $mapClass['row-fluid']; ?>">
							<div class="<?php echo $mapClass['span12']; ?>">
								<?php echo JText::_('OS_EMPLOYEE');?>
							</div>
						</div>
						<div class="<?php echo $mapClass['row-fluid']; ?>">
							<div class="<?php echo $mapClass['span12']; ?>">
								<?php echo $lists['employee'];?>
							</div>
						</div>
					<?php } ?>
				</div>
			</div>
			<?php 
			if($show_date == 1){
			?>
				<div class="<?php echo $mapClass['row-fluid']; ?>">
					<div class="<?php echo $mapClass['span12']; ?>">
						<?php echo JText::_('OS_SELECT_DATE');?>
					</div>
				</div>
				<div class="<?php echo $mapClass['row-fluid']; ?>">
					<div class="<?php echo $mapClass['span12']; ?>">
						<?php 
						echo JHtml::_('calendar',$jinput->get('selected_date','','string'),'selected_date','selected_date','%d-%m-%Y',array("class" => "input-small"));
						?>
					</div>
				</div>
			<?php } ?>
			<div class="<?php echo $mapClass['row-fluid']; ?>">
				<div class="<?php echo $mapClass['span12']; ?>">
					<input type="submit" class="btn btn-success" value="<?php echo JText::_('OS_SUBMIT');?>" />
				</div>
			</div>
			<input type="hidden" name="option" value="com_osservicesbooking" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="live_site" id="live_site" value="<?php echo JUri::root();?>" />
			<input type="hidden" name="show_category" id="show_category" value="<?php echo $show_category;?>" />
			<input type="hidden" name="show_venue" id="show_venue" value="<?php echo $show_venue;?>" />
			<input type="hidden" name="show_employee" id="show_employee" value="<?php echo $show_employee;?>" />
			<input type="hidden" name="show_service" id="show_service" value="<?php echo $show_service;?>" />
			<input type="hidden" name="show_date" id="show_date" value="<?php echo $show_date;?>" />
		</form>
	</div>
</div>