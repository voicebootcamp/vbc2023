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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

class EventbookingController extends RADControllerAdmin
{
	use RADControllerDownload;

	public function display($cachable = false, array $urlparams = [])
	{
		$document = Factory::getDocument();
		$baseUri  = Uri::base(true);

		$document->addStyleSheet($baseUri . '/components/com_eventbooking/assets/css/style.css');

		$customCssFile = JPATH_ADMINISTRATOR . '/components/com_eventbooking/assets/css/custom.css';

		if (file_exists($customCssFile) && filesize($customCssFile) > 0)
		{
			$document->addStyleSheet($baseUri . '/components/com_eventbooking/assets/css/custom.css', ['version' => filemtime($customCssFile)]);
		}

		$view = $this->input->getCmd('view');

		if (in_array($view, ['location', 'registrant']))
		{
			HTMLHelper::_('jquery.framework');
		}

		parent::display($cachable, $urlparams);

		if ($this->input->getCmd('format', 'html') != 'raw')
		{
			EventbookingHelper::callOverridableHelperMethod('Helper', 'displayCopyRight');
		}
	}

	/**
	 * This method is implemented to help calling by typing the url on web browser to update database schema to latest version
	 */
	public function upgrade()
	{
		$this->setRedirect('index.php?option=com_eventbooking&task=update.update');
	}

	/**
	 * Process download a file
	 */
	public function download_file()
	{
		$fileName = basename($this->input->getString('file_name'));
		$filePath = JPATH_ROOT . '/media/com_eventbooking/files' . '/' . $fileName;

		if (file_exists($filePath))
		{
			$this->processDownloadFile($filePath);
		}
		else
		{
			$app = $this->getApplication();
			$app->enqueueMessage(Text::_('File does not exist'), 'error');
			$app->redirect('index.php?option=com_eventbooking&view=dashboard');
		}
	}

	/**
	 * Get profile data of the registrant, return reson format using for ajax request
	 */
	public function get_profile_data()
	{
		$userId  = $this->input->getInt('user_id', 0);
		$eventId = $this->input->getInt('event_id');
		$data    = [];

		if ($userId && $eventId)
		{
			$rowFields = EventbookingHelperRegistration::getFormFields($eventId, 0);
			$data      = EventbookingHelperRegistration::getFormData($rowFields, $eventId, $userId);
		}

		if ($userId && !isset($data['first_name']))
		{
			//Load the name from Joomla default name
			$user = Factory::getUser($userId);
			$name = $user->name;

			if ($name)
			{
				$pos = strpos($name, ' ');

				if ($pos !== false)
				{
					$data['first_name'] = substr($name, 0, $pos);
					$data['last_name']  = substr($name, $pos + 1);
				}
				else
				{
					$data['first_name'] = $name;
					$data['last_name']  = '';
				}
			}
		}

		if ($userId && !isset($data['email']))
		{
			if (empty($user))
			{
				$user = Factory::getUser($userId);
			}

			$data['email'] = $user->email;
		}

		echo json_encode($data);

		$this->app->close();
	}
}
