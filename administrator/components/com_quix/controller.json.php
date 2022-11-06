<?php
/**
 * @package    com_quix
 * @author     ThemeXpert <info@themexpert.com>
 * @copyright  Copyright (C) 2015. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    3.0.0
 */
// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text as JText;

/**
 * Class QuixController
 *
 * @since  1.0.0
 */
class QuixController extends JControllerLegacy
{
    /**
     * Set session from ajax request
     *
     * @return bool
     * @throws \Exception
     * @since 3.0.0
     */
    public function setSession(): bool
    {
        (JSession::checkToken('get') or JSession::checkToken()) or jexit(JText::_('JINVALID_TOKEN'));
        $input   = JFactory::getApplication()->input;
        $session = JFactory::getSession();

        $key   = $input->get('key', false, 'string');
        $value = $input->get('value', false, 'string');
        if ($key && $value) {
            $session->set($key, $value);
            echo new JResponseJson(sprintf('"key": "%s", "value": "%s"; session has been updated.', $key, $value));

            return true;
        }

        echo new JResponseJson(new Exception(sprintf('Key or Value not found!')));

        return true;
    }

    /**
     * Set session from ajax request
     *
     * @return bool
     * @throws \Exception
     * @since 3.0.0
     */
    public function setCookie(): bool
    {
        (JSession::checkToken('get') or JSession::checkToken()) or jexit(JText::_('JINVALID_TOKEN'));
        $input = JFactory::getApplication()->input;

        $key   = $input->get('key', false, 'string');
        $value = $input->get('value', false, 'string');

        if ($key && $value) {
            $input->cookie->set($key, $value);
            echo new JResponseJson(sprintf('"key": "%s", "value": "%s"; Cookie has been updated.', $key, $value));

            return true;
        }

        echo new JResponseJson(new Exception(sprintf('Key or Value not found!')));

        return true;
    }

    /**
     * Update Component params
     *
     * @return bool
     * @throws \Exception
     * @since 3.0.0
     */
    public function setComponentParams(): bool
    {
        (JSession::checkToken('get') or JSession::checkToken()) or jexit(JText::_('JINVALID_TOKEN'));
        $input = JFactory::getApplication()->input;

        $key   = $input->get('key', false, 'string');
        $value = $input->get('value', false);

        if ($key && $value) {
            $db         = JFactory::getDbo();
            $query      = $db->getQuery(true);
            $component  = JComponentHelper::getComponent('com_quix');
            $data       = $component->getParams()->toArray();
            $data[$key] = $value;

            // Conditions for which records should be updated.
            $conditions = array(
                $db->quoteName('extension_id').' = '.$component->id
            );
            // Fields to update.
            $json   = json_encode($data);
            $fields = array(
                $db->quoteName('params').' = '.$db->quote($json)
            );

            $query->update($db->quoteName('#__extensions'))->set($fields)->where($conditions);
            $db->setQuery($query);

            try {
                // Clear relevant cache
                QuixHelperCache::cachecleaner('com_config');

                $db->execute();

                echo new JResponseJson(sprintf('"key": "%s", "value": "%s"; Params has been updated.', $key, $value));

                return true;
            } catch (RuntimeException $e) {
                $this->setError($e->getMessage());
                echo new JResponseJson($e);

                return true;
            }

        }

        echo new JResponseJson(new Exception(sprintf('Key or Value not found!')));

        return true;
    }
}
