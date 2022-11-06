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
class MaximenuckHelpersourceVirtuemart {

	private static $params;

	static function getChidrenItems($parent_id) {
		$db = JFactory::getDBO();
		$query_children = "SELECT *, #__virtuemart_categories.virtuemart_category_id as id, #__virtuemart_category_categories.category_parent_id as parent, #__virtuemart_categories.ordering as ordering, 2 as level"
				." FROM (((#__virtuemart_categories"
				." INNER JOIN #__virtuemart_category_categories"
				." ON #__virtuemart_categories.virtuemart_category_id = #__virtuemart_category_categories.category_child_id)"
				." INNER JOIN #__virtuemart_categories_".VMLANG
				." ON #__virtuemart_categories.virtuemart_category_id = #__virtuemart_categories_".VMLANG.".virtuemart_category_id)"
				." LEFT OUTER JOIN #__virtuemart_category_medias"
				." ON #__virtuemart_categories.virtuemart_category_id = #__virtuemart_category_medias.virtuemart_category_id)"
				." LEFT OUTER JOIN #__virtuemart_medias"
				." ON #__virtuemart_category_medias.virtuemart_media_id = #__virtuemart_medias.virtuemart_media_id"
				." WHERE #__virtuemart_category_categories.category_parent_id = " . (int) $parent_id . " AND #__virtuemart_categories.published = 1";
		if (self::$params->get('virtuemartsorting', 'default') == 'default') {
				$query_children .= " ORDER BY #__virtuemart_categories.ordering ASC, #__virtuemart_categories.virtuemart_category_id ASC";
		} else {
				$query_children .= " ORDER BY #__virtuemart_categories_".VMLANG.".category_name ASC, #__virtuemart_categories.virtuemart_category_id ASC";
		}

		$db->setQuery($query_children);

		if ($db->execute()) {
			$rows_children = $db->loadObjectList('id');
			return $rows_children;
		} else {
			echo '<p style="color:red;font-weight:bold;">Error loading SQL data : loading the Virtuemart categories in Maximenu CK</p>';
			return false;
		}
	}

	static function recurseCategories($category_id, $level, &$sortedCats, $depth) {
		$level++;
		if (self::hasChildren($category_id)) {
			$childCats = self::getChidrenItems($category_id);
			if(!empty($childCats)){
				foreach ($childCats as $childCat) {
					$childCat->level = $level;
					$sortedCats[] = $childCat;
					if ( ($depth > 0 && $childCat->level < $depth) || $depth == 0) {
						self::recurseCategories($childCat->id,$level, $sortedCats, $depth);
					}
				}
			}
		}
	}
	
