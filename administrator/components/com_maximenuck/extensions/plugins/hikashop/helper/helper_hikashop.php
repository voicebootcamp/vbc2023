<?php
/**
 * @name		Maximenu CK
 * @copyright	Copyright (C) 2020. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Cedric Keiflin - https://www.template-creator.com - https://www.joomlack.fr
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Helper Class.
 */
class MaximenuckHelpersourceHikashop {

	private static $params;

	private static $categorydepth;

	private static $root;

	private static $level;

	/*
	 * Get the items from the source
	 */
	public static function getItems($params, $all = false, $level = 1, $parent_id = 0) {
		if (empty(self::$params)) {
			self:$params = $params;
		}

		// load the hikashop config class
		if(!include_once(JPATH_ADMINISTRATOR.'/components/com_hikashop/helpers/helper.php')){
			echo 'This module can not work without the Hikashop Component';
			return;
		}

		$app = JFactory::getApplication();
		$input = $app->input;
		self::$level = $level;
		self::$params = $params;
		$usehikashopsuffix = $params->get('usehikashopsuffix', '0');
		$hikashopimagesuffix = $params->get('hikashopimagesuffix', '_mini');
		$usehikashopimages = $params->get('usehikashopimages', '0');
		$categoryroot = $params->get('hikashopcategoryroot', '2');
		$categorydepth = self::$categorydepth = $params->get('hikashopcategorydepth', '0');
		$hikashopshowall = $params->get('hikashopshowall', '1');
		$hikashopitemid = $params->get('hikashopitemid', '');
		$active_category_id = ($input->get('ctrl', 'category') == 'category') ? $input->get('cid', '0', 'int') : self::getActiveCategory($input->get('cid', '0', 'int'));
		$categoryClass = hikashop_get('class.category');

		// replace the root category with the active category if we want to show only the cats from the active path
		if (! $hikashopshowall && $active_category_id != 0) {
			$categoryroot = $active_category_id;
		}

		$db = JFactory::getDBO();

		$query = "SELECT category_left, category_right, category_depth"
				. " FROM #__hikashop_category"
				. " WHERE category_id = " . (int) $categoryroot
				;
		$db->setQuery($query);
		if ($db->execute()) {
			$root = self::$root = $db->loadObject();
		} else {
			echo '<p style="color:red;font-weight:bold;">Error loading SQL data : loading the hikashop root category in Maximenu CK</p>';
			return false;
		}

		// get the list of categories
		$rows = array();
		$activeCategories = array();
		self::getCategoryParentRecurse($active_category_id, $activeCategories);
		self::recurseCategories($categoryroot, 0, $rows, $categorydepth, $active_category_id);

		$user	= JFactory::getUser();
		$groups	= implode(',', $user->getAuthorisedGroups());

		// reset the array index
		$items = array();
		$i = 0;
		foreach ($rows as $row) {
			$element = $categoryClass->get($row->category_id);
			if(!empty($element->category_id)) {
				$translationHelper = hikashop_get('helper.translation');
				$translationHelper->getTranslations($element);
				$row->category_name = $element->category_name;
				$row->category_description = $element->category_description;
			}

			// check the access level
			if (isset($rows[$row->parent]) && $rows[$row->parent]->category_access != 'all' && $row->category_access == 'all') $row->category_access = $rows[$row->parent]->category_access;
			if ($row->category_access != 'all') {
				if (!count(array_intersect(explode(',',$groups), explode(',',$row->category_access)))) {
					unset($rows[$row->category_parent_id]);
					continue;
				}
			}

			// check if there are some products
			if ($params->get('hikashopshowemptycats', '1') == '0') {
				$childProducts = self::getChidrenProducts($row->id);
				if ((int)$row->category_right - (int)$row->category_left == 1 && $childProducts == 0) {
					unset($rows[$row->category_id]);
					continue;
				}
			}

			// check if the parent item is published
			if ($row->category_parent_id != $categoryroot && ! isset($rows[$row->category_parent_id])) {;
				unset($rows[$row->category_id]);
				continue;
			}

			$items[$i] = $row;
			$i++;
			
		}

		$configClass = hikashop_get('class.config');
		$uploadfoler = $configClass->get('uploadfolder', 'media/com_hikashop/upload/');

		foreach ($items as $i => &$item) {
			$item->params = new JRegistry();
			$itemid = $hikashopitemid ? '&Itemid=' . $hikashopitemid : '';
			if(empty($element->category_alias)){
				$item->alias = $item->category_name;
			}else{
				$item->alias = $item->category_alias;
			}
			if(method_exists($app,'stringURLSafe')){
				$itemalias = $app->stringURLSafe(strip_tags($item->alias));
			}else{
				$itemalias = JFilterOutput::stringURLSafe(strip_tags($item->alias));
			}
			// $item->flink = $item->link = JRoute::_('index.php?option=com_hikashop&ctrl=category&task=listing&cid=' . $item->id . '&name=' . $itemalias . $itemid);
			$item->flink = $item->link = hikashop_contentLink('category&task=listing&cid='.$item->id.'&name='.$itemalias.$itemid,$item);

			$item->deeper = false;
			$item->shallower = false;
			$item->level_diff = 0;
			$item->isthirdparty = true;

			if (isset($items[$i - 1])) {
				$items[$i - 1]->deeper = ($item->level > $items[$i - 1]->level);
				$items[$i - 1]->shallower = ($item->level < $items[$i - 1]->level);
				$items[$i - 1]->level_diff = ($items[$i - 1]->level - $item->level);
				if ($items[$i - 1]->deeper AND $params->get('layout', 'default') != '_:flatlist')
					$items[$i - 1]->classe .= " parent";
			}

			// test if it is the last item
			$item->is_end = !isset($items[$i + 1]);

			// add some classes
			$item->classe = " item" . $item->id;
			if (in_array($item->id, $activeCategories)) {
				$item->classe .= " active";
			}
			if (isset($active_category_id) && $active_category_id == $item->id) {
				$item->classe .= " current";
			}

			// search for parameters
			$patterns = "#{maximenu}(.*){/maximenu}#Uis";
			$result = preg_match($patterns, stripslashes($item->category_description), $results);

			$item->desc = '';
			$item->colwidth = '';
			$item->tagcoltitle = 'none';
			$item->tagclass = '';
			$item->leftmargin = '';
			$item->topmargin = '';
			$item->submenuwidth = '';

			// old method - kept for backward compatibility
			if (isset($results[1])) {
				$hikashopparams = explode('|', $results[1]);
				for ($j = 0; $j < count($hikashopparams); $j++) {
					$item->desc = stristr($hikashopparams[$j], "desc=") ? str_replace('desc=', '', $hikashopparams[$j]) : $item->desc;
					$item->colwidth = stristr($hikashopparams[$j], "col=") ? str_replace('col=', '', $hikashopparams[$j]) : $item->colwidth;
					$item->tagcoltitle = stristr($hikashopparams[$j], "taghtml=") ? str_replace('taghtml=', '', $hikashopparams[$j]) : $item->tagcoltitle;
					$item->tagclass = stristr($hikashopparams[$j], "tagclass=") ? ' ' . str_replace('tagclass=', '', $hikashopparams[$j]) : $item->tagclass;
					$item->leftmargin = stristr($hikashopparams[$j], "leftmargin=") ? str_replace('leftmargin=', '', $hikashopparams[$j]) : $item->leftmargin;
					$item->topmargin = stristr($hikashopparams[$j], "topmargin=") ? str_replace('topmargin=', '', $hikashopparams[$j]) : $item->topmargin;
					$item->submenucontainerwidth = stristr($hikashopparams[$j], "submenuwidth=") ? str_replace('submenuwidth=', '', $hikashopparams[$j]) : $item->submenuwidth;
					$item->createnewrow = stristr($hikashopparams[$j], "newrow") ? 1 : 0;
				}
			}

			// new method to get the settings
			$item->ckparams = isset($item->ckparams) ? $item->ckparams : '';
			$item->maximenuckparams = new JRegistry($item->ckparams);
			// $item->maximenuckparams = new JRegistry($item->maximenuckparams->get('maximenu', ''));
			$item->desc = $item->maximenuckparams->get('maximenu_desc', '');
			$item->createcolumn = $item->maximenuckparams->get('maximenu_createcolumn', '');
			$item->colwidth = $item->maximenuckparams->get('maximenu_colwidth', '180');
			$item->tagcoltitle = $item->maximenuckparams->get('maximenu_tagcoltitle', 'none');
			$item->tagclass = $item->maximenuckparams->get('maximenu_tagclass', '');
			$item->leftmargin = $item->maximenuckparams->get('maximenu_leftmargin', '');
			$item->topmargin = $item->maximenuckparams->get('maximenu_topmargin', '');
			$item->submenucontainerwidth = $item->maximenuckparams->get('maximenu_submenucontainerwidth', '');
			$item->submenucontainerheight = $item->maximenuckparams->get('maximenu_submenucontainerheight', '');
			$item->createnewrow = $item->maximenuckparams->get('maximenu_createnewrow', '');
			$item->type = $item->maximenuckparams->get('maximenu_type', '');
			$item->params->set('maximenu_icon', $item->maximenuckparams->get('maximenu_icon', ''));
			$item->liclass = $item->maximenuckparams->get('maximenu_liclass', '');
			$item->params = $item->maximenuckparams;
			$item->classe .= $item->tagclass;
			// variables definition
			$item->ftitle = $item->title = stripslashes(htmlspecialchars($item->category_name));
			$item->type = 'thirdparty';
			$item->content = "";
			$item->rel = "";
			if ($item->level == $level) {
				$item->parent_id = $parent_id;
			}

			// manage the class to show the item on desktop and mobile
			if ($item->maximenuckparams->get('maximenu_disablemobile') == '1') {
				$item->classe .= ' nomobileck';
			}

			if ($item->maximenuckparams->get('maximenu_disabledesktop') == '1') {
				$item->classe .= ' nodesktopck';
			}

			// manage images
			if (!$usehikashopsuffix)
				$hikashopimagesuffix = '';
			$item->menu_image = '';
			if ($usehikashopimages) {
				$imageurl = $item->file_path ? explode(".", $item->file_path) : '';
				$imagename = isset($imageurl[0]) ? $imageurl[0] : '';
				$imageext = isset($imageurl[1]) ? $imageurl[1] : '';
				if (JFile::exists(JPATH_ROOT . '/' . trim($uploadfoler, '/') . '/' . $imagename . $hikashopimagesuffix . '.' . $imageext)) {
					$item->menu_image = JUri::root(true) . '/' . trim($uploadfoler, '/') . '/' . $imagename . $hikashopimagesuffix . '.' . $imageext;
				}
			}
 
			$parentItem = isset($rows[$item->category_parent_id]) ? $rows[$item->category_parent_id] : null;
            
			// manage columns
			// if (! $parent_id) {
				if ( (isset($item->createcolumn) && $item->createcolumn && $item->colwidth)
						|| (!isset($item->createcolumn) && $item->colwidth)
						) {
					$item->colonne = true;
					// $parentItem = self::getParentItem($item->parent, $items);

					if (isset($parentItem->submenuswidth)) {
						if (! stristr($item->colwidth, '%') && ! stristr($item->colwidth, 'auto')) $parentItem->submenuswidth = strval($parentItem->submenuswidth) + strval($item->colwidth);
					} else if (isset($parentItem) && $parentItem) {
						if (! stristr($item->colwidth, '%') && ! stristr($item->colwidth, 'auto')) $parentItem->submenuswidth = strval($item->colwidth);
					}
					if (isset($items[$i - 1]) AND $items[$i - 1]->deeper) {
						$items[$i - 1]->nextcolumnwidth = $item->colwidth;
					} 
					$item->columnwidth = $item->colwidth;
					
				}
				if (isset($parentItem->submenucontainerwidth) AND $parentItem->submenucontainerwidth)
					$parentItem->submenuswidth = $parentItem->submenucontainerwidth;
			// }


            $item->name = $item->ftitle;


            // needed for the layouts
            $item->anchor_css = '';
            $item->anchor_title = '';
            // $item->type = '';

            // get plugin parameters that are used directly in the layout
            $item->colbgcolor = '';
			
        }

        // give the correct deep infos for the last item
        if (isset($items[$i])) {
            $items[$i]->level_diff = ($items[$i]->level - 1 + ((int)$level-1));
        }

        return $items;
    }

