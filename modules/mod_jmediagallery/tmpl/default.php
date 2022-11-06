<?php
/**
 * @version    1.0.0
 * @package    com_jmedia
 * @author     ThemeXpert <info@themexpert.com>
 * @copyright  Copyright (C) 2020. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined ('_JEXEC') or die ('restricted access');

$folders = [];
$items = '';
foreach ($files as $filePath) {
    $folderCurrent = "all";
    $realPath = str_replace($getFolder . '/', '', $filePath);
    $dirF = explode('/', $realPath);
    $total = count($dirF);

    $folders[] = $total <= 1 ? 'all' : $dirF[$total-2] ;

    $fileTag = '';
    $fileTags = array_unique($folders);
    foreach($fileTags as $key => $value) {
        if($key != 0) {
            $fileTag = "<div class='grid-item ".$getOptions['layout']." ".$value."' data-filter='".$value."'><a href='". JUri::root() . $filePath ."' class='image-link'><img src='". JUri::root() . $filePath ."' /></a></div>";;
        } else {
            $fileTag = "<div class='grid-item ".$getOptions['layout']." ".$value."' data-filter='".$value."'><a href='". JUri::root() . $filePath ."' class='image-link'><img src='". JUri::root() . $filePath ."' /></a></div>";;
        }
    }
    $items = $items . $fileTag;
}
$folders = array_unique($folders);

?>

<div class="quick-gallery <?php echo $moduleclass_sfx; ?>" >
    <?php if($getOptions['filter'] == 1) : ?>
        <ul id="filters" class="clearfix">
            <li><span class="filter active" data-filter="all">All</span></li>
            <?php foreach ($folders as $key => $folder) : ?>
                <?php if ($key != 0) : ?>
                    <li><span class="filter" data-filter="<?php echo ".".$folder; ?>"><?php echo ucfirst($folder); ?></span></li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <div id="gridItesList">
        <?php echo $items; ?>
    </div>
    <div class="clearfix"></div>
</div>
