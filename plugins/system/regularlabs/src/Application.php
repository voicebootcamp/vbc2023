<?php
/**
 * @package         Regular Labs Library
 * @version         22.10.1331
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            http://regularlabs.com
 * @copyright       Copyright © 2022 Regular Labs All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

namespace RegularLabs\Plugin\System\RegularLabs;

defined('_JEXEC') or die;

use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\Plugin\PluginHelper as JPluginHelper;
use RegularLabs\Library\Document as RL_Document;

class Application
{
    static function getThemesDirectory()
    {
        if (JFactory::getApplication()->get('themes.base'))
        {
            return JFactory::getApplication()->get('themes.base');
        }

        if (defined('JPATH_THEMES'))
        {
            return JPATH_THEMES;
        }

        if (defined('JPATH_BASE'))
        {
            return JPATH_BASE . '/themes';
        }

        return __DIR__ . '/themes';
    }

    public function render()
    {
        $app      = JFactory::getApplication();
        $document = RL_Document::get();
        $user     = $app->getIdentity() ?: JFactory::getUser();

        $app->loadDocument($document);

        $params = [
            'template'      => $app->get('theme'),
            'file'          => $app->get('themeFile', 'index.php'),
            'params'        => $app->get('themeParams'),
            'csp_nonce'     => $app->get('csp_nonce'),
            'themeInherits' => $app->get('themeInherits'),
            'directory'     => self::getThemesDirectory(),
        ];

        // Parse the document.
        $document->parse($params);

        // Trigger the onBeforeRender event.
        JPluginHelper::importPlugin('system');
        $app->triggerEvent('onBeforeRender');

        $caching = false;

        if ($app->isClient('site') && $app->get('caching') && $app->get('caching', 2) == 2 && ! $user->get('id'))
        {
            $caching = true;
        }

        // Render the document.
        $data = $document->render($caching, $params);

        // Set the application output data.
        $app->setBody($data);

        // Trigger the onAfterRender event.
        $app->triggerEvent('onAfterRender');

        // Mark afterRender in the profiler.
        // Causes issues, so commented out.
        // JDEBUG ? $app->profiler->mark('afterRender') : null;
    }
}
