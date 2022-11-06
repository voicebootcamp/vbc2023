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
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Uri\Uri;

class OSMembershipController extends MPFController
{
	use OSMembershipControllerData, OSMembershipControllerDisplay, MPFControllerDownload;

	/**
	 * Process downloading invoice for a subscription record based on given ID
	 */
	public function download_invoice()
	{
		$id = $this->input->getInt('id', 0);

		/* @var OSMembershipTableSubscriber $row */
		$row = Table::getInstance('Subscriber', 'OSMembershipTable');

		if (!$row->load($id))
		{
			throw new Exception(Text::_('Invalid Subscription Record:' . $id), 404);
		}

		if (!$row->invoice_number)
		{
			throw new RuntimeException('Sorry. This subscription does not have associated invoice to download');
		}

		// Check download invoice permission
		$canDownload = false;

		$user = Factory::getUser();

		if ($user->authorise('core.admin', 'com_osmembership'))
		{
			$canDownload = true;
		}
		elseif ($user->authorise('membershippro.subscriptions', 'com_osmembership'))
		{
			$plan = OSMembershipHelperDatabase::getPlan($row->plan_id);

			if (in_array($plan->subscriptions_manage_user_id, [0, $user->id]))
			{
				$canDownload = true;
			}
		}
		elseif ($row->user_id > 0 && ($row->user_id == $user->id))
		{
			$canDownload = true;
		}

		if ($canDownload)
		{
			$invoicePath = OSMembershipHelper::generateAndReturnInvoicePath($row);
			$this->processDownloadFile($invoicePath);
		}
		else
		{
			throw new Exception(Text::_('JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN'), 403);
		}
	}

	/**
	 * Download selected document from membership profile
	 *
	 * @throws Exception
	 */
	public function download_document()
	{
		$planIds = OSMembershipHelperSubscription::getActiveMembershipPlans();

		if (count($planIds) == 1)
		{
			throw new Exception(Text::_('JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN'), 403);
		}

		$id    = $this->input->getInt('id');
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('a.*')
			->from('#__osmembership_documents AS a')
			->where('a.id IN (SELECT document_id FROM #__osmembership_plan_documents AS b WHERE b.plan_id  IN (' . implode(',', $planIds) . ') )')
			->where('a.id = ' . $id);
		$db->setQuery($query);
		$document = $db->loadObject();

		if (!$document)
		{
			throw new Exception(Text::_('Document not found or you are not allowed to download this document'), 404);
		}

		$path     = OSMembershipHelper::getDocumentsPath();
		$filePath = JPath::clean($path . '/');
		$fileName = $document->attachment;

		if (file_exists($filePath . $fileName))
		{
			$this->processDownloadFile($filePath . $fileName, OSMembershipHelper::getOriginalFilename($fileName));
		}
		else
		{
			throw new Exception(Text::_('Document not found. Please contact administrator'), 404);
		}
	}

	/**
	 * Method to allow downloading schedule document
	 */
	public function download_schedule_document()
	{
		$id    = $this->input->getInt('id', 0);
		$db    = Factory::getDbo();
		$query = $db->getQuery(true)
			->select('*')
			->from('#__osmembership_scheduledocuments')
			->where('id  = ' . $id);
		$db->setQuery($query);
		$document = $db->loadObject();

		if (!$document)
		{
			throw new Exception('Document not found', 404);
		}

		// Check to see if the user has access to this document
		$canAccess     = false;
		$subscriptions = OSMembershipHelperSubscription::getUserSubscriptionsInfo();

		if (isset($subscriptions[$document->plan_id]))
		{
			$subscription = $subscriptions[$document->plan_id];

			if ($subscription->active_in_number_days >= $document->number_days)
			{
				$canAccess = true;
			}
		}

		if ($canAccess)
		{
			$this->processDownloadFile(JPATH_ROOT . '/media/com_osmembership/scheduledocuments/' . $document->document);
		}
		else
		{
			throw new Exception('You are not allowed to download this document');
		}
	}

