<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_jmedia
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('behavior.core');

$title = JText::_('Pro');
?>
<span class="btn btn-small has-tooltip" title="Thank you for using Pro">
	<span class="icon-ok" aria-hidden="true"></span>
	<?php echo $title; ?>
</span>
<style type="text/css">#toolbar-pro{float: right;}</style>