<?php

/**
 * @copyright   Copyright (C) 2015 Ryan Demmer. All rights reserved
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved
 * @license     GNU General Public License version 2 or later
 */
defined('JPATH_BASE') or die;

/**
 * JCE.
 *
 * @since       2.5.5
 */
class PlgSystemJMedia extends JPlugin
{
    /**
     * Listener for the `onAfterRoute` event
     *
     * @return  void
     *
     * @since   1.0
     */
    public function onAfterRoute()
    {
        return true;
        
        $app    = JFactory::getApplication();
        $input  = $app->input;
        $params = JComponentHelper::getParams('com_jmedia');
        $replaceMedia = $params->get('replace_media_manager', true);
        if (
            $input->get('option', '', 'string') === 'com_media'
            && $replaceMedia == true
            && $input->get('format', '') !== 'json'
        ) {

            $input->set('option', 'com_jmedia');

            if ($input->get('tmpl') === 'component') {
                $input->set('view', 'images');
            }
        }

    }

    /**
     * adds additional fields to the user editing form.
     *
     * @param  JForm  $form  The form to be altered
     * @param  mixed  $data  The associated data for the form
     *
     * @return bool
     *
     * @throws \Exception
     * @since   2.5.20
     */
    public function onContentPrepareForm($form, $data)
    {

        return true;

        $version = new JVersion();

        if ( ! $version->isCompatible('3.4')) {
            return true;
        }

        if ( ! ($form instanceof JForm)) {
            $this->_subject->setError('JERROR_NOT_A_FORM');

            return false;
        }

        $params = JComponentHelper::getParams('com_jmedia');

        if ((bool) $params->get('replace_media_manager', true) === false) {
            return true;
        }

        // get form name.
        $name = $form->getName();

        if ( ! $version->isCompatible('3.6')) {
            $valid = [
                'com_content.article',
                'com_categories.category.com_content',
                'com_templates.style',
                'com_tags.tag',
                'com_banners.banner',
                'com_contact.contact',
                'com_newsfeeds.newsfeed',
            ];

            // only allow some forms, see - https://github.com/joomla/joomla-cms/pull/8657
            if ( ! in_array($name, $valid)) {
                return true;
            }
        }

        $hasMedia = false;
        try {
            $fields = $form->getFieldset();
            foreach ($fields as $field) {
                if (method_exists($field, 'getAttribute') === false) {
                    continue;
                }

                $name = $field->getAttribute('name');
                $type = $field->getAttribute('type');

                if ($name && (strtolower($type) === 'media' or strtolower($type) === 'mediajce')) {

                    // avoid processing twice
                    if (strpos($form->getFieldAttribute($name, 'class', 'none'), 'jm-media-input') !== false) {
                        continue;
                    }

                    $group = (string) $field->group;
                    $form->setFieldAttribute($name, 'type', 'jmedia', $group);
                    $form->setFieldAttribute($name, 'converted', '1', $group);
                    $hasMedia = true;
                }
            }

            // form has a converted media field
            if ($hasMedia && JVERSION < 4) {
                $form->addFieldPath(JPATH_PLUGINS.'/system/jmedia/fields');
            } elseif ($hasMedia && JVERSION >= 4) {
                $form->addFieldPath(JPATH_PLUGINS.'/system/jmedia/fields_j4');
            }

        } catch (Exception $e) {
            // return false;
            // nothing for now
            return true;
        }

        return true;
    }

    /**
     * After save content logging method
     * This method adds a record to #__action_logs contains (message, date, context, user)
     * Method is called right after the content is saved
     *
     * @param  string  $context  The context of the content passed to the plugin
     * @param $data
     *
     * @return  void
     *
     * @throws \Exception
     * @since   3.9.0
     */
    public function onExtensionAfterSave($context, $data)
    {
        $app       = JFactory::getApplication();
        $component = $app->input->get('component', '', 'string');

        if ('com_config.component' == $context and 'com_jmedia' == $component and ! empty($data->extension_id)) {
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
        }
    }
}
