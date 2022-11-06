<?php
/**
 * @package   akeebabackup
 * @copyright Copyright (c)2006-2022 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Component\AkeebaBackup\Site\Model\Json\Task;

// Protect from unauthorized access
defined('_JEXEC') || die();

use Akeeba\Component\AkeebaBackup\Administrator\Model\StatisticsModel;

/**
 * List the backup records
 */
class ListBackups extends AbstractTask
{
	/**
	 * Execute the JSON API task
	 *
	 * @param   array  $parameters  The parameters to this task
	 *
	 * @return  mixed
	 *
	 * @throws  \RuntimeException  In case of an error
	 */
	public function execute(array $parameters = [])
	{
		// Get the passed configuration values
		$defConfig = [
			'from'  => 0,
			'limit' => 50,
		];

		$defConfig = array_merge($defConfig, $parameters);

		$from  = (int) $defConfig['from'];
		$limit = (int) $defConfig['limit'];

		/** @var StatisticsModel $model */
		$model = $this->factory->createModel('Statistics', 'Administrator');

		$model->setState('list.start', $from);
		$model->setState('list.limit', $limit);

		return $model->getStatisticsListWithMeta(false);
	}
}
