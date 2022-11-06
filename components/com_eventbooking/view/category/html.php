<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2022 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

class EventbookingViewCategoryHtml extends RADViewList
{
	/**
	 * Id of the active category
	 *
	 * @var int
	 */
	protected $categoryId;

	/**
	 * The active category
	 *
	 * @var stdClass
	 */
	protected $category = null;

	/**
	 * List of children categories
	 *
	 * @var array
	 */
	protected $categories = [];

	/**
	 * Component config
	 *
	 * @var RADConfig
	 */
	protected $config;

	/**
	 * Twitter bootstrap helper
	 *
	 * @var EventbookingHelperBootstrap
	 */
	protected $bootstrapHelper;

	/**
	 * ID of current user
	 *
	 * @var int
	 */
	protected $userId;

	/**
	 * The access levels of the current user
	 *
	 * @var array
	 */
	protected $viewLevels;

	/**
	 * The value represent database null date
	 *
	 * @var string
	 */
	protected $nullDate;

	/**
	 * Intro text
	 *
	 * @var string
	 */
	protected $introText;

	/**
	 * Contain name of views which could be used to get menu item parameters for the current view
	 *
	 * @var array
	 */
	protected $paramsViews = ['categories', 'category'];

	/**
	 * Prepare the view data before it is rendered
	 *
	 * @return  void
	 * @throws  \Exception
	 */
	protected function prepareView()
	{
		parent::prepareView();

		EventbookingHelper::callOverridableHelperMethod('Html', 'antiXSS', [$this->items, ['title', 'price_text']]);

		$user   = Factory::getUser();
		$config = EventbookingHelper::getConfig();

		// If category id is passed, make sure it is valid and the user is allowed to access
		if ($categoryId = (int) $this->state->get('id'))
		{
			$this->category = $this->model->getCategory();

			if (empty($this->category))
			{
				throw new Exception(Text::_('JGLOBAL_CATEGORY_NOT_FOUND'), 404);
			}

			if (!in_array($this->category->access, $user->getAuthorisedViewLevels()))
			{
				throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
			}

			if (!$this->params->get('hide_children_categories'))
			{
				$model = RADModel::getTempInstance('Categories', 'EventbookingModel', ['table_prefix' => '#__eb_']);

				$this->categories = $model->setState('limitstart', 0)
					->setState('limit', 0)
					->setState('filter_order', 'tbl.ordering')
					->setState('id', $categoryId)
					->getData();
			}
		}

		$active = Factory::getApplication()->getMenu()->getActive();

		if ($active && isset($active->query['view']) && $active->query['view'] == $this->getName())
		{
			// This is direct menu link to category view, so use the layout from menu item setup
		}
		elseif ($this->input->getInt('hmvc_call') && $this->input->getCmd('layout'))
		{
			// Use layout from the HMVC call, in this case, it's from EB view module
		}
		elseif (!empty($this->category->layout))
		{
			$this->setLayout($this->category->layout);
		}
		else
		{
			$this->setLayout('default');
		}

		// Calculate page intro text
		$fieldSuffix = EventbookingHelper::getFieldSuffix();
		$message     = EventbookingHelper::getMessages();

		if ($fieldSuffix && EventbookingHelper::isValidMessage($message->{'intro_text' . $fieldSuffix}))
		{
			$introText = $message->{'intro_text' . $fieldSuffix};
		}
		elseif (EventbookingHelper::isValidMessage($message->intro_text))
		{
			$introText = $message->intro_text;
		}
		else
		{
			$introText = '';
		}

		if ($config->multiple_booking)
		{
			// Store last access category for routing back from shopping cart
			Factory::getSession()->set('last_category_id', $categoryId);
		}

		$this->config          = $config;
		$this->categoryId      = $categoryId;
		$this->bootstrapHelper = EventbookingHelperBootstrap::getInstance();
		$this->viewLevels      = $user->getAuthorisedViewLevels();
		$this->userId          = $user->id;
		$this->nullDate        = Factory::getDbo()->getNullDate();
		$this->introText       = $introText;

		// Prepare events and locations alias for routing
		$eventsAlias    = [];
		$locationsAlias = [];

		foreach ($this->items as $item)
		{
			if ($config->insert_event_id)
			{
				$eventsAlias[$item->id] = $item->id . '-' . $item->alias;
			}
			else
			{
				$eventsAlias[$item->id] = $item->alias;
			}

			$locationsAlias[$item->location_id] = $item->location_alias;
		}

		EventbookingHelperRoute::$eventsAlias    = array_filter($eventsAlias);
		EventbookingHelperRoute::$locationsAlias = array_filter($locationsAlias);

		// Prepare document meta data before it is rendered
		$this->prepareDocument();
	}

