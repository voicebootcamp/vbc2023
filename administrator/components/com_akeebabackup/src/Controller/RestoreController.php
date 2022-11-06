<?php
/**
 * @package   akeebabackup
 * @copyright Copyright (c)2006-2022 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Component\AkeebaBackup\Administrator\Controller;

defined('_JEXEC') || die;

use Akeeba\Component\AkeebaBackup\Administrator\Controller\Mixin\ControllerEvents;
use Akeeba\Component\AkeebaBackup\Administrator\Controller\Mixin\CustomACL;
use Akeeba\Component\AkeebaBackup\Administrator\Controller\Mixin\RegisterControllerTasks;
use Akeeba\Component\AkeebaBackup\Administrator\Controller\Mixin\ReusableModels;
use Akeeba\Component\AkeebaBackup\Administrator\Model\RestoreModel;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Uri\Uri;
use Joomla\Input\Input;

class RestoreController extends BaseController
{
	use ControllerEvents;
	use CustomACL;
	use RegisterControllerTasks;
	use ReusableModels;

	public function __construct($config = [], MVCFactoryInterface $factory = null, ?CMSApplication $app = null, ?Input $input = null)
	{
		parent::__construct($config, $factory, $app, $input);

		$this->registerControllerTasks('main');
	}

	/**
	 * Main task, displays the main page
	 */
	public function main()
	{
		/** @var RestoreModel $model */
		$model = $this->getModel('Restore', 'Administrator');

		$message = $model->validateRequest();

		if ($message !== true)
		{
			$this->setRedirect(Uri::base() . 'index.php?option=com_akeebabackup&view=Manage', $message, 'error');
			$this->redirect();

			return;
		}

		$model->setState('restorationstep', 0);

		$this->display(false);
	}

	/**
	 * Start the restoration
	 */
	public function start()
	{
		$this->checkToken();

		/** @var RestoreModel $model */
		$model = $this->getModel('Restore', 'Administrator');

		$model->setState('restorationstep', 1);
		$message = $model->validateRequest();

		if ($message !== true)
		{
			$this->setRedirect(Uri::base() . 'index.php?option=com_akeebabackup&view=Manage', $message, 'error');
			$this->redirect();

			return;
		}

		$model->setState('jps_key', $this->input->get('jps_key', '', 'raw'));
		$model->setState('procengine', $this->input->get('procengine', 'direct', 'cmd'));
		$model->setState('zapbefore', $this->input->get('zapbefore', 0, 'int'));
		$model->setState('stealthmode', $this->input->get('stealthmode', 0, 'int'));
		$model->setState('min_exec', $this->input->get('min_exec', 0, 'int'));
		$model->setState('max_exec', $this->input->get('max_exec', 5, 'int'));
		$model->setState('ftp_host', $this->input->get('ftp_host', '', 'raw'));
		$model->setState('ftp_port', $this->input->get('ftp_port', 21, 'int'));
		$model->setState('ftp_user', $this->input->get('ftp_user', '', 'raw'));
		$model->setState('ftp_pass', $this->input->get('ftp_pass', '', 'raw'));
		$model->setState('ftp_root', $this->input->get('ftp_root', '', 'raw'));
		$model->setState('tmp_path', $this->input->get('tmp_path', '', 'raw'));
		$model->setState('ftp_ssl', $this->input->get('usessl', 'false', 'cmd') == 'true');
		$model->setState('ftp_pasv', $this->input->get('passive', 'true', 'cmd') == 'true');

		$status = $model->createRestorationINI();

		if ($status === false)
		{
			$this->setRedirect(Uri::base() . 'index.php?option=com_akeebabackup&view=Manage', Text::_('COM_AKEEBABACKUP_RESTORE_ERROR_CANT_WRITE'), 'error');
			$this->redirect();

			return;
		}

		$this->display(false);
	}

	/**
	 * Perform a step through AJAX
	 */
	public function ajax()
	{
		/** @var RestoreModel $model */
		$model = $this->getModel('Restore', 'Administrator');

		$ajax = $this->input->get('ajax', '', 'cmd');
		$model->setState('ajax', $ajax);

		$ret = $model->doAjax();

		@ob_end_clean();
		echo '###' . json_encode($ret) . '###';
		flush();

		$this->app->close();
	}
}