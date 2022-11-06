<?php

/**
 * @package    com_quix
 * @author     ThemeXpert <info@themexpert.com>
 * @copyright  Copyright (C) 2015. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    1.8.0
 */

// No direct access

use QuixNxt\Utils\Asset;

defined('_JEXEC') or die;
// Load jQuery
JHtml::_('jquery.framework');
$version = QuixAppHelper::getQuixMediaVersion();
?>
<link rel="preconnect" href="https://fonts.gstatic.com">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://ajax.googleapis.com">

<div class="qx-fb-frame">

  <div class="app-mount qx quix">
    <div id="qx-fb-mount"></div>
  </div>
</div>

<!--tinymce editor for inline editing-->
<?php

if (JFile::exists(JPATH_SITE.'/media/editors/tinymce/tinymce.min.js')) {
    JFactory::getDocument()->addScript(JUri::root().'media/editors/tinymce/tinymce.min.js');
} else {
    JFactory::getDocument()->addScript('https://cdnjs.cloudflare.com/ajax/libs/tinymce/5.6.2/tinymce.min.js');
}
?>

<!--core css-->
<link
        href="<?php echo Asset::getAssetUrl('/css/quix-elements.css'); ?>"
        rel="stylesheet" media="all" type="text/css" />
<!--icon-->
<link href="<?php echo Asset::getAssetUrl('/css/qxi.css'); ?>" rel="stylesheet" media="all" type="text/css" />
<link href="<?php echo \JUri::root()."media/quixnxt/css/qxicon.css?".$version; ?>" rel="stylesheet" media="all" type="text/css" />

<link
        href="<?php echo Asset::getAssetUrl('/css/quix-core.css'); ?>"
        rel="stylesheet" media="all" type="text/css" />
<!--core builder only-->
<link
        href="<?php echo Asset::getAssetUrl('/css/quix-builder.css'); ?>"
        rel="stylesheet" media="all" type="text/css" />
<!--iframe inside assets fix-->
<script>
  var quix = <?php echo json_encode(['url' => QUIXNXT_SITE_URL, 'version' => QUIXNXT_VERSION]) ?>;
</script>
<script src="<?php echo Asset::getAssetUrl('/js/iframe.js'); ?>" type="text/javascript"></script>
<script src="<?php echo Asset::getAssetUrl('/js/quix.js'); ?>" type="text/javascript"></script>
<script src="<?php echo Asset::getAssetUrl('/js/qxkit.js'); ?>" type="text/javascript"></script>
<script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js" type="text/javascript" defer></script>

<script>
    var interval = setInterval(function() {window.parent.postMessage({event: 'iframe-loaded'}, window.origin);}, 1000);
    setTimeout(function() {clearInterval(interval);}, 1000 * 60);// stop after one minute
</script>
