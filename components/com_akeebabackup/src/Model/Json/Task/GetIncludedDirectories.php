<?php
/**
 * @package   akeebabackup
 * @copyright Copyright (c)2006-2022 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Component\AkeebaBackup\Site\Model\Json\Task;

// Protect from unauthorized access
defined('_JEXEC') || die();

use Akeeba\Component\AkeebaBackup\Administrator\Model\IncludefoldersModel;
use Akeeba\Engine\Platform;
use Joomla\CMS\Factory as JoomlaFactory;

/**
 * Get the extra included directories
 */
class GetIncludedDirectories extends AbstractTask
{
	/**
	 * Execute the JSON API task
	 *
	 * @param   array $parameters The parameters to this task
	 *
	 * @return  mixed
	 *
	 * @throws  \RuntimeException  In case of an error
	 */
	public function execute(array $parameters = [])
	{
		// Get the passed configuration values
		$defConfig = ['profile' => 0];

		$defConfig = array_merge($defConfig, $parameters);

		$profile = (int)$defConfig['profile'];

		if ($profile <= 0)
		{
			$profile = 1;
		}

		// Set the active profile
		JoomlaFactory::getApplication()->getSession()->set('akeebabackup.profile', $profile);

		// Load the configuration
		Platform::getInstance()->load_configuration($profile);

		/** @var IncludefoldersModel $model */
		$model = $this->factory->createModel('Includefolders', 'Administrator');

		return $model->get_directories();
	}
}
