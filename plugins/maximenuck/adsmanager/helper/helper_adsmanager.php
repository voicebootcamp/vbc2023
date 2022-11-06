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
class MaximenuckHelpersourceAdsmanager {

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

		$usesuffix = $params->get('useadsmanagersuffix', '0');
		$imagesuffix = $params->get('adsmanagerimagesuffix', '_mini');
		$useimages = $params->get('useadsmanagerimages', '0');
		$categoryroot = $params->get('adsmanagercategoryroot', '0');
		$categorydepth = $params->get('adsmanagercategorydepth', '0');
		$shownumberproducts = (bool) $params->get('adsmanagershownumberproducts', '0');
		// $itemid = $params->get('adsmanageritemid', '');

		require_once(JPATH_SITE . '/components/com_adsmanager/lib/core.php');
		require_once (JPATH_ADMINISTRATOR . '/components/com_adsmanager/models/category.php');

		// get the model instance from the component
		$model = JModelLegacy::getInstance('Category', 'AdsmanagerModel');

		// get the active path
		$active_id = $input->get('catid', 0, 'int');
		$activepath = self::getActiveTree($active_id);

		// get the list of items
		if (($categoryroot && !$all)) {
			$tree = $model->getCatTree(true, $shownumberproducts);
			$model->parseTree($categoryroot, $tree, $items, 0);
		} else {
			$items = $model->getFlatTree(true, $shownumberproducts);
		}

		$lastitem = 0;
		foreach ($items as $i => &$item) {

			// check the tree depth
			if ($categorydepth AND ( ($item->level + 1) > $categorydepth)) {
				unset($items[$i]);
				continue;
			}

			$item->params = new JRegistry();
			$item->flink = TRoute::_("index.php?option=com_adsmanager&view=list&catid=" . $item->id);

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
			$item->classe = ' item' . $item->id;
			if (isset($active_id) && $active_id == $item->id) {
				$item->classe .= ' current';
			}
			if (in_array($item->id, $activepath)) {
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
			$item->ftitle = stripslashes(htmlspecialchars($item->name, ENT_COMPAT, 'UTF-8', false));
			$item->content = "";
			$item->rel = "";

			// add number of products to the title
			if ($shownumberproducts ) {
				$item->ftitle .= '<span class="maximenuck_nbproducts badge" data-number="' . $item->num_ads . '">' . $item->num_ads . '</span>';
			}

			// manage images
			if (!$usesuffix) {
				$imagesuffix = '';
			}
			$item->menu_image = '';
			if ($useimages) {
				$item->menu_image = self::getCatImageUrl($item->id, true, $imagesuffix);
			}

			// manage columns
			if ($item->colwidth) {
				$item->colonne = true;
				$parentItem = self::getParentItem($item->parent, $items);

				if (isset($parentItem->submenuswidth)) {
					$parentItem->submenuswidth = strval($parentItem->submenuswidth) + strval($item->colwidth);
				} else if ($parentItem) {
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
			$item->liclass = ''; 
			$item->colbgcolor = '';

		}

		// give the correct deep infos for the last item
		if (isset($items[$i])) {
			$items[$i]->level_diff = ($items[$i]->level - 1);
		}

		return $items;
	}

	/**
	* Get the parent category
	* 
	* @param	int		$id		The current category.
	* @param	array	$items	The list of categories object.
	*
	* @return	object
	*/
	static function getParentItem($id, $items) {
		foreach ($items as $item) {
			if ($item->id == $id)
				return $item;
		}
	}

	/**
	* Get the tree of the current category
	* 
	* @param	int	$catid	The current category.
	*
	* @return	array
	*/
	static function getActiveTree($catid, $mode='admin') {
		$model = JModelLegacy::getInstance('Category', 'AdsmanagerModel');
		$cats = $model->getCategories(true, $mode);
		$orderlist = array();
		$active_path = array();
		if(isset($cats))
		{
			foreach ($cats as $c ) {
				$orderlist[$c->id] = $c;
			}
		
			if (($catid != -1)&&($catid != 0))
			{
				$active_path[] = (string) $catid;
				$i=0;
				$i++;
				$current = $catid;

				while($orderlist[$current]->parent != 0)
				{
					$current = $orderlist[$current]->parent;
					$active_path[] = $orderlist[$current]->id;
					$i++;	
				}
			}
		}

		return $active_path;
	}

	/**
	* Get the image of the current category
	* 
	* @param	int	$catid	The current category.
	*
	* @return	mixed
	*/
	static function getCatImageUrl($catid, $thumb=false, $imagesuffix='') {
		$extensions = array("jpg","png","gif");
		$image_name = ($thumb == true) ? "cat_t":"cat";
		
		foreach($extensions as $ext) {
			if (file_exists(JPATH_ROOT."/images/com_adsmanager/categories/".$catid."$image_name.$ext"))
				return JURI::root(true)."/images/com_adsmanager/categories/".$catid."$image_name$imagesuffix.$ext";
		}
		return false;
	}
}
