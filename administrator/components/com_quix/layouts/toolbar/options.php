<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_messages
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
$component = urlencode('com_quix');
$path = urlencode('');

$uri = (string) JUri::getInstance();
$return = urlencode(base64_encode($uri));
?>
<a href="<?php echo 'index.php?option=com_config&amp;view=component&amp;component=' . $component . '&amp;path=' . $path . '&amp;return=' . $return; ?>"
    class="btn">
    <i class="icon-cog"></i>
    Settings
</a>