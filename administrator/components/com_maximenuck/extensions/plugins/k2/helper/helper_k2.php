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
class MaximenuckHelpersourceK2 {

	private static $params;

	/*
	 * Get the items from the source
	 */
	public static function getItems($params) {
		if (empty(self::$params)) {
			self:$params = $params;
		}

		$app = JFactory::getApplication();
		$input = $app->input;

		$usek2suffix = $params->get('usek2suffix', '0');
		$k2imagesuffix = $params->get('k2imagesuffix', '_mini');
		$usek2images = $params->get('usek2images', '0');
		$categoryroot = $params->get('k2categoryroot', '0');
		$categorydepth = $params->get('k2categorydepth', '0');
		// $start = $params->get('startLevel', '1');
        // $end = $params->get('endLevel', '10');
        // $dependantitems = $params->get('dependantitems', '0');
		$k2showall = $params->get('k2showall', '1');
		$active_path = array();

		// get the list of categories
		$items = array();
		$activeCategories = array();
		$active_category_id = $input->get('id', '0', 'int');
		self::getCategoryParentRecurse($active_category_id, $activeCategories);
		self::recurseCategories($categoryroot, 0, $items, $categorydepth, $active_category_id);

		require_once JPATH_SITE . '/components/com_k2/helpers/route.php';
		foreach ($items as $i => &$item) {

			$item->params = new JRegistry();
			// $item->flink = JRoute::_('index.php?option=com_k2&view=itemlist&layout=category&task=category&id=' . $item->id );
			$item->flink = JRoute::_(' index.php?option=com_k2&test=3&view=itemlist&layout=category&task=category&id=' . $item->id );
			$item->deeper = false;
			$item->shallower = false;
			$item->level_diff = 0;
			// $item->level =  $item->level - $categoryrootitem->level;

			if (isset($items[$i-1])) {
				$items[$i-1]->deeper = ($item->level > $items[$i-1]->level);
				$items[$i-1]->shallower = ($item->level < $items[$i-1]->level);
				$items[$i-1]->level_diff = ($items[$i-1]->level - $item->level);
				if ($items[$i-1]->deeper AND $params->get('layout', 'default') != '_:flatlist') 
					$items[$i-1]->classe .= " parent";
			}

			// test if it is the last item
			$item->is_end = !isset($items[$i + 1]);

			// add some classes
			$item->classe = " item" . $item->id;
			if (in_array($item->id, $activeCategories)) {
				$item->classe .= " active";
			}
			if ($active_category_id && $active_category_id == $item->id) {
				$item->classe .= " current";
			}

			// search for parameters
			$patterns = "#{maximenu}(.*){/maximenu}#Uis";
			$result = preg_match($patterns, stripslashes($item->description), $results);

			$item->desc = '';
			$item->colwidth = '';
			$item->tagcoltitle = 'none';
			$item->tagclass = '';
			$item->leftmargin = '';
			$item->topmargin = '';
			$item->submenuwidth = '';

			if (isset($results[1])) {
				$k2params = explode('|', $results[1]);
				// $parmsnumb = count($k2params);
				for ($j = 0; $j < count($k2params); $j++) {
					$item->desc = stristr($k2params[$j], "desc=") ? str_replace('desc=', '', $k2params[$j]) : $item->desc;
					$item->colwidth = stristr($k2params[$j], "col=") ? str_replace('col=', '', $k2params[$j]) : $item->colwidth;
					$item->tagcoltitle = stristr($k2params[$j], "taghtml=") ? str_replace('taghtml=', '', $k2params[$j]) : $item->tagcoltitle;
					$item->tagclass = stristr($k2params[$j], "tagclass=") ? ' '.str_replace('tagclass=', '', $k2params[$j]) : $item->tagclass;
					$item->leftmargin = stristr($k2params[$j], "leftmargin=") ? str_replace('leftmargin=', '', $k2params[$j]) : $item->leftmargin;
					$item->topmargin = stristr($k2params[$j], "topmargin=") ? str_replace('topmargin=', '', $k2params[$j]) : $item->topmargin;
					$item->submenucontainerwidth = stristr($k2params[$j], "submenuwidth=") ? str_replace('submenuwidth=', '', $k2params[$j]) : $item->submenuwidth;
					$item->createnewrow = stristr($k2params[$j], "newrow") ? 1 : 0;
				}
			}

			$item->classe .= $item->tagclass;
			// variables definition
			$item->ftitle = stripslashes(htmlspecialchars($item->name));
			$item->content = "";
			$item->rel = "";

			// manage images
			if (!$usek2suffix) $k2imagesuffix = '';
			$item->menu_image = '';
			if ($usek2images) {
				$imageurl = $item->image ? explode(".",$item->image): '';
				$imagename = isset($imageurl[0]) ? $imageurl[0] : '';
				$imageext = isset($imageurl[1]) ? $imageurl[1] : '';
                if (JFile::exists(JPATH_ROOT . '/media/k2/categories/' . $imagename . $k2imagesuffix . '.' . $imageext)) {
					$item->menu_image = 'media/k2/categories/' . $imagename . $k2imagesuffix . '.' . $imageext;
				}
            }
			

			// manage columns
			if ($item->colwidth) {
				$item->colonne = true;
				$parentItem = self::getParentItem($item->parent, $items);

				if (isset($parentItem->submenuswidth)) {
					$parentItem->submenuswidth = strval($parentItem->submenuswidth) + strval($item->colwidth);
				} else {
					$parentItem->submenuswidth = strval($item->colwidth);
				}
				if (isset($items[$i-1]) AND $items[$i-1]->deeper) {
					$items[$i-1]->nextcolumnwidth = $item->colwidth;
				}
				$item->columnwidth = $item->colwidth;
			}
			if (isset($parentItem->submenucontainerwidth) AND $parentItem->submenucontainerwidth) 
				$parentItem->submenuswidth = $parentItem->submenucontainerwidth;

			$item->name = $item->ftitle;

			// pour compat avec default.php
			$item->anchor_css = '';
			$item->anchor_title = '';
			$item->type = '';
			
			// get plugin parameters that are used directly in the layout
			$item->liclass = $item->params->get('maximenu_liclass', '');
			$item->colbgcolor = $item->params->get('maximenu_colbgcolor', '');
		}

		// give the correct deep infos for the last item
		if (isset($items[$i])) {
			$items[$i]->level_diff	= ($items[$i]->level - 1);
		}

		return $items;
	}

