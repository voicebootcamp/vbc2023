<?php
/**
 * @package     MPF
 * @subpackage  Controller
 *
 * @copyright   Copyright (C) 2016 Ossolution Team, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

/**
 * Joomla CMS Base View Html Class
 *
 * @package      MPF
 * @subpackage   View
 * @since        2.0
 */
class MPFViewHtml extends MPFView
{
	/**
	 * The view layout.
	 *
	 * @var string
	 */
	protected $layout = 'default';

	/**
	 * The paths queue.
	 *
	 * @var array
	 */
	protected $paths = [];

	/**
	 * Default Itemid variable value for the links in the view
	 *
	 * @var int
	 */
	public $Itemid;

	/**
	 * The input object passed from the controller while creating the view
	 *
	 * @var MPFInput
	 */

	protected $input;

	/**
	 * This is a front-end or back-end view.
	 * We need this field to determine whether we need to addToolbar or build the filter
	 *
	 * @var boolean
	 */
	protected $isAdminView = false;

	/**
	 * Options to allow hide default toolbar buttons from backend view
	 *
	 * @var array
	 */
	protected $hideButtons = [];

	/**
	 * Method to instantiate the view.
	 *
	 * @param   array  $config  A named configuration array for object construction
	 */
	public function __construct($config = [])
	{
		parent::__construct($config);

		if (isset($config['layout']))
		{
			$this->layout = $config['layout'];
		}

		if (isset($config['paths']))
		{
			$this->paths = $config['paths'];
		}
		else
		{
			$this->paths = [];
		}

		if (!empty($config['is_admin_view']))
		{
			$this->isAdminView = $config['is_admin_view'];
		}

		if (!empty($config['Itemid']))
		{
			$this->Itemid = $config['Itemid'];
		}

		if (isset($config['input']))
		{
			$this->input = $config['input'];
		}

		if (isset($config['hide_buttons']))
		{
			$this->hideButtons = $config['hide_buttons'];
		}
	}

	/**
	 * Method to display the view
	 */
	public function display()
	{
		$this->prepareView();

		echo $this->render();
	}

	/**
	 * Prepare data for view before it's rendered.
	 */
	protected function prepareView()
	{

	}

	/**
	 * Magic toString method that is a proxy for the render method.
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->render();
	}

	/**
	 * Method to escape output.
	 *
	 * @param   string  $output  The output to escape.
	 *
	 * @return string The escaped output.
	 */
	public function escape($output)
	{
		return htmlspecialchars((string) $output, ENT_COMPAT, 'UTF-8');
	}

	/**
	 * Method to get the view layout.
	 *
	 * @return string The layout name.
	 */
	public function getLayout()
	{
		return $this->layout;
	}

	/**
	 * Method to get the layout path.
	 *
	 * @param   string  $layout  The layout name.
	 *
	 * @return mixed The layout file name if found, false otherwise.
	 */
	public function getPath($layout)
	{
		$layouts = OSMembershipHelperHtml::getPossibleLayouts($layout);

		foreach ($layouts as $layout)
		{
			// Get the layout file name.
			$file = JPath::clean($layout . '.php');

			// Find the layout file path.
			$path = JPath::find($this->paths, $file);

			if ($path !== false)
			{
				return $path;
			}
		}

		return false;
	}

	/**
	 * Method to get the view paths.
	 *
	 * @return array The paths queue.
	 */
	public function getPaths()
	{
		return $this->paths;
	}

	/**
	 * Method to render the view.
	 *
	 * @return string The rendered view.
	 *
	 * @throws RuntimeException
	 */
	public function render()
	{
		// Get the layout path.
		$path = $this->getPath($this->getLayout());

		// Check if the layout path was found.
		if (!$path)
		{
			throw new RuntimeException('Layout Path Not Found');
		}

		// Start an output buffer.
		ob_start();

		// Load the layout.
		include $path;

		// Get the layout contents.
		return ob_get_clean();
	}

	/**
	 * Load sub-template for the current layout
	 *
	 * @param   string  $template
	 *
	 * @return string The output of sub-layout
	 * @throws RuntimeException
	 */
	public function loadTemplate($template, $data = [])
	{
		// Get the layout path.
		$path = $this->getPath($this->getLayout() . '_' . $template);

		// Check if the layout path was found.
		if (!$path)
		{
			throw new RuntimeException('Layout Path Not Found');
		}

		extract($data);

		// Start an output buffer.
		ob_start();

		// Load the layout.
		include $path;

		// Get the layout contents.
		return ob_get_clean();
	}

