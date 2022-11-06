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
class MaximenuckHelpersourceArticlesbydate {

	private static $params;

	/*
	 * Get the items from the source
	 */
	public static function getItems($params, $all = false, $level = 1, $parent_id = 0) {
		if (empty(self::$params)) {
			self::$params = $params;
		}

		// Get an instance of the generic articles model
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
		// Prep for Normal or Dynamic Modes
		$mode = $params->get('mode', 'normal');
		$option = $app->input->get('option', '', 'cmd');
		$view = $app->input->get('view', '', 'cmd');
		switch ($mode)
		{
			case 'dynamic':
				if ($option === 'com_content') {
					switch($view)
					{
						case 'category':
							$catids = array($app->input->get('id', 0, 'int'));
							break;
						case 'categories':
							$catids = array($app->input->get('id', 0, 'int'));
							break;
						case 'article':
							if ($params->get('articlesbydate_show_on_article_page', 1)) {
								$article_id = $app->input->get('id', 0, 'int');
								$catid = $app->input->get('catid', 0, 'int');

								if (!$catid) {
									// Get an instance of the generic article model
									$article = JModelLegacy::getInstance('Article', 'ContentModel', array('ignore_request' => true));

									$article->setState('params', $appParams);
									$article->setState('filter.published', 1);
									$article->setState('article.id', (int) $article_id);
									$item = $article->getItem();

									$catids = array($item->catid);
								}
								else {
									$catids = array($catid);
								}
							}
							else {
								// Return right away if show_on_article_page option is off
								return;
							}
							break;

						case 'featured':
						default:
							// Return right away if not on the category or article views
							return;
					}
				}
				else {
					// Return right away if not on a com_content page
					return;
				}

				break;

			case 'normal':
			default:
				$catids = $params->get('articlesbydate_catid');
				$articles->setState('filter.category_id.include', (bool) $params->get('articlesbydate_category_filtering_type', 1));
				break;
		}

		// Category filter
		if ($catids && !empty($catids) && isset($catids[0]) && $catids[0] !== '') {
			if ($params->get('articlesbydate_show_child_category_articles', 0) && (int) $params->get('articlesbydate_levels', 0) > 0) {
				// Get an instance of the generic categories model
				$categories = JModelLegacy::getInstance('Categories', 'ContentModel', array('ignore_request' => true));
				$categories->setState('params', $appParams);
				$levels = $params->get('articlesbydate_levels', 1) ? $params->get('articlesbydate_levels', 1) : 9999;
				$categories->setState('filter.get_children', $levels);
				$categories->setState('filter.published', 1);
				$categories->setState('filter.access', $access);
				$additional_catids = array();

				foreach($catids as $catid)
				{
					$categories->setState('filter.parentId', $catid);
					$recursive = true;
					$items = $categories->getItems($recursive);

					if ($items)
					{
						foreach($items as $category)
						{
							$condition = (($category->level - $categories->getParent()->level) <= $levels);
							if ($condition) {
								$additional_catids[] = $category->id;
							}

						}
					}
				}

				$catids = array_unique(array_merge($catids, $additional_catids));
			}

			$articles->setState('filter.category_id', $catids);
		}

		// Ordering
		$articles->setState('list.ordering', 'a.created');
		$articles->setState('list.direction', $params->get('articlesbydate_article_ordering_direction', 'DESC'));

		// New Parameters
		$articles->setState('filter.featured', $params->get('articlesbydate_show_front', 'show'));
//		$articles->setState('filter.author_id', $params->get('created_by', ""));
//		$articles->setState('filter.author_id.include', $params->get('author_filtering_type', 1));
//		$articles->setState('filter.author_alias', $params->get('created_by_alias', ""));
//		$articles->setState('filter.author_alias.include', $params->get('author_alias_filtering_type', 1));
		$excluded_articles = $params->get('articlesbydate_excluded_articles', '');

		if ($excluded_articles) {
			$excluded_articles = explode("\r\n", $excluded_articles);
			$articles->setState('filter.article_id', $excluded_articles);
			$articles->setState('filter.article_id.include', false); // Exclude
		}

		$date_filtering = $params->get('articlesbydate_date_filtering', 'off');
		if ($date_filtering !== 'off') {
			$articles->setState('filter.date_filtering', $date_filtering);
			$articles->setState('filter.date_field', $params->get('articlesbydate_date_field', 'a.created'));
			$articles->setState('filter.start_date_range', $params->get('articlesbydate_start_date_range', '1000-01-01 00:00:00'));
			$articles->setState('filter.end_date_range', $params->get('articlesbydate_end_date_range', '9999-12-31 23:59:59'));
			$articles->setState('filter.relative_date', $params->get('articlesbydate_relative_date', 30));
		}

		// Filter by language
		$articles->setState('filter.language', $app->getLanguageFilter());

		$items = $articles->getItems();

		// Prepare data for display using display options
		$menuItems = Array();
		$years = Array();
		$months = Array();
		$i = 0;
		$lastitem = 0;
		$lastyear = 0;
		$lastmonth = 0;
		$countitems = 0;
		$countitemsmonth = 0;
		foreach ($items as &$item)
		{
			$item->slug = $item->id.':'.$item->alias;
			$item->catslug = $item->catid ? $item->catid .':'.$item->category_alias : $item->catid;

			if ($access || in_array($item->access, $authorised)) {
				// We know that user has the privilege to view the article
				$item->link = JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catslug));
			}
			 else {
				// Angie Fixed Routing
				$app	= JFactory::getApplication();
				$menu	= $app->getMenu();
				$menuitems	= $menu->getItems('link', 'index.php?option=com_users&view=login');
				if(isset($menuitems[0])) {
					$Itemid = $menuitems[0]->id;
				} elseif ($app->input->get('Itemid', 0, 'int') > 0) { //use Itemid from requesting page only if there is no existing menu
					$Itemid = $app->input->get('Itemid', 0, 'int');
				}

				$item->link = JRoute::_('index.php?option=com_users&view=login&Itemid='.$Itemid);
			}

