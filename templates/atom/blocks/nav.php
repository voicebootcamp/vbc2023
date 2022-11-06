<?php
/**
 * @package     Quix.Joomla Page Builder
 * @subpackage  Templates.Atom
 *
 * @copyright   Copyright (C) 2005 - 2020 ThemeXpert, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
/** @var JDocumentHtml $this */
defined('_JEXEC') or die; ?>

<?php if ($this->params->get('hide_nav') !== '1') : ?>
  <nav class="qx-navbar-container" id="header">
    <div class="qx-container qx-navbar" qx-navbar>
      <div class="qx-navbar-left qx-width-3-5 qx-width-1-5@m">
        <a class="qx-navbar-item qx-logo" href="<?php echo $this->baseurl; ?>/">
            <?php echo AtomHelper::getSiteLogo(); ?>
        </a>

      </div>
      <div class="qx-navbar-right">
        <jdoc:include type="modules" name="position-1" style="none" />
      </div>
  </nav>
<?php endif; ?>
