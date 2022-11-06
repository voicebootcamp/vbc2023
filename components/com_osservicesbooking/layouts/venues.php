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
                <?php echo JText::_('OS_LIST_ALL_VENUES');?>
            </h1>
        </div>
        <?php
    }
}
if(count($venues) > 0)
{
    foreach ($venues as $venue)
    {
		if($venue->venue_name != "")
		{
			$venue_name = $venue->venue_name;
		}
		else
		{
			$venue_name = OSBHelper::getLanguageFieldValue($venue,'address');
		}
        ?>
        <div class="<?php echo $mapClass['row-fluid'];?>">
            <div class="<?php echo $mapClass['span12'];?>">
                <div class="<?php echo $mapClass['row-fluid'];?>">
                    <div class="<?php echo $mapClass['span4'];?>">
                        <div id="ospitem-watermark_box">
                            <?php
                            if($venue->image != "")
                            {
                                ?>
                                <img src="<?php echo JURI::root(true)?>/images/osservicesbooking/venue/<?php echo $venue->image?>" alt="<?php echo $venue_name; ?>" />
                                <?php
                            }
                            else
                            {
                                ?>
                                <img src="<?php echo JURI::root(true)?>/components/com_osservicesbooking/asset/images/no_image_available.png" alt="<?php echo $venue_name; ?>"/>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                    <div class="<?php echo $mapClass['span8'];?> ospitem-leftpad">
                        <div class="ospitem-leftpad">
                            <div class="<?php echo $mapClass['row-fluid'];?> ospitem-toppad">
                                <div class="<?php echo $mapClass['span12'];?>">
                                    <span class="ospitem-itemtitle title-blue">
                                        <a href="<?php echo JText::_('index.php?option=com_osservicesbooking&task=default_layout&vid='.$venue->id)?>" title="<?php echo JText::_('OS_VENUE_DETAILS');?>">
                                            <?php
                                            echo $venue_name;
                                            ?>
                                        </a>
                                        <?php
                                        if(($venue->lat_add != "") && ($venue->long_add != ""))
                                        {
                                            ?>
                                            <a href="<?php echo JURI::root()?>index.php?option=com_osservicesbooking&task=default_showmap&vid=<?php echo $venue->id?>&tmpl=component" class="osmodal" rel="{handler: 'iframe', size: {x: 600, y: 400}}" title="<?php echo JText::_('OS_VENUE_MAP');?>">
                                                <img src="<?php echo JURI::root()?>media/com_osservicesbooking/assets/css/images/location24.png" />
                                            </a>
                                            <?php
                                        }
                                        ?>
                                    </span>
                                </div>
                            </div>
                            <div class="<?php echo $mapClass['row-fluid'];?> ospitem-toppad">
                                <div class="<?php echo $mapClass['span12'];?>">
                                    <span style="font-size:11px;"><i>
                                        <?php
                                        if($venue->venue_name != "")
                                        {
                                            echo OSBHelper::getLanguageFieldValue($venue,'address').", ";
                                        }
                                        ?>
                                        <?php
                                        if($venue->city != "")
                                        {
                                            echo OSBHelper::getLanguageFieldValue($venue,'city').", ";
                                        }
                                        if($venue->state != "")
                                        {
                                            echo OSBHelper::getLanguageFieldValue($venue,'state').", ";
                                        }
                                        if($venue->country != "")
                                        {
                                            echo $venue->country;
                                        }
                                        ?></i>
                                    </span>
                                    <div class="clearfix"></div>
                                    <span>
                                        <?php
                                        if($venue->contact_name != "")
                                        {
                                            echo JText::_('OS_CONTACT_NAME').": ".$venue->contact_name;
                                        }
                                        ?>
                                        <BR />
                                        <?php
                                        if($venue->contact_phone != "")
                                        {
                                            echo JText::_('OS_CONTACT_PHONE').": ".$venue->contact_phone;
                                        }
                                        ?>
                                        <BR />
                                        <?php
                                        if($venue->contact_email != "")
                                        {
                                            echo JText::_('OS_CONTACT_EMAIL').": <a href='mailto:".$venue->contact_email."'>".$venue->contact_email."</a>";
                                        }
                                        ?>
											</span>
                                    <div class="clearfix"></div>
                                    <span>
                                        <?php echo JText::_('OS_SERVICES')?>: <?php echo $venue->services; ?>
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
    <div class="<?php echo $mapClass['row-fluid'];?>">
        <div class="<?php echo $mapClass['span12'];?>" style="text-align:center;padding:10px;">
            <strong>
                <?php
                echo JText::_('OS_NO_VENUES');
                ?>
            </strong>
        </div>
    </div>
    <?php
}
?>