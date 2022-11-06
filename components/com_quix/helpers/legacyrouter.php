<?php
/**
 * @package         Quix.Site
 * @subpackage      com_quix
 *
 * @copyright   (C) 2009 Open Source Matters, Inc. <https://www.joomla.org>
 * @license         GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Component\Router\RouterView;
use Joomla\CMS\Factory;

defined('_JEXEC') or die;

/**
 * Legacy routing rules class from com_content
 *
 * @since       3.6
 * @deprecated  4.0
 */
class QuixRouterRulesLegacy implements JComponentRouterRulesInterface
{
    /**
     * Router this rule belongs to
     *
     * @var    RouterView
     * @since  3.4
     */
    protected $router;

    /**
     * Constructor for this legacy router
     *
     * @param  JComponentRouterView  $router  The router this rule belongs to
     *
     * @since       3.6
     * @deprecated  4.0
     */
    public function __construct($router)
    {
        $this->router = $router;
    }


    /**
     * Preprocess the route for the com_content component
     *
     * @param  array  &$query  An array of URL arguments
     *
     * @return  void
     *
     * @since       3.6
     * @deprecated  4.0
     */
    public function preprocess(&$query)
    {
    }

    /**
     * Build method for URLs
     * This method is meant to transform the query parameters into a more
     * human-readable form. It is only executed when SEF mode is switched on.
     *
     * @param  array  &$query  An array of URL arguments
     *
     * @return  array  The URL arguments to use to assemble the subsequent URL.
     *
     * @since   3.3
     */
    public function build(&$query, &$segments)
    {
        $params = JComponentHelper::getParams('com_quix');

        $app       = Factory::getApplication();
        $menu      = $app->getMenu();
        $validView = ['page', 'collection', 'form'];
        $type      = $query['type'] ?? 'page';
        // $query['id'] = $query['id'] ?? 0;


        if (empty($query['Itemid'])) {
            $menuItem      = $menu->getActive();
            $menuItemGiven = false;
        // } elseif ($query['Itemid'] == 101 && $type === 'page') {
        //     $link = new JURI('index.php?option=com_quix&page=view&id=' . $query['id']);
        //     return $link;
        } else {
            $menuItem      = $menu->getItem($query['Itemid']);
            $menuItemGiven = true;
        }

        // Check again
        if ($menuItemGiven && isset($menuItem)
            && $menuItem->component !== 'com_quix') {
            $menuItemGiven = false;
            unset($query['Itemid']);
        }

        if (isset($query['view']) and in_array($query['view'], $validView)) {
            $view = $query['view'];
        } else {
            return $segments;
        }

        if (isset($query['type']) && $query['type'] == 'collection' && $query['view'] == 'form') {
            $menuItemGiven = false;
            unset($query['Itemid']);
        }

        if (
            $menuItem
            // && $type != 'collection'
            && $query['view'] == 'form'
            && isset($query['id'])
            && (isset($menuItem->query['id'])
                && $query['id'] != $menuItem->query['id'])
        ) {
            $menuItemGiven = false;
            unset($query['Itemid']);

            // its edit view, lets return raw edit url
            return $segments;
        }

        if (
            $menuItemGiven && is_object($menuItem)
            && isset($menuItem->query['view'])
            && $menuItem->query['view'] == $query['view']
            && $menuItem->query['id'] == $query['id']
        ) {
            unset($query['id']);
        }

        // Page
        if (($view == 'page')) {
            if (isset($query['id']) && $query['id']) {
                $id         = $this->getPageSegment($query['id']);
                $segments[] = str_replace(':', '-', $id);
                unset($query['id']);
            }

            unset($query['view']);
        }

        if (($view == 'collection')) {
            $segments[] = $query['view'];

            if (isset($query['id']) && $query['id']) {
                $id = $this->getCollectionSegment($query['id']);
                // $segments[] = 'collection';
                $segments[] = str_replace(':', '-', $id);
                unset($query['id']);
            }

            unset($query['view']);
        }

        // Form
        if (($view == 'form')) {
            if (isset($query['id']) && $query['id']) {
                if (isset($query['type']) && $query['type'] == 'collection') {
                    $segments[] = 'collection';
                    unset($query['type']);
                    $id = $this->getCollectionSegment($query['id']);
                } else {
                    $id = $this->getPageSegment($query['id']);
                }

                if ( ! $menuItemGiven) {
                    $segments[] = str_replace(':', '-', $id);
                }
                unset($query['id']);
            } else {
                unset($query['id']);
            }

            if (isset($query['layout']) && $query['layout']) {
                $segments[] = $query['layout'];
                unset($query['layout']);
            }

            if (isset($query['tmpl']) && $query['tmpl']) {
                unset($query['tmpl']);
            }

            unset($query['view']);
        }

        return $segments;
    }

