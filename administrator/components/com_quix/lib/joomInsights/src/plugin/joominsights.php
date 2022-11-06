<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Content.joomla
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Example Content Plugin
 *
 * @since  1.6
 */
class PlgSystemJoomInsights extends JPlugin
{
    /**
     * Application object.
     *
     * @var    JApplicationCms
     * @since  3.2
     */
    protected $app;
    protected $autoloadLanguage = true;

    /**
     * Remember me method to run onAfterInitialise
     * Only purpose is to initialise the login authentication process if a cookie is present
     *
     * @return  void
     *
     * @since   1.5
     * @throws  InvalidArgumentException
     */
    public function onAfterInitialise()
    {
        // Get the application if not done by JPlugin. This may happen during upgrades from Joomla 2.5.
        if (!$this->app->isClient('administrator') || !$this->isAllowedUser()) {
            return;
        }

        if (
            $this->app->input->getMethod() != 'GET' or
            $this->app->input->get('tmpl') == 'raw' or
            $this->app->input->get('layout') == 'edit' or
            $this->app->input->get('layout') == 'modal' or
            $this->app->input->get('tmpl') == 'component' or
            JFactory::getDocument()->getType() !== 'html'
        ) {
            return;
        }

        // make sure, db table exist
        $this->prepareDatabase();

        // go for another system event
        // prepare generic permission
        $this->getThePermissionForCollectionStats();

        // prepare for installaer view
        // $this->prepareInstallerScript();
    }