			// add the article to the slide
			$registry = new JRegistry;
			$registry->loadString($item->images);
			$item->images = $registry->toArray();
			$article_image = null;

			$menuItem = self::initItem();
			$menuItem->path = $params->get('articlesbydate_articleimgsource', 'introimage') != 'text' ? $article_image : null;
			$menuItem->flink = $menuItem->link = $item->link;
			$menuItem->ftitle = $item->title;
//				$menuItem->article->text = JHTML::_('content.prepare', $menuItem_article_text);
			// $menuItem->desc = $menuItem_article_text;
			$menuItem->id = $item->id;
			$menuItem->level = 3 + ($level - 1);

			// get active state
			$fulllink = str_replace(JUri::root(true),  trim(JUri::root(), '/'), $item->link);
			$menuItem->isactive = $menuItem->active = trim(JUri::root(), '/') . '/' . trim($fulllink, '/') == JUri::current();
			if ($menuItem->isactive) {
				$menuItem->classe = ' current active';
				$menuItem->anchor_css .= ' isactive';
			}

			$year = $item->created;
			$year = new DateTime($year);
			$year = $year->format('Y');
			if (! in_array($year, $years)) {
				$years[] = $year;
				$yearItem = self::initItem();
				$yearItem->ftitle = $year;
				$yearItem->type = 'separator';
				$yearItem->level = 1 + ($level - 1);

				if ($yearItem->level == $level) {
					$yearItem->parent_id = $parent_id;
				}

				if (isset($menuItems[$lastitem])) {
					$menuItems[$lastyear]->countitems = $countitemsyear;
					$menuItems[$lastitem]->deeper = ($yearItem->level > $menuItems[$lastitem]->level);
					$menuItems[$lastitem]->shallower = ($yearItem->level < $menuItems[$lastitem]->level);
					$menuItems[$lastitem]->level_diff = ($menuItems[$lastitem]->level - $yearItem->level);
					if ($menuItems[$lastitem]->deeper AND $params->get('layout', 'default') != '_:flatlist')
						$menuItems[$lastitem]->classe .= " parent";
				}
				$menuItems[$i] = $yearItem;
				$countitemsyear = 0;
				$lastitem = $i;
				$lastyear = $i;
				$i++;
			}

			$month = $item->created;
			$month = new DateTime($month);
			$month = $month->format('F');
			if (! in_array($year.$month, $months)) {
				$months[] = $year.$month;
				$monthItem = self::initItem();
				$monthItem->ftitle = JText::_(strtoupper($month));
				$monthItem->type = 'separator';
				$monthItem->level = 2 + ($level - 1);

				if (isset($menuItems[$lastitem])) {
					$menuItems[$lastmonth]->countitems = $countitemsmonth;
					$menuItems[$lastitem]->deeper = ($monthItem->level > $menuItems[$lastitem]->level);
					$menuItems[$lastitem]->shallower = ($monthItem->level < $menuItems[$lastitem]->level);
					$menuItems[$lastitem]->level_diff = ($menuItems[$lastitem]->level - $monthItem->level);
					if ($menuItems[$lastitem]->deeper AND $params->get('layout', 'default') != '_:flatlist')
						$menuItems[$lastitem]->classe .= " parent";
				}
				$menuItems[$i] = $monthItem;
				$countitemsmonth = 0;
				$lastitem = $i;
				$lastmonth = $i;
				$i++;
			}

			if ($menuItem->isactive) {
				$menuItems[$lastyear]->classe = ' current active';
				$menuItems[$lastyear]->anchor_css .= ' isactive';
				$menuItems[$lastmonth]->classe = ' current active';
				$menuItems[$lastmonth]->anchor_css .= ' isactive';
			}

			// test if it is the last item
			$menuItem->is_end = !isset($menuItems[$i + 1]);

			$menuItems[$i] = $menuItem;
			$countitemsyear++;
			$countitemsmonth++;

			if (isset($menuItems[$lastitem])) {
				$menuItems[$lastyear]->countitems = $countitemsyear;
				$menuItems[$lastmonth]->countitems = $countitemsmonth;
				$menuItems[$lastitem]->deeper = ($menuItem->level > $menuItems[$lastitem]->level);
				$menuItems[$lastitem]->shallower = ($menuItem->level < $menuItems[$lastitem]->level);
				$menuItems[$lastitem]->level_diff = ($menuItems[$lastitem]->level - $menuItem->level);
				if ($menuItems[$lastitem]->deeper AND $params->get('layout', 'default') != '_:flatlist')
						$menuItems[$lastitem]->classe .= " parent";
			}

			$lastitem = $i;
			$i++;
		}

		// give the correct deep infos for the last item
		if (isset($menuItems[$lastitem])) {
			$menuItems[$lastitem]->level_diff = ($menuItems[$lastitem]->level - 1 + ((int)$level-1));
		}

		return $menuItems;
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
