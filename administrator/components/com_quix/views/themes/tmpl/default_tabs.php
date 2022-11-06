<?php
/**
* @version    CVS: 1.0.0
* @package    com_quix
* @author     ThemeXpert <info@themexpert.com>
* @copyright  Copyright (C) 2015. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/

// No direct access
defined('_JEXEC') or die;
$app = JFactory::getApplication('administrator');
$filter_collection = $app->getUserStateFromRequest('com_quix.themes.filter.collection', 'filter_collection', '', 'string');
$listFilter = [
    'all' => ['title' => 'All', 'icon' => 'radio-checked'],
    'header' => ['title' => 'Header', 'icon' => 'arrow-up-2'],
    'footer' => ['title' => 'Footer', 'icon' => 'arrow-down-2'],
];
?>
<ul id="themes-tab" class="nav nav-tabs">
    <?php foreach ($listFilter as $key => $filter): ?>
    <?php if ($filter_collection == $key or ($filter_collection == '' && $key == 'all')) { ?>
    <li class="filter-<?php echo $key; ?> active"><?php } else { ?>
    <li class="filter-<?php echo $key; ?>"><?php } ?>
        <a
            href="index.php?option=com_quix&view=themes&filter_collection=<?php echo $key == 'all' ? '' : $key; ?>"><i
                class="icon-<?php echo $filter['icon']; ?>"></i>
            <?php echo $filter['title']; ?></a>
    </li>
    <?php endforeach;?>
</ul>
<style>
    #themes-tab {
        background: #fafbfc;
        padding: 0px;
        border-top: 1px solid #ddd;
    }

    #themes-tab a {
        font-size: 15px;
        width: 217px;
        border-radius: 0px;
        border: none;
        position: relative;
        margin: 0;
        z-index: 1;
        height: 50px;
        line-height: 50px;
        padding: 0px 20px;
    }

    #themes-tab .active a {
        border-bottom: 1px solid #ddd;
    }

    #themes-tab a:hover {
        border-bottom: 1px solid #ddd;
    }

    #themes-tab .filter-all.active a:before {
        background-color: #2172e8;
    }

    #themes-tab .filter-header.active a:before {
        background-color: #d93025;
    }

    #themes-tab .filter-footer.active a:before {
        background-color: #ff679d;
    }

    #themes-tab .active a:before {
        border-top-left-radius: 3px;
        border-top-right-radius: 3px;
        bottom: 0;
        content: '';
        display: block;
        height: 3px;
        left: 0;
        margin: 0 8px;
        position: absolute;
        right: 0;
        bottom: -1px;
        z-index: 6;
    }
</style>