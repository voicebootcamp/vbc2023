<?php
/*------------------------------------------------------------------------
# category.html.php - Ossolution Services Booking
# ------------------------------------------------------------------------
# author    Ossolution team
# copyright Copyright (C) 2022 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/

// No direct access.
defined('_JEXEC') or die;

class HTML_OSappscheduleCategory{
	/**
	 * List Categories
	 *
	 * @param unknown_type $categories
	 */
	static function listCategories($categories,$params,$list_type,$introtext)
	{
		global $mainframe,$mapClass,$jinput;
        jimport('joomla.filesystem.file');
        if(JFile::exists(JPATH_ROOT.'/templates/'.$mainframe->getTemplate().'/html/com_osservicesbooking/categories.php'))
		{
            $tpl = new OSappscheduleTemplate(JPATH_ROOT.'/templates/'.$mainframe->getTemplate().'/html/com_osservicesbooking/');
        }
		else
		{
            $tpl = new OSappscheduleTemplate(JPATH_COMPONENT.'/components/com_osservicesbooking/layouts/');
        }
        $tpl->set('mainframe',$mainframe);
        $tpl->set('categories',$categories);
        $tpl->set('params',$params);
        $tpl->set('list_type',$list_type);
        $tpl->set('mapClass',$mapClass);
        $tpl->set('jinput',$jinput);
		$tpl->set('introtext',$introtext);
        $body = $tpl->fetch("categories.php");
        echo $body;
	}
}
?>