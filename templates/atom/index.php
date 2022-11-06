<?php
/**
 * @package     Quix.Joomla Page Builder
 * @subpackage  Templates.Atom
 *
 * @copyright   Copyright (C) 2005 - 2020 ThemeXpert, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
/** @var JDocumentHtml $this */
defined('_JEXEC') or die;
require_once __DIR__.'/AtomHelper.php';

// Add html5 shiv
JHtml::_('script', 'jui/html5.js', ['version' => 'auto', 'relative' => true, 'conditional' => 'lt IE 9']);
?>
<?php AtomHelper::prepareHead(); ?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <?php AtomHelper::preloadAssets(); ?>
  <?php AtomHelper::loadJQuery(); ?>
  <?php AtomHelper::getTemplateColor(); ?>
  <?php AtomHelper::addCriticalCss(); ?>
  <jdoc:include type="head" />
</head>

<body class="tpl_atom site <?php echo AtomHelper::getBodyClass(); ?>">
  <?php if ($this->params->get('layout') == 1) : ?><div class="qx-page-container"><div class="qx-boxed-layout qx-margin-auto"><?php endif; ?>

      <?php include_once __DIR__.'/blocks/nav.php'; ?>

      <?php include_once __DIR__.'/blocks/main.php'; ?>

      <?php include_once __DIR__.'/blocks/footer.php'; ?>

      <?php AtomHelper::loadQuixCss(); ?>
      <?php AtomHelper::loadQuixVendorJs(); ?>
  <?php if ($this->params->get('layout') == 1) : ?></div></div><?php endif; ?>
</body>
</html>
