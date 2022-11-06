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
if ($this->params->get('hide_footer') !== '1') : ?>
  <!-- Footer -->
  <footer class="qx-section qx-section-default footer" role="contentinfo">
    <div class="qx-container<?php echo($this->params->get('fluidContainer') ? '-fluid' : ''); ?>">

      <jdoc:include type="modules" name="footer" style="none" />

      <hr />
      <p class="qx-align-right">
        <a href="#header" id="back-top" class="qx-link-text" qx-scroll>
            <?php echo JText::_('TPL_ATOM_BACKTOTOP'); ?>
        </a>
      </p>
      <p>
        &copy; <?php echo date('Y'); ?> <?php echo AtomHelper::getSiteName(); ?> by <a href="https://www.themexpert.com/" target="_blank" class="qx-link-text">ThemeXpert</a>
      </p>
    </div>
  </footer>
<?php endif; ?>

<jdoc:include type="modules" name="debug" style="none" />
