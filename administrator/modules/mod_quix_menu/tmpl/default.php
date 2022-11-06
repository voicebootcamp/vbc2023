<?php
/**
 * @package     Quix
 * @author      ThemeXpert http://www.themexpert.com
 * @copyright   Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @since       1.0.0
 */

// No direct access.
defined('_JEXEC') or die;
?>
<ul id="menu"
    class="nav nav-quix<?php echo($hideMainMenu ? ' disabled' : ''); ?>">
  <li
          class="dropdown<?php echo($hideMainMenu ? ' disabled' : ''); ?>">
    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
        <?php echo JText::_('COM_QUIX'); ?>
      <span class="caret"></span>
    </a>

      <?php if ( ! $hideMainMenu) : ?>
        <ul class="dropdown-menu">

          <li class="dropdown-submenu">
            <a class="dropdown-toggle menu-pages" data-toggle="dropdown"
               href="<?php echo JRoute::_('index.php?option=com_quix&view=pages'); ?>">
                <?php echo JText::_('MOD_QUIX_MENU_PAGES'); ?>
            </a>
            <ul class="dropdown-menu menu-scrollable">
              <li>
                <a class="no-dropdown menu-banners" target="_blank"
                   href="<?php echo JUri::root().'index.php?option=com_quix&task=page.add&quixlogin=true'; ?>">
                    <?php echo JText::_('MOD_QUIX_MENU_ADD_NEW'); ?>
                </a>
              </li>
            </ul>
          </li>

          <li class="dropdown-submenu">
            <a class="dropdown-toggle menu-collections" data-toggle="dropdown"
                    href="<?php echo JRoute::_('index.php?option=com_quix&view=collections'); ?>">
                <?php echo JText::_('MOD_QUIX_MENU_LIBRARIES'); ?>
            </a>
            <ul class="dropdown-menu menu-scrollable">
              <li>
                <a class="no-dropdown menu-header"
                   href="<?php echo JUri::base().'index.php?option=com_quix&view=collections&filter_collection=header'; ?>">
                    <?php echo JText::_('MOD_QUIX_MENU_HEADERS'); ?>
                </a>
              </li>
              <li>
                <a class="no-dropdown menu-header"
                   href="<?php echo JUri::base().'index.php?option=com_quix&view=collections&filter_collection=footer'; ?>">
                    <?php echo JText::_('MOD_QUIX_MENU_FOOTERS'); ?>
                </a>
              </li>
            </ul>
          </li>

          <!--
            <li>
                        <a
                            href="<?php echo JRoute::_('index.php?option=com_quix&view=themes'); ?>">
            <?php echo JText::_('MOD_QUIX_MENU_THEMES'); ?>
            </a>
    </li>
    -->

          <li>
            <a
                    href="<?php echo JRoute::_('index.php?option=com_quix&view=integrations'); ?>">
                <?php echo JText::_('MOD_QUIX_MENU_INTEGRATIONS'); ?>
            </a>
          </li>

          <!-- <li>
        <a
            href="<?php echo JRoute::_('index.php?option=com_quix&view=elements'); ?>">
    <span><?php echo JText::_('MOD_QUIX_MENU_ELEMENTS_LIST') ?></span>
    </a>
    </li> -->

          <!--<li>-->
          <!--    <a href="index.php?option=com_quix">-->
          <!--        --><?php //echo JText::_('MOD_QUIX_MENU_DASHBOARD'); ?>
          <!--    </a>-->
          <!--</li>-->

          <li>
            <a
                    href="<?php echo JRoute::_('index.php?option=com_quix&view=help'); ?>">
                <?php echo JText::_('MOD_QUIX_MENU_HELP'); ?>
            </a>
          </li>

          <!-- <li>
                        <a href="<?php echo JRoute::_('index.php?option=com_quix&view=filemanager'); ?>">
    <span><?php echo JText::_('MOD_QUIX_MENU_FILEMANAGER_LIST') ?></span>
    </a>
    </li> -->

          <li>
            <a href="https://www.facebook.com/groups/QuixUserGroup" target="_blank"
               rel="noopener noreferrer">
              <span><?php echo JText::_('MOD_QUIX_MENU_FEED_BACK') ?></span>
            </a>
          </li>

        </ul>
      <?php endif; ?>
  </li>
</ul>
