<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  System.stats
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

extract($displayData);

/**
 * Layout variables
 * -----------------
 * @var  PlgSystemStats             $plugin        Plugin rendering this layout
 * @var  \Joomla\Registry\Registry  $pluginParams  Plugin parameters
 * @var  array                      $statsData     Array containing the data that will be sent to the stats server
 */
?>
<div class="alert alert-success js-jinsights-alert" style="display:none;">
	<button data-dismiss="alert" class="close" type="button">×</button>
	<h3>{JoomInsights} <?php echo JText::_('would like your permission to collect some basic statistics.'); ?></h3>
	<p>
		<?php echo JText::_('Want to help us, make even more awesome product? Allow us to collect non-sensitive diagnostic data and usage information.'); ?>
		<a href="#" class="js-jinsights-btn-details alert-link"><?php echo JText::_('Select here to see the information.'); ?></a>
	</p>
	<?php
		echo $plugin->render('stats', compact('statsData'));
	?>
	<p class="actions">
		<small><a href="#" class="js-jinsights-btn-allow-never"><?php echo JText::_('No Thanks'); ?></a></small>
		<a href="#" class="btn btn-success js-jinsights-btn-allow"><?php echo JText::_("Sure, I'd love to help"); ?></a>
	</p>
</div>