    /**
     * Parse method for URLs
     * This method is meant to transform the human-readable URL back into
     * query parameters. It is only executed when SEF mode is switched on.
     *
     * @param  array  &$segments  The segments of the URL to parse.
     *
     * @return  array  The URL attributes to be used by the application.
     *
     * @since   3.3
     */
    public function parse(&$segments, &$vars)
    {
        $app   = JFactory::getApplication();
        $menu  = $app->getMenu();
        $item  = $menu->getActive();
        $total = count($segments);
        // $view  = (isset($item->query['view']) && $item->query['view']) ? $item->query['view'] : '';
        $view = $item->query['view'] ?? '';


        if ( ! $view) {
            if ($total > 2 && $segments[0] == 'collection') {
                $vars['view'] = 'form';
                $vars['type'] = 'collection';
                $vars['id']   = (int) $segments[1];

                if (isset($segments[2]) && $segments[2] == 'edit') {
                    $vars['layout'] = $segments[2];
                    $vars['tmpl']   = 'component';
                }

                return $vars;
            } elseif ($total == 2 && $segments[0] == 'collection') {
                $vars['view'] = 'collection';
                $vars['id']   = (int) $segments[1];
            } else {
                $view = 'page';
            }
        }

        if ($total == 1 && $segments[0] == 'edit') {
            // $id             = (isset($item->query['id']) && $item->query['id'])  ? $item->query['id'] : 0;
            $id             = $item->query['id'] ?? 0;
            $view           = 'form';
            $vars['view']   = 'form';
            $vars['id']     = $id;
            $vars['tmpl']   = 'component';
            $vars['layout'] = 'edit';

            return $vars;
        }

        if ($total === 3 && $segments[2] === 'edit') {
            $view           = 'form';
            $vars['view']   = 'form';
            $vars['type']   = $segments[0] === 'collection' ? 'collection' : '';
            $vars['id']     = $segments[1];
            $vars['tmpl']   = 'component';
            $vars['layout'] = 'edit';

            return $vars;
        }

        if ($view == 'page') {
            if ($total == 2) {

                if ($segments[1] == 'edit') {
                    $vars['view']   = 'form';
                    $vars['id']     = (int) $segments[0];
                    $vars['tmpl']   = 'component';
                    $vars['layout'] = 'edit';
                } else {
                    $vars['view'] = 'page';
                    $vars['id']   = (int) $segments[0];
                }
            }

            if ($total == 1) {
                $vars['view'] = 'page';
                $vars['id']   = (int) (isset($segments[0]) ? $segments[0] : 0);
            }
        } else {
            if ($view == 'form') {
                if (isset($item->id)) {
                    $vars['id'] = $item->query['id'];
                }
            }
        }


        // dd($segments, $vars);
        return $vars;
    }

    private static function getPageSegment($id)
    {
        if ( ! strpos($id, ':')) {
            $db      = JFactory::getDbo();
            $dbquery = $db->getQuery(true);
            $dbquery->select($dbquery->qn('title'))
                    ->from($dbquery->qn('#__quix'))
                    ->where('id = '.$dbquery->q($id));
            $db->setQuery($dbquery);

            $id .= ':'.JFilterOutput::stringURLSafe($db->loadResult());
        }

        return $id;
    }

    private static function getCollectionSegment($id)
    {
        if ( ! strpos($id, ':')) {
            $db      = JFactory::getDbo();
            $dbquery = $db->getQuery(true);
            $dbquery->select($dbquery->qn('title'))
                    ->from($dbquery->qn('#__quix_collections'))
                    ->where('id = '.$dbquery->q($id));
            $db->setQuery($dbquery);

            $id .= ':'.JFilterOutput::stringURLSafe($db->loadResult());
        }

        return $id;
    }


}
