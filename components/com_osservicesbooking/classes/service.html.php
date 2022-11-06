<?php
/*------------------------------------------------------------------------
# service.html.php - Ossolution Services Booking
# ------------------------------------------------------------------------
# author    Ossolution team
# copyright Copyright (C) 2022 Joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/

// No direct access.
defined('_JEXEC') or die;

class HTML_OsAppscheduleService{
    static function showCalendarView($id,$vid,$year,$month,$params){
        global $mainframe,$mapClass,$configClass,$jinput;
		if(version_compare(JVERSION, '4.0.0-dev', 'lt'))
		{
			JHTML::_('behavior.modal','osmodal');
			JHTML::_('behavior.modal','a.osmodal');
		}
		else
		{
			OSBHelperJquery::colorbox('osmodal');
			OSBHelperJquery::colorbox('a.osmodal');
		}
        //print_r($params);
        if($params->get('show_page_heading') == 1){
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
                        <?php echo JText::_('OS_CALENDAR_VIEW');?>
                    </h1>
                </div>
                <?php
            }
        }else{
			?>
                <div class="page-header">
                    <h1>
                        <?php echo JText::_('OS_CALENDAR_VIEW');?>
                    </h1>
                </div>
                <?php
		}
		$currentLink = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		
		$month						= (int)$month;
		$year						= (int)$year;
		
		if($month == 1)
		{
			$prvMonth				= 12;
			$prvYear				= $year - 1;
		}
		else
		{
			$prvMonth				= $month - 1;
			$prvYear				= $year;
		}

		if($month == 12)
		{
			$nxtMonth				= 1;
			$nxtYear				= $year + 1;
		}
		else
		{
			$nxtMonth				= $month + 1;
			$nxtYear				= $year;
		}
        ?>
        <form method="POST" action="<?php echo $currentLink; ?>" name="ftForm" id="ftForm">
            <div class="<?php echo $mapClass['row-fluid'];?>">
                <div class="<?php echo $mapClass['span12'];?>">
                    <?php
                    OsAppscheduleAjax::showCalendarView($id,$vid,$month,$year);
                    ?>
                </div>
            </div>
			<input type="hidden" name="current_item_value" id="current_item_value" value="" />
			<input type="hidden" name="live_site" id="live_site" value="<?php echo JUri::root();?>" />
			<input type="hidden" name="month" id="month" value="<?php echo (int) $month;?>" />
			<input type="hidden" name="year" id="year" value="<?php echo (int) $year;?>" />
			<input type="hidden" name="prvmonth" id="prvmonth" value="<?php echo (int) $prvMonth;?>" />
			<input type="hidden" name="prvyear" id="prvyear" value="<?php echo (int) $prvYear;?>" />
			<input type="hidden" name="nxtmonth" id="nxtmonth" value="<?php echo (int) $nxtMonth;?>" />
			<input type="hidden" name="nxtyear" id="nxtyear" value="<?php echo (int) $nxtYear;?>" />
            <input type="hidden" name="option" value="com_osservicesbooking" />
			<input type="hidden" name="id" id="id" value="<?php echo $id;?>" />
			<input type="hidden" name="vid" id="vid" value="<?php echo $vid;?>" />
            <input type="hidden" name="task" value="" />
        </form>
		<script type="text/javascript">
		function nextCalendarViewNonAjax()
		{
			jQuery("#month").val(jQuery("#nxtmonth").val());
			jQuery("#year").val(jQuery("#nxtyear").val());
			document.getElementById("ftForm").submit();
		}
		function previousCalendarViewNonAjax()
		{
			jQuery("#month").val(jQuery("#prvmonth").val());
			jQuery("#year").val(jQuery("#prvyear").val());
			document.getElementById("ftForm").submit();
		}
		function movingCalendarViewNonAjax()
		{
			jQuery("#month").val(jQuery("#ossm").val());
			jQuery("#year").val(jQuery("#ossy").val());
			document.getElementById("ftForm").submit();
		}
		</script>
        <?php
    }

    /**
     * @param $services
     * @param $params
     * @param $list_type
     */
    public static function listServices($services,$params,$list_type,$category_id,$introtext)
	{
        global $mapClass,$jinput,$mainframe;
        jimport('joomla.filesystem.file');
        if(JFile::exists(JPATH_ROOT.'/templates/'.$mainframe->getTemplate().'/html/com_osservicesbooking/services.php'))
		{
            $tpl = new OSappscheduleTemplate(JPATH_ROOT.'/templates/'.$mainframe->getTemplate().'/html/com_osservicesbooking/');
        }
		else
		{
            $tpl = new OSappscheduleTemplate(JPATH_COMPONENT.'/components/com_osservicesbooking/layouts/');
        }
        $tpl->set('mainframe',$mainframe);
        $tpl->set('services',$services);
        $tpl->set('params',$params);
        $tpl->set('list_type',$list_type);
        $tpl->set('mapClass',$mapClass);
        $tpl->set('jinput',$jinput);
		$tpl->set('category_id',$category_id);
		$tpl->set('introtext',$introtext);
        $body = $tpl->fetch("services.php");
        echo $body;
    }

    /**
     * @param $services
     * @param $categories
     * @param $employees
     * @param $show_category
     * @param $show_service
     * @param $show_employee
     * @param $params
     */
    public static function listItems($services,$categories,$employees,$show_category,$show_service,$show_employee,$params,$introtext)
    {
        global $jinput,$mapClass,$mainframe;
        jimport('joomla.filesystem.file');
        if(JFile::exists(JPATH_ROOT.'/templates/'.$mainframe->getTemplate().'/html/com_osservicesbooking/allitems.php')){
            $tpl = new OSappscheduleTemplate(JPATH_ROOT.'/templates/'.$mainframe->getTemplate().'/html/com_osservicesbooking/');
        }else{
            $tpl = new OSappscheduleTemplate(JPATH_COMPONENT.'/components/com_osservicesbooking/layouts/');
        }
        $tpl->set('mainframe',$mainframe);
        $tpl->set('services',$services);
        $tpl->set('categories',$categories);
        $tpl->set('employees',$employees);
        $tpl->set('show_category',$show_category);
        $tpl->set('show_service',$show_service);
        $tpl->set('params',$params);
        $tpl->set('show_employee',$show_employee);
        $tpl->set('mapClass',$mapClass);
        $tpl->set('jinput',$jinput);
		$tpl->set('introtext',$introtext);
        $body = $tpl->fetch("allitems.php");
        echo $body;
    }
}
?>