	/**
	 * Method to prepare document before it is rendered
	 *
	 * @return void
	 */
	protected function prepareDocument()
	{
		// Page title
		if (!$this->params->get('page_title') && $this->category)
		{
			// Page title
			if ($this->category->page_title)
			{
				$pageTitle = $this->category->page_title;
			}
			else
			{
				$pageTitle = Text::_('EB_SUB_CATEGORIES_PAGE_TITLE');
				$pageTitle = str_replace('[CATEGORY_NAME]', $this->category->name, $pageTitle);
			}

			$this->params->set('page_title', $pageTitle);
		}

		// Page heading
		if (!$this->params->get('page_heading'))
		{
			if ($this->params->get('display_events_type') == 3)
			{
				$pageHeading = Text::_('EB_EVENTS_ARCHIVE');
			}
			elseif ($this->category)
			{
				$pageHeading = $this->category->page_heading ?: $this->category->name;
			}
			else
			{
				$pageHeading = Text::_('EB_EVENT_LIST');
			}

			$this->params->set('page_heading', $pageHeading);
		}

		// Meta keywords and description
		$this->params->def('menu-meta_keywords', $this->category ? $this->category->meta_keywords : '');
		$this->params->set('menu-meta_description', $this->category ? $this->category->meta_description : '');

		// Load required assets for the view
		$this->loadAssets();

		// Build pathway
		$this->buildPathway();

		// Set page meta data
		$this->setDocumentMetadata();

		// Add Feed links to document
		if ($this->config->get('show_feed_link', 1))
		{
			$this->addFeedLinks();
		}

		// Use override menu item
		if ($this->params->get('menu_item_id') > 0)
		{
			$this->Itemid = $this->params->get('menu_item_id');
		}

		// Intro text
		if (EventbookingHelper::isValidMessage($this->params->get('intro_text')))
		{
			$this->introText = $this->params->get('intro_text');
		}

		// Add filter variables to pagination links if configured
		if ($this->params->get('show_search_bar', 0))
		{
			if ($this->state->search)
			{
				$this->pagination->setAdditionalUrlParam('search', $this->state->search);
			}

			if ($this->state->location_id)
			{
				$this->pagination->setAdditionalUrlParam('location_id', $this->state->location_id);
			}

			if ($this->state->filter_duration)
			{
				$this->pagination->setAdditionalUrlParam('filter_duration', $this->state->filter_duration);
			}

			if ($this->state->category_id)
			{
				$this->pagination->setAdditionalUrlParam('category_id', $this->state->category_id);
			}
		}
	}

	/**
	 * Load assets (javascript/css) for this specific view
	 *
	 * @return void
	 */
	protected function loadAssets()
	{
		if ($this->config->multiple_booking)
		{
			if ($this->deviceType == 'mobile')
			{
				EventbookingHelperJquery::colorbox('eb-colorbox-addcart', '100%', '450px', 'false', 'false');
			}
			else
			{
				EventbookingHelperJquery::colorbox('eb-colorbox-addcart', '800px', 'false', 'false', 'false', 'false');
			}
		}

		if ($this->config->show_list_of_registrants)
		{
			EventbookingHelperModal::iframeModal('a.eb-colorbox-register-lists', 'eb-registrant-lists-modal');
		}

		if ($this->config->show_location_in_category_view || ($this->getLayout() == 'timeline'))
		{
			EventbookingHelperModal::iframeModal('a.eb-colorbox-map', 'eb-map-modal');
		}

		if (!$this->config->get('link_thumb_to_event_detail_page', 1))
		{
			EventbookingHelperJquery::colorbox('a.eb-modal');
		}

		Factory::getDocument()->addScriptDeclaration('
	        function cancelRegistration(registrantId)
	        {
	            var form = document.adminForm ;
	    
	            if (confirm("' . Text::_('EB_CANCEL_REGISTRATION_CONFIRM') . '"))
	            {
	                form.task.value = "registrant.cancel" ;
	                form.id.value = registrantId ;
	                form.submit() ;
	            }
	        }
	    ');
	}

	/**
	 * Method to build document pathway
	 *
	 * @return void
	 */
	protected function buildPathway()
	{
		if ($this->input->getInt('hmvc_call'))
		{
			return;
		}

		$app    = Factory::getApplication();
		$active = $app->getMenu()->getActive();

		if (isset($active->query['view']) && in_array($active->query['view'], ['categories', 'category']))
		{
			$parentId = (int) $active->query['id'];

			if ($categoryId = $this->state->get('id'))
			{
				$pathway = $app->getPathway();
				$paths   = EventbookingHelperData::getCategoriesBreadcrumb($categoryId, $parentId);

				for ($i = count($paths) - 1; $i >= 0; $i--)
				{
					$path    = $paths[$i];
					$pathUrl = EventbookingHelperRoute::getCategoryRoute($path->id, $this->Itemid);
					$pathway->addItem($path->name, $pathUrl);
				}
			}
		}
	}

	/**
	 * Set meta data for the document
	 *
	 * @return void
	 */
	protected function setDocumentMetadata()
	{
		parent::setDocumentMetadata();

		if (!empty($this->category->image) && file_exists(JPATH_ROOT . '/' . $this->category->image))
		{
			Factory::getDocument()->setMetaData('og:image', Uri::root() . $this->category->image, 'property');
		}
	}
}
