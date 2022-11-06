<?php
/*
 ****************************************************************
 Copyright (C) 2008-2020 Soft Ventures, Inc. All rights reserved.
 ****************************************************************
 * @package	Appointment Booking Pro - ABPro
 * @copyright	Copyright (C) 2008-2020 Soft Ventures, Inc. All rights reserved.
 * @license	GNU/GPL, see http://www.gnu.org/licenses/gpl-2.0.html
 *
 * ABPro is distributed WITHOUT ANY WARRANTY, or implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This header must not be removed. Additional contributions/changes
 * may be added to this header as long as no information is deleted.
 *
 ************************************************************
 The latest version of ABPro is available to subscribers at:
 http://www.appointmentbookingpro.com/
 ************************************************************
*/

defined('_JEXEC') or die('Restricted access');



	$jinput = JFactory::getApplication()->input;
	$req_id = $jinput->getString( 'req_id', '' );
	$itemid = $jinput->getString( 'Itemid', '' );
	$option = $jinput->getString( 'option', '' );
	$which_message = $jinput->getString( 'which_message', 'confirmation' );
	$frompage = $jinput->getString( 'frompage', '' );
	$cc = $jinput->getString( 'cc', '' );

	include_once( JPATH_SITE."/components/com_rsappt_pro3/functions2.php" );

	if($cc == "cart"){
		// used cart confirmation stored in session
		$session = JFactory::getSession();
		$message = $session->get('confirmation_message');
	} else {
		$message = buildMessage($req_id, $which_message, "No", $cc, "Yes");
	}
	
	// get config stuff
	$database = JFactory::getDBO();
	$sql = 'SELECT * FROM #__sv_apptpro3_config';
	try{
		$database->setQuery($sql);
		$apptpro_config = NULL;
		$apptpro_config = $database -> loadObject();
	} catch (RuntimeException $e) {
		logIt($e->getMessage(), "wiz_confirm_tmpl_default", "", "");
		echo JText::_('RS1_SQL_ERROR');
		return false;
	}		
	$appWeb      = JFactory::getApplication();
	

?>
<script language="javascript">
	function do_continue(){
		document.getElementById("task").value="do_continue";
		document.frmRequest.submit();
	}

	function do_book_another(){
//		document.getElementById("task").value="do_book_another";
//		document.frmRequest.submit();
		window.history.go(-1);
	}
	
</script>
<style media="print" type="text/css">
@media print
{
	body * { visibility: hidden; }
	#sv_apptpro_request_gad * { visibility: visible; }
	#sv_apptpro_request_gad { position: absolute; top: 40px; left: 30px; }
	#sv_apptpro_request_gad input { visibility: hidden; }
}
</style>

<form name="frmRequest" action="<?php echo $this->request_url ?>" method="post">
<div id="sv_apptpro_request_gad" class="abpro_confirmation_screen">
<?php $document = JFactory::getDocument();
$document->addStyleSheet( JURI::base( true )."/components/com_rsappt_pro3/sv_apptpro.css");
?>

	<p>
	<div id="confirmation">
	<?php echo $message; ?>
    </div>
    </p>
	<!--<p>
    <a href=# onclick="do_book_another();return(false);"><?php echo JText::_('RS1_GAD_CONFIRMATION_BOOK_ANOTHER');?></a>
    </p>-->

	<?php if(!$appWeb->client->mobile){ ?>
	<p>
    <input type="button" value="<?php echo JText::_('RS1_PRINT_THIS_PAGE');?>" onclick="window.print();">
    <!--<input type="button" value="<?php echo JText::_('RS1_PRINT_THIS_PAGE');?>" onclick="PrintElem('confirmation');">-->
    </p>
	<?php } ?>

  <input type="hidden" name="option" value="<?php echo $option; ?>" />
  <input type="hidden" name="controller" value="bookingscreengad" />
  <input type="hidden" name="task" id="task" value="" />
  <input type="hidden" name="frompage_item" value="<?php echo $itemid ?>" />
  <input type="hidden" name="frompage" value="<?php echo $frompage ?>" />
  <br />
      <?php if($apptpro_config->hide_logo == 'No'){ ?>
        <span style="font-size:10px"> Appointment Booking Pro Ver. 4.0.5 - Copyright 2008-20<?php echo date("y");?> - <a href='http://www.softventures.com' target="_blank">Soft Ventures, Inc.</a></span>
      <?php } ?>
</div>
</form>
