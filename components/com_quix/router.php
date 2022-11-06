<?php
/**
 * @package         Quix PageBuilder
 * @subpackage      com_quix
 * @author          ThemeXpert <info@themexpert.com>
 *
 * @copyright   (C) 2009 Open Source Matters, Inc. <https://www.joomla.org>
 * @license         GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

/**
 * Routing class from com_quix
 *
 * @since 4.1.12
 */
class QuixRouter extends JComponentRouterView
{
    protected $noIDs = false;

    /**
     * Users Component router constructor
     *
     * @param  JApplicationCms  $app  The application object
     * @param  JMenu  $menu           The menu object to work with
     *
     * @since 4.1.11
     */
    public function __construct($app = null, $menu = null)
    {
        $params      = JComponentHelper::getParams('com_quix');
        $this->noIDs = (bool) $params->get('sef_ids', 0);

        $page = new JComponentRouterViewconfiguration('page');
        $page->setKey('id');
        $this->registerView($page);

        $collection = new JComponentRouterViewconfiguration('collection');
        $collection->setKey('id');
        $this->registerView($collection);

        $form = new JComponentRouterViewconfiguration('form');
        $form->addLayout('edit');
        $form->setKey('id');
        $form->setKey('type');
        $this->registerView($form);


        parent::__construct($app, $menu);

        $this->attachRule(new JComponentRouterRulesMenu($this));


        // we are implementing new router.
        // for testing we are not using legacy router
        // $params->get('sef_advanced', 0)
        if ( JVERSION >= 4) {
            $this->attachRule(new JComponentRouterRulesStandard($this));
            $this->attachRule(new JComponentRouterRulesMenu($this));
            $this->attachRule(new JComponentRouterRulesNomenu($this));
        } else {
            JLoader::register('QuixRouterRulesLegacy', __DIR__.'/helpers/legacyrouter.php');
            $this->attachRule(new QuixRouterRulesLegacy($this));
        }
    }

    /**
     * Method to get the segment(s) for a contact
     *
     * @param  int|string  $id    ID of the contact to retrieve the segments for
     * @param  array  $query  The request that is built right now
     *
     * @return  array  The segments of this item
     * @since 4.1.11
     */
    public function getPageSegment($id, array $query)
    {
        if ( ! strpos($id, ':')) {
            $db      = JFactory::getDbo();
            $dbquery = $db->getQuery(true);
            $dbquery->select($dbquery->qn('id'))
                    ->from($dbquery->qn('#__quix'))
                    ->where('id = '.$dbquery->q((int) $id));
            $db->setQuery($dbquery);

            $id .= ':'.$db->loadResult();
        }

        if ($this->noIDs) {
            list($void, $segment) = explode(':', $id, 2);

            return array($void => $segment);
        }

        return array((int) $id => $id);
    }

    /**
     * Method to get the segment(s) for a contact
     *
     * @param  int|string  $id    ID of the contact to retrieve the segments for
     * @param  array  $query  The request that is built right now
     *
     * @return  array  The segments of this item
     * @since 4.1.11
     */
    public function getCollectionSegment($id, array $query)
    {
        if ( ! strpos($id, ':')) {
            $db      = JFactory::getDbo();
            $dbQuery = $db->getQuery(true);
            $dbQuery->select($dbQuery->qn('id'))
                    ->from($dbQuery->qn('#__quix_collection'))
                    ->where('id = '.$dbQuery->q((int) $id));
            $db->setQuery($dbQuery);

            $id .= ':'.$db->loadResult();
        }

        if ($this->noIDs) {
            list($void, $segment) = explode(':', $id, 2);

            return array($void => $segment);
        }

        return array((int) $id => $id);
    }
}

/**
 * Users router functions
 *
 * These functions are proxys for the new router interface
 * for old SEF extensions.
 *
 * @param  array  &$query  REQUEST query
 *
 * @return  array  Segments of the SEF url
 *
 * @throws \Exception
 * @deprecated  4.0  Use Class based routers instead
 * @since       4.1.11
 */
function QuixBuildRoute(array &$query): array
{
    $app    = JFactory::getApplication();
    $router = new QuixRouter($app, $app->getMenu());

    return $router->build($query);
}

/**
 * Convert SEF URL segments into query variables
 *
 * @param  array  $segments  Segments in the current URL
 *
 * @return  array  Query variables
 * @throws \Exception
 * @deprecated  4.0  Use Class based routers instead
 * @since       4.1.11
 */
function QuixParseRoute(array $segments): array
{
    $app    = JFactory::getApplication();
    $router = new QuixRouter($app, $app->getMenu());

    return $router->parse($segments);
}
