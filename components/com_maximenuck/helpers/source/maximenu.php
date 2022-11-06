<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

use Maximenuck\Helper;
use Maximenuck\Helperfront;
use Maximenuck\CKFof;

class MaximenuckHelpersourceMaximenu {

	public static function parseItem($item, &$items) {
		if (is_array($item)) $item = CKFof::convertArrayToObject ($item);
			$item->pathTree = isset($item->pathTree) ? $item->pathTree : array($item->id);

			// create JRegistry settings from the maximenu interface
			$item->settings = Helper::decodeChars($item->settings);
			$item->settings = new JRegistry($item->settings);
			if ($item->settings->get('thirdparty', '0') === '1') {
				$item->params = new JRegistry();
				$itemtypeObj = new stdClass();
				$itemtypeObj->params = new JRegistry();
				$itemtypeObj->link = '';
				$itemtypeObj->content = self::getThirdparty($item);
			} else {
				switch ($item->type) {
					case 'menuitem' :
						$itemtypeObj = self::getMenuItem($item->id);
						break;
					case 'module' :
						$itemtypeObj = new stdClass();
						$itemtypeObj->params = new JRegistry();
						$itemtypeObj->link = '';
						$item->title = '';
						$itemtypeObj->content = self::getModule($item->id);
						break;
					case 'image' :
						$itemtypeObj = new stdClass();
						$itemtypeObj->params = new JRegistry();
						$itemtypeObj->link = '';
						$item->title = '';
						$itemtypeObj->content = self::getImage($item);
						break;
					default:
						$itemtypeObj = new stdClass();
						$itemtypeObj->params = new JRegistry();
						$itemtypeObj->link = '';
						break;
				}
			}
			
//CKFof::dump($itemtypeObj);
//			$item->link
//			$item = (object) array_merge( 
//				(array) $item, (array) $itemtypeObj);
			foreach ($itemtypeObj as $key => $val) {
				if (! isset($item->$key)) $item->$key = $val;
			}
//			var_dump($item->settings->get('thirdparty'));
//CKFof::dump($item);die;

			$i = count($items);
			if (isset($item->createcolumn) && $item->createcolumn == 1) {
				$item->params->set('maximenu_createcolumn', '1');
			}
			$items[$i] = $item;
			// createnewrow
			// columnwidth
			// colonne
			if (isset($item->submenu) and (! empty($item->submenu->columns))) {
//$items[$i]->params->set('maximenu_submenucontainerwidth', '800');
				$createnewrow = false;
				foreach($item->submenu->columns as $column) {
					if ($column->break == '1') {
						$createnewrow = true;
						continue;
					}
					if (! empty($column->children)) {
//						$items[$i]->params->set('maximenu_createcolumn', '1');
						
						foreach ($column->children as $c => $child) {
							self::parseItem($child, $items);
							$child->parent_id = $item->id;
							if ($c == 0) {
								$child->params->set('maximenu_createcolumn', '1');
								$child->params->set('maximenu_colwidth', '180');
							}
							if ($createnewrow === true) {
								$child->params->set('maximenu_createnewrow', '1');
								$child->createnewrow = 1;
								$createnewrow = false;
							}
						}
					}
				}
			}
	}

