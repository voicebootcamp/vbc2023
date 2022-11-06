<?php
/**
 * @package     Quix
 * @author      ThemeXpert http://www.themexpert.com
 * @copyright   Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @since       3.0.0
 */

defined('_JEXEC') or die;

/**
 * QuixSystemHelperEditor
 * Checking if context is set to quix view
 * ThemeBuilder concept: Quix Layout : Article, Digicom Product
 * @since 3.0.0
 */
class QuixSystemHelperEditor
{
    public function afterRoute()
    {
        $app = JFactory::getApplication();
        if ($app->isClient('site')) {
            $option = $app->input->get('option', '');
            $view = $app->input->get('view', '');

            if ($option === 'com_content' && $view === 'article') {
                $id = $app->input->get('id');

                // TODO: check for quix page
                $getMatch = QuixFrontendHelperTheme::getAllTypesMatch('article', 'com_content', 'article');
                $canMove = false;
                if ($getMatch && isset($getMatch->id) && $getMatch->condition_id === 0) {
                    $collection = QuixAppHelper::qxGetCollectionInfoById($getMatch->item_id);
                    if (!empty($collection) && $collection->state) {
                        $canMove = true;
                    }
                }

                if ($canMove) {
                    $app->input->set('option', 'com_quix');
                    $app->input->set('view', 'collection');
                    $app->input->set('id', $getMatch->item_id);
                    $app->input->set('content_id', $id);
                }
            }
        }
    }
}
