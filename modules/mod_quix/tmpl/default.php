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
<div class="mod-quix-library<?php echo $moduleclass_sfx; ?>">
    <?php echo is_object($item) ? $item->text : $item; ?>
</div>
