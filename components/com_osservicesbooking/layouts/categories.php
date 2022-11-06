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
                <?php echo JText::_('OS_LIST_ALL_CATEGORIES');?>
            </h1>
        </div>
        <?php
    }
}
if($introtext != "")
{
	?>
	<div class="<?php echo $mapClass['row-fluid'];?>" id="categoriesListingintrotext">
		<div class="<?php echo $mapClass['span12'];?>">
			<?php
			echo $introtext;
			?>
		</div>
	</div>
	<?php
}
if(count($categories) > 0)
{
	?>
	<div class="<?php echo $mapClass['row-fluid'];?>" id="categoriesListing">
		<div class="<?php echo $mapClass['span12'];?>">
			<?php
			if($list_type == 0)
			{
				foreach ($categories as $category)
				{
					?>
					<div class="<?php echo $mapClass['row-fluid'];?>">
						<div class="<?php echo $mapClass['span12'];?>">
							<div class="<?php echo $mapClass['row-fluid'];?>">
								<div class="<?php echo $mapClass['span4'];?>">
									<div id="ospitem-watermark_box">
										<a href="<?php echo $category->link; ?>" title="<?php echo JText::_('OS_CATEGORY_DETAILS');?>">
											<?php
											if($category->category_photo != "")
											{
												?>
												<img src="<?php echo JURI::root(true)?>/images/osservicesbooking/category/<?php echo $category->category_photo?>" alt="<?php echo OSBHelper::getLanguageFieldValue($category,'category_name'); ?>" />
												<?php
											}else{
												?>
												<img src="<?php echo JURI::root(true)?>/components/com_osservicesbooking/asset/images/no_image_available.png" alt="<?php echo OSBHelper::getLanguageFieldValue($category,'category_name'); ?>" />
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
														<a href="<?php echo $category->link; ?>" title="<?php echo JText::_('OS_CATEGORY_DETAILS');?>">
															<?php
															echo OSBHelper::getLanguageFieldValue($category,'category_name');
															?>
														</a>
													</span>
											</div>
										</div>
										<?php
										if($category->show_desc == 1)
										{
											?>
											<div class="<?php echo $mapClass['row-fluid'];?> ospitem-toppad">
												<div class="<?php echo $mapClass['span12'];?>">
													<span>
														<?php HelperOSappscheduleCommon::showDescription(OSBHelper::getLanguageFieldValue($category,'category_description'));?>
													</span>
												</div>
											</div>
										<?php
										}
										?>
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
				<div id="mainwrapper" class="<?php echo $mapClass['row-fluid'];?>">
					<?php
					$j = 0;
					foreach ($categories as $category)
					{
						$j++;
						$link = $category->link;
						?>
						<div class="<?php echo $mapClass['span4'];?> information_box">
							<div class="information_box_img">
								<a href="<?php echo $link; ?>" title="<?php echo JText::_('OS_CATEGORY_DETAILS');?>">
									<?php
									if($category->category_photo != "")
									{
										?>
										<img src="<?php echo JURI::root()?>images/osservicesbooking/category/<?php echo $category->category_photo?>" alt="<?php echo OSBHelper::getLanguageFieldValue($category,'category_name'); ?>" />
										<?php
									}else{
										?>
										<img src="<?php echo JURI::root()?>components/com_osservicesbooking/asset/images/no_image_available.png" alt="<?php echo OSBHelper::getLanguageFieldValue($category,'category_name'); ?>" />
										<?php
									}
									?>
								</a>
							</div>
							<span class="full-caption">
								<h3><a href="<?php echo $link; ?>" title="<?php echo JText::_('OS_CATEGORY_DETAILS');?>"><?php echo OSBHelper::getLanguageFieldValue($category,'category_name');?></a></h3>
								<?php
								if($category->show_desc == 1)
								{
									?>
									<div class="full-desc">
										<?php echo HelperOSappscheduleCommon::showDescription(strip_tags(OSBHelper::getLanguageFieldValue($category,'category_description')));?>
									</div>
									<?php
								}
								?>
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
                echo JText::_('OS_NO_CATEGORIES');
                ?>
            </strong>
        </div>
    </div>
    <?php
}
?>