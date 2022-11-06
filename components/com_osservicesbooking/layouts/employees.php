<?php
if(isset($params) && $params->get('show_page_heading') == 1){
    if($params->get('page_heading') != ""){
        ?>
        <div class="page-header">
            <h1>
                <?php echo $params->get('page_heading');?>
            </h1>
        </div>
        <?php
    }else{
        ?>
        <div class="page-header">
            <h1>
                <?php echo JText::_('OS_LIST_ALL_EMPLOYEES');?>
            </h1>
        </div>
        <?php
    }
}
if($introtext != "")
{
	?>
	<div class="<?php echo $mapClass['row-fluid'];?>" id="employeesListingintrotext">
		<div class="<?php echo $mapClass['span12'];?>">
			<?php
			echo $introtext;
			?>
		</div>
	</div>
	<?php
}
if(count($employees) > 0)
{
	?>
	<div class="<?php echo $mapClass['row-fluid'];?>" id="employeesListing">
		<div class="<?php echo $mapClass['span12'];?>">
			<?php
			if($list_type == 0) 
			{
				foreach ($employees as $employee) 
				{
					if($employee->vid > 0)
					{
						$vid = "&vid=".$employee->vid;
					}
					else
					{
						$vid = "";
					}
					?>
					<div class="<?php echo $mapClass['row-fluid'];?>">
						<div class="<?php echo $mapClass['span12'];?>">
							<div class="<?php echo $mapClass['row-fluid'];?>">
								<div class="<?php echo $mapClass['span4'];?>">
									<div id="ospitem-watermark_box">
										<a href="<?php echo JText::_('index.php?option=com_osservicesbooking&task=default_layout&employee_id=' . $employee->id.$vid)?>" title="<?php echo JText::_('OS_DETAILS');?>">
											<?php
											if ($employee->employee_photo != "") {
												?>
												<img  src="<?php echo JURI::root(true)?>/images/osservicesbooking/employee/<?php echo $employee->employee_photo?>" alt="<?php echo $employee->employee_name; ?>"/>
												<?php
											} else {
												?>
												<img src="<?php echo JURI::root(true)?>/components/com_osservicesbooking/asset/images/no_image_available.png" alt="<?php echo $employee->employee_name; ?>" />
												<?php
											}
											?>
										</a>
									</div>
								</div>
								<div class="<?php echo $mapClass['span8']?> ospitem-leftpad">
									<div class="ospitem-leftpad">
										<div class="<?php echo $mapClass['row-fluid'];?> ospitem-toppad">
											<div class="<?php echo $mapClass['span12'];?>">
												<span class="ospitem-itemtitle title-blue">
													<a href="<?php echo JText::_('index.php?option=com_osservicesbooking&task=default_layout&employee_id=' . $employee->id.$vid)?>"
													   title="<?php echo JText::_('OS_DETAILS');?>">
														<?php
														echo $employee->employee_name;
														?>
													</a>
												</span>
											</div>
										</div>
										<div class="<?php echo $mapClass['row-fluid'];?> ospitem-toppad">
											<div class="<?php echo $mapClass['span12'];?>">
												<span>
													<i class="<?php echo $mapClass['icon-tag'];?>"></i> <?php echo HelperOSappscheduleCommon::getServiceNames($employee->id); ?>
													<?php
													echo '<div class="clearfix"></div>';
													if($configClass['employee_phone_email'])
													{
														if ($employee->employee_phone != "") {
															echo "<i class='".$mapClass['icon-phone']."'></i>&nbsp;".$employee->employee_phone;
															echo '<div class="clearfix"></div>';
														}
														if ($employee->employee_email != "") {
															echo "<i class='".$mapClass['icon-mail']."'></i>&nbsp;<a href='mailto:" . $employee->employee_email . "'>" . $employee->employee_email . "</a>";
															echo '<div class="clearfix"></div>';
														}
													}
													if ($employee->employee_notes != "") 
													{
														echo nl2br($employee->employee_notes);
													}
													?>
												</span>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<?php
				}
			}
			else
			{
				?>
				<div id="mainwrapper" class="<?php echo $mapClass['row-fluid'];?>">
					<?php
					$j = 0;
					foreach ($employees as $employee){
						if($employee->vid > 0){
							$vid = "&vid=".$employee->vid;
						}else{
							$vid = "";
						}
						$j++;
						$link = Jroute::_('index.php?option=com_osservicesbooking&task=default_layout&employee_id='.$employee->id.$vid.'&Itemid='.$jinput->getInt('Itemid',0));
						?>
						<div class="<?php echo $mapClass['span4'];?> information_box">
							<div class="information_box_img">
								<a href="<?php echo $link; ?>" title="<?php echo JText::_('OS_DETAILS');?>">
									<?php
									if ($employee->employee_photo != "") {
										?>
										<img src="<?php echo JURI::root()?>images/osservicesbooking/employee/<?php echo $employee->employee_photo?>" alt="<?php echo $employee->employee_name; ?>" />
										<?php
									} else {
										?>
										<img src="<?php echo JURI::root()?>components/com_osservicesbooking/asset/images/no_image_available.png" alt="<?php echo $employee->employee_name; ?>" />
										<?php
									}
									?>
								</a>
							</div>
							<span class="full-caption">
								<h3><a href="<?php echo $link; ?>" title="<?php echo JText::_('OS_DETAILS');?>"><?php echo $employee->employee_name;?></a></h3>
								<div class="full-desc">
									<i class="<?php echo $mapClass['icon-tag']; ?>"></i> <?php echo HelperOSappscheduleCommon::getServiceNames($employee->id); ?>
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
						if($j == 3){
							$j = 0;
							?>
							</div><div class="<?php echo $mapClass['row-fluid'];?>">
							<?php
						}
					}
					?>
				</div>
				<div class="clearfix"></div>
				<?php
			}
			?>
		</div>
	</div>
<?php
}
else
{
    ?>
    <div class="<?php echo $mapClass['row-fluid'];?>">
        <div class="<?php echo $mapClass['span12'];?>" style="text-align:center;padding:10px;">
            <strong>
                <?php
                echo JText::_('OS_NO_EMPLOYEES');
                ?>
            </strong>
        </div>
    </div>
    <?php
}
?>