<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_jmedia
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$title = JText::_('JTOOLBAR_DELETE');
JText::script('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST');
?>
<script type="text/javascript">
(function($){
	// if any media is selected then only allow to submit otherwise show message
	deleteJMedia = function(){
		if ( $('#folderframe').contents().find('input:checked[name="rm[]"]').length == 0){
			alert(Joomla.JText._('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST'));
			return false;
		}

	JMediaManager.submit('folder.delete');
	};

})(jQuery);
</script>

<button onclick="deleteJMedia()" class="btn btn-small">
	<span class="icon-remove" title="<?php echo $title; ?>"></span> <?php echo $title; ?>
</button>
