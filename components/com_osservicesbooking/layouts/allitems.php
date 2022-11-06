<?php
if(isset($params) && $params->get('show_page_heading') == 1){
    if($params->get('page_heading') != "") {
        ?>
        <div class="page-header">
            <h1>
                <?php echo $params->get('page_heading');?>
            </h1>
        </div>
        <?php
    }
}
if($introtext != "")
{
	?>
	<div class="<?php echo $mapClass['row-fluid'];?>" id="itemsListingintrotext">
		<div class="<?php echo $mapClass['span12'];?>">
			<?php
			echo $introtext;
			?>
		</div>
	</div>
	<?php
}
if(($show_category == 1) && (count($categories) > 0))
{
    ?>
	<div class="<?php echo $mapClass['row-fluid'];?>" id="categoriesListing">
		<div class="<?php echo $mapClass['span12'];?>">
			<div class="sub-page-header">
				<h2>
					<?php echo JText::_('OS_LIST_ALL_CATEGORIES');?>
				</h2>
			</div>
			<div id="mainwrapper" class="<?php echo $mapClass['row-fluid'];?>">
			<?php
			$j = 0;
			foreach ($categories as $category){
				$j++;
				$link = Jroute::_('index.php?option=com_osservicesbooking&task=default_layout&category_id='.$category->id.'&Itemid='.$jinput->getInt('Itemid',0));
				?>
				<div class="<?php echo $mapClass['span4'];?> information_box">
					<div class="information_box_img">
						<a href="<?php echo $link; ?>" title="<?php echo JText::_('OS_CATEGORY_DETAILS');?>">
							<?php
							if($category->category_photo != ""){
								?>
								<img src="<?php echo JURI::root()?>images/osservicesbooking/category/<?php echo $category->category_photo?>" alt="<?php echo OSBHelper::getLanguageFieldValue($category,'category_name');?>" />
								<?php
							}else{
								?>
								<img src="<?php echo JURI::root()?>components/com_osservicesbooking/asset/images/no_image_available.png" alt="<?php echo OSBHelper::getLanguageFieldValue($category,'category_name');?>" />
								<?php
							}
							?>
						</a>
					</div>
					<span class="full-caption">
						<h3><a href="<?php echo $link; ?>" title="<?php echo JText::_('OS_CATEGORY_DETAILS');?>"><?php echo OSBHelper::getLanguageFieldValue($category,'category_name');?></a></h3>
						<?php
						if($category->show_desc == 1){
							?>
							<div class="full-desc">
								<?php HelperOSappscheduleCommon::showDescription(strip_tags(OSBHelper::getLanguageFieldValue($category,'category_description')));?>
							</div>
							<?php
						}
						?>
					</span>
				</div>
				<?php
				if($j == 3){
					$j = 0;
					?>
					</div><div class="<?php echo $mapClass['row-fluid'];?>">
					<?php
				}
			}
			?>
			</div>
		</div>
	</div>
    <?php
}

