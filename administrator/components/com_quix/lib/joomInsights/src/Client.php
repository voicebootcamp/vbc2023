<?php

namespace JoomInsights;

/**
 * JoomInsights Client
 *
 * This class is necessary to set project data
 *
 * @since 1.0.0
 */
class Client
{
    /**
     * The client version
     *
     * @var string
     * @since 1.0.0
     */
    public $version = '1.0.0';

    /**
     * Hash identifier of the plugin
     *
     * @var string
     * @since 1.0.0
     */
    public $hash;

    /**
     * Slug of the plugin
     *
     * @example test-slug
     *
     * @var string
     * @since   1.0.0
     */
    public $slug;

    /**
     * type of the extension
     *
     * @example package
     *
     * @var string
     * @since   1.0.0
     */
    public $type;

    /**
     * The project version
     *
     * @var string
     * @since 1.0.0
     */
    public $project_version;

    /**
     * Initialize the class
     *
     * @param  string  $hash  hash of the plugin
     * @param $slug
     * @param $type
     *
     * @since 1.0.0
     */
    public function __construct($hash, $slug, $type)
    {
        $this->hash = $hash;
        $this->slug = $slug;
        $this->type = $type;

        $this->project_version = '1.0.0';

        $this->path = \str_replace(JPATH_ROOT, '', __DIR__);

        // setup system plugin
        // we will introduce it later
        // TODO: use it, its already done
        // $this->setup = $this->setup();
    }

    /**
     * Initialize insights class
     *
     * @return Setup
     * @since 1.0.0
     */
    public function setup()
    {
        if ( ! class_exists(__NAMESPACE__.'\Setup')) {
            require_once __DIR__.'/Setup.php';
        }

        $setup = new Setup($this);

        return $setup;
    }

    /**
     * Initialize insights class
     *
     * @return Insights
     * @since 1.0.0
     */
    public function insights()
    {
        if ( ! class_exists(__NAMESPACE__.'\Insights')) {
            require_once __DIR__.'/Insights.php';
        }

        return new Insights($this);
    }

    /**
     * API Endpoint
     *
     * @return string
     * @since 1.0.0
     */
    public function endpoint()
    {
        $endpoint = 'https://joominsights.com/japi.php';

        return rtrim($endpoint, '/\\');;
    }

    /**
     * Submit request to api server
     *
     * @param $params
     * @param $action
     *
     * @return bool True on success
     *
     * @since   1.0.0
     */
    public function send_request($params, $action)
    {
        $version    = new \JVersion;
        $httpOption = new \Joomla\Registry\Registry;

        $headers = [
            'user-agent' => 'JoomInsights;',
            'Accept'     => 'application/json',
            'Token'      => $this->hash,
            'Slug'       => $this->slug,
        ];

        try {
            $url  = $this->endpoint().'?action='.$action;
            $http = \JHttpFactory::getHttp($httpOption);

            // limit the request timeout to 2 sec, to avoid server timeout issue
            $response = $http->post($url, [
                'body'    => array_merge($params, ['client' => $this->version]),
                'cookies' => [],
                'Token'   => $this->hash,
                'slug'    => $this->slug,
            ], $headers, 2);

        } catch (\UnexpectedValueException $e) {
            // There was an error sending stats. Should we do anything?
            // throw new \RuntimeException('Could not send site statistics to remote server: ' . $e->getMessage(), 500);
            return true;

        } catch (\RuntimeException $e) {
            // There was an error connecting to the server or in the post request
            // throw new \RuntimeException('Could not connect to statistics server: ' . $e->getMessage(), 500);
            return true;

        } catch (\Exception $e) {
            // An unexpected error in processing; don't let this failure kill the site
            // throw new \RuntimeException('Unexpected error connecting to statistics server: ' . $e->getMessage(), 500);
            return true;

        }

        if ($response === null || $response->code !== 200) {
            // TODO: Add a 'mark bad' setting here somehow
            // \JLog::add(\JText::_('Could not send site statistics to remote server.'), \JLog::WARNING, 'jerror');
            return true;
        }

        return true;
    }
}
