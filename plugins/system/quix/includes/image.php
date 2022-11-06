<?php
/**
 * @package     Quix
 * @author      ThemeXpert http://www.themexpert.com
 * @copyright   Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @since       1.0.0
 */

use QuixNxt\Utils\Image\Optimizer;
use Joomla\CMS\Exception\ExceptionHandler;


defined('_JEXEC') or die;

class QuixSystemHelperImage
{
    /**
     * @param  string  $path
     *
     * @throws \Exception
     * @since 3.0.0
     */
    public function process(string $path): void
    {
        if ( ! QUIXNXT_DEBUG) {
            header('Cache-Control: max-age=86400, public'); // 1 day
            header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + 86400));
        }

        header('Vary: Accept-Encoding, Version, Content-Type'); // for CDN to cache and invalidate

        $root   = JPATH_SITE.'/media/quixnxt/storage';
        $source = substr(JPATH_SITE.$path, strlen($root));
        // for images no jailbreak needed.
        // Asset::preventJailbreak($root, $source);

        preg_match('/^(?P<actual>.*)_(?P<size>[a-z0-9]+).(?P<ext>\w+)$/', $source, $matches);
        if ( ! $matches) {
            header('Status: 404 Not Found');
            exit(0);
        }

        $actual_source = ltrim("{$matches['actual']}.{$matches['ext']}", '/');
        $size          = $matches['size'];

        try {
            $optimizer = new Optimizer($actual_source);

            $optimizer->generate($size);
        } catch (Exception $e) {
            ExceptionHandler::render($e);
        }

        exit();
    }
}
