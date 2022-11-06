<?php

/**
 * @package         Google Structured Data
 * @version         5.1.6 Pro
 *
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

defined('_JEXEC') or die('Restricted Access');

?>

<div class="nr-box nr-box-hr">
    <div class="nr-box-title col-md-4">
        <?php echo JText::_('GSD_SDTT'); ?>
        <div><?php echo JText::_('GSD_SDTT_DESC'); ?></div>
    </div>
    <div class="nr-box-content">
        <form action="https://search.google.com/test/rich-results" class="gsdtt" target="_blank" method="get">
            <input id="url" name="url" required="true" type="url" placeholder="https://" value="<?php echo JURI::root(); ?>"/>
            <button class="btn btn-primary" type="submit"><?php echo JText::_('GSD_TEST'); ?></button>
        </form>
    </div>
</div>