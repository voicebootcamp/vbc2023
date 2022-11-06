<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2022 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;

class OSMembershipViewDocumentsHtml extends MPFViewHtml
{
	/**
	 * The list of documents
	 *
	 * @var array
	 */
	protected $items;

	/**
	 * The pagination object
	 *
	 * @var \Joomla\CMS\Pagination\Pagination
	 */
	protected $pagination;

	/**
	 * Path to the folder which document stored
	 *
	 * @var string
	 */
	protected $documentsPath;

	/**
	 * Active menu item parameters
	 *
	 * @var \Joomla\Registry\Registry
	 */
	protected $params;

	/**
	 * Display the view
	 *
	 * @throws Exception
	 */
	public function display()
	{
		$app = Factory::getApplication();

		if (!PluginHelper::isEnabled('osmembership', 'documents'))
		{
			$app->enqueueMessage(Text::_('Memebership Pro Documents plugin is not enabled. Please contact super administrator'));

			return;
		}

		// Make sure users are logged in before allow them to access
		$this->requestLogin();

		/* @var $model OSMembershipModelDocuments */
		$model               = $this->getModel();
		$this->items         = $model->getData();
		$this->pagination    = $model->getPagination();
		$this->documentsPath = OSMembershipHelper::getDocumentsPath();
		$this->params        = $this->getParams();

		parent::display();
	}
}
