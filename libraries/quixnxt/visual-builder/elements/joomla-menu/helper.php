<?php
/**
 * @version    1.0.0
 * @package    Joomla Menu
 * @author     ThemeXpert <info@themexpert.com>
 * @copyright  Copyright (C) 2015. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;
use Joomla\Registry\Registry;

/**
* QuixJoomlaMenuElement helper class
*/
if (!class_exists('QuixJoomlaMenuElement')) {
    class QuixJoomlaMenuElement
    {
        public static function getAjax($data = [])
        {
            $app = JFactory::getApplication();
            if (! $data) {
                $data = $app->input->get('data', '', 'BASE64', 'raw');
                $data = base64_decode($data);
            }

            $params = new Registry($data);

            $list       = self::getList($params);
            $base       = self::getBase($params);
            $active     = self::getActive($params);
            $default    = self::getDefault();
            $active_id  = $active->id;
            $default_id = $default->id;
            $path       = $base->tree;
            $showAll    = $params->get('showAllChildren', 1);

            $preparedList = self::prepareList($list, $active_id, $default_id, $path);

            return [
                'list'       => $preparedList,
                'base'       => $base,
                'active'     => $active,
                'default'    => $default,
                'active_id'  => $active_id,
                'default_id' => $default_id,
                'path'       => $base->tree,
                'showAll'    => $showAll
            ];
        }

        /**
         * Get a list of the menu items.
         *
         * @param  \Joomla\Registry\Registry  &$params  The module options.
         *
         * @return  array
         *
         * @since   1.5
         */
        public static function prepareList(&$list, $active_id, $default_id, $path)
        {
            foreach ($list as $i => &$item) {
                $class = 'item-'.$item->id;


                if (in_array($item->id, $path)) {
                    $class .= ' qx-active';
                } elseif ($item->type === 'alias') {
                    $aliasToId = $item->getParams()->get('aliasoptions');

                    if (count($path) > 0 && $aliasToId == $path[count($path) - 1]) {
                        $class .= ' qx-active';
                    } elseif (in_array($aliasToId, $path)) {
                        $class .= ' alias-parent-active';
                    }
                }

                if ($item->type === 'separator') {
                    $class .= ' divider';
                }

                if ($item->deeper) {
                    $class .= ' deeper';
                }

                if ($item->parent) {
                    $class .= ' qx-parent';
                }

                $item->class = $class;

                switch ($item->type) :
                    case 'separator':
                        $item->render = self::prepareSeparator($item);
                        break;
                    case 'component':
                        $item->render = self::prepareComponent($item);
                        break;
                    case 'heading':
                        $item->render = self::prepareHeading($item);
                        break;
                    case 'url':
                        $item->render = self::prepareUrl($item);
                        break;
                    default:
                        $item->render = self::prepareDefault($item);
                        break;
                endswitch;
            }

            return $list;
        }

        /**
         * Prepare the Url list
         *
         * @params $item object
         *
         * @return string
         *
         * @since 3.0.0
         */
        public static function prepareUrl(&$item)
        {
            $attributes = [];

            if ($item->anchor_title) {
                $attributes['title'] = $item->anchor_title;
            }

            if ($item->anchor_css) {
                $attributes['class'] = $item->anchor_css;
            }

            if ($item->anchor_rel) {
                $attributes['rel'] = $item->anchor_rel;
            }

            $linktype = $item->title;

            if ($item->menu_image) {
                if ($item->menu_image_css) {
                    $image_attributes['class'] = $item->menu_image_css;
                    $linktype                  = JHtml::_('image', $item->menu_image, $item->title, $image_attributes);
                } else {
                    $linktype = JHtml::_('image', $item->menu_image, $item->title);
                }

                if ($item->getParams()->get('menu_text', 1)) {
                    $linktype .= '<span class="image-title">'.$item->title.'</span>';
                }
            }

            if ($item->browserNav == 1) {
                $attributes['target'] = '_blank';
                $attributes['rel']    = 'noopener noreferrer';

                if ($item->anchor_rel == 'nofollow') {
                    $attributes['rel'] .= ' nofollow';
                }
            } elseif ($item->browserNav == 2) {
                $options = 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,'.$params->get('window_open');

                $attributes['onclick'] = "window.open(this.href, 'targetWindow', '".$options."'); return false;";
            }

            return JHtml::_('link', JFilterOutput::ampReplace(htmlspecialchars($item->flink, ENT_COMPAT, 'UTF-8', false)), $linktype, $attributes);
        }

        /**
         * Prepare the Heading list
         *
         * @params $item object
         *
         * @return object
         *
         * @since 3.0.0
         */
        public static function prepareHeading(&$item)
        {
            $title      = $item->anchor_title ? ' title="'.$item->anchor_title.'"' : '';
            $anchor_css = $item->anchor_css ?: '';

            $linktype = $item->title;

            if ($item->menu_image) {
                if ($item->menu_image_css) {
                    $image_attributes['class'] = $item->menu_image_css;
                    $linktype                  = JHtml::_('image', $item->menu_image, $item->title, $image_attributes);
                } else {
                    $linktype = JHtml::_('image', $item->menu_image, $item->title);
                }

                if ($item->getParams()->get('menu_text', 1)) {
                    $linktype .= '<span class="image-title">'.$item->title.'</span>';
                }
            }

            return '<a class="qx-nav-header '.$anchor_css.'"'.$title.'>'.$linktype.'</a>';
        }

        /**
         * Prepare the Separator list
         *
         * @params $item object
         *
         * @return object
         *
         * @since 3.0.0
         */
        public static function prepareSeparator(&$item)
        {
            $title      = $item->anchor_title ? ' title="'.$item->anchor_title.'"' : '';
            $anchor_css = $item->anchor_css ?: '';

            $linktype = $item->title;

            if ($item->menu_image) {
                if ($item->menu_image_css) {
                    $image_attributes['class'] = $item->menu_image_css;
                    $linktype                  = JHtml::_('image', $item->menu_image, $item->title, $image_attributes);
                } else {
                    $linktype = JHtml::_('image', $item->menu_image, $item->title);
                }

                if ($item->getParams()->get('menu_text', 1)) {
                    $linktype .= '<span class="image-title">'.$item->title.'</span>';
                }
            }

            return '<a class="separator '.$anchor_css.'" '.$title.'>'.$linktype.'</a>';
        }

        /**
         * Prepare the Component list
         *
         * @params $item object
         *
         * @return object
         *
         * @since 3.0.0
         */
        public static function prepareComponent(&$item)
        {
            $attributes = [];

            if ($item->anchor_title) {
                $attributes['title'] = $item->anchor_title;
            }

            if ($item->anchor_css) {
                $attributes['class'] = $item->anchor_css;
            }

            if ($item->anchor_rel) {
                $attributes['rel'] = $item->anchor_rel;
            }

            $linktype = $item->title;

            if ($item->menu_image) {
                if ($item->menu_image_css) {
                    $image_attributes['class'] = $item->menu_image_css;
                    $linktype                  = JHtml::_('image', $item->menu_image, $item->title, $image_attributes);
                } else {
                    $linktype = JHtml::_('image', $item->menu_image, $item->title);
                }

                if ($item->getParams()->get('menu_text', 1)) {
                    $linktype .= '<span class="image-title">'.$item->title.'</span>';
                }
            }

            if ($item->browserNav == 1) {
                $attributes['target'] = '_blank';
            } elseif ($item->browserNav == 2) {
                $options = 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes';

                $attributes['onclick'] = "window.open(this.href, 'targetWindow', '".$options."'); return false;";
            }

            return JHtml::_('link', JFilterOutput::ampReplace(htmlspecialchars($item->flink, ENT_COMPAT, 'UTF-8', false)), $linktype, $attributes);
        }

        /**
         * Prepare the default html list
         *
         * @params $item object
         *
         * @return object
         *
         * @since 3.0.0
         */
        public static function prepareDefault(&$item)
        {
            $attributes = [];

            if ($item->anchor_title) {
                $attributes['title'] = $item->anchor_title;
            }

            if ($item->anchor_css) {
                $attributes['class'] = $item->anchor_css;
            }

            if ($item->anchor_rel) {
                $attributes['rel'] = $item->anchor_rel;
            }

            $linktype = $item->title;

            if ($item->menu_image) {
                if ($item->menu_image_css) {
                    $image_attributes['class'] = $item->menu_image_css;
                    $linktype                  = JHtml::_('image', $item->menu_image, $item->title, $image_attributes);
                } else {
                    $linktype = JHtml::_('image', $item->menu_image, $item->title);
                }

                if ($item->getParams()->get('menu_text', 1)) {
                    $linktype .= '<span class="image-title">'.$item->title.'</span>';
                }
            }

            if ($item->browserNav == 1) {
                $attributes['target'] = '_blank';
                $attributes['rel']    = 'noopener noreferrer';

                if ($item->anchor_rel == 'nofollow') {
                    $attributes['rel'] .= ' nofollow';
                }
            } elseif ($item->browserNav == 2) {
                $options = 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,'.$params->get('window_open');

                $attributes['onclick'] = "window.open(this.href, 'targetWindow', '".$options."'); return false;";
            }

            return JHtml::_('link', JFilterOutput::ampReplace(htmlspecialchars($item->flink, ENT_COMPAT, 'UTF-8', false)), $linktype, $attributes);
        }

        /**
         * Get a list of the menu items.
         *
         * @param  \Joomla\Registry\Registry  &$params  The module options.
         *
         * @return  array
         *
         * @since   1.5
         */
        public static function getList(&$params)
        {
            $app  = JFactory::getApplication();
            $menu = $app->getMenu();


            // Get active menu item
            $base = self::getBase($params);

            $user   = JFactory::getUser();
            $levels = $user->getAuthorisedViewLevels();
            asort($levels);
            $key   = 'quix_menu_items'.$params.implode(',', $levels).'.'.$base->id;
            $cache = JFactory::getCache('quix-element-joomla-menu', '');

            if ($cache->contains($key)) {
                $items = $cache->get($key);
            } else {
                $path             = $base->tree;
                $start            = (int) $params->get('startLevel', 1);
                $end              = (int) $params->get('endLevel', 0);
                $showAll          = $params->get('showAllChildren', 1);
                $items            = $menu->getItems('menutype', $params->get('menutype'));
                $hidden_parents   = [];
                $lastitem         = 0;
                $itemSubIndicator = '';

                if ($items) {
                    foreach ($items as $i => $item) {
                        $item->parent = false;

                        if (isset($items[$lastitem]) && $items[$lastitem]->id == $item->parent_id && $item->getParams()->get('menu_show', 1) == 1) {
                            $items[$lastitem]->parent = true;
                        }

                        if (($start && $start > $item->level)
                            || ($end && $item->level > $end)
                            || ( ! $showAll && $item->level > 1 && ! in_array($item->parent_id, $path))
                            || ($start > 1 && ! in_array($item->tree[$start - 2], $path))) {
                            unset($items[$i]);
                            continue;
                        }

                        // Exclude item with menu item option set to exclude from menu modules
                        if (($item->getParams()->get('menu_show', 1) == 0) || in_array($item->parent_id, $hidden_parents)) {
                            $hidden_parents[] = $item->id;
                            unset($items[$i]);
                            continue;
                        }

                        $item->deeper     = false;
                        $item->shallower  = false;
                        $item->level_diff = 0;

                        if (isset($items[$lastitem])) {
                            $items[$lastitem]->deeper     = ($item->level > $items[$lastitem]->level);
                            $items[$lastitem]->shallower  = ($item->level < $items[$lastitem]->level);
                            $items[$lastitem]->level_diff = ($items[$lastitem]->level - $item->level);
                        }

                        $lastitem     = $i;
                        $item->active = false;
                        $item->flink  = $item->link;

                        // Reverted back for CMS version 2.5.6
                        switch ($item->type) {
                            case 'separator':
                                break;

                            case 'heading':
                                // No further action needed.
                                break;

                            case 'url':
                                if ((strpos($item->link, 'index.php?') === 0) && (strpos($item->link, 'Itemid=') === false)) {
                                    // If this is an internal Joomla link, ensure the Itemid is set.
                                    $item->flink = $item->link.'&Itemid='.$item->id;
                                }
                                break;

                            case 'alias':
                                $item->flink = 'index.php?Itemid='.$item->getParams()->get('aliasoptions');

                                // Get the language of the target menu item when site is multilingual
                                if (JLanguageMultilang::isEnabled()) {
                                    $newItem = JFactory::getApplication()->getMenu()->getItem((int) $item->getParams()->get('aliasoptions'));

                                    // Use language code if not set to ALL
                                    if ($newItem != null && $newItem->language && $newItem->language !== '*') {
                                        $item->flink .= '&lang='.$newItem->language;
                                    }
                                }
                                break;

                            default:
                                $item->flink = 'index.php?Itemid='.$item->id;
                                break;
                        }

                        if ((strpos($item->flink, 'index.php?') !== false) && strcasecmp(substr($item->flink, 0, 4), 'http')) {
                            $item->flink = JRoute::_($item->flink, true, $item->getParams()->get('secure'));
                        } else {
                            $item->flink = JRoute::_($item->flink);
                        }

                        // We prevent the double encoding because for some reason the $item is shared for menu modules and we get double encoding
                        // when the cause of that is found the argument should be removed

                        $item->title          = htmlspecialchars($item->title, ENT_COMPAT, 'UTF-8', false);
                        $item->anchor_css     = htmlspecialchars($item->getParams()->get('menu-anchor_css', ''), ENT_COMPAT, 'UTF-8', false);
                        $item->anchor_title   = htmlspecialchars($item->getParams()->get('menu-anchor_title', ''), ENT_COMPAT, 'UTF-8', false);
                        $item->anchor_rel     = htmlspecialchars($item->getParams()->get('menu-anchor_rel', ''), ENT_COMPAT, 'UTF-8', false);
                        $item->menu_image     = $item->getParams()->get('menu_image', '') ?
                            htmlspecialchars($item->getParams()->get('menu_image', ''), ENT_COMPAT, 'UTF-8', false) : '';
                        $item->menu_image_css = htmlspecialchars($item->getParams()->get('menu_image_css', ''), ENT_COMPAT, 'UTF-8', false);
                    }

                    if (isset($items[$lastitem])) {
                        $items[$lastitem]->deeper     = (($start ?: 1) > $items[$lastitem]->level);
                        $items[$lastitem]->shallower  = (($start ?: 1) < $items[$lastitem]->level);
                        $items[$lastitem]->level_diff = ($items[$lastitem]->level - ($start ?: 1));
                    }

                    $items[$lastitem]->paramsjson = $item->getParams()->toArray();
                }

                $cache->store($items, $key);
            }

            return $items;
        }

        /**
         * Get base menu item.
         *
         * @param  \Joomla\Registry\Registry  &$params  The module options.
         *
         * @return  object
         *
         * @since    3.0.2
         */
        public static function getBase(&$params)
        {
            // Get base menu item from parameters
            if ($params->get('base')) {
                $base = JFactory::getApplication()->getMenu('site')->getItem($params->get('base'));
            } else {
                $base = false;
            }

            // Use active menu item if no base found
            if (! $base) {
                $base = self::getActive($params);
            }

            return $base;
        }

        /**
         * Get active menu item.
         *
         * @param  \Joomla\Registry\Registry  &$params  The module options.
         *
         * @return  object
         *
         * @since    3.0.2
         */
        public static function getActive(&$params)
        {
            // $menu = JApplicationSite::getInstance()->getMenu('site');
            $menu = JFactory::getApplication()->getMenu('site');
            return $menu->getActive() ?: self::getDefault();
        }

        /**
         * Get default menu item (home page) for current language.
         *
         * @return  object
         */
        public static function getDefault()
        {
            $menu = JFactory::getApplication()->getMenu('site');
            $lang = JFactory::getLanguage();

            // Look for the home menu
            if (JLanguageMultilang::isEnabled()) {
                return $menu->getDefault($lang->getTag());
            } else {
                return $menu->getDefault();
            }
        }
    }
}
