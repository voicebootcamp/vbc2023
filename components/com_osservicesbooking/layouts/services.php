<?php
if(isset($params) && $params->get('show_page_heading') == 1)
{
    if($params->get('page_heading') != "")
	{
        ?>
        <div class="page-header">
            <h1>
                <?php echo $params->get('page_heading');?>
            </h1>
        </div>
        <?php
    }
	else
	{
        ?>
        <div class="page-header">
            <h1>
                <?php echo JText::_('OS_LIST_ALL_SERVICES');?>
            </h1>
        </div>
        <?php
    }
}
if($introtext != "")
{
	?>
	<div class="<?php echo $mapClass['row-fluid'];?>" id="servicesListingintrotext">
		<div class="<?php echo $mapClass['span12'];?>">
			<?php
			echo $introtext;
			?>
		</div>
	</div>
	<?php
}
if(count($services) > 0)
{
	?>
	<div class="<?php echo $mapClass['row-fluid'];?>" id="servicesListing">
		<div class="<?php echo $mapClass['span12'];?>">
			<?php
			if($list_type == 0)
			{
				foreach ($services as $service)
				{
					$link = Jroute::_('index.php?option=com_osservicesbooking&task=default_layout&sid='.$service->id.'&Itemid='.$jinput->getInt('Itemid',0));
					?>
					<div class="<?php echo $mapClass['row-fluid'];?>">
						<div class="<?php echo $mapClass['span12'];?>">
							<div class="<?php echo $mapClass['row-fluid'];?>">
								<div class="<?php echo $mapClass['span4'];?>">
									<div id="ospitem-watermark_box">
										<a href="<?php echo $link; ?>" title="<?php echo JText::_('OS_SERVICE_DETAILS');?>">
											<?php
											if($service->service_photo != ""){
												?>
												<img src="<?php echo JURI::root(true)?>/images/osservicesbooking/services/<?php echo $service->service_photo?>" alt="<?php echo OSBHelper::getLanguageFieldValue($service,'service_name'); ?>"/>
												<?php
											}else{
												?>
												<img src="<?php echo JURI::root(true)?>/components/com_osservicesbooking/asset/images/no_image_available.png" alt="<?php echo OSBHelper::getLanguageFieldValue($service,'service_name'); ?>"/>
												<?php
											}
											?>
										</a>
									</div>
								</div>
								<div class="<?php echo $mapClass['span8'];?> ospitem-leftpad">
									<div class="ospitem-leftpad">
										<div class="<?php echo $mapClass['row-fluid'];?> ospitem-toppad">
											<div class="<?php echo $mapClass['span12'];?>">
												<span class="ospitem-itemtitle title-blue">
													<a href="<?php echo $link; ?>" title="<?php echo JText::_('OS_SERVICE_DETAILS');?>">
														<?php
														echo OSBHelper::getLanguageFieldValue($service,'service_name');
														?>
													</a>
												</span>
											</div>
										</div>
										<div class="<?php echo $mapClass['row-fluid'];?> ospitem-toppad">
											<div class="<?php echo $mapClass['span12'];?>">
												<span>
													<i class="<?php echo $mapClass['icon-tag'];?>"></i> <?php echo HelperOSappscheduleCommon::getCategoryName($service->id); ?>
													<div class="clearfix"></div>
													<?php HelperOSappscheduleCommon::showDescription(OSBHelper::getLanguageFieldValue($service,'service_description'));?>
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
			{ //grid view
				?>
				<div class="<?php echo $mapClass['row-fluid'];?>">
					<?php
					$j = 0;
					foreach ($services as $service){
						$j++;
						$link = Jroute::_('index.php?option=com_osservicesbooking&task=default_layout&sid='.$service->id.'&Itemid='.$jinput->getInt('Itemid',0));
						?>
						<div class="<?php echo $mapClass['span4'];?> information_box">
							<div class="information_box_img">
								<a href="<?php echo $link; ?>" title="<?php echo JText::_('OS_SERVICE_DETAILS');?>">
									<?php
									if($service->service_photo != ""){
										?>
										<img src="<?php echo JURI::root()?>images/osservicesbooking/services/<?php echo $service->service_photo; ?>" alt="<?php echo OSBHelper::getLanguageFieldValue($service,'service_name'); ?>"/>
										<?php
									}else{
										?>
										<img src="<?php echo JURI::root()?>components/com_osservicesbooking/asset/images/no_image_available.png" alt="<?php echo OSBHelper::getLanguageFieldValue($service,'service_name'); ?>"/>
										<?php
									}
									?>
								</a>
							</div>
							<span class="full-caption">
								<h3><a href="<?php echo $link; ?>" title="<?php echo JText::_('OS_SERVICE_DETAILS');?>"><?php echo OSBHelper::getLanguageFieldValue($service,'service_name');?></a></h3>
								<div class="full-desc">
									<i class="icon-tag"></i> <?php echo HelperOSappscheduleCommon::getCategoryName($service->id); ?>
									<div class="clearfix"></div>
									<?php HelperOSappscheduleCommon::showDescription(strip_tags(OSBHelper::getLanguageFieldValue($service,'service_description')));?>
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
                echo JText::_('OS_NO_SERVICES');
                ?>
            </strong>
        </div>
    </div>
    <?php
}
?>