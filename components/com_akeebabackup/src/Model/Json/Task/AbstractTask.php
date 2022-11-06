<?php
/**
 * @package   akeebabackup
 * @copyright Copyright (c)2006-2022 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Component\AkeebaBackup\Site\Model\Json\Task;

// Protect from unauthorized access
defined('_JEXEC') || die();

use Akeeba\Component\AkeebaBackup\Site\Model\Json\TaskInterface;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;

class AbstractTask implements TaskInterface
{
	/**
	 * The container of the component we belong to
	 *
	 * @var  MVCFactoryInterface
	 */
	protected $factory = null;

	/**
	 * The method name
	 *
	 * @var  string
	 */
	protected $methodName = '';

	/**
	 * Public constructor
	 *
	 * @param   MVCFactoryInterface  $factory  The container of the component we belong to
	 */
	public function __construct(MVCFactoryInterface $factory)
	{
		$this->factory = $factory;

		$path = explode('\\', get_class($this));
		$shortName = array_pop($path);
		$this->methodName = lcfirst($shortName);
	}

	/**
	 * Return the JSON API task's name ("method" name). Remote clients will use it to call us.
	 *
	 * @return  string
	 */
	public function getMethodName()
	{
		return $this->methodName;
	}

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
		throw new \LogicException(
			sprintf(
				'%s has not implemented its execute() method yet.',
				self::class
			)
		);
	}
}
