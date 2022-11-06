<?php

namespace JoomInsights;

/**
 * JoomInsights Client
 *
 * This class is necessary to set project data
 *
 * @since 1.0.0
 */
class Setup
{
    /**
     * JoomInsights\Client
     *
     * @var object
     * @since 1.0.0
     */
    protected $client;

    /**
     * extension
     *
     * @since 1.0.0
     */
    public $extension;

    /**
     * status
     *
     * @since 1.0.0
     */
    public $status;

    /**
     * Initialize the class
     *
     * @since 1.0.0
     */
    public function __construct($client)
    {
        if (is_object($client) && is_a($client, 'JoomInsights\Client')) {
            $this->client = $client;
        }

        // check if plugin exist by checking file path
        // if not, install
        if ( ! \JFolder::exists(JPATH_SITE.'/plugins/system/joominsights')) {
            $this->installPlugin();
        }

        // then check if plugin enabled
        // if not then enable it
        if ( ! \JPluginHelper::isEnabled('system', 'joominsights')) {
            $this->enablePlugin();

            // then check if table #__joominsights exist
            // if not then create it
            // no need, plugin will handle it
            // $this->updateRecordDatabaseTable();
        }

        // now get extension info
        $this->extension = $this->getExtensionInfo();

        // now we have our plugin installed and enabled
        // create db record of not already
        $this->getDbRecord();

        // now we can trigger the permission
        $this->status = true;

        return $this;
    }

    /**
     * enablePlugin
     *
     * @since 1.0.0
     */
    public function updateRecordDatabaseTable()
    {
        $db  = \JFactory::getDbo();
        $sql = '
		CREATE TABLE IF NOT EXISTS `#__joominsights` (
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
          ) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;          
		';
        $db->setQuery($sql);
        $db->execute();
    }

    /**
     * enablePlugin
     *
     * @since 1.0.0
     */
    public function getExtensionInfo()
    {
        $db    = \JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*')->from('#__extensions')
              ->where($db->quoteName('element').' = '.$db->quote($this->client->slug))
              ->where($db->quoteName('type').' = '.$db->quote($this->client->type));
        $db->setQuery($query);

        return $db->loadObject();
    }

    /**
     * enablePlugin
     *
     * @since 1.0.0
     */
    public function installPlugin()
    {
        // Detect the package type
        $p_dir                  = __DIR__.'/plugin';
        $type                   = \JInstallerHelper::detectType($p_dir);
        $package                = [];
        $package['packagefile'] = null;
        $package['extractdir']  = null;
        $package['dir']         = $p_dir;
        $package['type']        = $type;

        // Get an installer instance.
        $installer = \JInstaller::getInstance();
        $installer->setPath('source', $package['dir']);

        return $installer->install($package['dir']);
    }

    /**
     * enablePlugin
     *
     * @since 1.0.0
     */
    public function enablePlugin()
    {
        // Get a table object for the extension type
        $table = \JTable::getInstance('Extension');
        $table->load([
            'type'    => 'plugin',
            'element' => 'joominsights',
            'folder'  => 'system'
        ]);

        $table->enabled = 1;

        return $table->store();
    }

    /**
     * updateDbRecord
     *
     * @since 1.0.0
     */
    public function getDbRecord()
    {
        $db    = \JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*')->from('#__joominsights')
              ->where($db->quoteName('extensionid').' = '.$db->quote($this->extension->extension_id));

        $db->setQuery($query);
        $info = $db->loadObject();
        if ( ! count($info)) {
            $this->createNewRecord();
        }

        return true;
    }

    /**
     * updateDbRecord
     *
     * @since 1.0.0
     */
    public function createNewRecord()
    {
        $db    = \JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*')->from('#__extensions')
              ->where($db->quoteName('element').' = '.$db->quote($this->client->slug))
              ->where($db->quoteName('type').' = '.$db->quote($this->client->type));

        $db->setQuery($query);
        $info = $db->loadObject();

        // if not then create
        $db   = \JFactory::getDbo();
        $date = \JFactory::getDate();
        $now  = $date->toSQL();
        // Create and populate an object.
        $obj              = new \stdClass();
        $obj->extensionid = $info->extension_id;
        $obj->name        = $info->element;
        $obj->type        = $this->client->type;
        $obj->hash        = $this->client->hash;
        $obj->path        = $this->client->path;
        $obj->status      = 0;
        $obj->lastask     = '';
        $obj->created     = $now;
        $obj->params      = '';

        // Insert the object into the user profile table.
        return \JFactory::getDbo()->insertObject('#__joominsights', $obj);
    }
}
