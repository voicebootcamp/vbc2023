<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_messages
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
$a = ['banner_bg1.png', 'banner_bg2.png'];
$selected = array_rand($a, 1);
?>
<div class="freewarning-banner" style="margin: 0 auto 20px;text-align: center;">
	<a target="_blank"
		href="https://www.themexpert.com/quix-pagebuilder?utm_campaign=quix-pro&utm_source=joomla-admin&utm_medium=top-banner">
		<img style="width:auto;"
			src="<?php echo JUri::root() . 'libraries/quixnxt/assets/images/' . $a[$selected]; ?>">
	</a>
</div>
