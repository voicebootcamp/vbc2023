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
<nav class="main-nav-container" aria-label="Quix Main Menu">
    <ul class="nav flex-column main-nav metismenu">
        <li class="item item-level-1">
            <a class="no-dropdown"
               href="index.php?option=com_quix&amp;view=pages"
               aria-label="Help"
            >
                <span class="icon-puzzle-piece icon-fw" aria-hidden="true"></span>
                <span class="sidebar-item-title"><?php echo JText::_('MOD_QUIX_QUIX_TITLE'); ?></span>
            </a>
        </li>
    </ul>
</nav>
