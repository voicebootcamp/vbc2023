<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2022 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die ;

use Joomla\CMS\Editor\Editor;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Toolbar\ToolbarHelper;

ToolbarHelper::title(Text::_('Configuration'), 'generic.png');
ToolbarHelper::apply();
ToolbarHelper::save('save');
ToolbarHelper::cancel('cancel');

if (Factory::getUser()->authorise('core.admin', 'com_osmembership'))
{
	ToolbarHelper::preferences('com_osmembership');
}

HTMLHelper::_('bootstrap.tooltip', '.hasTooltip', ['html' => true, 'sanitize' => false]);

if (!OSMembershipHelper::isJoomla4())
{
	HTMLHelper::_('formbehavior.chosen', 'select.chosen');
}

$document = Factory::getDocument();
$document->addStyleDeclaration(".hasTip{display:block !important}");
$config = $this->config;
$editor  = Editor::getInstance($config->get('editor') ?: Factory::getApplication()->get('editor'));

$translatable = Multilanguage::isEnabled() && count($this->languages);

HTMLHelper::_('behavior.keepalive');

if (OSMembershipHelper::isJoomla4())
{
	$tabApiPrefix = 'uitab.';

	Factory::getDocument()->getWebAssetManager()->useScript('showon');
}
else
{
	$tabApiPrefix = 'bootstrap.';

	HTMLHelper::_('script', 'jui/cms.js', array('version' => 'auto', 'relative' => true));
	HTMLHelper::_('behavior.tabstate');
}

