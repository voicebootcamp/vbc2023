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
use Joomla\CMS\Router\Route;

class OSMembershipControllerDownloadId extends OSMembershipController
{
	/**
	 * Generate Download IDs for user
	 *
	 * @throws Exception
	 */
	public function generate_download_ids()
	{
		JSession::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

		if (!Factory::getUser()->id)
		{
			throw new Exception('OSM_GUEST_COULD_NOT_GENERATE_DOWNLOAD_IDS', 403);
		}

		$numberDownloadIds = $this->input->post->getInt('number_download_ids', 1);

		if (!$numberDownloadIds)
		{
			$numberDownloadIds = 1;
		}

		/* @var OSMembershipModelDownloadids $model */
		$model = $this->getModel('Downloadids');
		$model->generateDownloadIds($numberDownloadIds);

		$this->setRedirect(Route::_('index.php?option=com_osmembership&view=downloadids&Itemid=' . $this->input->getInt('Itemid')), Text::sprintf('OSM_COUNT_DOWNLOAD_ID_GENERATED', $numberDownloadIds));
	}
}
