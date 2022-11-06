<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_jmedia
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
$feature = [
	['icon' => 'tree-2', 'title' => 'Directory Tree','desc' => 'Beautiful directory tree with collapse and expand option.'],
	['icon' => 'search', 'title' => 'File Search','desc' => 'File Search, File Filter by Type and ordering.'],
	['icon' => 'contract-2', 'title' => 'Upload Files','desc' => 'Upload from computer and Upload Files from URL'],
	['icon' => 'cube', 'title' => 'Files and Folders manage','desc' => 'Create Folders, Remove Files, Remove Folders'],
	['icon' => 'file-2', 'title' => 'File Operations','desc' => 'Copy and Move Files and Folders, File Rename'],
	['icon' => 'key', 'title' => 'Change File Permissions','desc' => 'Ability to Change File Permissions'],
	['icon' => 'new', 'title' => 'Extra','desc' => 'Copy Url, File Download'],
	['icon' => 'list', 'title' => 'Files List','desc' => 'Grid View, List View'],
	['icon' => 'checkedout', 'title' => 'ACL','desc' => 'Joomla! ACL Support and File Type and Upload Restriction.']
];
?>
<style type="text/css">
	#about-content{
		color: #606060;
		font-family: "Open Sans",sans-serif;
		font-weight: 400;
		line-height: 1.8;
		text-align: left;
		font-size: 1.6rem;
	}
	#about-content p{font-size: 1rem;}
	#about-content .wrap-icon{padding-right: 15px;}
	#about-content [class^="icon-"]:before{color: #c67605;}
  #about-content .grid {
      margin: 30px 0;
      display: grid;
      grid-template-columns: 1fr 1fr;
      grid-column-gap: 30px;
      width: auto;
  }
  #about-content .grid .grid-item {
      border: 1px solid #ccc;
      padding: 20px 20px 0px;
      border-radius: 4px;
  }
</style>
<div id="about-content" class="container-fluid">
    <h1 class="center content-title" style="font-size:40px;margin: 10px auto 30px;">JMedia - Features</h1>
    
    <div class="grid">
    <?php foreach ($feature as $key=>$item) { ?>
    	<?php if( $key != 0 && $key % 2 == 0 ){ ?>
    		</div><div class="grid">
		<?php } ?>
		    <div class="grid-item">
		    	<h3 class="content-title">
		    		<span class="wrap-icon"><i class="icon-<?php echo $item['icon']; ?>"></i></span>
		    		<span class="wrap-title"><?php echo $item['title']; ?></span>
		    	</h3>
		    	<p><?php echo $item['desc']; ?></p>
		    </div>
    <?php } ?>
	</div>
</div>
