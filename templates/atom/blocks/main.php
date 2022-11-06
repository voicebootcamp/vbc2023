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

/**
 * Adjust content width
 */
$leftSidebar  = $this->countModules('left-sidebar');
$rightSidebar = $this->countModules('right-sidebar');
if ($leftSidebar && $rightSidebar) {
    $gridWidth = 'qx-width-1-2';
} elseif ($leftSidebar && ! $rightSidebar) {
    $gridWidth = 'qx-width-2-3';
} elseif ( ! $leftSidebar && $rightSidebar) {
    $gridWidth = 'qx-width-2-3';
} else {
    $gridWidth = 'qx-width-1-1';
}
?>
<!-- Body -->
<section class="qx-section<?php echo $this->params->get('layout') !== '1' ? ' qx-padding-remove-vertical' : ''; ?>">
  <div class="qx-container-fluid">
    <div class="qx-grid qx-grid-stack" qx-grid>
        <?php if ($leftSidebar) : ?>
          <div class="qx-width-1-4">
            <div class="sidebar">
              <jdoc:include type="modules" name="left-sidebar" style="xhtml" />
            </div>
          </div>
        <?php endif; ?>
      <main id="content" class="main-content <?php echo $gridWidth ?>" role="main">
          <?php
          $results = JFactory::getApplication()->triggerEvent('onQuixLoadMainbody');
          if ( ! $results) {
              ?>
            <jdoc:include type="message" />
            <jdoc:include type="component" />
          <?php } else {
              echo implode("\n", $results);
          }
          ?>
      </main>
        <?php if ($rightSidebar) : ?>
          <div class="qx-width-1-4">
            <div class="sidebar">
              <jdoc:include type="modules" name="right-sidebar" style="xhtml" />
            </div>
          </div>
        <?php endif; ?>
    </div>
  </div>
</section>
