<?php
/*------------------------------------------------------------------------
# plugin.php - Ossolution emailss Booking
# ------------------------------------------------------------------------
# author    Ossolution team
# copyright Copyright (C) 2019 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/
// no direct access
defined('_JEXEC') or die;

class OSappschedulePlugin{
	static function display($option,$task){
		global $mainframe,$jinput;
		$mainframe = JFactory::getApplication();
        $cid        = $jinput->get('cid',array(),'ARRAY');
        \Joomla\Utilities\ArrayHelper::toInteger($cid,array(0));
		switch ($task){
			default:
			case "plugin_list":
				OSappschedulePlugin::plugin_list($option);
			break;
			case "plugin_unpublish":
				OSappschedulePlugin::plugin_state($option,$cid,0);
			break;
			case "plugin_publish":
				OSappschedulePlugin::plugin_state($option,$cid,1);
			break;	
			case "plugin_remove":
				OSappschedulePlugin::plugin_remove($cid);
			break;
			case "plugin_edit":
				OSappschedulePlugin::plugin_modify($option,$cid[0]);
			break;
			case "plugin_apply":
				OSappschedulePlugin::plugin_save($option,0);
			break;
			case "plugin_save":
				OSappschedulePlugin::plugin_save($option,1);
			break;
			case "goto_index":
				$mainframe = JFactory::getApplication();
				$mainframe->redirect("index.php");
			break;
			case "plugin_orderup":
				OSappschedulePlugin::plugin_order($option,$cid[0],-1);
			break;
			case "plugin_orderdown":
				OSappschedulePlugin::plugin_order($option,$cid[0],1);
			break;
			case "plugin_saveorder":
				OSappschedulePlugin::plugin_saveorder($option,$cid);
			break;
			case "plugin_gotolist":
				$mainframe = JFactory::getApplication();
				$mainframe->redirect("index.php?option=com_osservicesbooking&task=plugin_list");
			break;
			case "plugin_install":
				OSappschedulePlugin::install();
			break;
			case "plugin_saveorderAjax":
                OSappschedulePlugin::saveorderAjax($option);
            break;
		}
	}

	static function saveorderAjax($option)
	{
        global $jinput;
        $db				= JFactory::getDBO();
        $cid 			= $jinput->get( 'cid', array(), 'array' );
        $order			= $jinput->get( 'order', array(), 'array' );
        $row			= JTable::getInstance('Plugins','OsAppTable');
        $groupings		= array();
        // update ordering values
        $txt = "";
        for( $i=0; $i < count($cid); $i++ )
        {
            $row->load( $cid[$i] );
            // track parents
            if ($row->ordering != $order[$i])
            {
                $row->ordering = $order[$i];
                //$txt .= $cid[$i]." - ".$row->ordering."     ";
                $row->store();
				$row->reorder();
            } // if
        } // for
		for( $i=0; $i < count($cid); $i++ )
        {
			$row->load( $cid[$i] );
			$row->reorder();
		}
    }

	
	/**
	 * Install payment plugin
	 *
	 */
	static function install(){
		global $mainframe,$jinput;
		$db = & JFactory::getDBO();
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.archive');
		$db = JFactory::getDBO();
        if (version_compare(JVERSION, '3.4.0', 'ge'))
        {
            $plugin = $jinput->files->get('plugin_package', null, 'raw');
        }
        else
        {
            $plugin = $jinput->files->get('plugin_package', null, 'none');
        }
		if ($plugin['error'] || $plugin['size'] < 1)
		{
			$jinput->set('msg', JText::_('Upload plugin package error'));
			$mainframe->enqueueMessage($jinput->get('msg','','string'));
			$mainframe->redirect("index.php?option=com_osservicesbooking&task=plugin_list");
		}
		$config = new JConfig();
		$dest = $config->tmp_path . '/' . $plugin['name'];
		//$uploaded = JFile::upload($plugin['tmp_name'], $dest);
		if (version_compare(JVERSION, '3.4.4', 'ge'))
		{
			$uploaded = JFile::upload($plugin['tmp_name'], $dest , false, true);
		}else{
			$uploaded = JFile::upload($plugin['tmp_name'], $dest);
		}
		if (!$uploaded)
		{
            $jinput->set('msg', JText::_('Upload plugin package fail'));
			$mainframe->enqueueMessage($jinput->get('msg','','string'));
			$mainframe->redirect("index.php?option=com_osservicesbooking&task=plugin_list");
		}
		// Temporary folder to extract the archive into
		$tmpdir = uniqid('install_');
		$extractdir = JPath::clean(dirname($dest) . '/' . $tmpdir);
		//$result = JArchive::extract($dest, $extractdir);
		if (OSBHelper::isJoomla4())
        {
            $archive = new Joomla\Archive\Archive(array('tmp_path' => JFactory::getConfig()->get('tmp_path')));
            $result  = $archive->extract($dest, $extractdir);
        }
        else
        {
            $result = JArchive::extract($dest, $extractdir);
        }
		if (!$result)
		{
            $jinput->set('msg', JText::_('Extract plugin error'));
			$mainframe->enqueueMessage($jinput->get('msg','','string'));
			$mainframe->redirect("index.php?option=com_osservicesbooking&task=plugin_list");
		}
		$dirList = array_merge(JFolder::files($extractdir, ''), JFolder::folders($extractdir, ''));
		if (count($dirList) == 1)
		{
			if (JFolder::exists($extractdir . '/' . $dirList[0]))
			{
				$extractdir = JPath::clean($extractdir . '/' . $dirList[0]);
			}
		}
		//Now, search for xml file
		$xmlfiles = JFolder::files($extractdir, '.xml$', 1, true);
		if (empty($xmlfiles))
		{
            $jinput->set('msg', JText::_('Could not find XML file'));
			$mainframe->enqueueMessage($jinput->get('msg','','string'));
			$mainframe->redirect("index.php?option=com_osservicesbooking&task=plugin_list");
		}
		$file = $xmlfiles[0];
		$root = simplexml_load_file($file);
		$pluginType = $root->attributes()->type;
		$pluginGroup = $root->attributes()->group;
		if ($root->getName() !== 'install')
		{
            $jinput->set('msg', JText::_('Invalid XML file'));
			$mainframe->enqueueMessage($jinput->get('msg','','string'));
			$mainframe->redirect("index.php?option=com_osservicesbooking&task=plugin_list");
		}
		if ($pluginType != 'osbplugin')
		{
            $jinput->set('msg', JText::_('Invalid OSB payment plugin'));
			$mainframe->enqueueMessage($jinput->get('msg','','string'));
			$mainframe->redirect("index.php?option=com_osservicesbooking&task=plugin_list");
		}
		
		$name = (string) $root->name;
		$title = (string) $root->title;
		$author = (string) $root->author;
		$creationDate = (string) $root->creationDate;
		$copyright = (string) $root->copyright;
		$license = (string) $root->license;
		$authorEmail = (string) $root->authorEmail;
		$authorUrl = (string) $root->authorUrl;
		$version = (string) $root->version;
		$description = (string) $root->description;
		$row = & JTable::getInstance('Plugins', 'OsAppTable') ;		
		$sql = 'SELECT id FROM #__app_sch_plugins WHERE name="'.$name.'"';
		$db->setQuery($sql);
		$pluginId = (int) $db->loadResult();
		if ($pluginId)
		{
			$row->load($pluginId);
			$row->name = $name;
			$row->title = $title;
			$row->author = $author;
			$row->creation_date = $creationDate;
			$row->copyright = $copyright;
			$row->license = $license;
			$row->author_email = $authorEmail;
			$row->author_url = $authorUrl;
			$row->version = $version;
			$row->description = $description;
		}
		else
		{
			$row->name = $name;
			$row->title = $title;
			$row->author = $author;
			$row->creation_date = $creationDate;
			$row->copyright = $copyright;
			$row->license = $license;
			$row->author_email = $authorEmail;
			$row->author_url = $authorUrl;
			$row->version = $version;
			$row->description = $description;
			$row->published = 0;
			$row->ordering = $row->getNextOrder('published=1');
		}
		
		if (!$row->store()) 
		{
			$msg = JText::_("OS_ERROR_SAVING_ORDERING")." - ".$row->getError();
			$mainframe->enqueueMessage($msg);
			$mainframe->redirect('index.php?option=com_osservicesbooking&task=plugin_list');
		}
		$pluginDir = JPATH_ROOT . '/components/com_osservicesbooking/plugins';
		JFile::move($file, $pluginDir . '/' . basename($file));
		$files = $root->files->children();
		for ($i = 0, $n = count($files); $i < $n; $i++)
		{
			$file = $files[$i];
			if ($file->getName() == 'filename')
			{
				$fileName = $file;
				if (!JFile::exists($pluginDir . '/' . $fileName))
				{
					JFile::copy($extractdir . '/' . $fileName, $pluginDir . '/' . $fileName);
				}
			}
			elseif ($file->getName() == 'folder')
			{
				$folderName = $file;
				if (JFolder::exists($extractdir . '/' . $folderName))
				{
					JFolder::move($extractdir . '/' . $folderName, $pluginDir . '/' . $folderName);
				}
			}
		}
		
		JFolder::delete($extractdir);
				
		$msg = JText::_('OS_PAYMENT_PLUGIN_HAS_BEEN_INSTALLED_SUCCESSFULLY');
		$mainframe->enqueueMessage($msg);
		$mainframe->redirect("index.php?option=com_osservicesbooking&task=plugin_list");
	}
	
	/**
	 * Save plugin
	 *
	 * @param unknown_type $option
	 * @param unknown_type $save
	 * @return unknown
	 */
	static function plugin_save($option,$save){
		global $mainframe,$jinput;
		$id = $jinput->getInt('id',0);
		$row = & JTable::getInstance('Plugins', 'OsAppTable');
		$data = $jinput->post->getArray();
		if ($id > 0)
			$row->load($id);									
		if (!$row->bind($data)) {
			return false;
		}				
		//Save parameters
		$params   = $jinput->get('params',array(),'ARRAY');//JRequest::getVar( 'params', null, 'post', 'array' );
		if (is_array($params))
		{
			$txt = array ();
			foreach ($params as $k => $v) {
				if (is_array($v)) {
					$v = implode(',', $v);	
				}
				$v =  str_replace("\r\n", '@@', $v) ;				
				$txt[] = "$k=\"$v\"";
			}
			$row->params = implode("\n", $txt);
		}
		if (!$row->store()) {
			return false;
		}				
		$data['id'] = $row->id ;
		
		if($save == 1)
		{
			$mainframe->enqueueMessage(JText::_('OS_ITEM_HAS_BEEN_SAVED'));
			$mainframe->redirect("index.php?option=com_osservicesbooking&task=plugin_list");
		}
		else
		{
			$mainframe->enqueueMessage(JText::_('OS_ITEM_HAS_BEEN_SAVED'));
			$mainframe->redirect("index.php?option=com_osservicesbooking&task=plugin_edit&cid[]=$row->id");
		}
	}
	
	/**
	 * Remove payment plugins
	 *
	 * @param unknown_type $cid
	 */
	static function plugin_remove($cid)
	{
		global $mainframe;
		jimport('joomla.filesystem.folder') ;
		jimport('joomla.filesystem.file') ;
		$row = & JTable::getInstance('Plugins', 'OsAppTable');				
		$pluginDir = JPATH_ROOT.DS.'components'.DS.'com_osservicesbooking'.DS.'plugins' ;
		foreach ($cid as $id) 
		{
			$row->load($id);
			$name = $row->name ;			
			$file = $pluginDir.DS.$name.'.xml' ;
			$root = simplexml_load_file($file);
			$files = $root->files->children();
			//$pluginDir = JPATH_ROOT . '/components/com_osservicesbooking/plugins';
			for ($i = 0, $n = count($files); $i < $n; $i++)
			{
				$file = $files[$i];
				if ($file->getName() == 'filename')
				{
					$fileName = $file;
					if (JFile::exists($pluginDir . '/' . $fileName))
					{
						JFile::delete($pluginDir . '/' . $fileName);
					}
				}
				elseif ($file->getName() == 'folder')
				{
					$folderName = $file;
					if ($folderName)
					{
						if (JFolder::exists($pluginDir . '/' . $folderName))
						{
							JFolder::delete($pluginDir . '/' . $folderName);
						}
					}
				}
			}
			$files = (array)$root->languages->children();
			$languageFolder = JPATH_ROOT . '/language';
			for ($i = 0, $n = count($files); $i < $n; $i++)
			{
				$fileName = $files[$i];
				$pos = strpos($fileName, '.');
				$languageSubFolder = substr($fileName, 0, $pos);
				if (JFile::exists($languageFolder . '/' . $languageSubFolder . '/' . $fileName))
				{
					JFile::delete($languageFolder . '/' . $languageSubFolder . '/' . $fileName);
				}
			}
			JFile::delete($pluginDir . '/' . $name . '.xml');
			$row->delete();	
		}				
		$mainframe->enqueueMessage(JText::_("OS_ITEMS_HAS_BEEN_DELETED"),'message');
		OSappschedulePlugin::plugin_list($option);
	}
	
	/**
	 * change order price group
	 *
	 * @param unknown_type $option
	 * @param unknown_type $cid
	 * @param unknown_type $direction
	 */
	static function plugin_order($option,$id,$direction){
		global $mainframe;
		$mainframe = JFactory::getApplication();
		$row = &JTable::getInstance('Plugins','OsAppTable');
		$row->load($id);
		$row->move( $direction);
		$row->reorder();
		$mainframe->enqueueMessage(JText::_("OS_NEW_ORDERING_SAVED"),'message');
		OSappschedulePlugin::plugin_list($option);
	}
	
	/**
	 * save new order
	 *
	 * @param unknown_type $option
	 * @param unknown_type $cid
	 */
	static function plugin_saveorder($option,$cid){
		global $mainframe,$jinput;
		$mainframe = JFactory::getApplication();
		$msg = JText::_("OS_NEW_ORDERING_SAVED");
        $row = &JTable::getInstance('Plugins','OsAppTable');
		$order 	= $jinput->get('order',array(),'ARRAY');
        for( $i=0; $i < count($cid); $i++ )
        {
			$row->load( (int) $cid[$i] );
			if ($row->ordering != $order[$i]){
				$row->ordering = $order[$i];
				if (!$row->store()) {
					$msg = JText::_("OS_ERROR_SAVING_ORDERING");
                    break;
				}
			}
		}
		// execute updateOrder
		$row->reorder();
		$mainframe->enqueueMessage($msg,'message');
		OSappschedulePlugin::plugin_list($option);
	}
	
	/**
	 * List all plugins
	 *
	 * @param unknown_type $option
	 */
	static function plugin_list($option){
		global $mainframe,$jinput;
		$db = JFactory::getDbo();
		// filte sort
		$filter_order 				= $mainframe->getUserStateFromRequest($option.'.plugin.filter_order','filter_order','ordering','string');
		$filter_order_Dir 			= $mainframe->getUserStateFromRequest($option.'.plugin.filter_order_Dir','filter_order_Dir','asc','string');
		$lists['order'] 			= $filter_order;
		$lists['order_Dir'] 		= $filter_order_Dir;
		$order_by 					= " ORDER BY $filter_order $filter_order_Dir";
		$limitstart					= $jinput->getInt('limitstart',0);
		$limit						= $jinput->getInt('limit',20);
		$keyword					= $db->escape($jinput->get('keyword','','string'));
		$query = "Select count(id) from #__app_sch_plugins where 1=1";
		if($keyword != ""){
			$query .= " and name like '%$keyword%'";
		}
		$db->setQuery($query);
		$count						= $db->loadResult();
		jimport('joomla.html.pagination');
		$pageNav					= new JPagination($count,$limitstart,$limit);
		$query						= "Select * from #__app_sch_plugins where 1=1";
		if($keyword != ""){
			$query .= " and name like '%$keyword%'";
		}
		$query .= $order_by;
		$db->setQuery($query,$pageNav->limitstart,$pageNav->limit);
		//echo $db->getQuery();
		$rows = $db->loadObjectList();
		HTML_OSappschedulePlugin::listPlugins($option,$rows,$pageNav,$lists);
	}
	
	/**
	 * Plugin modification
	 *
	 * @param unknown_type $option
	 * @param unknown_type $id
	 */
	static function plugin_modify($option, $id){
		global $mainframe;
		$db = JFactory::getDbo();
		$db->setQuery("Select * from #__app_sch_plugins where id = '$id'");
		$item = $db->loadObject();

		$optionState[] = JHTML::_('select.option',1,JText::_('OS_PUBLISH'));
		$optionState[] = JHTML::_('select.option',0,JText::_('OS_UNPUBLISH'));
		$lists['published'] = JHtml::_('select.genericlist',$optionState,'published','class="inputbox form-select input-small"','value','text',$item->published);
		
		$registry = new JRegistry();
		$registry->loadString($item->params);
		$data = new stdClass();
		$data->params = $registry->toArray();
		$form = JForm::getInstance('osservicesbooking', JPATH_ROOT . '/components/com_osservicesbooking/plugins/' . $item->name . '.xml', array(), false, '//config');
		$form->bind($data);
		$lists['access'] = OSBHelper::accessDropdown('access',$item->access);
		HTML_OSappschedulePlugin::editPlugin($option,$item,$lists,$form);
	}
	
	/**
	 * Plugin change state
	 *
	 * @param unknown_type $option
	 * @param unknown_type $cid
	 * @param unknown_type $state
	 */
	static function plugin_state($option,$cid,$state){
		global $mainframe;
		$db 		= JFactory::getDBO();
		if(count($cid)>0)	{
			$cids 	= implode(",",$cid);
			$db->setQuery("UPDATE #__app_sch_plugins SET `published` = '$state' WHERE id IN ($cids)");
			$db->execute();
		}
		$mainframe->enqueueMessage(JText::_("OS_ITEMS_STATUS_HAS_BEEN_CHANGED"),'message');
		OSappschedulePlugin::plugin_list($option);
	}
}
?>