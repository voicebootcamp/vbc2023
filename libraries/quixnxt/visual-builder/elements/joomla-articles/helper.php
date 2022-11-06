<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
use Joomla\Registry\Registry;

use Joomla\Component\Content\Site\Helper\RouteHelper;

if (JVERSION < 4) {
    require_once JPATH_SITE . '/components/com_content/helpers/route.php';
}
JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_content/models', 'ContentModel');

/**
* Joomla article element class
* instead of using direct method use class
 * @since 2.0.0
*/
class QuixJoomlaArticlesElement
{
    public $element;

    public function __construct()
    {
        $this->element = [];
        $this->element['extension'] = 'com_content';
        $this->element['published'] = 1;
        $this->element['action'] = 0;
        $this->element['show_root'] = 1;
        $this->element['client_id'] = null;

        $this->name = 'joomla-articles';
        $this->id = 'joomla-articles';
        $this->class = '';
        $this->multiple = 0;
        $this->required = 'true';
        $this->autofocus = '';
        $this->readonly = 'false';
        $this->disabled = 'false';
        $this->onchange = '';
        $this->value = '';
    }

    /**
     * Method to get the field options for category
     * Use the extension attribute in a form to specify the.specific extension for
     * which categories should be displayed.
     * Use the show_root attribute to specify whether to show the global category root in the list.
     *
     * @return  array    The field option objects.
     *
     * @since   11.1
     */
    public function getOptions()
    {
        $options = [];
        $extension = $this->element['extension'] ? (string) $this->element['extension'] : (string) $this->element['scope'];
        $published = (string) $this->element['published'];

        // Load the category options for a given extension.
        if (!empty($extension)) {
            // Filter over published state or not depending upon if it is present.
            if ($published) {
                $options = JHtml::_('category.options', $extension, ['filter.published' => explode(',', $published)]);
            } else {
                $options = JHtml::_('category.options', $extension);
            }

            // Verify permissions.  If the action attribute is set, then we scan the options.
            if ((string) $this->element['action']) {
                // Get the current user object.
                $user = JFactory::getUser();

                foreach ($options as $i => $option) {
                    /*
                     * To take save or create in a category you need to have create rights for that category
                     * unless the item is already in that category.
                     * Unset the option if the user isn't authorised for it. In this field assets are always categories.
                     */
                    if ($user->authorise('core.create', $extension . '.category.' . $option->value) != true) {
                        unset($options[$i]);
                    }
                }
            }

            if (isset($this->element['show_root'])) {
                array_unshift($options, JHtml::_('select.option', 'root', JText::_('JGLOBAL_ROOT')));
            }
        } else {
            JLog::add(JText::_('JLIB_FORM_ERROR_FIELDS_CATEGORY_ERROR_EXTENSION_EMPTY'), JLog::WARNING, 'jerror');
        }

        // Merge any additional options in the XML definition.
        // $options = array_merge(parent::getOptions(), $options);

        return $options;
    }

    /**
     * Method to get the field input markup for a generic list.
     * Use the multiple attribute to enable multiselect.
     *
     * @return  string  The field input markup.
     *
     * @since   11.1
     */
    public function getInput()
    {
        $html = [];
        $attr = '';

        // Initialize some field attributes.
        $attr .= !empty($this->class) ? ' class="' . $this->class . '"' : '';
        $attr .= !empty($this->size) ? ' size="' . $this->size . '"' : '';
        $attr .= $this->multiple ? ' multiple' : '';
        $attr .= $this->required ? ' required aria-required="true"' : '';
        $attr .= $this->autofocus ? ' autofocus' : '';

        // To avoid user's confusion, readonly="true" should imply disabled="true".
        if ((string) $this->readonly == '1' || (string) $this->readonly == 'true' || (string) $this->disabled == '1' || (string) $this->disabled == 'true') {
            $attr .= ' disabled="disabled"';
        }

        // Initialize JavaScript field attributes.
        $attr .= $this->onchange ? ' onchange="' . $this->onchange . '"' : '';

        // Get the field options.
        return (array) $this->getOptions();
    }