	/**
	 * Download a file uploaded by users
	 *
	 * @throws Exception
	 */
	public function download_file()
	{
		$filePath = JPATH_ROOT . '/media/com_osmembership/upload/';
		$fileName = $this->input->get('file_name', '', 'string');
		$fileName = basename($fileName);

		if (file_exists($filePath . $fileName))
		{
			// Check permission
			$canDownload = false;
			$user        = Factory::getUser();

			if ($user->authorise('core.admin', 'com_osmembership')
				|| $user->authorise('membershippro.subscriptions', 'com_osmembership'))
			{
				// Users with registrants management is allowed to download file
				$canDownload = true;
			}
			elseif ($user->id)
			{
				// User can only download the file uploaded by himself
				$db = Factory::getDbo();

				// Get list of published file upload custom fields
				$query = $db->getQuery(true)
					->select('id')
					->from('#__osmembership_fields')
					->where('fieldtype = "File"');
				$db->setQuery($query);
				$fieldIds = $db->loadColumn();

				if (count($fieldIds))
				{
					$query->clear()
						->select('COUNT(*)')
						->from('#__osmembership_subscribers AS a')
						->innerJoin('#__osmembership_field_value AS b ON a.id = b.subscriber_id')
						->where('a.user_id = ' . $user->id)
						->where('b.field_id IN (' . implode(',', $fieldIds) . ')')
						->where('b.field_value = ' . $db->quote($fileName));
					$db->setQuery($query);
					$total = (int) $db->loadResult();

					if ($total)
					{
						$canDownload = true;
					}
				}
			}

			if (!$canDownload)
			{
				$this->app->enqueueMessage(Text::_('You do not have permission to download this file'), 'error');
				$this->app->redirect(Uri::root(), 403);

				return;
			}

			$this->processDownloadFile($filePath . $fileName, OSMembershipHelper::getOriginalFilename($fileName));
		}
		else
		{
			$this->app->enqueueMessage(Text::_('OSM_FILE_NOT_EXIST'));
			$this->app->redirect('index.php?option=com_osmembership&Itemid=' . $this->input->getInt('Itemid'), 404);
		}
	}

	/**
	 * Process upload file
	 */
	public function upload_file()
	{
		$config     = OSMembershipHelper::getConfig();
		$json       = [];
		$pathUpload = JPATH_ROOT . '/media/com_osmembership/upload';

		if (!Folder::exists($pathUpload))
		{
			Folder::create($pathUpload);
		}

		$allowedExtensions = '';

		$fieldId = $this->input->getInt('field_id');

		if ($fieldId)
		{
			$db    = Factory::getDbo();
			$query = $db->getQuery(true)
				->select('allowed_file_types')
				->from('#__osmembership_fields')
				->where('id = ' . $fieldId);
			$db->setQuery($query);
			$allowedExtensions = trim($db->loadResult());
		}

		if (!$allowedExtensions)
		{
			if ($config->allowed_file_types)
			{
				$allowedExtensions = $config->allowed_file_types;
			}
			else
			{
				$allowedExtensions = 'doc|docx|ppt|pptx|pdf|zip|rar|bmp|gif|jpg|jpeg|png|swf|zipx';
			}
		}

		if (strpos($allowedExtensions, ',') !== false)
		{
			$allowedExtensions = explode(',', $allowedExtensions);
		}
		else
		{
			$allowedExtensions = explode('|', $allowedExtensions);
		}

		$allowedExtensions = array_map('trim', $allowedExtensions);

		$file     = $this->input->files->get('file', [], 'raw');
		$fileName = $file['name'];
		$fileExt  = File::getExt($fileName);

		if (in_array(strtolower($fileExt), $allowedExtensions))
		{
			$canUpload = true;

			if ($config->upload_max_file_size > 0)
			{
				$maxFileSizeInByte = $config->upload_max_file_size * 1024 * 1024;

				if ($file['size'] > $maxFileSizeInByte)
				{
					$json['error'] = Text::sprintf('OSM_FILE_SIZE_TOO_LARGE', $config->upload_max_file_size);
					$canUpload     = false;
				}
			}

			if ($canUpload)
			{
				$fileName = File::makeSafe($fileName);

				if (File::exists($pathUpload . '/' . $fileName))
				{
					$targetFileName = time() . '_' . $fileName;
				}
				else
				{
					$targetFileName = $fileName;
				}

				File::upload($file['tmp_name'], $pathUpload . '/' . $targetFileName, false, true);

				$json['success'] = Text::sprintf('OSM_FILE_UPLOADED', $fileName);
				$json['file']    = $targetFileName;
			}
		}
		else
		{
			$json['error'] = Text::sprintf('OSM_FILE_NOT_ALLOWED', $fileExt, implode(', ', $allowedExtensions));
		}

		echo json_encode($json);

		$this->app->close();
	}

