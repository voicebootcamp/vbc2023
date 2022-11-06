<?php
/*------------------------------------------------------------------------
# venue.html.php - Ossolution Services Booking
# ------------------------------------------------------------------------
# author    Ossolution team
# copyright Copyright (C) 2019 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/

// No direct access.
defined('_JEXEC') or die;

class HTML_OSappscheduleVenueFnt{
	static function listVenues($venues,$params,$introtext)
	{
		global $mainframe,$mapClass,$configClass;
		if(!OSBHelper::isJoomla4())
		{
			JHTML::_('behavior.modal','osmodal');
		}
		else
		{
			OSBHelperJquery::colorbox('osmodal');
			OSBHelperJquery::colorbox('a.osmodal');
		}
		//print_r($params);
        jimport('joomla.filesystem.file');
        if(JFile::exists(JPATH_ROOT.'/templates/'.$mainframe->getTemplate().'/html/com_osservicesbooking/venues.php'))
		{
            $tpl = new OSappscheduleTemplate(JPATH_ROOT.'/templates/'.$mainframe->getTemplate().'/html/com_osservicesbooking/');
        }
		else
		{
            $tpl = new OSappscheduleTemplate(JPATH_COMPONENT.'/components/com_osservicesbooking/layouts/');
        }
        $tpl->set('mainframe',$mainframe);
        $tpl->set('venues',$venues);
        $tpl->set('params',$params);
        $tpl->set('mapClass',$mapClass);
		$tpl->set('introtext',$introtext);
        $body = $tpl->fetch("venues.php");
        echo $body;
	}
}
?>