	static function getChidrenItems($parent_id) {
		$db = JFactory::getDBO();
		// $query = "SELECT *,"
				// ." 1 as level"
				// ." FROM #__hikashop_categories"
				// ." WHERE published = 1"
				// ." AND parent = " . (int) $parent_id
				// ." ORDER BY ordering ASC";
		$ordering = self::$params->get('hikashoporderby', 'ordering');
		// var_dump(self::$params);
		switch ($ordering) {
			case 'name' :
				$orderby = " ORDER BY #__hikashop_category.category_name ASC";
				break;
			case 'order' :
			default :
				$orderby = " ORDER BY ordering ASC";
				break;
		}
		$query = "SELECT *,"
				. " #__hikashop_category.category_id as id,"
				. " #__hikashop_category.category_depth-".self::$root->category_depth."+".((int)self::$level-1)." as level,"
				. " #__hikashop_category.category_parent_id as parent,"
				. " #__hikashop_category.category_ordering as ordering"
				. " FROM #__hikashop_category"
				. " LEFT OUTER JOIN #__hikashop_file"
				. " ON #__hikashop_file.file_ref_id = #__hikashop_category.category_id"
				. " AND #__hikashop_file.file_type = 'category'"
				. " WHERE #__hikashop_category.category_type = 'product'"
				. " AND #__hikashop_category.category_parent_id = " . (int) $parent_id
				. " AND #__hikashop_category.category_published = 1"
				. " AND #__hikashop_category.category_depth > 1"
				. (self::$categorydepth ? " AND #__hikashop_category.category_depth <= " . ((int)self::$categorydepth + (int)self::$root->category_depth) : "")
				. " AND #__hikashop_category.category_left > " . self::$root->category_left
				. " AND #__hikashop_category.category_right <" . self::$root->category_right
				. $orderby;
		$db->setQuery($query);

		if ($db->execute()) {
			$rows = $db->loadObjectList('id');
			return $rows;
		} else {
			echo '<p style="color:red;font-weight:bold;">Error loading SQL data : loading the hikashop categories in Maximenu CK</p>';
			return false;
		}
	}

