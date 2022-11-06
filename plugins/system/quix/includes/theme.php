<?php
/**
 * @package    Quix
 * @author     ThemeXpert http://www.themexpert.com
 * @copyright  Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 * @since      1.0.0
 */

defined('_JEXEC') or die;

class QuixSystemHelperTheme
{
    public static $loadableItems = [];

    public function addHtml($html, $position = 'before')
    {
        // check if theme has header
        if ($html) {
            $app    = JFactory::getApplication();
            $buffer = null;
            if ($position === 'before') {
                $buffer  = $app->getBody();
                $pattern = "/<\/?body+((\s+(\w|\w[\w-]*\w)(\s*=\s*(?:\".*?\"|'.*?'|[^'\">\s]+))?)+\s*|\s*)\/?>/";
                preg_match($pattern, $buffer, $match);
                $buffer = str_replace($match[0], $match[0].$html, $buffer);
            } elseif ($position === 'after') {
                $buffer = $app->getBody();
                $buffer = str_replace('</body>', $html.'</body>', $buffer);
            }

            if ($buffer !== null) {
                $app->setBody($buffer);
            }

        }
    }

    /**
     * @param  string  $type
     *
     * @return string
     * @since 3.0.0
     */
    public function getItem(string $type): string
    {
        $items = QuixFrontendHelperTheme::getByType($type);
        if ($items === null) {
            return '';
        }

        $html       = [];
        $canProceed = [];

        foreach ($items as $item) {
            $item_status = (int) $item->item_status;
            if ($item_status !== 1) {
                continue;
            }
            if ( ! isset($canProceed[$item->item_id]) || $canProceed[$item->item_id] === true) {
                $canProceed[$item->item_id] = $this->checkCondition($item);
            }
        }
        foreach ($canProceed as $headerId => $value) {

            if ( ! $value) {
                continue;
            }

            $collection = QuixAppHelper::qxGetCollectionInfoById($headerId);

            QuixAppHelper::renderQuixInstance($collection);

            $html[] = $collection->text;
        }

        return implode('', $html);
    }

    public function checkItem($type): array
    {
        $items = QuixFrontendHelperTheme::getByType($type);
        if ($items === null) {
            return [];
        }

        $canProceed = [];
        foreach ($items as $item) {
            $item_status = (int) $item->item_status;
            if ($item_status !== 1) {
                continue;
            }
            $canProceed[$item->item_id] = $this->checkCondition($item);
        }

        // Filtering the array
        return array_filter($canProceed);
    }

    public function checkCondition($condition): bool
    {
        $params        = json_decode($condition->params);
        $typeCondition = $params->typeCondition ?? false;
        $app           = JFactory::getApplication();
        $canProceed    = false;

        if ($condition->condition_type === 'all-menu') {
            $canProceed = $typeCondition === 'include';
        } elseif ($condition->condition_type === 'menus') {
            $condition_id = (int) $condition->condition_id;
            $itemId       = $app->input->get('Itemid', '', 'int');

            if ($itemId === $condition_id) {
                $canProceed = $typeCondition === 'include';
            } else {
                $canProceed = $typeCondition !== 'include';
            }
        }

        return $canProceed;
    }

    public function removeTemplateBlocks($type = 'header')
    {

        $config              = JComponentHelper::getComponent('com_quix')->params;
        $header_auto_replace = $config->get('header_auto_replace', 1);
        if ( ! $header_auto_replace) {
            return true;
        }

        $app    = JFactory::getApplication();
        $buffer = $app->getBody();

        $buffer = $this->sanitizeOutput($buffer, $type);
        $app->setBody($buffer);
    }

    /**
     * @param $buffer
     * @param $type
     *
     * @return false|string
     * @since 3.0.0
     */
    public function sanitizeOutput($buffer, $type)
    {

        $doc = new DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML($buffer, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $xpath = new DOMXpath($doc);

        if ($type === 'header') {
            // example 1: for everything with an id
            $selectors = [
                "//header[@class='header']",
                "//nav[@class='navigation']",
                "//div[@id='t4-header']",
                "//div[@id='t4-mainnav']",
                "//header[@id='t3-header']",
                "//nav[@id='t3-mainnav']",
                "//header[@id='sp-header']",
                "//section[@id='g-navigation']",
            ];
            $elements  = $xpath->query(implode('|', $selectors));

            /** @var \DOMNodeList $elements */
            /** @var \DOMNode $element */
            if ($elements->length > 0) {
                foreach ($elements as $element) {
                    $element->parentNode->removeChild($element);
                }

                return $xpath->document->saveHTML();
            }

        }

        if ($type === 'footer') {
            // example 1: for everything with an id
            $selectors = [
                "//footer[@class='footer']",
                "//footer[@id='t3-footer']",
                "//div[@id='t4-footer']",
                "//footer[@id='sp-footer']",
                "//footer[@id='g-footer']",
            ];
            $elements  = $xpath->query(implode('|', $selectors));

            /** @var \DOMNodeList $elements */
            /** @var \DOMNode $element */
            if ($elements->length > 0) {
                foreach ($elements as $element) {
                    $element->parentNode->removeChild($element);
                }

                return $xpath->document->saveHTML();
            }
        }

        return $buffer;
    }
}
