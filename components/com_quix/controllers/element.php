<?php
/**
 * @version    3.0
 * @package    com_quix
 * @author     ThemeXpert <info@themexpert.com>
 * @copyright  Copyright (C) 2015. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

use QuixNxt\AssetManagers\ScriptManager;
use QuixNxt\AssetManagers\StyleManager;
use QuixNxt\Elements\ElementBag;
use QuixNxt\Elements\QuixElement;

defined('_JEXEC') or die;

/**
 * Handle App API request through one controller
 *
 * @since  3.0.0
 */
class QuixControllerElement extends JControllerLegacy
{
    /**
     * Get a list of elements with all the necessary information
     *
     * @since 3.0.0
     */
    public function getElements(): void
    {
        $app   = JFactory::getApplication();
        $input = $app->input;

        $elementBag = ElementBag::getInstance();
        $missing    = explode(',', $input->get('elements', '', 'string'));

        $m = memory_get_usage();

        $templates = [
            'elements' => [],
            'js'       => [],
            'css'       => [],
            'special'  => [
                'animation.twig' => file_get_contents(QuixElement::QUIX_VISUAL_BUILDER_PATH.'/../shared/animation.twig'),
                'global.twig'    => file_get_contents(QuixElement::QUIX_VISUAL_BUILDER_PATH.'/../shared/global.twig'),
            ],
        ];
        foreach ($missing as $slug) {
            $templates['elements'][$slug] = $elementBag->get($slug)->getTemplates();
        }

        $templates['js'] = ScriptManager::getInstance()->getUrls();
        $templates['css'] = StyleManager::getInstance()->getUrls();

        $memory = QuixAppHelper::formatBytes(memory_get_usage() - $m);

        echo new JResponseJson($templates, "Memory usage on generating templates: {$memory}");

        $app->close();
    }
}