	static function getChidrenProducts($parent_id) {
		$db = JFactory::getDBO();

		$query = "SELECT count(#__hikashop_product.product_id)"
				. " FROM #__hikashop_product_category"
				. " LEFT JOIN #__hikashop_product"
				. " ON #__hikashop_product_category.product_id = #__hikashop_product.product_id"
				. " WHERE #__hikashop_product_category.category_id = " . (int) $parent_id
				. " AND #__hikashop_product.product_published = '1'"
				;
		$db->setQuery($query);

		if ($db->execute()) {
			$rows = $db->loadResult();
			return $rows;
		} else {
			echo '<p style="color:red;font-weight:bold;">Error loading SQL data : loading the hikashop categories in Maximenu CK</p>';
			return false;
		}
	}

	static function recurseCategories($category_id, $level, &$sortedCats, $depth, $active_category_id) {
		$level++;
		// if (self::hasChildren($category_id)) {
			$childCats = self::getChidrenItems($category_id);
			if(!empty($childCats)){
				foreach ($childCats as $childCat) {
					// $childCat->level = $level;
					$sortedCats[$childCat->id] = $childCat;
					if ( ($depth > 0 && $level < $depth) || $depth == 0) {
						self::recurseCategories($childCat->id, $level, $sortedCats, $depth, $active_category_id);
					}
				}
			}
		// }
	}