	/**
	 * Method to allow downloading update package for the given extension
	 *
	 * @throws Exception
	 */
	public function download_update_package()
	{
		// Check and make sure Joomla update is supported on this site before processing further
		$documentsPath        = OSMembershipHelper::getDocumentsPath();
		$updatePackagesFolder = JPath::clean($documentsPath . '/update_packages');

		if (!Folder::exists($updatePackagesFolder))
		{
			throw new Exception('Joomla Update is not supported on this site', 403);
		}

		$db         = Factory::getDbo();
		$query      = $db->getQuery(true);
		$domain     = $this->input->getString('domain');
		$downloadId = trim($this->input->getString('download_id'));
		$documentId = $this->input->getInt('document_id', 0);

		if (empty($domain))
		{
			throw new Exception('Invalid Domain', 403);
		}

		if (empty($downloadId))
		{
			throw new Exception('Invalid Download ID', 403);
		}

		if (empty($documentId))
		{
			throw new Exception('Invalid Extension ID', 403);
		}

		$query->select('*')
			->from('#__osmembership_downloadids')
			->where('download_id = ' . $db->quote($downloadId));
		$db->setQuery($query);
		$registeredId = $db->loadObject();

		if (!$registeredId)
		{
			throw new Exception('Invalid Download ID', 404);
		}

		$domain           = str_replace('www.', '', $domain);
		$registeredDomain = str_replace('www.', '', $registeredId->domain);

		if ($registeredDomain && $registeredDomain != $domain)
		{
			throw new Exception('This download ID as used for different domain already. You need to register a new download ID for this domain', 403);
		}

		$userId = $registeredId->user_id;
		$user   = Factory::getUser($userId);

		if (!$user->id)
		{
			throw new Exception('User does not exist', 404);
		}

		// Check to see whether user has permission to download this documentl
		$planIds = OSMembershipHelperSubscription::getActiveMembershipPlans($userId);

		if (count($planIds) == 1)
		{
			throw new Exception(Text::_('JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN'), 403);
		}

		// Remove 0 from $planIds array
		array_shift($planIds);

		$query->clear()
			->select('a.*')
			->from('#__osmembership_documents AS a')
			->where('a.id IN (SELECT document_id FROM #__osmembership_plan_documents AS b WHERE b.plan_id  IN (' . implode(',', $planIds) . ') )')
			->where('a.id = ' . $documentId);
		$db->setQuery($query);
		$document = $db->loadObject();

		if (!$document)
		{
			throw new Exception(Text::_('Update package not found or you are not allowed to download this update package'), 404);
		}

		if (!$document->update_package)
		{
			throw new Exception(Text::_('Update package does not exist for this document'), 404);
		}

		$filePath = $updatePackagesFolder . '/' . $document->update_package;

		if (!File::exists($filePath))
		{
			throw new Exception('Update package not found', 404);
		}

		// OK, valid
		if (empty($registeredId->domain))
		{
			$query->clear()
				->update('#__osmembership_downloadids')
				->set('domain = ' . $db->quote($domain))
				->where('id = ' . $registeredId->id);
			$db->setQuery($query);
			$db->execute();
		}

		//Log the download to database
		$columns = [
			'download_id',
			'document_id',
			'download_date',
			'domain',
			'server_ip',
		];

		$values = [
			$registeredId->id,
			$documentId,
			$db->quote(Factory::getDate('now')->toSql()),
			$db->quote($domain),
			$db->quote(@$_SERVER['REMOTE_ADDR']),
		];

		$query->clear()
			->insert('#__osmembership_downloadlogs')
			->columns($db->quoteName($columns))
			->values(implode(',', $values));

		$db->setQuery($query);
		$db->execute();

		$this->processDownloadFile($filePath, OSMembershipHelper::getOriginalFilename($document->update_package));
	}
}
