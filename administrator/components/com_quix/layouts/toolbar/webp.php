<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_messages
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die; 
?>
<div id="webp-alert" class="alert alert-warning clearfix" style="font-family: sans-serif;font-size: 13px;">
	<button type="button" class="close" data-dismiss="alert" aria-label="Close">
		<span aria-hidden="true">&times;</span>
	</button>
	<p style="margin: 0px;"><strong style="margin: 0 0 10px;">WebP not enabled!</strong></p>
	<p style="margin: 0px;">Image formats like WebP often provide better compression than PNG or JPEG, which means faster downloads and less data consumption. You'll need to enable <strong>PHP GD library</strong> for WebP compression. Consult <a href="https://www.themexpert.com/docs/quix-builder/optimization/gd-library" target="_blank"><b>this guide</b></a> or talk to your webhost to enable PHP GD library.</p>
</div>
<script type="text/javascript">
jQuery(document).ready(function(){
	sessionStorage.getItem("hideWebpAlert") == "true" ? jQuery('#webp-alert').hide() : ''; 
});
jQuery('#webp-alert').on('closed.bs.alert', function () {
	sessionStorage.setItem("hideWebpAlert", true);
})
</script>