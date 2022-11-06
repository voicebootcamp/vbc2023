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
class MaximenuckHelpersourceJoomshopping {

	static $_activeitem;

    /**
     * Get a list of the menu items.
     *
     * @param	JRegistry	$params	The module options.
     *
     * @return	array
     */
    static function getItems(&$params, $all) {
        jimport('joomla.application.module.helper');
		$input = new JInput();

        $usesuffix = $params->get('usejoomshoppingsuffix', '0');
        $imagesuffix = $params->get('joomshoppingimagesuffix', '_mini');
        $useimages = $params->get('usejoomshoppingimages', '0');
        $categoryroot = $params->get('joomshoppingcategoryroot', '0');
        $categorydepth = $params->get('joomshoppingcategorydepth', '0');
        $itemid = $params->get('joomshoppingitemid', '');

		require_once (JPATH_SITE.'/components/com_jshopping/lib/factory.php'); 
		require_once (JPATH_SITE.'/components/com_jshopping/lib/jtableauto.php');
		require_once (JPATH_SITE.'/components/com_jshopping/tables/config.php'); 
		require_once (JPATH_SITE.'/components/com_jshopping/lib/functions.php');
		require_once (JPATH_SITE.'/components/com_jshopping/lib/multilangfield.php');
		require_once (JPATH_ADMINISTRATOR.'/components/com_jshopping/models/categories.php'); 
		JTable::addIncludePath(JPATH_SITE.'/components/com_jshopping/tables'); 
		
		// get the active path		
		$category_id = $input->get('category_id', 0, 'int');
		$category = JTable::getInstance('category', 'jshop');        
		$category->load($category_id);
		$activepath = $category->getTreeParentCategories();

		$model = JModelLegacy::getInstance('Categories', 'JshoppingModel');
		if ( ($categoryroot && !$all) || $categorydepth) {
			$items = self::getTreeSubCategories($categoryroot, 0, $categorydepth);
		} else {
			$items = $model->getTreeAllCategories();
		}
        
        $active_category_id = $input->get('category_id', '0', 'int');
		if ($active_category_id) self::$_activeitem = $items[$active_category_id];

        $lastitem = 0;
        foreach ($items as $i => &$item) {

            $item->params = new JRegistry();
            $itemid = $itemid ? '&Itemid=' . $itemid : '';
            $item->flink = JRoute::_('index.php?option=com_jshopping&controller=category&task=view&category_id=' . $item->category_id . $itemid);

            $item->deeper = false;
            $item->shallower = false;
            $item->level_diff = 0;
            $item->level = $item->level + 1;

            if (isset($items[$i - 1])) {
                $items[$i - 1]->deeper = ($item->level > $items[$i - 1]->level);
                $items[$i - 1]->shallower = ($item->level < $items[$i - 1]->level);
                $items[$i - 1]->level_diff = ($items[$i - 1]->level - $item->level);
                if ($items[$i - 1]->deeper AND $params->get('layout', 'default') != '_:flatlist')
                    $items[$i - 1]->classe .= " parent";
            }

            // test if it is the last item
            $item->is_end = !isset($items[$i + 1]);

			// manage item class
            $item->classe = ' item'.$item->category_id;
            if (isset($active_category_id) && $active_category_id == $item->category_id) {
                $item->classe .= ' current';
            }
            if (in_array($item->category_id, $activepath)) {
                $item->classe .= ' active';  
                $item->isactive = true;
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
                $cat_params = explode('|', $results[1]);
                for ($j = 0; $j < count($cat_params); $j++) {
                    $item->desc = stristr($cat_params[$j], "desc=") ? str_replace('desc=', '', $cat_params[$j]) : $item->desc;
                    $item->colwidth = stristr($cat_params[$j], "col=") ? str_replace('col=', '', $cat_params[$j]) : $item->colwidth;
                    $item->tagcoltitle = stristr($cat_params[$j], "taghtml=") ? str_replace('taghtml=', '', $cat_params[$j]) : $item->tagcoltitle;
                    $item->tagclass = stristr($cat_params[$j], "tagclass=") ? ' ' . str_replace('tagclass=', '', $cat_params[$j]) : $item->tagclass;
                    $item->leftmargin = stristr($cat_params[$j], "leftmargin=") ? str_replace('leftmargin=', '', $cat_params[$j]) : $item->leftmargin;
                    $item->topmargin = stristr($cat_params[$j], "topmargin=") ? str_replace('topmargin=', '', $cat_params[$j]) : $item->topmargin;
                    $item->submenucontainerwidth = stristr($cat_params[$j], "submenuwidth=") ? str_replace('submenuwidth=', '', $cat_params[$j]) : $item->submenuwidth;
                }
            }

            $item->classe .= $item->tagclass;
            // variables definition
            $item->ftitle = stripslashes(htmlspecialchars($item->name));
            $item->content = "";
            $item->rel = "";

            // manage images
            if (!$usesuffix)
                $imagesuffix = '';
            $item->menu_image = '';
            if ($useimages) {
                $imageurl = explode('.', $item->category_image);
                $imagename = isset($imageurl[0]) ? $imageurl[0] : '';
                $imageext = isset($imageurl[1]) ? $imageurl[1] : '';
                if (JFile::exists(JPATH_ROOT . '/components/com_jshopping/files/img_categories/' . $imagename . $imagesuffix . '.' . $imageext)) {
                    $item->menu_image = 'components/com_jshopping/files/img_categories/' . $imagename . $imagesuffix . '.' . $imageext;
                }
            }
            
            // manage columns
            if ($item->colwidth) {
                $item->colonne = true;
                $parentItem = self::getParentItem($item->parent, $items);

                if (isset($parentItem->submenuswidth)) {
                    $parentItem->submenuswidth = strval($parentItem->submenuswidth) + strval($item->colwidth);
                } else if( $parentItem ) {
                    $parentItem->submenuswidth = strval($item->colwidth);
                }
                if (isset($items[$i - 1]) AND $items[$i - 1]->deeper) {
                    $items[$i - 1]->columnwidth = $item->colwidth;
                } else {
                    $item->columnwidth = $item->colwidth;
                }
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
            $items[$i]->level_diff = ($items[$i]->level - 1);
        }

        return $items;
    }

    static function getParentItem($id, $items) {
        foreach ($items as $item) {
            if ($item->category_id == $id)
                return $item;
        }
    }
	
	static public function getTreeSubCategories($categoryroot, $level, $categorydepth) {
		$category = JTable::getInstance('category', 'jshop');        
		$category->load($category_id);
		$model = JModelLegacy::getInstance('Categories', 'JshoppingModel');

		$subcatsCount = $model->getAllCatCountSubCat();
		$cats = $category->getSubCategories($categoryroot, 'ordering');
		$items = Array();

		foreach ($cats as $cat) {
			$cat->level = $level;
			$items[] = $cat;
			if ($subcatsCount[$cat->category_id] && ($categorydepth == 0 || $categorydepth > ($level+1)) ) {
				$subcats = self::getTreeSubCategories($cat->category_id, $cat->level + 1, $categorydepth);
				foreach ($subcats as $subcat) {
					$items[] = $subcat;
				}
			}
		}
		return $items;
	}
}
