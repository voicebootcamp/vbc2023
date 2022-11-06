<?php
/**
* @package    com_quix
* @author     ThemeXpert <info@themexpert.com>
* @copyright  Copyright (C) 2015. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
* @version    1.0.0
*/
// No direct access
defined('_JEXEC') or die;
$sidebar_menu = $this->config->get('sidebar_menu');
$items = QuixFrontendHelperAMP::sidebarMenu($sidebar_menu);
?>
<!-- Start Sidebar -->
<amp-sidebar id="header-sidebar" class="ampstart-sidebar px3  " layout="nodisplay">
    <div class="flex justify-start items-center ampstart-sidebar-header">
        <div role="button" aria-label="close sidebar" on="tap:header-sidebar.toggle" tabindex="0"
            class="ampstart-navbar-trigger items-start">✕</div>
    </div>
    <nav class="ampstart-sidebar-nav ampstart-nav">
        <ul class="list-reset m0 p0 ampstart-label">
            <?php foreach ($items as $key => $item) { ?>
                <li class="ampstart-nav-item ">
                    <?php echo JHtml::_('link', JFilterOutput::ampReplace(htmlspecialchars($item->link, ENT_COMPAT, 'UTF-8', false)), $item->title, ''); ?>
                </li>
            <?php } ?>
        </ul>
    </nav>
</amp-sidebar>
<!-- End Sidebar -->
