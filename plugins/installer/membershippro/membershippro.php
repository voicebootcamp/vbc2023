<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2022 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Uri\Uri;

if (!file_exists(JPATH_ADMINISTRATOR . '/components/com_osmembership/osmembership.php'))
{
	return;
}

class plgInstallerMembershipPro extends CMSPlugin
{
	public function onInstallerBeforePackageDownload(&$url, &$headers)
	{
		$uri = Uri::getInstance($url);

		$host       = $uri->getHost();
		$validHosts = ['joomdonation.com', 'www.joomdonation.com'];
		if (!in_array($host, $validHosts))
		{
			return true;
		}

		$documentId = $uri->getVar('document_id');

		if ($documentId != 97)
		{
			return true;
		}

		// Get Download ID and append it to the URL

		// Require library + register autoloader
		require_once JPATH_ADMINISTRATOR . '/components/com_osmembership/loader.php';

		$config = OSMembershipHelper::getConfig();

		// Append the Download ID to the download URL
		if (!empty($config->download_id))
		{
			$uri->setVar('download_id', $config->download_id);
			$url = $uri->toString();

			// Append domain to URL for logging
			$siteUri = Uri::getInstance();
			$uri->setVar('domain', $siteUri->getHost());
			$uri->setVar('php_version', PHP_VERSION);
			$uri->setVar('joomla_version', JVERSION);

			$uri->setVar('version', OSMembershipHelper::getInstalledVersion());

			$url = $uri->toString();
		}

		return true;
	}
}
