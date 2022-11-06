<?php

namespace JoomInsights;

/**
 * JoomInsights Insights
 *
 * This is a tracker class to track plugin usage based on if the customer has opted in.
 * No personal information is being tracked by this class, only general settings, active plugins, environment details
 * and admin email.
 *
 * @since 1.0.0
 */
class Insights
{
    /**
     * JoomInsights\Client
     *
     * @var object
     * @since 1.0.0
     */
    protected $client;

    /**
     * extra_data
     *
     * @var object
     * @since 1.0.0
     */
    protected $extra_data;

    /**
     * Initialize the class
     *
     * @param  Client
     *
     * @param  null  $slug
     *
     * @since 1.0.0
     */
    public function __construct(Client $client, $slug = null)
    {
        if (is_string($client) && ! empty($slug)) {
            $client = new Client($client, $slug, 'package');
        }

        if (is_object($client) && is_a($client, 'JoomInsights\Client')) {
            $this->client = $client;
        }
    }

    /**
     * Initialize insights
     *
     * @param  bool  $askPermission
     *
     * @return void
     * @since 1.0.0
     */
    public function init($askPermission = false)
    {
        if ($askPermission) {
            $dispatcher = \JDispatcher::getInstance();
            $dispatcher->trigger('onJoomInsightsAfterInstall', array($this));
        }
    }

    /**
     * Send tracking data to JoomInsights server
     *
     * @param  string  $type
     *
     * @return bool
     * @throws \Exception
     * @since 1.0.0
     */
    public function send_tracking_data($type = 'install')
    {
        if ( ! $this->tracking_allowed()) {
            return false;
        }

        // Send a maximum of once per week
        $last_send = $this->get_last_send();
        if ($last_send && $last_send > strtotime('-1 week')) {
            return false;
        }

        $this->client->send_request($this->get_tracking_data(), $type);

        return true;
    }

    /**
     * Get the tracking data points
     *
     * @return array
     * @throws \Exception
     * @since 1.0.0
     */
    public function get_tracking_data()
    {
        $app     = \JFactory::getApplication();
        $db      = \JFactory::getDBo();
        $name    = $app->get('fromname');
        $email   = $app->get('mailfrom');
        $version = new \JVersion;

        $languages       = \JLanguageHelper::getLanguages('lang_code');
        $isMultilingual = count($languages) > 1;

        $allLanguage = array_map(function ($item) {
            return $item->lang_code;
        }, $languages);

        $data = [
            'url'          => \JUri::root(),
            'site'         => $app->getCfg('sitename'),
            'admin_email'  => $email,
            'first_name'   => $name,
            'server'       => $this->get_server_info(),
            'users'        => $this->get_user_counts(),
            'extensions'   => $this->get_all_extensions(),
            'ip_address'   => $this->get_user_ip_address(),
            'template'     => $this->get_default_template(),
            'jversion'     => $version->getShortVersion(),
            'databasetype' => $db->name,
            'dbversion'    => $db->getVersion(),
            'locales'      => $allLanguage,
            'multilingual' => $isMultilingual
        ];

        // Add metadata
        if ($extra = $this->get_extra_data()) {
            $data['extra'] = $extra;
        }

        return $data;
    }

    /**
     * Add extra data if needed
     *
     * @param  array  $data
     *
     * @return \self
     * @since 1.0.0
     */
    public function add_extra($data = [])
    {
        $this->extra_data = $data;

        return $this;
    }

    /**
     * If a child class wants to send extra data
     *
     * @return mixed
     * @since 1.0.0
     */
    protected function get_extra_data()
    {
        $extra_data = $this->extra_data;

        if (is_callable($extra_data)) {
            return $extra_data();
        } elseif (is_array($extra_data)) {
            return $extra_data;
        }

        return [];
    }

    /**
     * Check if the user has opted into tracking
     * TODO: store config for tracking disabled
     *
     * @return bool
     * @since 1.0.0
     */
    private function tracking_allowed()
    {
        $allow_tracking = 'yes';

        return $allow_tracking == 'yes';
    }

    /**
     * Get the last time a tracking was sent
     *
     * @return false|string
     * @since 1.0.0
     */
    private function get_last_send()
    {
        // TODO: get client__tracking_last_send from storage
        return false;
    }

    /**
     * Check if the current server is localhost
     *
     * @return boolean
     * @since 1.0.0
     */
    private function is_local_server()
    {
        return in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']);
    }

    /**
     * Get server related info.
     *
     * @return array
     * @since 1.0.0
     */
    private static function get_server_info()
    {
        $server_data = [];

        if (isset($_SERVER['SERVER_SOFTWARE']) && ! empty($_SERVER['SERVER_SOFTWARE'])) {
            $server_data['software'] = $_SERVER['SERVER_SOFTWARE'];
        }

        if (function_exists('phpversion')) {
            $server_data['php_version'] = phpversion();
        }

        return $server_data;
    }

    /**
     * Get user totals based on user role.
     *
     * @return array
     * @throws \Exception
     * @since 1.0.0
     */
    public function get_user_counts()
    {
        // query count users
        $db    = \JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('count(*) as total')->from('#__users');
        $db->setQuery($query);

        return $db->loadObject()->total;
    }

    /**
     * Get user totals based on user role.
     *
     * @return array
     * @since 1.0.0
     */
    public function get_default_template()
    {
        // query count users
        $db    = \JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*')->from('#__template_styles')->where('client_id = 0')->where('home = 1');
        $db->setQuery($query);

        return $db->loadObject()->template;
    }

    /**
     * Get user totals based on user role.
     *
     * @return array
     * @since 1.0.0
     */
    public function get_all_extensions()
    {
        $db    = \JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*')->from('#__extensions');
        $db->setQuery($query);
        $getAllExtensions = $db->loadObjectList();
        $newList          = [];

        $cores    = \JExtensionHelper::getCoreExtensions();
        $coreComp = array_map(function ($item) {
            return $item[1];
        }, $cores);
        $coreComp = array_unique($coreComp);

        foreach ($getAllExtensions as $key => $extension) {
            $response = [];
            if (in_array($extension->element, $coreComp)) {
                continue;
            } else {
                $newList[] = $extension->element;
            }
        }
        $newList = array_unique($newList);

        return $newList;
    }

    /**
     * Get user IP Address
     *
     * @since 1.0.0
     */
    private function get_user_ip_address()
    {
        // Get the handler to download the blocks
        $httpOption = new \Joomla\Registry\Registry;
        $http       = \JHttpFactory::getHttp($httpOption);

        try {
            // request timeout limit to 2 sec, so we dont dead the server timeout
            $result = $http->get('https://icanhazip.com/', null, 2);
            if ($result->code != 200 && $result->code != 310) {
                return '';
            }
            $ip = trim($result->body);
            if ( ! filter_var($ip, FILTER_VALIDATE_IP)) {
                return '0.0.0.0';
            }

            return $ip;
        } catch (\Throwable $th) {
            return '0.0.0.0';
        }

    }
}
