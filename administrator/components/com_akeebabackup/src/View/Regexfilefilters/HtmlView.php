<?php
/**
 * @package   akeebabackup
 * @copyright Copyright (c)2006-2022 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Component\AkeebaBackup\Administrator\View\Regexfilefilters;

defined('_JEXEC') || die;

use Akeeba\Component\AkeebaBackup\Administrator\Model\RegexdatabasefiltersModel;
use Akeeba\Component\AkeebaBackup\Administrator\Model\RegexfilefiltersModel;
use Akeeba\Component\AkeebaBackup\Administrator\View\Mixin\LoadAnyTemplate;
use Akeeba\Component\AkeebaBackup\Administrator\View\Mixin\ProfileIdAndName;
use Akeeba\Component\AkeebaBackup\Administrator\View\Mixin\TaskBasedEvents;
use Akeeba\Engine\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;

#[\AllowDynamicProperties]
class HtmlView extends BaseHtmlView
{
	use ProfileIdAndName;
	use LoadAnyTemplate;
	use TaskBasedEvents;

	/**
	 * SELECT element for choosing a database root
	 *
	 * @var  string
	 */
	public $root_select = '';

	/**
	 * List of database roots
	 *
	 * @var  array
	 */
	public $roots = [];

	/**
	 * Main page
	 */
	public function onBeforeMain()
	{
		$this->document->getWebAssetManager()
			->useScript('com_akeebabackup.regexfilefilters');

		$this->addToolbar();

		HTMLHelper::_('bootstrap.tooltip', '.hasTooltip', [
			'placement' => 'right',
		]);

		/** @var RegexfilefiltersModel $model */
		$model = $this->getModel();


		// Get a JSON representation of the available roots
		$filters   = Factory::getFilters();
		$root_info = $filters->getInclusions('dir');
		$roots     = [];
		$options   = [];

		if (!empty($root_info))
		{
			// Loop all dir definitions
			foreach ($root_info as $dir_definition)
			{
				if (is_null($dir_definition[1]))
				{
					// Site root definition has a null element 1. It is always pushed on top of the stack.
					array_unshift($roots, $dir_definition[0]);
				}
				else
				{
					$roots[] = $dir_definition[0];
				}

				$options[] = HTMLHelper::_('select.option', $dir_definition[0], $dir_definition[0]);
			}
		}

		$siteRoot          = $roots[0];
		$selectOptions     = [
			'list.select' => $siteRoot,
			'id'          => 'active_root',
			'list.attr'   => [
				'class' => 'form-select',
			],
		];
		$this->root_select = HTMLHelper::_('select.genericlist', $options, 'root', $selectOptions);
		$this->roots       = $roots;

		// Add script options
		$this->document
			->addScriptOptions('akeebabackup.System.params.AjaxURL', Route::_('index.php?option=com_akeebabackup&view=Regexfilefilters&task=ajax', false))
			->addScriptOptions('akeebabackup.Regexfilefilters.guiData', $model->get_regex_filters($siteRoot));

		// Translations
		Text::script('COM_AKEEBABACKUP_FILEFILTERS_LABEL_UIROOT');
		Text::script('COM_AKEEBABACKUP_FILEFILTERS_LABEL_UIERRORFILTER');
		Text::script('COM_AKEEBABACKUP_FILEFILTERS_TYPE_DIRECTORIES');
		Text::script('COM_AKEEBABACKUP_FILEFILTERS_TYPE_SKIPFILES');
		Text::script('COM_AKEEBABACKUP_FILEFILTERS_TYPE_SKIPDIRS');
		Text::script('COM_AKEEBABACKUP_FILEFILTERS_TYPE_FILES');

		$this->getProfileIdAndName();
	}

	private function addToolbar(): void
	{
		$toolbar = Toolbar::getInstance();
		ToolbarHelper::title(Text::_('COM_AKEEBABACKUP_REGEXFSFILTERS'), 'icon-akeeba');

		$toolbar->back()
			->text('COM_AKEEBABACKUP_CONTROLPANEL')
			->icon('fa fa-' . (\Joomla\CMS\Factory::getApplication()->getLanguage()->isRtl() ? 'arrow-right' : 'arrow-left'))
			->url('index.php?option=com_akeebabackup');

		$toolbar->help(null, false, 'https://www.akeeba.com/documentation/akeeba-backup-joomla/regex-files-directories-exclusion.html');
	}

}