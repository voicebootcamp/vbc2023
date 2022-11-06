<?php
/**
 * @version    CVS: 1.0.0
 * @package    com_quix
 * @author     ThemeXpert <info@themexpert.com>
 * @copyright  Copyright (C) 2015. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

use Lullabot\AMP\AMP;
use Lullabot\AMP\Validate\Scope;

/**
 * Installation class to perform additional changes during install/uninstall/update
 *
 * @package     Joomla.Administrator
 * @subpackage  com_quix
 * @since       1.3.0
 */
class QuixFrontendHelperAMP
{
    /**
     * prepareOutputAmp
     *
     * @param   string   html
     *
     * @return  html
     */
    public static function prepareOutputAmp($html)
    {
        // Notice this is a HTML fragment, i.e. anything that can appear below <body>
        // Create an AMP object
        $amp = new AMP();

        // prepare image witn amp-img tag
        self::prepareImage($html);

        // remove data attributes
        self::removeDataAttr($html);

        // remove all inline styles
        self::removeInlineStyles($html);

        // remove svg headers
        self::removeXmlSvgHeader($html);

        // Load up the HTML into the AMP object
        // Note that we only support UTF-8 or ASCII string input and output. (UTF-8 is a superset of ASCII)
        $amp->loadHtml($html);

        // If you're feeding it a complete document use the following line instead
        $amp->loadHtml($html, ['scope' => Scope::HTML_SCOPE]);

        // If you want some performance statistics (see https://github.com/Lullabot/amp-library/issues/24)
        $amp->loadHtml($html, ['add_stats_html_comment' => true]);

        // debug
        // $amp->debug($amp, $mode = 'html');

        // Convert to AMP HTML and store output in a variable
        return $amp->convertToAmpHtml();
    }

    /**
     * prepareImage
     *
     * @param   string   html
     *
     * @return  void
     */
    public static function prepareImage(&$html)
    {
        $config = \JComponentHelper::getParams('com_media');
        $imagePath = $config->get('image_path', 'images');
        preg_match_all("/<img.*\/>/", $html, $out);

        foreach ($out as $t1) {
            foreach ($t1 as $img) {
                preg_match('/(src=["\'](.*?)["\'])/', $img, $match);

                $path = $match[2];

                if (strpos($path, 'http', 0) !== false || strpos($path, '//', 0) !== false) {
                    $height = '80';
                    $width = '100';

                    $src = $path;
                } else {
                    $position = strpos($path, $imagePath, 0);
                    if ($position !== false) {
                        if ($position > 1) {
                            // only add jpath
                            $imgTmp = JPATH_SITE . $imagePath . $path;
                            $src = $imagePath . $path;
                        } else {
                            // only add jpath
                            $imgTmp = JPATH_SITE . $path;
                            $src = $path;
                        }
                    } elseif (strpos($path, 'libraries', 0) !== false) {
                        $imgTmp = JPATH_SITE . '/' . $path;
                        $src = $path;
                    } else {
                        // add jpath + image folder
                        $imgTmp = JPATH_SITE . '/' . $imagePath . $path;
                        $src = $imagePath . $path;
                    }

                    $src = JUri::root() . ltrim($src, '/');

                    $size = getimagesize($imgTmp);
                    $height = $size[1];
                    $width = $size[0];
                }

                $imgReplace = str_replace($img, '<amp-img src="' . $src . '" width="' . $width . '" height="' . $height . '" layout="responsive"></amp-img>', $img);
                $html = str_replace($img, $imgReplace, $html);
            }
        }
    }

    /**
     * removeDataAttr
     *
     * @param   string   html
     *
     * @return  void
     */
    public static function removeDataAttr(&$html)
    {
        preg_match_all('/(data-*=["\'](.*?)["\'])/', $html, $datas);
        foreach ($datas as $t2) {
            foreach ($t2 as $data) {
                $html = str_replace($data, '', $html);
            }
        }
    }

    /**
     * removeInlineStyles
     *
     * @param   string   html
     *
     * @return  void
     */
    public static function removeInlineStyles(&$html)
    {
        preg_match_all('/(style=["\'](.*?)["\'])/', $html, $styles);
        foreach ($styles as $t3) {
            foreach ($t3 as $style) {
                $html = str_replace($style, '', $html);
            }
        }
    }