$bootstrapHelper = OSMembershipHelperBootstrap::getInstance();
?>
<form action="<?php echo Route::_('index.php?option=com_osmembership&view=configuration'); ?>" method="post" name="adminForm" id="adminForm" class="form-horizontal osm-configuration" enctype="multipart/form-data">
    <?php echo HTMLHelper::_($tabApiPrefix . 'startTabSet', 'configuration', array('active' => 'general-page')); ?>
        <?php echo HTMLHelper::_($tabApiPrefix . 'addTab', 'configuration', 'general-page', Text::_('OSM_GENERAL', true)); ?>
        <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
                <div class="<?php echo $bootstrapHelper->getClassMapping('span6'); ?>">
                    <?php echo $this->loadTemplate('subscriptions', array('config' => $config)); ?>
                    <?php echo $this->loadTemplate('mail', array('config' => $config)); ?>
                </div>
                <div class="<?php echo $bootstrapHelper->getClassMapping('span6'); ?>">
	                <?php echo $this->loadTemplate('user_registration', array('config' => $config)); ?>
                    <?php echo $this->loadTemplate('themes', array('config' => $config)); ?>
                    <?php echo $this->loadTemplate('gdpr', array('config' => $config)); ?>
                    <?php echo $this->loadTemplate('other', array('config' => $config)); ?>
                </div>
        </div>
        <?php echo HTMLHelper::_($tabApiPrefix . 'endTab'); ?>
        <?php
        echo HTMLHelper::_($tabApiPrefix . 'addTab', 'configuration', 'pdf-settings-page', Text::_('OSM_PDF_SETTINGS', true));
        echo $this->loadTemplate('pdf_settings', array('config' => $config, 'editor' => $editor));
        echo HTMLHelper::_($tabApiPrefix . 'endTab');

        echo HTMLHelper::_($tabApiPrefix . 'addTab', 'configuration', 'invoice-page', Text::_('OSM_INVOICE_SETTINGS', true));
        echo $this->loadTemplate('invoice', array('config' => $config, 'editor' => $editor));
        echo HTMLHelper::_($tabApiPrefix . 'endTab');

        echo HTMLHelper::_($tabApiPrefix . 'addTab', 'configuration', 'card-page', Text::_('OSM_MEMBER_CARD_SETTINGS', true));
        echo $this->loadTemplate('card', array('config' => $config, 'editor' => $editor));
        echo HTMLHelper::_($tabApiPrefix . 'endTab');

        echo HTMLHelper::_($tabApiPrefix . 'addTab', 'configuration', 'export-settings-page', Text::_('OSM_EXPORT_SETTINGS', true));
        echo $this->loadTemplate('export_fields', array('config' => $config));
        echo HTMLHelper::_($tabApiPrefix . 'endTab');

        echo HTMLHelper::_($tabApiPrefix . 'addTab', 'configuration', 'api-page', Text::_('OSM_API_SETTINGS', true));
        echo $this->loadTemplate('api', array('config' => $config));
        echo HTMLHelper::_($tabApiPrefix . 'endTab');

        if ($translatable)
        {
            echo HTMLHelper::_($tabApiPrefix . 'addTab', 'configuration', 'invoice-translation', Text::_('OSM_INVOICE_TRANSLATION', true));
            echo $this->loadTemplate('translation', array('config' => $config, 'editor' => $editor));
            echo HTMLHelper::_($tabApiPrefix . 'endTab');
        }

        if (PluginHelper::isEnabled('editors', 'codemirror'))
        {
	        $editorPlugin = 'codemirror';
        }
        elseif (PluginHelper::isEnabled('editor', 'none'))
        {
	        $editorPlugin = 'none';
        }
        else
        {
	        $editorPlugin = null;
        }

        if (file_exists(JPATH_ROOT . '/media/com_osmembership/assets/css/custom.css'))
        {
	        $customCss = file_get_contents(JPATH_ROOT . '/media/com_osmembership/assets/css/custom.css');
        }
        else
        {
	        $customCss = '';
        }

        echo HTMLHelper::_($tabApiPrefix . 'addTab', 'configuration', 'custom-css', Text::_('OSM_CUSTOM_CSS', true));

        if ($editorPlugin)
        {
            echo Editor::getInstance($editorPlugin)->display('custom_css', $customCss, '100%', '550', '75', '8', false, null, null, null, array('syntax' => 'css'));
        }
        else
        {
            if (OSMembershipHelper::isJoomla4())
            {
                $cssClass = 'form-control';
            }
            else
            {
                $cssClass = 'input-xxlarge';
            }
        ?>
            <textarea name="custom_css" rows="20" class="<?php echo $cssClass; ?>" style="width: 100%;"><?php echo $customCss; ?></textarea>
        <?php
        }

        echo HTMLHelper::_($tabApiPrefix . 'endTab');

        if (file_exists(JPATH_ROOT . '/components/com_osmembership/fields.xml'))
        {
	        echo HTMLHelper::_($tabApiPrefix . 'addTab', 'configuration', 'custom-fields', Text::_('OSM_CUSTOM_FIELDS', true));
	        echo $this->loadTemplate('custom_fields', ['editorPlugin' => $editorPlugin]);
	        echo HTMLHelper::_($tabApiPrefix . 'endTab');
        }

		echo HTMLHelper::_($tabApiPrefix . 'addTab', 'configuration', 'common-tags-page', Text::_('OSM_COMMON_TAGS', true));
		echo $this->loadTemplate('common_tags', array('config' => $config));
		echo HTMLHelper::_($tabApiPrefix . 'endTab');

        echo HTMLHelper::_($tabApiPrefix . 'addTab', 'configuration', 'anti-spam-page', Text::_('OSM_ANTI_SPAM_SETTINGS', true));
        echo $this->loadTemplate('anti_spam', array('config' => $config));
        echo HTMLHelper::_($tabApiPrefix . 'endTab');

        // Add support for custom settings layout
        if (file_exists(__DIR__ . '/default_custom_settings.php'))
        {
            echo HTMLHelper::_($tabApiPrefix . 'addTab', 'configuration', 'custom-settings-page', Text::_('OSM_CUSTOM_SETTINGS', true));
            echo $this->loadTemplate('custom_settings', array('config' => $config, 'editor' => $editor));
            echo HTMLHelper::_($tabApiPrefix . 'endTab');
        }

        echo HTMLHelper::_($tabApiPrefix . 'endTabSet');
    ?>
    <input type="hidden" name="task" value="" />
    <div class="clearfix"></div>
</form>
