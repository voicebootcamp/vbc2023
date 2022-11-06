<?php
/**
 * @package    com_quix
 * @author     ThemeXpert <info@themexpert.com>
 * @copyright  Copyright (C) 2015. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    3.0.0
 */

// No direct access
use QuixNxt\Utils\Asset;
use QuixNxt\Utils\Schema;

defined('_JEXEC') or die;
JHtml::_('jquery.framework');
JHtml::_('behavior.formvalidator');

$title     = JFilterOutput::stringURLSafe($this->item->title);
$actionUrl = 'index.php?option=com_quix&view=form&layout=edit&builder=frontend&id='.(int) $this->item->id;
?>
  <!--load twig template and declare them as script-->
<?php echo $this->loadTemplate('modal'); ?>

  <form
          action="<?php echo JRoute::_($actionUrl); ?>" method="post" enctype="multipart/form-data"
          name="adminForm" id="adminForm" class="qx-fb form-validate">

    <div class="qx-fb-frame">
      <div id="qx-fb-frame-toolbar" class="qx-flex qx-flex-between qx-box-shadow-medium"></div>
      <div class="qx-fb-frame-preview qx-width-viewport qx-flex qx-margin-auto" data-preview="desktop">
        <iframe
                title="quixFrame"
                style="display: none"
                data-src="<?php echo $this->iframeUrl; ?>"
                name="quixFrame"
                id="quix-iframe-wrapper"
                sandbox="allow-top-navigation-by-user-activation allow-forms allow-popups allow-modals allow-pointer-lock
        allow-same-origin allow-scripts"
                allowfullscreen="allowfullscreen"
                allow="clipboard-read; clipboard-write">
        </iframe>
      </div>

        <?php if (QuixHelperLicense::licenseStatus() !== 'pro'): ?>
          <div id="qx-notice-bar">
            <div>Activate Your License and Get Access to Premium Quix Templates, Support & Extension Updates.</div>
            <a href="<?php JUri::root() ?>/administrator/index.php?option=com_quix&view=config" target="_blank">CONNECT & ACTIVATE</a>
          </div>
        <?php endif; ?>

    </div>

    <input type="hidden" name="jform[data]" id="jform_data" value="" />
    <input type="hidden" name="jform[id]" id="jform_id" value="<?php echo (int) $this->item->id; ?>" />
    <input type="hidden" name="jform[title]" id="jform_title_hidden" value="<?php echo $this->item->title; ?>" />
    <input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>" />
    <input type="hidden" id="jform_Itemid" value="<?php echo $this->Itemid; ?>" />
    <input type="hidden" name="jform[created_by]"
           value="<?php echo $this->item->created_by || JFactory::getUser()->id; ?>" />
    <input type="hidden" name="jform[checked_out]" value="<?php echo $this->item->checked_out; ?>" />
    <input type="hidden" name="jform[checked_out_time]" value="<?php echo $this->item->checked_out_time; ?>" />
    <input type="hidden" id="jform_task" name="task" value="" />
    <input type="hidden" id="jform_type" name="type" value="<?php echo $this->type ?>" />
    <input type="hidden" id="return_url" name="return" value="<?php echo $this->return_page; ?>" />

      <?php if (isset($this->item->type)) : ?>
        <input type="hidden" id="jform_template_type" name="jform[type]" value="<?php echo $this->item->type ?>" />
      <?php endif; ?>
    <input type="hidden" id="jform_builder_version" name="jform[builder_version]"
           value="<?php echo $this->item->builder_version ?>" />
    <input type="hidden" id="jform_token" name="<?php echo JSession::getFormToken(); ?>" value="1" />


      <?php echo $this->loadTemplate('options'); ?>
  </form>

<?php
$data       = QuixFrontendHelperAssets::processDataForBuilder($this->item->data, $this->item->builder_version);
$dataDebug  = QUIXNXT_DEBUG ? 'true' : 'false';
$root       = JUri::root();
$config     = JComponentHelper::getParams('com_media');
$imagePath  = $config->get('image_path', 'images');
$jmediaPath = $root.$imagePath;
$version = QuixAppHelper::getQuixMediaVersion();

QuixFrontendHelperAssets::prepareApiScript();
QuixFrontendHelperAssets::loadLiveBuilderAssets();

JFactory::getDocument()
        ->addStylesheet(Asset::getAssetUrl('/css/quix-builder.css'), ['version' => $version], [])
        ->addStyleSheet(Asset::getAssetUrl('/css/qxi.css'))
        ->addStyleSheet(\JUri::root()."media/quixnxt/css/qxicon.css", ['version' => $version])
        ->addScriptDeclaration('const JVERSION = "'.JVERSION.'";')
        ->addScriptDeclaration('const QuixPageAlias = "'.$title.'";')
        ->addScriptDeclaration("var QUIX_PAGE = {$data};")
        ->addScriptDeclaration("var QUIXNXT_DEBUG = {$dataDebug};")
        ->addScriptDeclaration("var QUIX_ROOT_URL = '{$root}';")
        ->addScriptDeclaration("var QUIXNXT_JMEDIA_PATH_URL = '{$jmediaPath}/';")
        ->addScriptDeclaration("window.qx_elements = ".Schema::getAvailableElements().';')
        ->addScriptDeclaration("window.addEventListener('load', function() {var qxAlerts = qxAlerts ?? []; qxAlerts.map(item => qxUIkit.modal.alert(item));qxAlerts = [];});")
        ->addScriptDeclaration("window.QUIX_SHAPES = ".json_encode(file_get_contents(JPATH_SITE.'/media/quixnxt/json/shapes.json')).';');
?>

  <iframe
          title="q-store"
          data-src="https://getquix.net/media/quixblocks/js/qstore.html" id="q-store" style="display: none;"></iframe>

  <script>
      let quixIframeWrapper = document.getElementById('quix-iframe-wrapper');
      quixIframeWrapper.src = quixIframeWrapper.attributes['data-src'].value;
      quixIframeWrapper.style.display = 'block';

      setTimeout(function() {
          document.getElementById('q-store').src = document.getElementById('q-store').attributes['data-src'].value;
      }, 5000);
  </script>

  <script data-cfasync="false" src="<?php echo Asset::getAssetUrl('/js/edit.js'); ?>" type="text/javascript" defer></script>
  <script data-cfasync="false" src="<?php echo Asset::getAssetUrl('/js/assets-helper.js'); ?>" type="text/javascript" defer></script>
  <script data-cfasync="false" src="<?php echo Asset::getAssetUrl('/builder/vendor.js'); ?>" type="text/javascript" defer></script>
  <script data-cfasync="false" src="<?php echo Asset::getAssetUrl('/js/qxfb.js') ?>" type="text/javascript" defer></script>
<?php
$session      = JFactory::getSession();
$status       = $session->get('guide-quix-builder', 'show');
$tourComplete = JFactory::getApplication()->input->cookie->get('guide-quix-builder', null);

// show_tour_guide
$config = JComponentHelper::getParams('com_quix');
$show_tour_guide = $tourComplete = $config->get('guide-quix-builder', 'show');

if ($tourComplete !== 'hide' && $status !== 'hide') { ?>
<script data-cfasync="false" src="https://cdn.jsdelivr.net/npm/shepherd.js@5.0.1/dist/js/shepherd.js" type="text/javascript" defer></script>
<?php
    JFactory::getDocument()->addScript(Asset::getAssetUrl('/js/guide.js'));
} ?>