    /**
     * removeXmlSvgHeader
     *
     * @param   string   html
     *
     * @return  void
     */
    public static function removeXmlSvgHeader(&$html)
    {
        $html = str_replace('<?xml version="1.0"?>', '', $html);
        preg_match_all("/\<div class=\"qx-shape(.*?)\"\>(.*?)\<\/div\>/is", $html, $shapes);
        // print_r($shapes);die;
        foreach ($shapes as $t4) {
            foreach ($t4 as $shape) {
                $html = str_replace($shape, '', $html);
            }
        }
    }

    /**
     * AmpDebug
     *
     * @param   string   html
     *
     * @return  void
     */
    public static function debug(&$amp, $mode)
    {
        // If you want some performance statistics (see https://github.com/Lullabot/amp-library/issues/24)
        if ($mode == 'text') {
            // Print validation issues and fixes made to HTML provided in the $html string
            print($amp->warningsHumanText());
        } elseif ($mode == 'html') {
            // warnings that have been passed through htmlspecialchars() function
            print($amp->warningsHumanHtml());
        } else {
            // You can do the above steps all over again without having to create a fresh object
            $amp->loadHtml($another_string);
        }
    }

    /**
     * sidebarMenu
     *
     * @param   string   html
     *
     * @return  void
     */
    public static function sidebarMenu($menutype)
    {
        JModelLegacy::addIncludePath(JPATH_SITE . '/administrator/components/com_menus/models');

        $app = JFactory::getApplication();
        $results = [];

        if ($menutype) {
            // $model = JModelLegacy::getInstance('Items', 'MenusModel', ['ignore_request' => true]);
            // $model->getState();
            // $model->setState('filter.menutype', $menutype);
            // $model->setState('list.select', 'a.id, a.title, a.level');
            // $model->setState('list.start', '0');
            // $model->setState('list.limit', '0');
            // $model->setState('filter.published', 1);
            // $results = $model->getItems();

            $app = JFactory::getApplication();
            $menu = $app->getMenu();
            $results = $menu->getItems('menutype', $menutype);

            /** @var  MenusModelItems  $model */

            // Pad the option text with spaces using depth level as a multiplier.
            for ($i = 0, $n = count($results); $i < $n; $i++) {
                $results[$i]->title = str_repeat(' - ', $results[$i]->level) . $results[$i]->title;

                $results[$i]->flink = $results[$i]->link;
                // Reverted back for CMS version 2.5.6
                switch ($results[$i]->type) {
                    case 'separator':
                        break;

                    case 'heading':
                        // No further action needed.
                        break;

                    case 'url':
                        if ((strpos($results[$i]->link, 'index.php?') === 0) && (strpos($results[$i]->link, 'Itemid=') === false)) {
                            // If this is an internal Joomla link, ensure the Itemid is set.
                            $results[$i]->flink = $results[$i]->link . '&Itemid=' . $results[$i]->id;
                        }
                        break;

                    case 'alias':
                        $results[$i]->flink = 'index.php?Itemid=' . $results[$i]->params->get('aliasoptions');

                        // Get the language of the target menu item when site is multilingual
                        if (JLanguageMultilang::isEnabled()) {
                            $newItem = JFactory::getApplication()->getMenu()->getItem((int) $results[$i]->params->get('aliasoptions'));

                            // Use language code if not set to ALL
                            if ($newItem != null && $newItem->language && $newItem->language !== '*') {
                                $results[$i]->flink .= '&lang=' . $newItem->language;
                            }
                        }
                        break;

                    default:
                        $results[$i]->flink = 'index.php?Itemid=' . $results[$i]->id;
                        break;
                }

                if ((strpos($results[$i]->flink, 'index.php?') !== false) && strcasecmp(substr($results[$i]->flink, 0, 4), 'http')) {
                    $results[$i]->flink = JRoute::_($results[$i]->flink, true, $results[$i]->params->get('secure'));
                } else {
                    $results[$i]->flink = JRoute::_($results[$i]->flink);
                }
            }
        }

        return $results;
    }
}
