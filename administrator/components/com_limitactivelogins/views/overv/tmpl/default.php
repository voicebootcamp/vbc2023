<?php
/* ======================================================
 # Limit Active Logins for Joomla! - v1.1.2 (pro version)
 # -------------------------------------------------------
 # For Joomla! CMS (v4.x)
 # Author: Web357 (Yiannis Christodoulou)
 # Copyright (©) 2014-2022 Web357. All rights reserved.
 # License: GNU/GPLv3, http://www.gnu.org/licenses/gpl-3.0.html
 # Website: https:/www.web357.com
 # Demo: https://limitactivelogins.web357.com/
 # Support: support@web357.com
 # Last modified: Thursday 31 March 2022, 12:05:22 PM
 ========================================================= */

defined('_JEXEC') or die;
?>

<div id="j-sidebar-container" class="span2">
    <?php echo $this->sidebar; ?>
</div>

<div id="j-main-container" class="span10">		

	<div class="row-fluid">
    
        <div class="span7">
        	<?php
			echo $this->form->getLabel('description');
			?>
        </div>
        
        <div class="span5">
			<?php
            echo $this->form->getLabel('W357FRM_HEADER_VERSION_CHECK');
            echo $this->form->getLabel('info');
            ?>
            <?php
            echo $this->form->getLabel('jedreview');
            ?>
        </div>
        
    </div>

    <?php echo Web357Framework\Functions::showFooter("com_limitactivelogins", JText::_('COM_LIMITACTIVELOGINS_CLEAN')); ?>

</div>