    /**
     * handle the uninstall/disable event
     * send data to system
     *
     * @return  void
     *
     * @since   1.1
     * @throws  html
     */
    public function getThePermissionForCollectionStats()
    {
        $registeredExtensions = $this->getRegisteredIds(false);
        
        if (!count($registeredExtensions)) {
            return;
        }
        
        foreach ($registeredExtensions as $key => $item) {
            JFactory::getLanguage()->load($item->name);
            JText::script($item->name);
        }
        JFactory::getDocument()->addScriptDeclaration('
            window.JoomInsightsExtIds = ' . json_encode($registeredExtensions) . ';
        ');

        JHtml::_('jquery.framework');
        JHtml::_('script', 'plg_system_joominsights/joominsights.js', ['version' => 'auto', 'relative' => true]);
        // now
        // 1. prepare js permission loop
        // 2. cancel and accept event
        // for quix, from system plugin, force send data to api using ajax
    }

    /**
     * handle the uninstall/disable event
     * send data to system
     *
     * @return  void
     *
     * @since   1.1
     * @throws  html
     */
    public function prepareInstallerScript($client)
    {
        if ($this->app->input->get('option', '') == 'com_installer' && $this->app->input->get('view', '') == 'manage') {
            $registeredExtensionsId = $this->getRegisteredIds();
            if (!count($registeredExtensionsId)) {
                return;
            }
            $extensionsIds = [];
            foreach ($registeredExtensionsId as $key => $item) {
                $extensionsIds[] = $item->extensionid;
            }
            JFactory::getDocument()->addScriptDeclaration('
				window.extensionsIds = ' . json_encode($extensionsIds) . ';
			');

            JHtml::_('script', 'plg_system_joominsights/joominsights_manage.js', ['version' => 'auto', 'relative' => true]);
            JHtml::_('stylesheet', 'plg_system_joominsights/joominsights.css', ['version' => 'auto', 'relative' => true]);

            $this->deactivate_scripts();
        }
    }

    /**
     * Ask for permission
     * send data to system
     *
     * @return  void
     *
     * @since   1.1
     * @throws  html
     */
    public function onJoomInsightsAfterInstall($insights)
    {
        // all insights object is here
        // auto send the data to server
        // client is forcing
        
    }

    /**
     * Send the stats to the server.
     * On first load | on demand mode it will show a message asking users to select mode.
     *
     * @return  void
     *
     * @since   3.5
     *
     * @throws  Exception         If user is not allowed.
     * @throws  RuntimeException  If there is an error saving the params or sending the data.
     */
    public function onAjaxJoomInsightsRequestStats()
    {
        if (!$this->isAllowedUser() || !$this->isAjaxRequest()) {
            throw new Exception(JText::_('JGLOBAL_AUTH_ACCESS_DENIED'), 403);
        }

        $data = [
            'sent' => 0,
            'html' => $this->getRenderer('message')->render($this->getLayoutData())
        ];

        echo json_encode($data);

        return;
    }

    /**
     * User selected to always send data
     *
     * @return  void
     *
     * @since   3.5
     *
     * @throws  Exception         If user is not allowed.
     * @throws  RuntimeException  If there is an error saving the params or sending the data.
     */
    public function onAjaxJoomInsightsSendAlways()
    {
        // if (!$this->isAllowedUser() || !$this->isAjaxRequest()) {
        //     throw new Exception(JText::_('JGLOBAL_AUTH_ACCESS_DENIED'), 403);
        // }
        
        // update db record
        $updateRecord = $this->updatePermission();
        if (!$updateRecord) {
            throw new RuntimeException('Unable to save plugin settings', 500);
        }
        
        // client api call
        $this->sendStats($updateRecord);

        echo json_encode(['sent' => 1]);
    }

    /**
     * createTable
     *
     * @return  void
     */
    public function updatePermission()
    {
        // Create an object for the record we are going to update.
        $db = \JFactory::getDbo();
        $object = new \stdClass();
        $id = $this->app->input->get('joominsightsid', '', 'int');
        
        // Must be a valid primary key value.
        $object->id = $id;
        $object->status = 1;
        
        // Update their details in the users table using id as the primary key.
        $db->updateObject('#__joominsights', $object, 'id');

        $query = $db->getQuery(true);
        $query->select('a.*')->from('#__joominsights as a');
        $query->where('a.id = ' . $id);
        $db->setQuery($query);
        return $db->loadObject();
    }

    /**
     * Check valid AJAX request
     *
     * @return  boolean
     *
     * @since   3.5
     */
    private function isAjaxRequest()
    {
        return strtolower($this->app->input->server->get('HTTP_X_REQUESTED_WITH', '')) === 'xmlhttprequest';
    }

    /**
     * Check if current user is allowed to send the data
     *
     * @return  boolean
     *
     * @since   3.5
     */
    private function isAllowedUser()
    {
        return JFactory::getUser()->authorise('core.admin');
    }

    /**
     * prepareDatabase
     *
     * @return  void
     */
    public function prepareDatabase()
    {
        //  check if table exist
        try {
            JTable::addIncludePath(__DIR__ . '/table');
            JTable::getInstance('JoomInsights', 'Table', []);
        } catch (\Throwable $th) {
            // seems table does not exist
            $this->_createTable();
        }

        return true;
    }

    /**
     * Render a layout of this plugin
     *
     * @param   string  $layoutId  Layout identifier
     * @param   array   $data      Optional data for the layout
     *
     * @return  string
     *
     * @since   3.5
     */
    public function render($layoutId, $data = [])
    {
        $data = array_merge($this->getLayoutData(), $data);

        return $this->getRenderer($layoutId)->render($data);
    }

    /**
     * createTable
     *
     * @return  void
     */
    private function _createTable()
    {
        $db = JFactory::getDbo();
        $sql = 'CREATE TABLE IF NOT EXISTS `#__joominsights` (
                `id` smallint(6) NOT NULL AUTO_INCREMENT,
                `extensionid` int(11) NOT NULL,
                `name` varchar(255) NOT NULL,
                `type` tinytext NOT NULL,
                `path` varchar(255) NOT NULL,
                `hash` varchar(255) NOT NULL,
                `status` int(11) NOT NULL,
                `lastask` date NOT NULL,
                `created` date NOT NULL,
                `params` varchar(255) NOT NULL,
                PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
        $db->setQuery($sql);
        $db->execute();
    }

    /**
     * createTable
     *
     * @return  void
     */
    public function getRegisteredIds($status = true)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('a.*')->from('#__joominsights as a');
        if ($status) {
            $query->where('status = 1');
        } else {
            $query->where('status = 0');
        }
        $db->setQuery($query);
        return $db->loadObjectList();
    }

    /**
     * Get the data that will be sent to the stats server.
     *
     * @return  array
     *
     * @since   3.5
     */
    private function getStatsData()
    {
        $this->db = JFactory::getDBo();
        return [
            'php_version' => PHP_VERSION,
            'db_type' => $this->db->name,
            'db_version' => $this->db->getVersion(),
            'cms_version' => JVERSION,
            'server_os' => php_uname('s') . ' ' . php_uname('r'),
            'url' => JUri::root(),
            'site' => $this->app->getCfg('sitename'),
            'admin_email' => $this->app->getCfg('mailfrom'),
            'first_name' => $this->app->getCfg('fromname'),
            'users' => $this->app->getCfg('sitename'),
            'ip_address' => $this->app->getCfg('sitename'),
            'template' => $this->app->getCfg('sitename'),
            'version' => $this->app->getCfg('sitename'),
        ];
    }

    /**
     * Get the data for the layout
     *
     * @return  array
     *
     * @since   3.5
     */
    protected function getLayoutData()
    {
        return [
            'plugin' => $this,
            'pluginParams' => $this->params,
            'statsData' => $this->getStatsData()
        ];
    }

    /**
     * Get the layout paths
     *
     * @return  array
     *
     * @since   3.5
     */
    protected function getLayoutPaths()
    {
        $template = JFactory::getApplication()->getTemplate();

        return [
            JPATH_ADMINISTRATOR . '/templates/' . $template . '/html/layouts/plugins/' . $this->_type . '/' . $this->_name,
            __DIR__ . '/layouts',
        ];
    }

    /**
     * Get the plugin renderer
     *
     * @param   string  $layoutId  Layout identifier
     *
     * @return  JLayout
     *
     * @since   3.5
     */
    protected function getRenderer($layoutId = 'default')
    {
        $renderer = new JLayoutFile($layoutId);

        $renderer->setIncludePaths($this->getLayoutPaths());

        return $renderer;
    }

    /**
     * Handle the plugin deactivation feedback
     *
     * @return void
     */
    public function deactivate_scripts()
    {
        $reasons = $this->get_uninstall_reasons();
        echo $this->getRenderer('reasons')->render(['reasons' => $reasons]);
    }
    /**
	 * Send the stats to the stats server
	 *
	 * @return  boolean
	 *
	 * @since   3.5
	 *
	 * @throws  RuntimeException  If there is an error sending the data.
	 */
	private function sendStats($updateRecord)
	{
        
        if( !JFile::exists( JPATH_ROOT . $updateRecord->path . '/Client.php') ) return; 

		if ( ! class_exists( 'JoomInsights\Client' ) ) {
			require_once JPATH_ROOT . $updateRecord->path . '/Client.php';
        }
        
        // init the client
        $client = new JoomInsights\Client($updateRecord->hash, $updateRecord->name, $updateRecord->type);
        
        // // Active post installation work, create/remove post installation message
        $metadata = [];
        $client->insights()
               ->add_extra($metadata)
               ->init(true);
        
        // send initial data, create site records, create a logs
        $client->insights()->send_tracking_data('install');        
    }

    private function get_uninstall_reasons()
    {
        $reasons = [
            [
                'id' => 'could-not-understand',
                'text' => 'I couldn\'t understand how to make it work',
                'type' => 'textarea',
                'placeholder' => 'Would you like us to assist you?'
            ],
            [
                'id' => 'found-better-plugin',
                'text' => 'I found a better plugin',
                'type' => 'text',
                'placeholder' => 'Which plugin?'
            ],
            [
                'id' => 'not-have-that-feature',
                'text' => 'The plugin is great, but I need specific feature that you don\'t support',
                'type' => 'textarea',
                'placeholder' => 'Could you tell us more about that feature?'
            ],
            [
                'id' => 'is-not-working',
                'text' => 'The plugin is not working',
                'type' => 'textarea',
                'placeholder' => 'Could you tell us a bit more whats not working?'
            ],
            [
                'id' => 'looking-for-other',
                'text' => 'It\'s not what I was looking for',
                'type' => '',
                'placeholder' => ''
            ],
            [
                'id' => 'did-not-work-as-expected',
                'text' => 'The plugin didn\'t work as expected',
                'type' => 'textarea',
                'placeholder' => 'What did you expect?'
            ],
            [
                'id' => 'other',
                'text' => 'Other',
                'type' => 'textarea',
                'placeholder' => 'Could you tell us a bit more?'
            ],
        ];

        return $reasons;
    }
}