    /**
     * Get a list of articles from a specific category
     *
     * @param   \Joomla\Registry\Registry  &$params  object holding the models parameters
     *
     * @return  mixed
     *
     * @since  1.6
     */
    public static function getAjax($data = [])
    {
        $app = JFactory::getApplication();

        if (!$data) {
            $data = $app->input->get('data', '', 'BASE64', 'raw');
            $data = base64_decode($data);
        }

        $params = new Registry($data);

        // Get an instance of the generic articles model
        $articles = JModelLegacy::getInstance('Articles', 'ContentModel', ['ignore_request' => true]);

        // Set application parameters in model
        $contentParams = JComponentHelper::getParams('com_content');
        // Load the parameters.
        if (!$app->isClient('administrator')) {
            $contentParams = $app->getParams();
        }

        $articles->setState('params', $contentParams);

        // Set the filters based on the module params
        $articles->setState('list.start', 0);
        $articles->setState('list.limit', (int) $params->get('count', 0));
        $articles->setState('filter.published', 1);
        $articles->setState('filter.category_id', 0);

        // Access filter
        $access = !$contentParams->get('show_noauth');
        $authorised = JAccess::getAuthorisedViewLevels(JFactory::getUser()->get('id'));
        $articles->setState('filter.access', $access);

        $catids = (array) $params->get('category');
        $articles->setState('filter.category_id.include', (bool) $params->get('category_filtering_type', 1));

        // Category filter
        if ($catids) {
            if ($params->get('show_child_category_articles', 0) && (int) $params->get('category_depth', 999) > 0) {
                // Get an instance of the generic categories model
                $categories = JModelLegacy::getInstance('Categories', 'ContentModel', ['ignore_request' => true]);
                $categories->setState('params', $contentParams);
                $levels = $params->get('category_depth', 999) ? $params->get('category_depth', 999) : 9999;
                $categories->setState('filter.get_children', $levels);
                $categories->setState('filter.published', 1);
                $categories->setState('filter.access', $access);
                $additional_catids = [];

                foreach ($catids as $catid) {
                    $categories->setState('filter.parentId', $catid);
                    $recursive = true;
                    $items = $categories->getItems($recursive);

                    if ($items) {
                        foreach ($items as $category) {
                            $condition = (($category->level - $categories->getParent()->level) <= $levels);

                            if ($condition) {
                                $additional_catids[] = $category->id;
                            }
                        }
                    }
                }

                $catids = array_unique(array_merge($catids, $additional_catids));
            }

            $articles->setState('filter.category_id', $catids);
        }

        // Ordering
        $articles->setState('list.ordering', $params->get('article_ordering', 'a.ordering'));
        $articles->setState('list.direction', $params->get('article_ordering_direction', 'ASC'));

        // New Parameters
        $articles->setState('filter.featured', $params->get('show_featured', 'show'));

        // Filter by language
        if (!$app->isClient('administrator')) {
            $articles->setState('filter.language', $app->getLanguageFilter());
        }
        $items = $articles->getItems();

        // Display options
        $show_date = $params->get('show_date', 1);
        $show_date_field = $params->get('show_date_field', 'created');
        $show_date_format = $params->get('show_date_format', 'Y-m-d H:i:s');
        $show_category = $params->get('show_category', 1);
        $show_hits = $params->get('show_hits', 1);
        $show_author = $params->get('show_author', 1);
        $show_introtext = $params->get('show_introtext', 1);
        $introtext_limit = $params->get('introtext_limit', 100);

        // Find current Article ID if on an article page
        $option = $app->input->get('option');
        $view = $app->input->get('view');

        if ($option === 'com_content' && $view === 'article') {
            $active_article_id = $app->input->getInt('id');
        } else {
            $active_article_id = 0;
        }

        // Prepare data for display using display options
        foreach ($items as &$item) {
            $item->images = json_decode($item->images, true);
            $item->slug = $item->id . ':' . $item->alias;
            $item->catslug = $item->catid . ':' . $item->category_alias;

            if ($access || in_array($item->access, $authorised)) {
                // We know that user has the privilege to view the article
                if (JVERSION < 4) {
                    $item->link = ContentHelperRoute::getArticleRoute($item->slug, $item->catid, $item->language);
                } else {
                    $item->link = RouteHelper::getArticleRoute($item->slug, $item->catid, $item->language);
                }
            } else {
                $app = JFactory::getApplication();
                $menu = $app->getMenu();
                $menuitems = $menu->getItems('link', 'index.php?option=com_users&view=login');

                if (isset($menuitems[0])) {
                    $Itemid = $menuitems[0]->id;
                } elseif ($app->input->getInt('Itemid') > 0) {
                    // Use Itemid from requesting page only if there is no existing menu
                    $Itemid = $app->input->getInt('Itemid');
                }

                $item->link = JRoute::_('index.php?option=com_users&view=login&Itemid=' . $Itemid);
            }

            $item->link = JRoute::_($item->link);

            // Used for styling the active article
            $item->active = $item->id == $active_article_id ? 'active' : '';
            $item->displayDate = '';

            if ($show_date) {
                $item->displayDate = JHtml::_('date', $item->$show_date_field, $show_date_format);
            }

            if ($item->catid) {
                if (JVERSION < 4) {
                    $item->displayCategoryLink = JRoute::_(ContentHelperRoute::getCategoryRoute($item->catid));
                } else {
                    $item->displayCategoryLink = JRoute::_(RouteHelper::getCategoryRoute($item->catid));
                }

                $item->displayCategoryTitle = $show_category ? '<a href="' . $item->displayCategoryLink . '">' . $item->category_title . '</a>' : '';
            } else {
                $item->displayCategoryTitle = $show_category ? $item->category_title : '';
            }

            $item->displayHits = $show_hits ? $item->hits : '';
            $item->displayAuthorName = $show_author ? $item->author : '';

            if ($show_introtext) {
                $item->introtext = JHtml::_('content.prepare', $item->introtext, '', 'mod_articles_category.content');
                $item->introtext = self::_cleanIntrotext($item->introtext);
            }

            $item->displayIntrotext = $show_introtext ? self::truncate($item->introtext, $introtext_limit) : '';
            $item->displayReadmore = $item->alternative_readmore;
        }

        return $items;
    }