	function getParentItem($id, $items) {
		foreach ($items as $item) {
			if ($item->id == $id)
				return $item;
		}
	}

	static function getChidrenItems($parent_id) {
		$db = JFactory::getDBO();
		$query = "SELECT *,"
				." 1 as level"
				." FROM #__k2_categories"
				." WHERE published = 1"
				." AND parent = " . (int) $parent_id
				." ORDER BY ordering ASC";

		$db->setQuery($query);

		if ($db->execute()) {
			$rows = $db->loadObjectList('id');
			return $rows;
		} else {
			echo '<p style="color:red;font-weight:bold;">Error loading SQL data : loading the k2 categories in Maximenu CK</p>';
			return false;
		}
	}

	static function recurseCategories($category_id, $level, &$sortedCats, $depth, $active_category_id) {
		$level++;
		// if (self::hasChildren($category_id)) {
			$childCats = self::getChidrenItems($category_id);
			if(!empty($childCats)){
				foreach ($childCats as $childCat) {
					$childCat->level = $level;
					$sortedCats[] = $childCat;
					if ( ($depth > 0 && $childCat->level < $depth) || $depth == 0) {
						self::recurseCategories($childCat->id,$level, $sortedCats, $depth, $active_category_id);
					}
				}
			}
		// }
	}

	static function getCategoryParentRecurse($category_id, &$activeCategories) {
			$activeCategories[] = $category_id;
			$db = JFactory::getDBO();
			$query = "SELECT parent"
					." FROM #__k2_categories"
					." WHERE published = 1"
					." AND id = " . (int) $category_id;
			
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
}
