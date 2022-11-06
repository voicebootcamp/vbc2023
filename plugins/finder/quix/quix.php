<?php
/**
 * @package        Quix
 * @author         ThemeXpert http://www.themexpert.com
 * @copyright      Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license        GNU General Public License version 3 or later; see LICENSE.txt
 * @since          1.0.0
 */

defined('_JEXEC') or die;

use Joomla\CMS\Exception\ExceptionHandler;
use Joomla\Registry\Registry;

require_once JPATH_ADMINISTRATOR.'/components/com_finder/helpers/indexer/adapter.php';

/**
 * Smart Search adapter for com_quix.
 *
 * @since  2.5
 */
class PlgFinderQuix extends FinderIndexerAdapter
{
    /**
     * The plugin identifier.
     *
     * @var    string
     * @since  2.5
     */
    protected $context = 'Quix';

    /**
     * The extension name.
     *
     * @var    string
     * @since  2.5
     */
    protected $extension = 'com_quix';

    /**
     * The sublayout to use when rendering the results.
     *
     * @var    string
     * @since  2.5
     */
    protected $layout = 'page';

    /**
     * The type of content that the adapter indexes.
     *
     * @var    string
     * @since  2.5
     */
    protected $type_title = 'Quix Page';

    /**
     * The table name.
     *
     * @var    string
     * @since  2.5
     */
    protected $table = '#__quix';

    /**
     * Load the language file on instantiation.
     *
     * @var    boolean
     * @since  3.1
     */
    protected $autoloadLanguage = true;

    protected $quixImported = false;

    /**
     * Method to update the item link information when the item category is
     * changed. This is fired when the item category is published or unpublished
     * from the list view.
     *
     * @param  string  $extension  The extension whose category has been updated.
     * @param  array  $pks         A list of primary key ids of the content that has changed state.
     * @param  integer  $value     The value of the state that the content has been changed to.
     *
     * @return  void
     *
     * @since   2.5
     */
    public function onFinderCategoryChangeState($extension, $pks, $value)
    {
        // Make sure we're handling com_quix categories.
        if ($extension == 'com_quix') {
            $this->categoryStateChange($pks, $value);
        }
    }

    /**
     * Method to remove the link information for items that have been deleted.
     *
     * @param  string  $context  The context of the action being performed.
     * @param  JTable  $table    A JTable object containing the record to be deleted
     *
     * @return  boolean  True on success.
     *
     * @throws  Exception on database error.
     * @since   2.5
     */
    public function onFinderAfterDelete($context, $table)
    {
        if ($context == 'com_quix.page') {
            $id = $table->id;
        } elseif ($context == 'com_finder.index') {
            $id = $table->link_id;
        } else {
            return true;
        }

        // Remove item from the index.
        return $this->remove($id);
    }

    /**
     * Smart Search after save content method.
     * Reindexes the link information for an article that has been saved.
     * It also makes adjustments if the access level of an item or the
     * category to which it belongs has changed.
     *
     * @param  string  $context  The context of the content passed to the plugin.
     * @param  JTable  $row      A JTable object.
     * @param  boolean  $isNew   True if the content has just been created.
     *
     * @return  boolean  True on success.
     *
     * @throws  Exception on database error.
     * @since   2.5
     */
    public function onFinderAfterSave($context, $row, $isNew)
    {
        // We only want to handle articles here.
        if ($context == 'com_quix.page') {
            // Check if the access levels are different.
            if ( ! $isNew && $this->old_access != $row->access) {
                // Process the change.
                $this->itemAccessChange($row);
            }

            // Reindex the item.
            $this->reindex($row->id);
        }

        // Check for access changes in the category.
        if ($context == 'com_categories.category') {
            // Check if the access levels are different.
            if ( ! $isNew && $this->old_cataccess != $row->access) {
                $this->categoryAccessChange($row);
            }
        }

        return true;
    }

    /**
     * Smart Search before content save method.
     * This event is fired before the data is actually saved.
     *
     * @param  string  $context  The context of the content passed to the plugin.
     * @param  JTable  $row      A JTable object.
     * @param  boolean  $isNew   If the content is just about to be created.
     *
     * @return  boolean  True on success.
     *
     * @throws  Exception on database error.
     * @since   2.5
     */
    public function onFinderBeforeSave($context, $row, $isNew)
    {
        // We only want to handle articles here.
        if ($context == 'com_quix.page') {
            // Query the database for the old access level if the item isn't new.
            if ( ! $isNew) {
                $this->checkItemAccess($row);
            }
        }

        // Check for access levels from the category.
        if ($context == 'com_categories.category') {
            // Query the database for the old access level if the item isn't new.
            if ( ! $isNew) {
                $this->checkCategoryAccess($row);
            }
        }

        return true;
    }