	/**
	 * Get a list of the menu items.
	 *
	 * @param	JRegistry	$params	The module options.
	 *
	 * @return	array
	 */
	public static function getItems(&$params) {

		$id = 4;
		$menu = CKFof::dbLoad('#__maximenuck_menus', $id);
		$layouthtml = unserialize($menu->layouthtml);
//CKFof::dump($layouthtml);

		$items = array();

		$i = 0;
		foreach ($layouthtml as $item) {
			// type
			// title
			// desc
			// id
			// level
			// submenu
				// columns (array)
					// children (array of items)
			// settings
			self::parseItem($item, $items);
			$i++;
		}
//CKFof::dump($items);die;
/*-----------------------------------*/
		// If no active menu, use default
		$active = (self::getActive()) ? self::getActive() : self::getDefault();
		$base = self::getBase($params);

//		$user = JFactory::getUser();
//		$levels = $user->getAuthorisedViewLevels();
//		asort($levels);
//		$key = 'menu_items' . $params . implode(',', $levels) . '.' . $active->id;
//		$cache = JFactory::getCache('mod_maximenuck', '');
//		if (!($items = $cache->get($key)) || (int) $params->get('cache') == '0') {
			// Initialise variables.
			$list = array();
			$modules = array();
			$db = JFactory::getDbo();
			$document = JFactory::getDocument();

			// load the libraries
			jimport('joomla.application.module.helper');

			$path = $base->tree;
			$start = (int) $params->get('startLevel');
			$end = (int) $params->get('endLevel');
//			$items = $menu->getItems('menutype', $params->get('menutype'));
			// if no items in the menu then exit
			if (!$items)
				return false;

			$hidden_parents = array();
			$lastitem = 0;
			// list all modules
			$modulesList = Helperfront::CreateModulesList();

			// check for imbrication with third party items
			$nbadditems = 0;
			foreach ($items as $i => $item) {
				if ($item->type == 'component' && $item->component == 'com_maximenuckhikashop') {
					require_once JPATH_ROOT . '/plugins/system/maximenuck_hikashop/helper/helper_maximenuck_hikashop.php';
					$className = 'modMaximenuckhikashopHelper';
					$itemparams = new JRegistry();
					if (isset($item->query) && is_array($item->query)) {
						$itemparams->loadArray($item->query);
					}
					$additems = $className::getItems($itemparams, false, $item->level, $item->parent_id);

					if (is_int($i)) {
						array_splice($items, $i + $nbadditems, 1, $additems);
					} else {
						$pos   = array_search($i, array_keys($items));
						$items = array_merge(
							array_slice($items, 1, $pos),
							$additems,
							array_slice($items, $pos)
						);
					}
					$nbadditems += count($additems) - 1;
				}
				$lastitem = $i;
			}

			$lastitem = 0;
			foreach ($items as $i => $item) {
				$isdependant = $params->get('dependantitems', false) ? ($start > 1 && !in_array($item->tree[$start - 2], $path)) : false;
				$item->isthirdparty = (isset($item->isthirdparty) && $item->isthirdparty) ? true : false;
				$item->parent = false;

				if (isset($items[$lastitem]) && isset($item->parent_id) && $items[$lastitem]->id == $item->parent_id && $item->params->get('menu_show', 1) == 1)
				{
					$items[$lastitem]->parent = true;
				}

				if (! $item->isthirdparty && (($start && $start > $item->level) || ($end && $item->level > $end) || $isdependant)
				) {
					unset($items[$i]);
					continue;
				}

				// Exclude item with menu item option set to exclude from menu modules
				if (! $item->isthirdparty && (($item->params->get('menu_show', 1) == 0) || in_array($item->parent_id, $hidden_parents))
				)
				{
					$hidden_parents[] = $item->id;
					unset($items[$i]);
					continue;
				}

				$item->deeper = false;
				$item->shallower = false;
				$item->level_diff = 0;

				if (isset($items[$lastitem])) {
					$items[$lastitem]->deeper = ($item->level > $items[$lastitem]->level);
					$items[$lastitem]->shallower = ($item->level < $items[$lastitem]->level);
					$items[$lastitem]->level_diff = ($items[$lastitem]->level - $item->level);
				}

				// Test if this is the last item
				$item->is_end = !isset($items[$i + 1]);

				// if (! $item->isthirdparty) $item->parent = (boolean) $menu->getItems('parent_id', (int) $item->id, true);
				$item->active = false;
				$item->current = false;
				$item->flink = $item->link;
				if (! $item->isthirdparty) $item->classe = '';
				switch ($item->type) {
					case 'separator':
					case 'heading':
						$item->classe .= ' headingck';
						// No further action needed.
						break;

					case 'url':
						if ((strpos($item->link, 'index.php?') === 0) && (strpos($item->link, 'Itemid=') === false)) {
							// If this is an internal Joomla link, ensure the Itemid is set.
							$item->flink = $item->link . '&Itemid=' . $item->id;
						}
						$item->flink = JFilterOutput::ampReplace(htmlspecialchars($item->flink));
						break;

					case 'thirdparty':
						break;

					case 'alias':
						// If this is an alias use the item id stored in the parameters to make the link.
						$item->flink = 'index.php?Itemid=' . $item->params->get('aliasoptions');
						break;

					default:
						// get the router according to the joomla version
						// no more used, see new method below
						// if (version_compare(JVERSION, '3.0.0') < 0) {
							// $router = JSite::getRouter();
						// } else {
							// $router = $app::getRouter();
						// }
						
						// Get the router.
						// $appsite = JApplication::getInstance('site');
						// $router = $appsite->getRouter();

						// if ($router->getMode() == JROUTER_MODE_SEF)
						// {
							// $item->flink = 'index.php?Itemid=' . $item->id;

							// if (isset($item->query['format']) && $app->getCfg('sef_suffix'))
							// {
								// $item->flink .= '&format=' . $item->query['format'];
							// }
						// }
						// else
						// {
							$item->flink .= '&Itemid=' . $item->id;
						// }
						break;
				}

				if (strcasecmp(substr($item->flink, 0, 4), 'http') && (strpos($item->flink, 'index.php?') !== false)) {
					$item->flink = JRoute::_($item->flink, true, $item->params->get('secure'));
				} else {
					$item->flink = JRoute::_($item->flink);
				}

				$item->anchor_css = htmlspecialchars($item->params->get('menu-anchor_css', ''), ENT_COMPAT, 'UTF-8', false);
				$item->anchor_title = htmlspecialchars($item->params->get('menu-anchor_title', ''), ENT_COMPAT, 'UTF-8', false);
				$item->menu_image = $item->params->get('menu_image', '') ? htmlspecialchars($item->params->get('menu_image', ''), ENT_COMPAT, 'UTF-8', false) : (isset($item->menu_image) && $item->menu_image ? $item->menu_image : '');



				//  ---------------- begin the maximenu work on items --------------------

				$item->ftitle = htmlspecialchars(($item->title === null ? $item->ftitle : $item->title), ENT_COMPAT, 'UTF-8', false);
				$item->ftitle = JFilterOutput::ampReplace($item->ftitle);
				$parentItem = new stdClass();
				
				if (isset($item->parent_id) && $item->parent_id) $parentItem = self::getParentItem($item->parent_id, $items);

				// ---- add some classes ----
				// add itemid class
				$item->classe .= ' item' . $item->id;
				// add current class
				if (isset($active) && $active->id == $item->id) {
					$item->classe .= ' current';
					$item->current = true;
				}
				// add active class
				if (is_array($path) &&
						( ($item->type == 'alias' && in_array($item->params->get('aliasoptions'), $path)) || in_array($item->id, $path))) {
					$item->classe .= ' active';
					$item->active = true;
				}
				// add the parent class
				if ($item->deeper) {
					$item->classe .= ' deeper';
				}

				// add last and first class
				$item->classe .= $item->is_end ? ' last' : '';
				$item->classe .= !isset($items[$i - 1]) ? ' first' : '';

				if (isset($items[$lastitem])) {
					if ($items[$lastitem]->parent && ($end == 0 || (int)$items[$lastitem]->level < (int)$end) && ! $items[$lastitem]->isthirdparty) {
						if ($params->get('layout', 'default') != '_:flatlist')
							$items[$lastitem]->classe .= ' parent';
					}
				
					$items[$lastitem]->classe .= $items[$lastitem]->shallower ? ' last' : '';
					$item->classe .= $items[$lastitem]->deeper ? ' first' : '';
					if (isset($items[$i + 1]) AND $item->level - $items[$i + 1]->level > 1 AND $parentItem) {
						$parentItem->classe = isset($parentItem->classe) ? $parentItem->classe . ' last' : 'last';
					}
				}

				// manage the class to show the item on desktop and mobile
				if ($item->params->get('maximenu_disablemobile') == '1') {
					$item->classe .= ' nomobileck';
				}

				// compatibility with Mobile Menu CK
				if ($item->params->get('mobilemenuck_enablemobile', '1') == '0') {
					$item->classe .= ' mobilemenuck-hide';
				}
				
				if ($item->params->get('maximenu_disabledesktop') == '1' || $item->params->get('mobilemenuck_enabledesktop', '1') == '0') {
					$item->classe .= ' nodesktopck';
				}


				// ---- manage params ----
				// -- manage column --
				$item->colwidth = $item->params->get('maximenu_colwidth', '180');
				$item->createnewrow = $item->params->get('maximenu_createnewrow', 0) || stristr($item->ftitle, '[newrow]');
				// check if there is a width for the subcontainer
				preg_match('/\[subwidth=([0-9]+)\]/', $item->ftitle, $subwidth);
				$subwidth = isset($subwidth[1]) ? $subwidth[1] : '';
				if ($subwidth)
					$item->ftitle = preg_replace('/\[subwidth=[0-9]+\]/', '', $item->ftitle);
				$item->submenucontainerwidth = $item->params->get('maximenu_submenucontainerwidth', '') ? $item->params->get('maximenu_submenucontainerwidth', '') : $subwidth;

				if ($item->params->get('maximenu_createcolumn', 0)) {
					$item->colonne = true;
					// add the value to give the total parent container width
					if (isset($parentItem->submenuswidth)) {
						if (! stristr($item->colwidth, '%') ) $parentItem->submenuswidth = strval($parentItem->submenuswidth) + strval($item->colwidth);
					} else if (isset($parentItem) && $parentItem) {
						if (! stristr($item->colwidth, '%') ) $parentItem->submenuswidth = strval($item->colwidth);
					}

					// if specified by user with the plugin, then give the width to the parent container
					if (isset($items[$lastitem]) && $items[$lastitem]->deeper) {
						$items[$lastitem]->nextcolumnwidth = $item->colwidth;
					}
					$item->columnwidth = $item->colwidth;
				} elseif (preg_match('/\[col=([0-9]+)\]/', $item->ftitle, $resultat)) {
					$item->ftitle = str_replace('[newrow]', '', $item->ftitle);
					$item->ftitle = preg_replace('/\[col=[0-9]+\]/', '', $item->ftitle);
					$item->colonne = true;
					if (isset($parentItem->submenuswidth)) {
						if (! stristr($item->colwidth, '%') ) $parentItem->submenuswidth = strval($parentItem->submenuswidth) + strval($resultat[1]);
					} else {
						if (! stristr($item->colwidth, '%') ) $parentItem->submenuswidth = strval($resultat[1]);
					}
					if (isset($items[$lastitem]) && $items[$lastitem]->deeper) {
						$items[$lastitem]->nextcolumnwidth = $resultat[1];
					}
					$item->columnwidth = $resultat[1];
				}
				if (isset($parentItem->submenucontainerwidth) AND $parentItem->submenucontainerwidth) {
					$parentItem->submenuswidth = $parentItem->submenucontainerwidth;
				}

				// -- manage module --
				$moduleid = $item->params->get('maximenu_module', '');
				$style = $item->params->get('maximenu_forcemoduletitle', 0) ? 'xhtml' : '';
				if ($item->params->get('maximenu_insertmodule', 0)) {
					if (!isset($modules[$moduleid])) {
						$modules[$moduleid] = self::GenModuleById($moduleid, $params, $modulesList, $style, $item->level);
					}
					// for maximenu imbricated, use another css class
					$special_subclass = ($modulesList[$moduleid]->module == 'mod_maximenuck') ? '2' : '';
					$item->content = '<div class="maximenuck_mod' . $special_subclass . '">' . $modules[$moduleid] . '<div class="clr"></div></div>';
				} elseif (preg_match('/\[modid=([0-9]+)\]/', $item->ftitle, $resultat)) {
					// for maximenu imbricated, use another css class
					$special_subclass = ($modulesList[$resultat[1]]->module == 'mod_maximenuck') ? '2' : '';
					$item->ftitle = preg_replace('/\[modid=[0-9]+\]/', '', $item->ftitle);
					$item->content = '<div class="maximenuck_mod' . $special_subclass . '">' . self::GenModuleById($resultat[1], $params, $modulesList, $style, $item->level) . '<div class="clr"></div></div>';
				}

				// -- manage rel attribute --
				$item->rel = '';
				if ($rel = $item->params->get('maximenu_relattr', '')) {
					$item->rel = ' rel="' . $rel . '"';
				} elseif (preg_match('/\[rel=([a-z]+)\]/i', $item->ftitle, $resultat)) {
					$item->ftitle = preg_replace('/\[rel=[a-z]+\]/i', '', $item->ftitle);
					$item->rel = ' rel="' . $resultat[1] . '"';
				}

				// -- manage link description --
				$item->description = $item->params->get('maximenu_desc', $item->desc);
				if ($item->description) {
					$item->desc = $item->description;
				} else {
					$resultat = explode("||", $item->ftitle);
					if (isset($resultat[1])) {
						$item->desc = $resultat[1];
					} else {
						$item->desc = '';
					}
					$item->ftitle = $resultat[0];
				}

				// add the anchor tag and url suffix
				$item->flink .= $item->params->get('maximenu_urlsuffix', '') ? $item->params->get('maximenu_urlsuffix', '') : '';
				$item->flink .= $item->params->get('maximenu_anchor', '') ? '#' . $item->params->get('maximenu_anchor', '') : '';

				// add styles to the page for customization
				$menuID = $params->get('menuid', 'maximenuck');

				// get plugin parameters that are used directly in the layout
				$item->leftmargin = $item->params->get('maximenu_leftmargin', '');
				$item->topmargin = $item->params->get('maximenu_topmargin', '');
				$item->liclass = $item->params->get('maximenu_liclass', '');
				$item->colbgcolor = $item->params->get('maximenu_colbgcolor', '');
				$item->tagcoltitle = $item->params->get('maximenu_tagcoltitle', 'none');
				$item->submenucontainerheight = $item->params->get('maximenu_submenucontainerheight', '');
				$item->access_key = htmlspecialchars($item->params->get('maximenu_accesskey', ''), ENT_COMPAT, 'UTF-8', false);

				// get mobile plugin parameters that are used directly in the layout
				$item->mobile_data = '';
				$mobileicon = $item->params->get('maximenumobile_icon', $item->params->get('mobilemenuck_icon', ''));
				$item->mobile_data .= $mobileicon ? ' data-mobileicon="' . $mobileicon . '"' : '';
				$mobiletext = $item->params->get('maximenumobile_textreplacement', $item->params->get('mobilemenuck_textreplacement', ''));
				$item->mobile_data .= $mobiletext ? ' data-mobiletext="' . $mobiletext . '"' : '';

				// set the item styles if the plugin is enabled
				// if (JPluginHelper::isEnabled('system', 'maximenuckparams')) {
					// if ($params->get('doCompile') || $params->get('loadcompiledcss', '0') == '0') {
						// $itemcss = self::injectItemCss($item, $menuID, $params);
						// if ($itemcss) {
							// if ($params->get('loadcompiledcss', '0') == '0') {
								// $document->addStyleDeclaration($itemcss);
							// } else {
								// self::$_itemcss .= $itemcss;
							// }
						// }
					// }
				// }

				$lastitem = $i;
			} // end of boucle for each items

			// give the correct deep infos for the last item
			if (isset($items[$lastitem])) {
				$items[$lastitem]->deeper = (($start ? $start : 1) > $items[$lastitem]->level);
				$items[$lastitem]->shallower = (($start ? $start : 1) < $items[$lastitem]->level);
				$items[$lastitem]->level_diff = ($items[$lastitem]->level - ($start ? $start : 1));
			}
//			$cache->store($items, $key);
//		}
		return $items;
	}

