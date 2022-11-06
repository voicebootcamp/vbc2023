<?php
/**
 * @name		Maximenu CK
 * @copyright	Copyright (C) 2018. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Cedric Keiflin - https://www.template-creator.com - https://www.joomlack.fr
 */

// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\Component\Content\Administrator\Extension\ContentComponent;
use Joomla\Component\Content\Site\Helper\RouteHelper;
use Joomla\Component\Content\Site\Helper\AssciationHelper;

/**
 * Helper Class.
 */
class MaximenuckHelpersourceCategories {

	private static $params;

	private static $flexi_exists;

	/*
	 * Get the items from the source
	 */
	public static function getItems($params, $all = false, $level = 1, $parent_id = 0) {
		if (empty(self::$params)) {
			self::$params = $params;
		}

		$input = JFactory::getApplication()->input;

		$options               = array();
		$options['countItems'] = $params->get('numitems', 0);

		$categories = JCategories::getInstance('Content', $options);
		$category   = $categories->get($params->get('categories_catid', 'root'));

		$categories_items = $category->getChildren(true);

		// if no categories found, only list the articles
		if (empty($categories_items)) {
			$i = 1;
			$menuItem = self::initItem();

			$menuItems = self::getArticles($category->id, $level-1, $i);
		} else {
			// load the main helper
			include_once JPATH_ROOT . '/modules/mod_maximenuck/helper.php';

			// load Flexicontent if exists
			$flexi_path = JPATH_SITE . '/components/com_flexicontent/';
			self::$flexi_exists = file_exists($flexi_path);
			if (self::$flexi_exists) {
				require_once(JPATH_ADMINISTRATOR . '/components/com_flexicontent/defineconstants.php');
				require_once($flexi_path . 'helpers/route.php');
			}

			// List the active items
			$activeCategories = array();
			$isArticle = $input->get('view', 'article') == 'article';
			if ($isArticle) {
				$active_category_id = $input->get('catid', '0', 'int');
			} else {
				$active_category_id = $input->get('id', '0', 'int');
			}
			self::getCategoryParentRecurse($active_category_id, $activeCategories);

			// Prepare data for display using display options
			$menuItems = Array();
			$i = 0;
			$lastitem = 0;
			$countitems = 0;
			$diff_level = 1 - $categories_items[0]->level;

			foreach ($categories_items as &$item)
			{
				if (self::$flexi_exists) {
					$item->link = JRoute::_(FlexicontentHelperRoute::getCategoryRoute($item->id));
				} else {
					$item->link = JRoute::_(ContentHelperRoute::getCategoryRoute($item->id));
				}

				$article_image = null;

				$menuItem = self::initItem();
				$menuItem->path = null;
				$menuItem->flink = $menuItem->link = $item->link;
				$menuItem->ftitle = $item->title;
	//				$menuItem->article->text = JHTML::_('content.prepare', $menuItem_article_text);
				// $menuItem->desc = $menuItem_article_text;
				$menuItem->id = $item->id;
				$menuItem->level = $item->level + $diff_level + ($level - 1);
				if ($menuItem->level == $level) {
					$menuItem->parent_id = $parent_id;
				}

				if ($params->get('categories_levels', 0) > 0 && $params->get('categories_levels', 0) < $menuItem->level) continue;
				// get active state
				$fulllink = str_replace(JUri::root(true),  trim(JUri::root(), '/'), $item->link);
				$menuItem->isactive = $menuItem->active = $fulllink == JUri::current();
				if (in_array($item->id, $activeCategories)) {
					$menuItem->isactive = true;
				}
				if ($menuItem->isactive) {
					$menuItem->classe = ' current active';
					$menuItem->anchor_css .= ' isactive';
				}

				$nbarticles  = 0;
				if ($params->get('categories_show_articles', 1) == 1) {
					$menuItem->articles = self::getArticles($menuItem->id, $menuItem->level, $i);
					$nbarticles = count($menuItem->articles);
				}
				if ($nbarticles > 0) $menuItem->classe .= " parent";
				$menuItems[$i] = $menuItem;
				if (isset($menuItems[$lastitem])) {
					$menuItems[$lastitem]->deeper = ($menuItem->level > $menuItems[$lastitem]->level);
					$menuItems[$lastitem]->shallower = ($menuItem->level < $menuItems[$lastitem]->level);
					$menuItems[$lastitem]->level_diff = ($menuItems[$lastitem]->level - $menuItem->level);
					if ($menuItems[$lastitem]->deeper AND $params->get('layout', 'default') != '_:flatlist') {
						$menuItems[$lastitem]->classe .= " parent";
					}
				}

				if ($params->get('categories_show_articles', 1) == 1 && $nbarticles > 0) {
					$menuItems = array_merge($menuItems, $menuItem->articles);
					$menuItems[$i]->deeper = true;
					$menuItems[$i]->level_diff = -1;
					$i += $nbarticles;
				} else {
					$nbarticles = 0;
				}

				$lastitem = $i;
				$i++;
			}

			if (isset($menuItems[$lastitem])) {
				$menuItems[$lastitem]->deeper = ($menuItem->level > $menuItems[$lastitem]->level);
				$menuItems[$lastitem]->shallower = ($menuItem->level < $menuItems[$lastitem]->level);
				$menuItems[$lastitem]->level_diff = ($menuItems[$lastitem]->level - $menuItem->level);
			}
		}

		return $menuItems;
	}