    /**
     * Method to update the link information for items that have been changed
     * from outside the edit screen. This is fired when the item is published,
     * unpublished, archived, or unarchived from the list view.
     *
     * @param  string  $context  The context for the content passed to the plugin.
     * @param  array  $pks       An array of primary key ids of the content that has changed state.
     * @param  integer  $value   The value of the state that the content has been changed to.
     *
     * @return  void
     *
     * @since   2.5
     */
    public function onFinderChangeState($context, $pks, $value)
    {
        // We only want to handle articles here.
        if ($context == 'com_quix.page') {
            $this->itemStateChange($pks, $value);
        }

        // Handle when the plugin is disabled.
        if ($context == 'com_plugins.plugin' && $value === 0) {
            $this->pluginDisable($pks);
        }
    }

    /**
     * Method to index an item. The item must be a FinderIndexerResult object.
     *
     * @param  FinderIndexerResult  $item  The item to index as an FinderIndexerResult object.
     * @param  string  $format             The item format.  Not used.
     *
     * @return  void
     *
     * @throws  Exception on database error.
     * @since   2.5
     */
    protected function index(FinderIndexerResult $item, $format = 'html')
    {
        $item->setLanguage();

        // Check if the extension is enabled.
        if (JComponentHelper::isEnabled($this->extension) == false) {
            return;
        }

        $item->context = 'com_quix.page';

        // Initialise the item parameters.
        $registry = new Registry;
        $registry->loadString($item->params);
        $item->params = JComponentHelper::getParams('com_quix', true);
        $item->params->merge($registry);

        try {
            QuixAppHelper::renderQuixInstance($item);
        } catch (Exception $e) {
            ExceptionHandler::render($e);
        }
        $itemContent = $item->text;

        $pluginParams = JComponentHelper::getParams('com_finder', true);
        $limit        = $pluginParams->get('description_length', 250);
        $MemoryLimit  = $pluginParams->get('memory_table_limit', 30000);

        $item->summary = JHtml::_('string.truncate', $itemContent, $limit, $noSplit = true, $allowHtml = false);
        $item->body    = JHtml::_('string.truncate', $itemContent, $MemoryLimit, $noSplit = true, $allowHtml = true);

        // $item->summary = FinderIndexerHelper::prepareContent($body, $item->params);
        // $item->body = FinderIndexerHelper::prepareContent($body, $item->params);

        unset($item->data);

        // Build the necessary route and path information.
        $item->url   = $this->getUrl($item->id, $this->extension, $this->layout);
        $item->route = QuixFrontendHelperRoute::getPageRoute($item->id, $item->language);
        // $item->path  = FinderIndexerHelper::getContentPath($item->route);

        // Get the menu title if it exists.
        $title = $this->getItemMenuTitle($item->url);

        // Adjust the title if necessary.
        if ( ! empty($title) && $this->params->get('use_menu_title', true)) {
            $item->title = $title;
        }

        // Process meta data
        $registry = new Registry;
        $registry->loadString($item->metadata);
        $item->metadata = $registry;

        // Add the meta-author.
        $item->metaauthor = $item->metadata->get('author');
        // Add the meta-data processing instructions.
        $item->addInstruction(FinderIndexer::META_CONTEXT, 'metakey');
        $item->addInstruction(FinderIndexer::META_CONTEXT, 'metadesc');
        $item->addInstruction(FinderIndexer::META_CONTEXT, 'metaauthor');
        $item->addInstruction(FinderIndexer::META_CONTEXT, 'author');

        // Translate the state. Articles should only be published if the category is published.
        $item->state = $this->translateState($item->state, 1);

        // Add the type taxonomy data.
        $item->addTaxonomy('Type', 'WebPage');

        // Add the author taxonomy data.
        if ( ! empty($item->author)) {
            $item->addTaxonomy('Author', $item->author);
        }

        // // Add the category taxonomy data.
        // $item->addTaxonomy('Category', $item->category, $item->cat_state, $item->cat_access);

        // Add the language taxonomy data.
        $item->addTaxonomy('Language', $item->language);

        // Get content extras.
        FinderIndexerHelper::getContentExtras($item);

        // Index the item.
        $this->indexer->index($item);
    }

    /**
     * Method to setup the indexer to be run.
     *
     * @return  boolean  True on success.
     *
     * @since   2.5
     */
    protected function setup()
    {
        // Load dependent classes.
        include_once JPATH_SITE.'/components/com_quix/helpers/route.php';

        return true;
    }

    /**
     * Method to get the SQL query used to retrieve the list of content items.
     *
     * @param  mixed  $query  A JDatabaseQuery object or null.
     *
     * @return  JDatabaseQuery  A database object.
     *
     * @since   2.5
     */
    protected function getListQuery($query = null)
    {
        $db = JFactory::getDbo();

        // Check if we can use the supplied SQL query.
        $query = $query instanceof JDatabaseQuery ? $query : $db->getQuery(true)
                                                                ->select('a.*')
                                                                ->from('#__quix AS a');

        return $query;
    }
}
