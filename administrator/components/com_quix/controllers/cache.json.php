<?php
/**
 * @package    com_quix
 * @author     ThemeXpert <info@themexpert.com>
 * @copyright  Copyright (C) 2015. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    1.0.0
 */
defined('_JEXEC') or die;
use Joomla\CMS\Language\Text as JText;
use QuixNxt\Utils\Icon;

/**
 * Cache control
 * we have cache when QUIXNXT_DEBUG is false
 * cache items for page: views, icons, images
 * cache items for builder: twig, element-schema, element-form
 *
 * QuixHelper::cachecleaner('com_quix');
 * QuixHelper::cachecleaner('mod_quix');
 * QuixHelper::cachecleaner('lib_quix');
 * QuixHelper::cachecleaner('quix-twig');
 *
 * Twig cache path: JPATH_CACHE.'/quix/templates'
 * element-schema, element-form cache path: $storagePath / json
 *
 * @since  3.0.0
 */
class QuixControllerCache extends JControllerLegacy
{
    private $storagePath = JPATH_ROOT.'/media/quixnxt/storage/';

    /**
     * Clean all icons cache
     *
     * @since 3.0.0
     */
    public function cleanBuilders()
    {
        try {
            $this->remove($this->storagePath.'json');

            $this->remove(dirname(JPATH_BASE).'/administrator/cache/quix');
            $this->remove(dirname(JPATH_BASE).'/administrator/cache/lib_quix');

            $this->remove(dirname(JPATH_BASE).'/cache/quix');
            $this->remove(dirname(JPATH_BASE).'/cache/lib_quix');

            echo new JResponseJson('', JText::_('Quix builder cache cleaned.'), false, true);
        } catch (Exception $e) {
            echo new JResponseJson($e);
        }

        jexit(0);
    }

    /**
     * Clean all icons cache
     *
     * @since 3.0.0
     */
    public function cleanIcons()
    {
        try {
            $this->remove($this->storagePath.'icons');
            echo new JResponseJson('', JText::_('Quix icons cache cleaned.'), false, true);
        } catch (Exception $e) {
            echo new JResponseJson($e);
        }

        sleep(2);

        new Icon('qxif-joomla');

        jexit(0);
    }

    /**
     * Clean all images cache
     *
     * @since 3.0.0
     */
    public function cleanImages()
    {
        try {
            $this->remove($this->storagePath.'images');

            echo new JResponseJson('', JText::_('Quix dynamic cache cleaned.'), false, true);
        } catch (Exception $e) {
            echo new JResponseJson($e);
        }

        jexit(0);
    }

    /**
     * Clean all pages cache
     *
     * @since 3.0.0
     */
    public function cleanPages()
    {
        try {
            $this->remove($this->storagePath.'views');
            echo new JResponseJson('', JText::_('Quix view cache cleaned.'), false, true);
        } catch (Exception $e) {
            echo new JResponseJson($e);
        }

        jexit(0);
    }

    /**
     * Do the remove operation
     *
     * @param  string  $path
     *
     * @return ?bool
     * @since 3.0.0
     */
    public function remove(string $path)
    {
        if (JFolder::exists($path)) {
            JFolder::delete($path);
        }

        $this->clearCommonCache();

        return true;
    }

    /**
     * Clear all common cache
     * include legacy folders call
     *
     * @since 3.0.0
     */
    public function clearCommonCache()
    {
        QuixHelper::cachecleaner('com_quix');
        QuixHelper::cachecleaner('mod_quix');
        QuixHelper::cachecleaner('lib_quix');
        QuixHelper::cachecleaner('quix-twig');
        QuixHelper::cachecleaner('quix');
    }

}
