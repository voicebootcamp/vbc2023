<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_jmedia
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
include JPATH_LIBRARIES.'/jmedia/php-server/vendor/autoload.php';

/**
 * File JMedia Controller
 *
 * @since  1.6
 */
class JMediaControllerApi extends JControllerLegacy
{
    /**
     * all action hooks
     *
     * @return  void
     *
     * @since   1.5
     */
    public function action()
    {
        // Check for request forgeries
        $this->checkToken('request');

        $params = JComponentHelper::getParams('com_jmedia');

        if (DIRECTORY_SEPARATOR == '\\') {
            $base = str_replace(DIRECTORY_SEPARATOR, '\\\\', COM_JMEDIA_BASE);
        } else {
            $base = COM_JMEDIA_BASE;
        }

        $restrict_uploads         = $params->get('restrict_uploads', true);
        $upload_mime              = $params->get('upload_mime', ['image/jpeg', 'image/jpg', 'image/gif', 'image/png', 'image/bmp']);
        $upload_mime_custom       = $params->get('upload_mime_custom', '');
        $upload_mime_custom_array = explode(',', $upload_mime_custom);
        $mime_types               = array_merge($upload_mime, $upload_mime_custom_array);

        // check for first time opening
        $path    = $_POST['path'] ?? '/';
        $session = JFactory::getSession();
        if ($path != '/') {
            $session->set('JMEDIA_LAST_PATH', $path);
        }

        try {
            $config = [
                'root'     => $base,
                'fm_cache' => JPATH_ROOT.'/cache/',
                'uploads'  => [
                    'max_upload_size' => $params->get('upload_maxsize', 50),
                    'mime_check'      => $restrict_uploads ? true : false,
                    'allowed_types'   => $restrict_uploads ? $mime_types : ['.*']
                ]
            ];

            (new \ThemeXpert\FileManager\FileManager($config))->run();
        } catch (\Psr\Cache\InvalidArgumentException $e) {
            http_response_code(500);
            echo json_encode(['message' => $e->getMessage()]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['message' => $e->getMessage()]);
        }

        jexit();
    }

    /**
     * Update config API
     *
     * @return  void
     *
     * @since   1.0
     */
    public function updateconfig_x()
    {
        // Check for request forgeries
        $this->checkToken('request');

        // get input
        $input    = JFactory::getAppliation();
        $username = $input->get('username', '', 'string');
        $license  = $input->get('license', '', 'string');

        $params = new JRegistry;
        $params->loadString($data->params);

        $username = $params->get('username');
        $license  = $params->get('license');

        if ( ! empty($username) and ! empty($license)) {
            $db = JFactory::getDbo();

            $extra_query = 'username='.urlencode($username);
            $extra_query .= '&amp;key='.urlencode($license);

            $fields = [
                $db->quoteName('extra_query').'='.$db->quote($extra_query),
                $db->quoteName('last_check_timestamp').'=0'
            ];

            // 10014
            $query = $db->getQuery(true)
                        ->update($db->quoteName('#__update_sites'))
                        ->set($fields)
                        ->where($db->quoteName('name').'='.$db->quote('JMedia Update Site'));
            $db->setQuery($query);
            $db->execute();
        }

        $db    = JFactory::getDbo();
        $query = $db->getQuery(true)
                    ->select('*')
                    ->from('#__update_sites')
                    ->where($db->quoteName('name').' = '.$db->quote('JMedia Update Site'));

        $db->setQuery($query);
        $result = $db->loadObject();

        if ($result->extra_query) {
            $fields = [
                $db->quoteName('extra_query').'='.$db->quote($result->extra_query),
                $db->quoteName('last_check_timestamp').'=0'
            ];

            $query = $db->getQuery(true)
                        ->update($db->quoteName('#__update_sites'))
                        ->set($fields)
                        ->where($db->quoteName('name').'='.$db->quote('JMedia Pro Update Site'));
            $db->setQuery($query);
            $db->execute();
        }

        $cache = JFactory::getCache();
        $cache->cleanCache();
    }

    /**
     * Permission ACL
     *
     * @return  boolian
     *
     * @since   1.0
     */
    public function checkPermission()
    {
        $user = JFactory::getUser();

        http_response_code(500);
        echo json_encode(['message' => $e->getMessage()]);

        jexit();
    }

    /**
     * Fonts JSON
     *
     * @return  json
     *
     * @since   1.0
     */
    public function fontJSON(): json
    {
        // Check for request forgeries
        // $this->checkToken('request');

        $path  = JPATH_SITE.'/media/com_jmedia/json/qx-fonts.json';
        $fonts = file_get_contents($path);
        header('Content-Type: application/json');
        echo $fonts;
        jexit();
    }
}