	private static function getArticles($catid, $level, &$i) {

		$params = self::$params;

		$articles = self::getArticlesModel();

		// Set application parameters in model
		$app = JFactory::getApplication();
		$appParams = $app->getParams();
		$articles->setState('params', $appParams);

		$articles->setState('filter.published', 1);

		// Access filter
		$access = !JComponentHelper::getParams('com_content')->get('show_noauth');
		$authorised = JAccess::getAuthorisedViewLevels(JFactory::getUser()->get('id'));
		$articles->setState('filter.access', $access);

		// Ordering
		$articles->setState('list.ordering', $params->get('categories_ordering', 'a.ordering'));
		$articles->setState('list.direction', $params->get('categories_ordering_direction', 'ASC'));

		// Filter by language
		$articles->setState('filter.language', $app->getLanguageFilter());
		$articles->setState('filter.category_id', $catid);
		$items = $articles->getItems();

		$menuItems = Array();
		$j = 1;
		foreach ($items as &$item) {

			if ($item->catid != $catid) continue;
			$item->slug = $item->id.':'.$item->alias;
			$item->catslug = $item->catid ? $item->catid .':'.$item->category_alias : $item->catid;
			if (self::$flexi_exists) {
				$item->link = JRoute::_(FlexicontentHelperRoute::getItemRoute($item->slug, $item->catslug));
			} else {
				$item->link = JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catslug));
			}

			$menuItem = self::initItem();
			$menuItem->path = null;
			$menuItem->flink = $menuItem->link = $item->link;
			$menuItem->ftitle = $item->title;
			// $menuItem->article->text = JHTML::_('content.prepare', $menuItem_article_text);
			// $menuItem->desc = $menuItem_article_text;
			$menuItem->id = $item->id;
			$menuItem->level = $level + 1;
			$menuItem->isthirdparty = true;
			$menuItem->type = 'thirdparty';

			// get active state
			$fulllink = trim(JUri::root(), '/') . str_replace(JUri::root(true),  '', $item->link);
			$menuItem->isactive = $menuItem->active = $fulllink == JUri::current();
			if ($menuItem->isactive) {
				$menuItem->classe = ' current active';
				$menuItem->anchor_css .= ' isactive';
			}
			$menuItems[$i + $j] = $menuItem;
			$j++;
		}

		return $menuItems;
	}

	static function getCategoryParentRecurse($category_id, &$activeCategories) {
			$activeCategories[] = $category_id;
			$db = JFactory::getDBO();
			$query = "SELECT parent_id"
					." FROM #__categories"
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

		// special for the thirdparty plugins
		$item->isthirdparty = true;
		$item->type = 'thirdparty';

		return $item;
	}

	private static function getArticlesModel() {
		$app     = Factory::getApplication();
		if (version_compare(JVERSION, '4') >= 0) {
			$factory = $app->bootComponent('com_content')->getMVCFactory();

			// Get an instance of the generic articles model
			$articles = $factory->createModel('Articles', 'Site', ['ignore_request' => true]);
		} else {
			// load the content articles file
			$com_path = JPATH_SITE . '/components/com_content/';
			include_once $com_path . 'router.php';
			include_once $com_path . 'helpers/route.php';
			JModelLegacy::addIncludePath($com_path . '/models', 'ContentModel');

			// Get an instance of the generic articles model
			$articles = JModelLegacy::getInstance('Articles', 'ContentModel', array('ignore_request' => true));
		}

		return $articles;
	}
}