	/**
	 * Load common template for the view
	 *
	 * @param   string  $layout
	 *
	 * @return string The output of common layout
	 * @throws RuntimeException
	 */
	public function loadCommonLayout($layout, $data = [])
	{
		$app       = Factory::getApplication();
		$themeFile = str_replace('/tmpl', '', $layout);

		if (File::exists(JPATH_THEMES . '/' . $app->getTemplate() . '/html/com_osmembership/' . $themeFile))
		{
			$path = JPATH_THEMES . '/' . $app->getTemplate() . '/html/com_osmembership/' . $themeFile;
		}
		elseif (File::exists(JPATH_ROOT . '/components/com_osmembership/view/' . $layout))
		{
			$path = JPATH_ROOT . '/components/com_osmembership/view/' . $layout;
		}
		else
		{
			throw new RuntimeException(Text::sprintf('The given shared layout %s does not exist', $layout));
		}

		// Start an output buffer.
		ob_start();
		extract($data);

		// Load the layout.
		include $path;

		// Get the layout contents.
		return ob_get_clean();
	}

	/**
	 * Method to set the view layout.
	 *
	 * @param   string  $layout  The layout name.
	 *
	 * @return MPFViewHtml Method supports chaining.
	 */
	public function setLayout($layout)
	{
		$this->layout = $layout;

		return $this;
	}

	/**
	 * Get page params of the given view
	 *
	 * @param   array  $views
	 * @param   array  $query
	 *
	 * @return \Joomla\Registry\Registry
	 */
	protected function getParams($views = [], $query = [])
	{
		// Default to current view
		if (empty($views))
		{
			$views = [$this->getName()];
		}

		if ($this->input->getInt('hmvc_call') && $this->input->getInt('Itemid'))
		{
			$active = Factory::getApplication()->getMenu()->getItem($this->input->getInt('Itemid'));
		}

		if (empty($active))
		{
			$active = Factory::getApplication()->getMenu()->getActive();
		}

		if ($active && isset($active->query['view']) && in_array($active->query['view'], $views))
		{
			$params = $active->getParams();
			$temp   = clone ComponentHelper::getParams('com_menus');
			$params = $temp->merge($params);

			if ($active->query['view'] != $this->getName() || array_diff($query, $active->query))
			{
				$params->set('page_title', '');
				$params->set('page_heading', '');
				$params->set('show_page_heading', true);
			}

			return $params;
		}

		return new \Joomla\Registry\Registry;
	}

	/**
	 * Method to set the view paths.
	 *
	 * @param   array  $paths  The paths queue.
	 *
	 * @return MPFViewHtml Method supports chaining.
	 */
	public function setPaths($paths)
	{
		$this->paths = $paths;

		return $this;
	}

	/**
	 * Set document meta data
	 *
	 * @param   Joomla\Registry\Registry  $params
	 *
	 * @return void
	 */
	protected function setDocumentMetadata($params)
	{
		/* @var JDocumentHtml $document */
		$document         = Factory::getDocument();
		$siteNamePosition = Factory::getApplication()->get('sitename_pagetitles');
		$siteName         = Factory::getApplication()->get('sitename');

		if ($pageTitle = $params->get('page_title'))
		{
			if ($siteNamePosition == 0)
			{
				$document->setTitle($pageTitle);
			}
			elseif ($siteNamePosition == 1)
			{
				$document->setTitle($siteName . ' - ' . $pageTitle);
			}
			else
			{
				$document->setTitle($pageTitle . ' - ' . $siteName);
			}
		}

		if ($params->get('menu-meta_keywords'))
		{
			$document->setMetaData('keywords', $params->get('menu-meta_keywords'));
		}

		if ($params->get('menu-meta_description'))
		{
			$document->setDescription($params->get('menu-meta_description'));
		}

		if ($params->get('robots'))
		{
			$document->setMetaData('robots', $params->get('robots'));
		}
	}

	/**
	 * Method to request user login before they can access to thsi page
	 *
	 * @param   string  $msg  The redirect message
	 *
	 * @throws Exception
	 */
	protected function requestLogin($msg = 'OSM_PLEASE_LOGIN')
	{
		if (!Factory::getUser()->get('id'))
		{
			$app    = Factory::getApplication();
			$active = $app->getMenu()->getActive();

			$option = isset($active->query['option']) ? $active->query['option'] : '';
			$view   = isset($active->query['view']) ? $active->query['view'] : '';

			if ($option == 'com_osmembership' && $view == strtolower($this->getName()))
			{
				$returnUrl = 'index.php?Itemid=' . $active->id;
			}
			else
			{
				$returnUrl = Uri::getInstance()->toString();
			}

			$url = Route::_('index.php?option=com_users&view=login&return=' . base64_encode($returnUrl), false);

			$app->enqueueMessage(Text::_($msg));
			$app->redirect($url);
		}
	}
}