	/**
	 * Get a the parent item object
	 *
	 * @param Object $id The current item
	 * @param Array $items The list of all items
	 *
	 * @return object
	 */
	static function getParentItem($id, $items) {
		foreach ($items as $item) {
			if ($item->id == $id)
				return $item;
		}
		return new stdClass();
	}

	/**
	 * Get base menu item.
	 *
	 * @param   JRegistry  &$params  The module options.
	 *
	 * @return   object
	 *
	 * @since	3.0.2
	 */
	public static function getBase(&$params)
	{
		// Get base menu item from parameters
		if ($params->get('base'))
		{
			$base = JFactory::getApplication()->getMenu()->getItem($params->get('base'));
		}
		else
		{
			$base = false;
		}

		// Use active menu item if no base found
		if (!$base)
		{
			$base = self::getActive($params);
		}

		return $base;
	}

	/**
	 * Get active menu item.
	 *
	 * @param   JRegistry  &$params  The module options.
	 *
	 * @return  object
	 *
	 * @since	3.0.2
	 */
	public static function getActive()
	{
		$menu = JFactory::getApplication()->getMenu();

		return $menu->getActive() ? $menu->getActive() : $menu->getDefault();
	}

	/**
	 * Get active menu item.
	 *
	 * @param   JRegistry  &$params  The module options.
	 *
	 * @return  object
	 *
	 * @since	3.0.2
	 */
	public static function getDefault()
	{
		$menu = JFactory::getApplication()->getMenu();

		return $menu->getDefault();
	}