if(($show_service == 1) && (count($services) > 0))
{
    ?>
	<div class="<?php echo $mapClass['row-fluid'];?>" id="servicesListing">
		<div class="<?php echo $mapClass['span12'];?>">
			<div class="sub-page-header">
				<h2>
					<?php echo JText::_('OS_LIST_ALL_SERVICES');?>
				</h2>
			</div>
			<div id="mainwrapper" class="<?php echo $mapClass['row-fluid'];?>">
			<?php
			$j = 0;
			foreach ($services as $service)
			{
				$j++;
				$link = Jroute::_('index.php?option=com_osservicesbooking&task=default_layout&sid='.$service->id.'&Itemid='.$jinput->getInt('Itemid',0));
				?>
				<div class="<?php echo $mapClass['span4'];?> information_box">
					<div class="information_box_img">
						<a href="<?php echo $link; ?>" title="<?php echo JText::_('OS_SERVICE_DETAILS');?>">
							<?php
							if($service->service_photo != ""){
								?>
								<img src="<?php echo JURI::root()?>images/osservicesbooking/services/<?php echo $service->service_photo; ?>" alt="<?php echo OSBHelper::getLanguageFieldValue($service,'service_name');?>" />
								<?php
							}else{
								?>
								<img src="<?php echo JURI::root()?>components/com_osservicesbooking/asset/images/no_image_available.png" alt="<?php echo OSBHelper::getLanguageFieldValue($service,'service_name');?>" />
								<?php
							}
							?>
						</a>
					</div>
					<span class="full-caption">
						<h3><a href="<?php echo $link; ?>" title="<?php echo JText::_('OS_SERVICE_DETAILS');?>"><?php echo OSBHelper::getLanguageFieldValue($service,'service_name');?></a></h3>
						<div class="full-desc">
							<i class="<?php echo $mapClass['icon-tag'];?>"></i> <?php echo HelperOSappscheduleCommon::getCategoryName($service->id); ?>
							<div class="clearfix"></div>
							<?php echo HelperOSappscheduleCommon::showDescription(strip_tags(OSBHelper::getLanguageFieldValue($service,'service_description')));?>
						</div>
					</span>
				</div>
				<?php
				if($j == 3){
					$j = 0;
					?>
					</div><div class="<?php echo $mapClass['row-fluid'];?>">
					<?php
				}
			}
			?>

			</div>
		</div>
	</div>
    <?php
}

if(($show_employee == 1) && (count($employees) > 0))
{
    ?>
	<div class="<?php echo $mapClass['row-fluid'];?>" id="employeesListing">
		<div class="<?php echo $mapClass['span12'];?>">
			<div class="sub-page-header">
				<h2>
					<?php echo JText::_('OS_LIST_ALL_EMPLOYEES');?>
				</h2>
			</div>
			<div id="mainwrapper" class="<?php echo $mapClass['row-fluid'];?>">
			<?php
			$j = 0;
			foreach ($employees as $employee)
			{
				$j++;
				$link = Jroute::_('index.php?option=com_osservicesbooking&task=default_layout&employee_id='.$employee->id.'&Itemid='.$jinput->getInt('Itemid',0));
				?>
				<div class="<?php echo $mapClass['span4'];?> information_box">
					<div class="information_box_img">
						<a href="<?php echo $link; ?>" title="<?php echo JText::_('OS_DETAILS');?>">
							<?php
							if ($employee->employee_photo != "") {
								?>
								<img src="<?php echo JURI::root()?>images/osservicesbooking/employee/<?php echo $employee->employee_photo?>" alt="<?php echo $employee->employee_name;?>" />
								<?php
							} else {
								?>
								<img src="<?php echo JURI::root()?>components/com_osservicesbooking/asset/images/no_image_available.png" alt="<?php echo $employee->employee_name;?>" />
								<?php
							}
							?>
						</a>
					</div>
					<span class="full-caption">
						<h3><a href="<?php echo $link; ?>" title="<?php echo JText::_('OS_DETAILS');?>"><?php echo $employee->employee_name;?></a></h3>
						<div class="full-desc">
							<i class="<?php echo $mapClass['icon-tag'];?>"></i> <?php echo HelperOSappscheduleCommon::getServiceNames($employee->id); ?>
							<?php
							echo '<div class="clearfix"></div>';
							if ($employee->employee_phone != "") {
								echo "<i class='".$mapClass['icon-phone']."'></i>&nbsp;".$employee->employee_phone;
								echo '<div class="clearfix"></div>';
							}
							if ($employee->employee_email != "") {
								echo "<i class='".$mapClass['icon-mail']."'></i>&nbsp;<a href='mailto:" . $employee->employee_email . "'>" . $employee->employee_email . "</a>";
								echo '<div class="clearfix"></div>';
							}
							if ($employee->employee_notes != "") {
								echo $employee->employee_notes;
							}
							?>
						</div>
					</span>
				</div>
				<?php
				if($j == 3)
				{
					$j = 0;
					?>
					</div><div class="<?php echo $mapClass['row-fluid'];?>">
					<?php
				}
			}
			?>
			</div>
		</div>
	</div>
    <?php
}
?>