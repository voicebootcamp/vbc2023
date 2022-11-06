<?php
/**
 * @package         Sourcerer
 * @version         9.3.0PRO
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            http://regularlabs.com
 * @copyright       Copyright © 2022 Regular Labs All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

namespace RegularLabs\Plugin\System\Sourcerer;

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text as JText;
use RegularLabs\Library\ArrayHelper as RL_Array;
use RegularLabs\Library\Document as RL_Document;
use RegularLabs\Library\Html as RL_Html;
use RegularLabs\Library\ObjectHelper as RL_Object;
use RegularLabs\Library\Php as RL_Php;
use RegularLabs\Library\PluginTag as RL_PluginTag;
use RegularLabs\Library\Protect as RL_Protect;
use RegularLabs\Library\RegEx as RL_RegEx;

class Replace
{
    static $article      = null;
    static $current_area = null;

    public static function replace(&$string, $area = 'article', $article = '', $remove = false)
    {
        if ( ! is_string($string) || $string == '')
        {
            return;
        }

        Protect::_($string);

        $regex = Params::getRegex();

        $array       = self::stringToSplitArray($string, $regex, false);
        $array_count = count($array);

        if ($array_count <= 1)
        {
            return;
        }

        self::$article = $article;

        for ($i = 1; $i < $array_count - 1; $i++)
        {
            if ( ! fmod($i, 2) || ! RL_RegEx::match($regex, $array[$i], $match))
            {
                continue;
            }

            $content = self::handleMatch($match, $area, $remove);

            $array[$i] = $match['start_pre'] . $match['start_post'] . $content . $match['end_pre'] . $match['end_post'];
        }

        $string = implode('', $array);
    }

    public static function replaceInTheRest(&$string)
    {
        if ( ! is_string($string) || $string == '')
        {
            return;
        }

        [$start_tags, $end_tags] = Params::getTags();

        [$pre_string, $string, $post_string] = RL_Html::getContentContainingSearches(
            $string,
            $start_tags,
            $end_tags
        );

        if ($string == '')
        {
            $string = $pre_string . $string . $post_string;

            return;
        }

        // COMPONENT
        if (RL_Document::isFeed())
        {
            $string = RL_RegEx::replace('(<item[^>]*>)', '\1<!-- START: SRC_COMPONENT -->', $string);
            $string = str_replace('</item>', '<!-- END: SRC_COMPONENT --></item>', $string);
        }

        if (strpos($string, '<!-- START: SRC_COMPONENT -->') === false)
        {
            Area::tag($string, 'component');
        }

        $components = Area::get($string, 'component');
        foreach ($components as $component)
        {
            self::replace($component[1], 'components', '');
            $string = str_replace($component[0], $component[1], $string);
        }

        // EVERYWHERE
        self::replace($string, 'other');

        $string = $pre_string . $string . $post_string;
    }

    private static function cleanTags(&$string)
    {
        $params = Params::get();

        foreach ($params->html_tags_syntax as $html_tags_syntax)
        {
            [$start, $end] = $html_tags_syntax;

            $tag_regex = $start . '\s*(\/?\s*[a-z\!][^' . $end . ']*?(?:\s+.*?)?)' . $end;
            $string    = RL_RegEx::replace($tag_regex, '<\1\2>', $string);
        }
    }

    private static function convertWysiwygToPlainText($content)
    {
        $content = RL_Html::convertWysiwygToPlainText($content);

        // Remove trailing spaces from EOT lines
        $content = RL_RegEx::replace('(=\s*<<<([^\s]+)) ?(\n.*?\2;) ?', '\1\3', $content);

        return $content;
    }

    private static function getPhpFileCodeByType($file, $type)
    {
        if (empty($file))
        {
            return '';
        }

        if ( ! in_array($type, ['include', 'include_once', 'require', 'require_once']))
        {
            return '';
        }

        $lines = [];

        $files = RL_Array::toArray($file);

        $params = Params::get();

        foreach ($files as $file)
        {
            if (empty($file))
            {
                continue;
            }

            if (strpos($type, 'include') !== false && ! file_exists(JPATH_SITE . $params->include_path . $file))
            {
                continue;
            }

            $lines[] = $type . ' JPATH_SITE . \'' . $params->include_path . $file . '\';';
        }

        return trim(implode("\n", $lines));
    }

    private static function getPhpFilesCode($data)
    {
        $lines = [];

        // Add the php file if include=... is used in the {source} tag
        $file    = RL_Object::getValue($data, ['include', 'file', 'php']);
        $lines[] = self::getPhpFileCodeByType($file, 'include');

        // Add the php file if include_once=... is used in the {source} tag
        $file    = RL_Object::getValue($data, ['include_once']);
        $lines[] = self::getPhpFileCodeByType($file, 'include_once');

        // Add the php file if require=... is used in the {source} tag
        $file    = RL_Object::getValue($data, ['require']);
        $lines[] = self::getPhpFileCodeByType($file, 'require');

        // Add the php file if require_once=... is used in the {source} tag
        $file    = RL_Object::getValue($data, ['require_once']);
        $lines[] = self::getPhpFileCodeByType($file, 'require_once');

        $lines = trim(implode("\n", $lines));

        return ! empty($lines) ? "<?php\n" . $lines . "\n?>" : '';
    }

    private static function handleMatch(&$match, $area = 'article', $remove = false)
    {
        if ($remove)
        {
            return '';
        }

        $params = Params::get();

        $data = RL_PluginTag::getAttributesFromString($match['data']);

        $content = trim($match['content']);

        $data->raw ??= false;

        // Remove html tags if code is placed via the WYSIWYG editor
        if ( ! $data->raw)
        {
            $content = self::convertWysiwygToPlainText($content);
        }

        self::replacePhpShortCodes($content);

        self::loadFiles($data, $content);

        self::replaceTags($content, $area);

        if ($data->raw)
        {
            return $content;
        }

        $trim = $data->trim ?? $params->trim;

        if ($trim)
        {
            $tags = RL_Html::cleanSurroundingTags([
                'start_pre'  => $match['start_pre'],
                'start_post' => $match['start_post'],
            ], ['div', 'p', 'span']);

            $match = array_merge($match, $tags);

            $tags = RL_Html::cleanSurroundingTags([
                'end_pre'  => $match['end_pre'],
                'end_post' => $match['end_post'],
            ], ['div', 'p', 'span']);

            $match = array_merge($match, $tags);

            $tags = RL_Html::cleanSurroundingTags([
                'start_pre' => $match['start_pre'],
                'end_post'  => $match['end_post'],
            ], ['div', 'p', 'span']);

            $match = array_merge($match, $tags);
        }

        return $content;
    }

    private static function loadFiles($data, &$content)
    {
        // Load the css file if the css attribute is used in the {source} tag
        self::loadStylesheets($data);

        // Load the js file if the js attribute is used in the {source} tag
        self::loadScripts($data);

        // Add the php file if the include/include_once/require/require_once attribute is used in the {source} tag
        $content = self::getPhpFilesCode($data)
            . $content;
    }

    private static function loadMediaFile($file, $type, $options = [])
    {
        if (empty($file))
        {
            return;
        }

        $files = RL_Array::toArray($file);

        foreach ($files as $file)
        {
            if (empty($file))
            {
                continue;
            }

            if (strpos($file, '//') === false)
            {
                $file = trim($file, ' /');
            }

            switch ($type)
            {
                case 'javascript':
                case 'js':
                    RL_Document::script($file, $options, [], false);
                    break;

                case 'css':
                case 'stylesheet':
                default:
                    RL_Document::style($file, $options, false);
                    break;
            }
        }
    }

    private static function loadScripts($data)
    {
        // Load the js file if js=... is used in the {source} tag
        $file  = RL_Object::getValue($data, ['js', 'javascript', 'script']);
        $defer = RL_Object::getValue($data, ['defer'], false);
        $async = RL_Object::getValue($data, ['async'], false);

        self::loadMediaFile($file, 'js', ['defer' => $defer, 'async' => $async]);
    }

    private static function loadStylesheets($data)
    {
        $file = RL_Object::getValue($data, ['css', 'style', 'stylesheet']);

        self::loadMediaFile($file, 'css');
    }

    private static function replacePhpShortCodes(&$string)
    {
        // Replace <? with <?php
        $string = RL_RegEx::replace('<\?(\s.*?)\?>', '<?php\1?>', $string);
        // Replace <?= with <?php echo
        $string = RL_RegEx::replace('<\?=\s*(.*?)\?>', '<?php echo \1?>', $string);
    }

    private static function replaceTags(&$string, $area = 'article')
    {
        if ( ! is_string($string) || $string == '')
        {
            return;
        }

        // allow in component?
        if (RL_Protect::isRestrictedComponent(Params::get('components', []), $area))
        {
            Protect::protectTags($string);

            return;
        }

        self::replaceTagsByType($string, $area, 'php');
        self::replaceTagsByType($string, $area, 'all');
        self::replaceTagsByType($string, $area, 'js');
        self::replaceTagsByType($string, $area, 'css');
    }

    /**
     * Replace any html style tags by a comment tag if not permitted
     * Match: <...>
     */
    private static function replaceTagsAll(&$string, $enabled = true, $security_pass = true)
    {
        if ( ! is_string($string) || $string == '')
        {
            return;
        }

        if ( ! $enabled)
        {
            // replace source block content with HTML comment
            $string = Protect::getMessageCommentTag(JText::_('SRC_CODE_REMOVED_NOT_ENABLED'));

            return;
        }

        if ( ! $security_pass)
        {
            // replace source block content with HTML comment
            $string = Protect::getMessageCommentTag(JText::sprintf('SRC_CODE_REMOVED_SECURITY', ''));

            return;
        }

        self::cleanTags($string);

        $area = Params::getArea(self::$current_area);
        $forbidden_tags_array = explode(',', $area->forbidden_tags);
        RL_Array::clean($forbidden_tags_array);
        // remove the comment tag syntax from the array - they cannot be disabled
        $forbidden_tags_array = array_diff($forbidden_tags_array, ['!--']);
        // reindex the array
        $forbidden_tags_array = [...$forbidden_tags_array];

        $has_forbidden_tags = false;
        foreach ($forbidden_tags_array as $forbidden_tag)
        {
            if ( ! (strpos($string, '<' . $forbidden_tag) == false))
            {
                $has_forbidden_tags = true;
                break;
            }
        }

        if ( ! $has_forbidden_tags)
        {
            return;
        }

        // double tags
        $tag_regex = '<\s*([a-z\!][^>\s]*?)(?:\s+.*?)?>.*?</\1>';
        RL_RegEx::matchAll($tag_regex, $string, $matches);

        if ( ! empty($matches))
        {
            foreach ($matches as $match)
            {
                if ( ! in_array($match[1], $forbidden_tags_array))
                {
                    continue;
                }

                $tag    = Protect::getMessageCommentTag(JText::sprintf('SRC_TAG_REMOVED_FORBIDDEN', $match[1]));
                $string = str_replace($match[0], $tag, $string);
            }
        }

        // single tags
        $tag_regex = '<\s*([a-z\!][^>\s]*?)(?:\s+.*?)?>';
        RL_RegEx::matchAll($tag_regex, $string, $matches);

        if ( ! empty($matches))
        {
            foreach ($matches as $match)
            {
                if ( ! in_array($match[1], $forbidden_tags_array))
                {
                    continue;
                }

                $tag    = Protect::getMessageCommentTag(JText::sprintf('SRC_TAG_REMOVED_FORBIDDEN', $match[1]));
                $string = str_replace($match[0], $tag, $string);
            }
        }
    }

    private static function replaceTagsByType(&$string, $area = 'article', $type = 'all')
    {
        if ( ! is_string($string) || $string == '')
        {
            return;
        }

        $type_ext = '_' . $type;
        if ($type == 'all')
        {
            $type_ext = '';
        }

        $a             = Params::getArea($area);
        $security_pass = $a->{'security_pass' . $type_ext} ?? true;
        $enable = $a->{'enable' . $type_ext} ?? true;

        switch ($type)
        {
            case 'php':
                self::replaceTagsPHP($string, $enable, $security_pass);
                break;
            case 'js':
                self::replaceTagsJS($string, $enable, $security_pass);
                break;
            case 'css':
                self::replaceTagsCSS($string, $enable, $security_pass);
                break;
            default:
                self::replaceTagsAll($string, $enable, $security_pass);
                break;
        }
    }

    /**
     * Replace the CSS tags by a comment tag if not permitted
     */
    private static function replaceTagsCSS(&$string, $enabled = 1, $security_pass = 1)
    {
        if ( ! is_string($string) || $string == '')
        {
            return;
        }

        // quick check to see if i is necessary to do anything
        if ((strpos($string, 'style') === false) && (strpos($string, 'link') === false))
        {
            return;
        }

        // Match:
        // <script ...>...</script>
        $tag_regex =
            '(-start-' . '\s*style\s[^' . '-end-' . ']*?[^/]\s*' . '-end-'
            . '(.*?)'
            . '-start-' . '\s*\/\s*style\s*' . '-end-)';
        $arr       = self::stringToSplitArray($string, $tag_regex);
        $arr_count = count($arr);

        // Match:
        // <script ...>
        // single script tags are not xhtml compliant and should not occur, but just in case they do...
        if ($arr_count == 1)
        {
            $tag_regex = '(-start-' . '\s*link\s[^' . '-end-' . ']*?(rel="stylesheet"|type="text/css").*?' . '-end-)';
            $arr       = self::stringToSplitArray($string, $tag_regex);
            $arr_count = count($arr);
        }

        if ($arr_count <= 1)
        {
            return;
        }

        if ( ! $enabled)
        {
            // replace source block content with HTML comment
            $string = Protect::getMessageCommentTag(JText::sprintf('SRC_CODE_REMOVED_NOT_ALLOWED', JText::_('SRC_CSS')));

            return;
        }

        if ( ! $security_pass)
        {
            // replace source block content with HTML comment
            $string = Protect::getMessageCommentTag(JText::sprintf('SRC_CODE_REMOVED_SECURITY', JText::_('SRC_CSS')));

            return;
        }
    }

    /**
     * Replace the JavaScript tags by a comment tag if not permitted
     */
    private static function replaceTagsJS(&$string, $enabled = 1, $security_pass = 1)
    {
        if ( ! is_string($string) || $string == '')
        {
            return;
        }

        // quick check to see if i is necessary to do anything
        if ((strpos($string, 'script') === false))
        {
            return;
        }

        // Match:
        // <script ...>...</script>
        $tag_regex =
            '(-start-' . '\s*script\s[^' . '-end-' . ']*?[^/]\s*' . '-end-'
            . '(.*?)'
            . '-start-' . '\s*\/\s*script\s*' . '-end-)';
        $arr       = self::stringToSplitArray($string, $tag_regex);
        $arr_count = count($arr);

        // Match:
        // <script ...>
        // single script tags are not xhtml compliant and should not occur, but just incase they do...
        if ($arr_count == 1)
        {
            $tag_regex = '(-start-' . '\s*script\s.*?' . '-end-)';
            $arr       = self::stringToSplitArray($string, $tag_regex);
            $arr_count = count($arr);
        }

        if ($arr_count <= 1)
        {
            return;
        }

        if ( ! $enabled)
        {
            // replace source block content with HTML comment
            $string = Protect::getMessageCommentTag(JText::sprintf('SRC_CODE_REMOVED_NOT_ALLOWED', JText::_('SRC_JAVASCRIPT')));

            return;
        }

        if ( ! $security_pass)
        {
            // replace source block content with HTML comment
            $string = Protect::getMessageCommentTag(JText::sprintf('SRC_CODE_REMOVED_SECURITY', JText::_('SRC_JAVASCRIPT')));

            return;
        }
    }

    /**
     * Replace the PHP tags with the evaluated PHP scripts
     * Or replace by a comment tag the PHP tags if not permitted
     */
    private static function replaceTagsPHP(&$string, $enabled = 1, $security_pass = 1)
    {
        if ( ! is_string($string) || $string == '')
        {
            return;
        }

        if ((strpos($string, '<?') === false) && (strpos($string, '[[?') === false))
        {
            return;
        }

        // Match ( read {} as <> ):
        // {?php ... ?}
        // {? ... ?}
        $string_array       = self::stringToSplitArray($string, '-start-' . '\?(?:php)?[\s<](.*?)\?' . '-end-');
        $string_array_count = count($string_array);

        if ($string_array_count < 1)
        {
            $string = implode('', $string_array);

            return;
        }

        if ( ! $enabled)
        {
            // replace source block content with HTML comment
            $string_array    = [];
            $string_array[0] = Protect::getMessageCommentTag(JText::sprintf('SRC_CODE_REMOVED_NOT_ALLOWED', JText::_('SRC_PHP')));

            $string = implode('', $string_array);

            return;
        }
        if ( ! $security_pass)
        {
            // replace source block content with HTML comment
            $string_array    = [];
            $string_array[0] = Protect::getMessageCommentTag(JText::sprintf('SRC_CODE_REMOVED_SECURITY', JText::_('SRC_PHP')));

            $string = implode('', $string_array);

            return;
        }

        // if source block content has more than 1 php block, combine them
        if ($string_array_count > 3)
        {
            for ($i = 2; $i < $string_array_count - 1; $i++)
            {
                if (fmod($i, 2) == 0)
                {
                    $string_array[1] .= "<!-- SRC_SEMICOLON --> ?>" . $string_array[$i] . "<?php ";
                    unset($string_array[$i]);
                    continue;
                }

                $string_array[1] .= $string_array[$i];
                unset($string_array[$i]);
            }
        }

        $semicolon = '<!-- SRC_SEMICOLON -->';
        $script    = trim($string_array[1]) . $semicolon;
        $script    = RL_RegEx::replace('(;\s*)?' . RL_RegEx::quote($semicolon), ';', $script);

        $area = Params::getArea(self::$current_area);

        $forbidden_php_array = explode(',', $area->forbidden_php);
        RL_Array::clean($forbidden_php_array);

        $forbidden_php_regex = '[^a-z_](' . implode('|', $forbidden_php_array) . ')(\s*\(|\s+[\'"])';

        RL_RegEx::matchAll($forbidden_php_regex, ' ' . $script, $functions);

        if ( ! empty($functions))
        {
            $functionsArray = [];
            foreach ($functions as $function)
            {
                $functionsArray[] = $function[1] . ')';
            }

            $comment = JText::_('SRC_PHP_CODE_REMOVED_FORBIDDEN') . ': ( ' . implode(', ', $functionsArray) . ' )';

            $string_array[1] = RL_Document::isHtml()
                ? Protect::getMessageCommentTag($comment)
                : $string_array[1] = '';

            $string = implode('', $string_array);

            return;
        }

        $output = RL_Php::execute('<?php ' . $script . ' ?>', self::$article);

        $string_array[1] = $output;

        $string = implode('', $string_array);
    }

    private static function stringToSplitArray($string, $search, $tags = true)
    {
        $params = Params::get();

        if ( ! $tags)
        {
            $string = RL_RegEx::replace($search, $params->splitter . '\1' . $params->splitter, $string);

            return explode($params->splitter, $string);
        }

        foreach ($params->html_tags_syntax as $html_tags_syntax)
        {
            [$start, $end] = $html_tags_syntax;

            $tag_search = str_replace('-start-', $start, $search);
            $tag_search = str_replace('-end-', $end, $tag_search);
            $string     = RL_RegEx::replace($tag_search, $params->splitter . '\1' . $params->splitter, $string);
        }

        return explode($params->splitter, $string);
    }
}