	static function getCategoryParentRecurse($category_id, &$activeCategories) {
			$activeCategories[] = $category_id;
			$db = JFactory::getDBO();
			$query = "SELECT #__hikashop_category.category_parent_id as parent"
					." FROM #__hikashop_category"
					." WHERE #__hikashop_category.category_published = 1"
					." AND #__hikashop_category.category_id = " . (int) $category_id;
			
			$db->setQuery($query);

			if ($db->execute()) {
				$parent_category_id = (int)$db->loadResult();
			} else {
				$parent_category_id = null;
			}

			if($parent_category_id){
				self::getCategoryParentRecurse($parent_category_id, $activeCategories);
			}
	}

    static function getParentItem($id, $items) {
        foreach ($items as $item) {
            if ($item->id == $id)
                return $item;
        }
		return false;
    }
    
    static function getActiveCategory($productid) {

        $query = "SELECT category_id"
                . " FROM #__hikashop_product_category"
                . " WHERE product_id = " . $productid . ";";
        $db = JFactory::getDBO();
        $db->setQuery($query);

        if ($db->execute()) {
            $categoryid = $db->loadResult();
        } else {
            echo '<p style="color:red;font-weight:bold;">Error loading SQL data : loading the active hikashop category in Maximenu CK</p>';
            return false;
        }
        
        return $categoryid;
    }
}