	public static function getMenuItem($id) {
		$item = CKFof::dbLoad('#__menu', $id);

		$item->params = new JRegistry($item->params);
		return $item;
	}

	public static function getModule($id) {
		$attribs['style'] = 'none';
		$module = CKFof::dbLoad('#__modules', $id);
		if ($module->published == 0) return '';
		return JModuleHelper::renderModule($module, $attribs);
	}

	public static function getThirdparty($item) {
		if ( !JPluginHelper::isEnabled('maximenuck', $item->type)) {
			return '';
		}
//		$doc = JFactory::getDocument();

		JPluginHelper::importPlugin( 'maximenuck' );
//		$dispatcher = JEventDispatcher::getInstance();
		$otheritems = CKFof::triggerEvent( 'onMaximenuckRenderItem' .  ucfirst($item->type) , array($item));

		ob_start();
		if (count($otheritems) == 1) {
			// load only the first instance found, because each plugin type must be unique
			// add override feature here, look in the template
			$template = JFactory::getApplication()->getTemplate();
			$overridefile = JPATH_ROOT . '/templates/' . $template . '/html/maximenuck/' . strtolower($item->type) . '.php';
			// var_dump($overridefile);die;
			if (file_exists($overridefile)) {
			// die('ok');
				$item = $e;
				include_once $overridefile;
			} else {
				// normal use
				$html = $otheritems[0];
			}
			echo $html;
		} else {
			echo '<p style="text-align:center;color:red;font-size:14px;">ERROR - MAXIMENU CK DEBUG : ELEMENT TYPE INSTANCE : ' . $item->type . '. Number of instances found : ' . count($otheritems) . '</p>';
		}
		$element_code = ob_get_clean();
		return $element_code;
	}

	public static function getImage($item) {
//		CKFof::dump($item->settings);die;
		$url = $item->settings->get('imageurl');
		if (! $url) return '';
		$width = $item->settings->get('imagewidth');
		$width = $width ? ' width="' . $width . '"' : '';

		$html = '<img src="' . $url . '" ' . $width . ' />';
		return $html;
	}
}
