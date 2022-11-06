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

// Output as HTML5
$this->setHtml5(true);
AtomHelper::prepareHead();

?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <?php AtomHelper::preloadAssets(); ?>
    <?php AtomHelper::loadJQuery(); ?>
  <jdoc:include type="head" />
</head>
<body class="contentpane modal<?php echo $this->direction === 'rtl' ? ' rtl' : ''; ?>">
<jdoc:include type="message" />
<jdoc:include type="component" />
</body>
</html>
