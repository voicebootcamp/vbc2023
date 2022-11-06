<?php
/**
 * @package   akeebabackup
 * @copyright Copyright (c)2006-2022 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Component\AkeebaBackup\Administrator\CliCommands;

defined('_JEXEC') || die;

use Akeeba\Component\AkeebaBackup\Administrator\CliCommands\MixIt\InitialiseEngine;
use Akeeba\Engine\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Factory\MVCFactoryAwareTrait;
use Joomla\Console\Command\AbstractCommand;
use Akeeba\Component\AkeebaBackup\Administrator\CliCommands\MixIt\ArgumentUtilities;
use Akeeba\Component\AkeebaBackup\Administrator\CliCommands\MixIt\ConfigureIO;
use Akeeba\Component\AkeebaBackup\Administrator\CliCommands\MixIt\FilterRoots;
use Akeeba\Component\AkeebaBackup\Administrator\CliCommands\MixIt\IsPro;
use Akeeba\Component\AkeebaBackup\Administrator\CliCommands\MixIt\PrintFormattedArray;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * akeeba:filter:exclude
 *
 * Set an exclusion filter to Akeeba Backup.
 *
 * @since   7.5.0
 */
class FilterExclude extends AbstractCommand
{
	use ConfigureIO;
	use ArgumentUtilities;
	use PrintFormattedArray;
	use IsPro;
	use FilterRoots;
	use MVCFactoryAwareTrait;
	use InitialiseEngine;

	/**
	 * The default command name
	 *
	 * @var    string
	 * @since  7.5.0
	 */
	protected static $defaultName = 'akeeba:filter:exclude';

	/**
	 * Internal function to execute the command.
	 *
	 * @param   InputInterface   $input   The input to inject into the command.
	 * @param   OutputInterface  $output  The output to inject into the command.
	 *
	 * @return  integer  The command exit code
	 *
	 * @since   7.5.0
	 */
	protected function doExecute(InputInterface $input, OutputInterface $output): int
	{
		$this->configureSymfonyIO($input, $output);

		try
		{
			$this->initialiseComponent($this->getApplication());
		}
		catch (\Throwable $e)
		{
			$this->ioStyle->error([
				Text::_('COM_AKEEBABACKUP_CLI_ERR_CANNOT_LOAD_BACKUP_ENGINGE'),
				$e->getMessage(),
			]);

			return 255;
		}

		$profileId = (int) ($this->cliInput->getOption('profile') ?? 1);

		define('AKEEBA_PROFILE', $profileId);

		$filterType = (string) ($this->cliInput->getOption('filterType') ?? 'files');
		$target     = (in_array($filterType, [
			'tables', 'tabledata', 'regextables', 'regextabledata', 'multidb',
		])) ? 'db' : 'fs';
		$root       = (string) ($this->cliInput->getOption('root') ?? (($target == 'fs') ? '[SITEROOT]' : '[SITEDB]'));

		if (!in_array($root, $this->getRoots($target)))
		{
			$this->ioStyle->error(Text::sprintf('COM_AKEEBABACKUP_CLI_FILTER_DELETE_ERR_UNKNOWN_ROOT', $target, $root));

			return 1;
		}

		$filter = (string) $this->cliInput->getArgument('filter') ?? '';

		$this->ioStyle->title(Text::sprintf(
			'COM_AKEEBABACKUP_CLI_FILTER_EXCLUDE_HEAD',
			$target === 'db' ? Text::_('COM_AKEEBABACKUP_CLI_FILTER_TYPE_DATABASE') : Text::_('COM_AKEEBABACKUP_CLI_FILTER_TYPE_FILESYSTEM'),
			$filter,
			$filterType,
			$profileId
		));

		// Delete the filter
		$filterObject = Factory::getFilterObject($filterType);

		if ((stripos($filterType, 'regex') !== false) && !$this->isPro())
		{
			$this->ioStyle->error(Text::sprintf('COM_AKEEBABACKUP_CLI_FILTER_DELETE_ONLY_PRO', $filterType));

			return 1;
		}

		$success = $filterObject->set($root, $filter);

		if (!$success)
		{
			$this->ioStyle->error(Text::sprintf('COM_AKEEBABACKUP_CLI_FILTER_EXCLUDE_ERR_FAILED', $filter, $filterType));

			return 2;
		}

		Factory::getFilters()->save();

		$this->ioStyle->success(Text::sprintf('COM_AKEEBABACKUP_CLI_FILTER_EXCLUDE_LBL_SUCCESS', $filter, $filterType));

		return 0;
	}

	/**
	 * Configure the command.
	 *
	 * @return  void
	 *
	 * @since   7.5.0
	 */
	protected function configure(): void
	{
		$this->addArgument('filter', InputArgument::REQUIRED, Text::_('COM_AKEEBABACKUP_CLI_FILTER_EXCLUDE_OPT_FILTER'));
		$this->addOption('root', null, InputOption::VALUE_OPTIONAL, Text::_('COM_AKEEBABACKUP_CLI_FILTER_EXCLUDE_OPT_ROOT'), '');
		$this->addOption('filterType', null, InputOption::VALUE_REQUIRED, Text::_('COM_AKEEBABACKUP_CLI_FILTER_EXCLUDE_OPT_FILTERTYPE'), 'files');
		$this->addOption('profile', null, InputOption::VALUE_OPTIONAL, Text::_('COM_AKEEBABACKUP_CLI_FILTER_EXCLUDE_OPT_PROFILE'), 1);

		$this->setDescription(Text::_('COM_AKEEBABACKUP_CLI_FILTER_EXCLUDE_DESC'));
		$this->setHelp(Text::_('COM_AKEEBABACKUP_CLI_FILTER_EXCLUDE_HELP'));
	}
}