	/**
	* Checks for children of the category $virtuemart_category_id
	*
	* @param int $virtuemart_category_id the category ID to check
	* @return boolean true when the category has childs, false when not
	*/
	static function hasChildren($virtuemart_category_id) {

		$db = JFactory::getDBO();
		$q = "SELECT `category_child_id`
		FROM `#__virtuemart_category_categories`
		WHERE `category_parent_id` = ".(int)$virtuemart_category_id;
		$db->setQuery($q);
		$db->execute();
		if ($db->getAffectedRows() > 0){
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Get a list of the menu items.
	 *
	 * @param	JRegistry	$params	The module options.
	 *
	 * @return	array
	 */
    static function getItems(&$params, $all = false, $level = 1, $parent_id = 0) {
		self::$params = $params;

		if (! defined('DS') ) {
			define('DS', '/');
		}
		jimport('joomla.application.module.helper');
		if (!class_exists( 'VmConfig' )) require_once(JPATH_ADMINISTRATOR.'/components/com_virtuemart/helpers/config.php');
		$config= VmConfig::loadConfig();

		if(!class_exists('VmModel'))require(JPATH_ADMINISTRATOR.'/components/com_virtuemart/helpers/vmmodel.php');
		// for joomla 2.5
		/*
		$usevmsuffix = $params->get('usevmsuffix', '0');
		$vmimagesuffix = $params->get('vmimagesuffix', '_mini');
		$usevmimages = $params->get('usevmimages', '0');
		$vmcategoryroot = $params->get('vmcategoryroot', '0');
		$vmcategorydepth = $params->get('vmcategorydepth', '0');
		*/

		// for joomla 3
		$usevmsuffix = $params->get('usevirtuemartsuffix', '0');
		$vmimagesuffix = $params->get('virtuemartimagesuffix', '_mini');
		$usevmimages = $params->get('usevirtuemartimages', '0');
		$vmcategoryroot = $params->get('virtuemartcategoryroot', '0');
		$vmcategorydepth = $params->get('virtuemartcategorydepth', '0');

		// $active_path = array();

        // $db = JFactory::getDBO();
		$active_category_id = JRequest::getInt('virtuemart_category_id', '0');

		// get the active tree
		$categoryModel = VmModel::getModel('Category');
		$parentCategories = $categoryModel->getCategoryRecurse($active_category_id,0);

        // $level = 0;
        $items = array();
        $i = 0;
		$vmcategoryrootitem = new stdClass();
		$vmcategoryrootitem->level = 0;
		$vmcategoryrootitem->enfants = '';

		// get the list of categories
		self::recurseCategories($vmcategoryroot, 0, $items, $vmcategorydepth);

		$j = 0;
        $lastitem = 0;

		foreach ($items as $i => &$item) {

			$newItem = self::initItem();
			foreach ($newItem as $prop => $val) {
				if (! isset($item->$prop)) $item->$prop = $val;
			}

			$item->flink = $item->link = JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_category_id=' . $item->id);

			$item->level = $item->level + $level - 1;
			if ($item->level == $level) {
				$item->parent_id = $parent_id;
			}

			if (isset($items[$i-1])) {
				$items[$i-1]->deeper = ($item->level > $items[$i-1]->level);
				$items[$i-1]->shallower = ($item->level < $items[$i-1]->level);
				$items[$i-1]->level_diff = ($items[$i-1]->level - $item->level);
				if ($items[$i-1]->deeper AND $params->get('layout', 'default') != '_:flatlist') $items[$i-1]->classe .= " parent";
			}

			// if ($item->deeper) $item->classe .= " parent";

            

            // test if it is the last item
			$item->is_end = !isset($items[$i + 1]);

            // add some classes
            $item->classe .= " item" . $item->id;
			if (in_array($item->id, $parentCategories)) {
				$item->classe .= " active";
			}
            if ($active_category_id && $active_category_id == $item->id) {
                $item->classe .= " current";
            }


            // search for parameters
            $patterns = "#{maximenu}(.*){/maximenu}#Uis";
            $result = preg_match($patterns, stripslashes($item->category_description), $results);

			$imageonly = '';

            if (isset($results[1])) {
                $vmparams = explode('|', $results[1]);
                // $parmsnumb = count($vmparams);
                for ($j = 0; $j < count($vmparams); $j++) {
                    $item->desc = stristr($vmparams[$j], "desc=") ? str_replace('desc=', '', $vmparams[$j]) : $item->desc;
                    $item->colwidth = stristr($vmparams[$j], "col=") ? str_replace('col=', '', $vmparams[$j]) : $item->colwidth;
                    $item->tagcoltitle = stristr($vmparams[$j], "taghtml=") ? str_replace('taghtml=', '', $vmparams[$j]) : $item->tagcoltitle;
					$item->tagclass = stristr($vmparams[$j], "tagclass=") ? ' '.str_replace('tagclass=', '', $vmparams[$j]) : $item->tagclass;
                    $item->leftmargin = stristr($vmparams[$j], "leftmargin=") ? str_replace('leftmargin=', '', $vmparams[$j]) : $item->leftmargin;
                    $item->topmargin = stristr($vmparams[$j], "topmargin=") ? str_replace('topmargin=', '', $vmparams[$j]) : $item->topmargin;
					$item->submenucontainerwidth = stristr($vmparams[$j], "submenuwidth=") ? str_replace('submenuwidth=', '', $vmparams[$j]) : $item->submenuwidth;
					$item->createnewrow = stristr($vmparams[$j], "newrow") ? 1 : 0;
					$item->type = stristr($vmparams[$j], "separator") ? 'separator' : $item->type;
					$imageonly = stristr($vmparams[$j], "notext") ? 1 : $imageonly;
                }
            }

			if ($imageonly) {
				$item->params->set('menu_text', 0);
			}

			$item->classe .= $item->tagclass;
			// manage tag encapsulation
			// $item->tagcoltitle = $item->params->set('maximenu_tagcoltitle', $item->taghtml);


            // variables definition
            $item->ftitle = stripslashes(htmlspecialchars($item->category_name));
            $item->content = "";
            $item->rel = "";

			
			
            // manage images
            if (!$usevmsuffix) $vmimagesuffix = '';
            $item->menu_image = '';
            if ($usevmimages) {
				$imageurl = $item->file_url ? explode(".",$item->file_url): '';
				$imagelocation = isset($imageurl[0]) ? $imageurl[0] : '';
				$imageext = isset($imageurl[1]) ? $imageurl[1] : '';
                if (JFile::exists(JPATH_ROOT . '/'. $imagelocation . $vmimagesuffix . '.' . $imageext)) {
					$item->menu_image = $imagelocation . $vmimagesuffix . '.' . $imageext;					
				}
            }
			

			// manage columns
            if ($item->colwidth) {
				$item->colonne = true;
                $parentItem = self::getParentItem($item->parent, $items);

                if (isset($parentItem->submenuswidth)) {
                    $parentItem->submenuswidth = strval($parentItem->submenuswidth) + strval($item->colwidth);
                } else {
                    if (is_object($parentItem)) $parentItem->submenuswidth = strval($item->colwidth);
                }
                if (isset($items[$i-1]) AND $items[$i-1]->deeper) {
					$items[$i-1]->nextcolumnwidth = $item->colwidth;
				}
				$item->columnwidth = $item->colwidth;
            }
			if (isset($parentItem->submenucontainerwidth) AND $parentItem->submenucontainerwidth) $parentItem->submenuswidth = $parentItem->submenucontainerwidth;


            $item->name = $item->ftitle;
			
			// get plugin parameters that are used directly in the layout
			// $item->leftmargin = $item->params->get('maximenu_leftmargin', '');
			// $item->topmargin = $item->params->get('maximenu_topmargin', '');
			$item->liclass = $item->params->get('maximenu_liclass', '');
			$item->colbgcolor = $item->params->get('maximenu_colbgcolor', '');
			
			
				

			
			// $lastitem = $i;
        }

		// give the correct deep infos for the last item
		if (isset($items[$i])) {
			// $items[$i]->deeper		= (($start?$start:1) > $items[$i]->level);
			// $items[$i]->shallower	= (($start?$start:1) < $items[$i]->level);
			$items[$i]->level_diff	= ($items[$i]->level - 1 - $vmcategoryrootitem->level);
		}

        return $items;
    }

    static function getParentItem($id, $items) {
        foreach ($items as $item) {
            if ($item->id == $id)
                return $item;
        }
    }

	public static function initItem() {
		$item = new stdClass();
		$item->params = new JRegistry();
		$item->deeper = false;
		$item->shallower = false;
		$item->level_diff = 0;
		$item->isthirdparty = false;
		$item->is_end = false;
		$item->classe = '';
		$item->desc = '';
		$item->colwidth = '';
		$item->tagcoltitle = 'none';
		$item->tagclass = '';
		$item->leftmargin = '';
		$item->topmargin = '';
		$item->submenuwidth = '';
		$item->liclass = '';
		$item->anchor_css = '';
		$item->anchor_title = '';
		$item->colbgcolor = '';
		$item->menu_image = '';
		$item->type = '';
		$item->content = '';
		$item->rel = '';
		$item->link = '';
		$item->title = '';
		$item->parent_id = '';
		$item->id = '';

		// special for the thirdparty plugins
		$item->isthirdparty = true;
		$item->type = 'thirdparty';

		return $item;
	}
}
