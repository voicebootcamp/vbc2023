<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_messages
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Include the HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
?>
<form action="<?php echo JRoute::_('index.php?option=com_quix'); ?>" method="post" name="adminForm" id="message-form" class="form-validate form-horizontal">
<?php if (!empty($this->sidebar)): ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
   <div id="j-main-container" class="span10">
	<?php else : ?>
	<div id="j-main-container">
	<?php endif; ?>
    <?php echo QuixHelper::askreview(); ?>
    
        <div class="card center" style="width: 70%;margin: 0 auto;">
          <div class="card-body">
              <h3>BUILT-IN SEO TOOLS</h3>
              <h1>Rank Your Page Way Faster Than Others</h1>
              <img src="<?php echo JUri::root(); ?>/media/quix/images/Quix2_SEO.png" style="height: 400px;" />
              <br><br><br>
              <p class="lead">With QuixRank, our unrivaled SEO feature, you'll make search engine friendly pages without needing an SEO expert.</p>
                
              <?php echo QuixHelper::getBuyPro('rank'); ?>     
          </div>


        </div>

	</div>
    
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>

</form>