    /**
     * Strips unnecessary tags from the introtext
     *
     * @param   string  $introtext  introtext to sanitize
     *
     * @return mixed|string
     *
     * @since  1.6
     */
    public static function _cleanIntrotext($introtext)
    {
        $introtext = str_replace('<p>', ' ', $introtext);
        $introtext = str_replace('</p>', ' ', $introtext);
        $introtext = strip_tags($introtext, '<a><em><strong>');
        $introtext = trim($introtext);

        return $introtext;
    }

    /**
     * Method to truncate introtext
     *
     * The goal is to get the proper length plain text string with as much of
     * the html intact as possible with all tags properly closed.
     *
     * @param   string   $html       The content of the introtext to be truncated
     * @param   integer  $maxLength  The maximum number of charactes to render
     *
     * @return  string  The truncated string
     *
     * @since   1.6
     */
    public static function truncate($html, $maxLength = 0)
    {
        $baseLength = strlen($html);

        // First get the plain text string. This is the rendered text we want to end up with.
        $ptString = JHtml::_('string.truncate', $html, $maxLength, $noSplit = true, $allowHtml = false);

        for ($maxLength; $maxLength < $baseLength;) {
            // Now get the string if we allow html.
            $htmlString = JHtml::_('string.truncate', $html, $maxLength, $noSplit = true, $allowHtml = true);

            // Now get the plain text from the html string.
            $htmlStringToPtString = JHtml::_('string.truncate', $htmlString, $maxLength, $noSplit = true, $allowHtml = false);

            // If the new plain text string matches the original plain text string we are done.
            if ($ptString == $htmlStringToPtString) {
                return $htmlString;
            }

            // Get the number of html tag characters in the first $maxlength characters
            $diffLength = strlen($ptString) - strlen($htmlStringToPtString);

            // Set new $maxlength that adjusts for the html tags
            $maxLength += $diffLength;

            if ($baseLength <= $maxLength || $diffLength <= 0) {
                return $htmlString;
            }
        }

        return $html;
    }
}
