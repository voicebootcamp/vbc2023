<?php
/**
 * @package   akeebabackup
 * @copyright Copyright (c)2006-2022 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Component\AkeebaBackup\Administrator\View\Schedule;

defined('_JEXEC') || die;

use Akeeba\Component\AkeebaBackup\Administrator\Model\ScheduleModel;
use Akeeba\Component\AkeebaBackup\Administrator\View\Mixin\LoadAnyTemplate;
use Akeeba\Component\AkeebaBackup\Administrator\View\Mixin\ProfileIdAndName;
use Akeeba\Component\AkeebaBackup\Administrator\View\Mixin\TaskBasedEvents;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;

#[\AllowDynamicProperties]
class HtmlView extends BaseHtmlView
{
	use TaskBasedEvents;
	use LoadAnyTemplate;
	use ProfileIdAndName;

	/**
	 * Check for failed backups information
	 *
	 * @var   object
	 * @since 9.0.0
	 */
	public $checkinfo = null;

	/**
	 * CRON information
	 *
	 * @var   object
	 * @since 9.0.0
	 */
	public $croninfo = null;

	/**
	 * Is the console plugin enabled?
	 *
	 * @var   bool
	 * @since 9.0.12
	 */
	public $isConsolePluginEnabled = false;

	protected function onBeforeMain()
	{
		$toolbar = Toolbar::getInstance();
		ToolbarHelper::title(Text::_('COM_AKEEBABACKUP_SCHEDULE'), 'icon-akeeba');

		$toolbar->back()
			->text('COM_AKEEBABACKUP_CONTROLPANEL')
			->icon('fa fa-' . (Factory::getApplication()->getLanguage()->isRtl() ? 'arrow-right' : 'arrow-left'))
			->url('index.php?option=com_akeebabackup');

		$toolbar->help(null, false, 'https://www.akeeba.com/documentation/akeeba-backup-joomla/automating-your-backup.html');

		$this->getProfileIdAndName();

		$this->isConsolePluginEnabled = PluginHelper::isEnabled('console', 'akeebabackup');

		// Get the CRON paths
		/** @var ScheduleModel $model */
		$model           = $this->getModel();
		$this->croninfo  = $model->getPaths();
		$this->checkinfo = $model->getCheckPaths();
	}
}