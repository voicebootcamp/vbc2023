<?php
/**
 * @package   akeebabackup
 * @copyright Copyright (c)2006-2022 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Component\AkeebaBackup\Site\Model\Json\Task;

// Protect from unauthorized access
defined('_JEXEC') || die();

use Akeeba\Component\AkeebaBackup\Administrator\Model\StatisticModel;
use RuntimeException;

/**
 * Delete a backup record
 */
class Delete extends AbstractTask
{
	/**
	 * Execute the JSON API task
	 *
	 * @param   array  $parameters  The parameters to this task
	 *
	 * @return  mixed
	 *
	 * @throws  RuntimeException  In case of an error
	 */
	public function execute(array $parameters = [])
	{
		// Get the passed configuration values
		$defConfig = [
			'backup_id' => 0,
		];

		$defConfig = array_merge($defConfig, $parameters);

		$backup_id = (int) $defConfig['backup_id'];

		/** @var StatisticModel $model */
		$model = $this->factory->createModel('Statistic', 'Administrator');
		$model->setState('id', $backup_id);

		$ids = [$backup_id];

		if (!$model->delete($ids))
		{
			throw new RuntimeException($model->getError(), 500);
		}

		return true;
	}
}
