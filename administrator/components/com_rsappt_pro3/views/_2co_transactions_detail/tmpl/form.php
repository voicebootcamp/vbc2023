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

?>
<script language="javascript">

<form action="<?php echo JRoute::_($this->request_url) ?>" method="post" name="adminForm" id="adminForm">
<link href="<?php echo JURI::root( true );?>/components/com_rsappt_pro3/sv_apptpro.css" rel="stylesheet">
<fieldset class="adminform">
  <table border="0" cellpadding="2" cellspacing="0">
    <tr class="admin_detail_row0">
      <td colspan="2"><u><?php echo JText::_('RS1_ADMIN_SCRN_2CO_DETAIL_INTRO');?></u></td>
    </tr>
    <tr class="admin_detail_row1">
      <td><?php echo JText::_('RS1_ADMIN_SCRN_2CO_TXN_DETAIL_ID');?></td>
      <td><?php echo $this->detail->order_number; ?>&nbsp;</td>
    </tr>
    <tr class="admin_detail_row0">
      <td><?php echo JText::_('RS1_ADMIN_SCRN_2CO_TXN_DETAIL_REQID');?></td>
      <td colspan="2"><input type="text" readonly="readonly" size="10" maxsize="10" name="merchant_order_id" value="<?php echo $this->detail->merchant_order_id; ?>" /></td>
    </tr>
    <tr class="admin_detail_row1">
      <td><?php echo JText::_('RS1_ADMIN_SCRN_2CO_TXN_DETAIL_FIRSTNAME');?></td>
      <td colspan="2"><input type="text" readonly="readonly" size="20" maxsize="100" name="firstname" value="<?php echo stripslashes($this->detail->first_name); ?>" /></td>
    </tr>
    <tr class="admin_detail_row0">
      <td><?php echo JText::_('RS1_ADMIN_SCRN_2CO_TXN_DETAIL_LASTNAME');?></td>
      <td colspan="2"><input type="text" readonly="readonly" size="20" maxsize="100" name="lastname" value="<?php echo stripslashes($this->detail->last_name); ?>" /></td>
    </tr>
    <tr class="admin_detail_row1">
      <td><?php echo JText::_('RS1_ADMIN_SCRN_2CO_TXN_DETAIL_EMAIL');?></td>
      <td colspan="2"><input type="text" readonly="readonly" size="50" maxsize="100" name="email" value="<?php echo $this->detail->email; ?>" /></td>
    </tr>
    <tr class="admin_detail_row0">
      <td><?php echo JText::_('RS1_ADMIN_SCRN_2CO_TXN_DETAIL_PHONE');?></td>
      <td colspan="2"><input type="text" readonly="readonly" size="20" maxsize="100" name="phone" value="<?php echo $this->detail->phone; ?>" /></td>
    </tr>
    <tr class="admin_detail_row1">
      <td><?php echo JText::_('RS1_ADMIN_SCRN_2CO_TXN_DETAIL_STREET');?></td>
      <td colspan="2"><input type="text" readonly="readonly" size="50" maxsize="100" name="street" value="<?php echo $this->detail->street_address; ?>" /></td>
    </tr>
    <tr class="admin_detail_row0">
      <td><?php echo JText::_('RS1_ADMIN_SCRN_2CO_TXN_DETAIL_CITY');?></td>
      <td colspan="2"><input type="text" readonly="readonly" size="20" maxsize="50" name="city" value="<?php echo $this->detail->city; ?>" /></td>
    </tr>
    <tr class="admin_detail_row1">
      <td><?php echo JText::_('RS1_ADMIN_SCRN_2CO_TXN_DETAIL_PROVSTATE');?></td>
      <td colspan="2"><input type="text" readonly="readonly" size="20" maxsize="40" name="state" value="<?php echo $this->detail->state; ?>" /></td>
    </tr>
    <tr class="admin_detail_row0">
      <td><?php echo JText::_('RS1_ADMIN_SCRN_2CO_TXN_DETAIL_POSTALZIP');?></td>
      <td colspan="2"><input type="text" readonly="readonly" size="20" maxsize="21" name="zipcode" value="<?php echo $this->detail->zip; ?>" /></td>
    </tr>
    <tr class="admin_detail_row1">
      <td><?php echo JText::_('RS1_ADMIN_SCRN_2CO_TXN_DETAIL_COUNTRY');?></td>
      <td colspan="2"><input type="text" readonly="readonly" size="20" maxsize="21" name="country" value="<?php echo $this->detail->country; ?>" /></td>
    </tr>
    <tr class="admin_detail_row0">
      <td><?php echo JText::_('RS1_ADMIN_SCRN_2CO_TXN_DETAIL_AMOUNT');?></td>
      <td colspan="2"><input type="text" readonly="readonly" size="6" maxsize="6" name="mc_gross" value="<?php echo $this->detail->total; ?>" /></td>
    </tr>
    <tr class="admin_detail_row1">
      <td><?php echo JText::_('RS1_ADMIN_SCRN_2CO_TXN_DETAIL_ITEM');?></td>
      <td colspan="2"><input type="text" readonly="readonly" size="50" maxsize="50" name="li_1_name" value="<?php echo $this->detail->li_1_name; ?>" /></td>
    </tr>
    <tr class="admin_detail_row0">
      <td><?php echo JText::_('RS1_ADMIN_SCRN_2CO_TXN_DETAIL_STAMP');?></td>
      <td colspan="2"><input type="text" readonly="readonly" size="50" maxsize="10" name="reasoncode" value="<?php echo $this->detail->stamp; ?>" /></td>
    </tr>
<!--    <tr class="admin_detail_row0">
      <td valign="top"><?php echo JText::_('RS1_ADMIN_SCRN_PP_MEMO');?></td>
      <td colspan="2"><textarea rows="2" cols="30" readonly="readonly" ><?php echo stripslashes($this->detail->memo); ?></textarea></td>
    </tr>
-->  </table>
</fieldset>
  <input type="hidden" name="id_authnet_transactions" value="<?php echo $this->detail->id_authnet_transactions; ?>" />
  <input type="hidden" name="task" value="" />
  <input type="hidden" name="controller" value="_2co_transactions_detail" />
  <input type="hidden" name="frompage" value="<?php echo $this->frompage; ?>" />
  <br />
  <span style="font-size:10px"> Appointment Booking Pro Ver. 4.0.5 - Copyright 2008-20<?php echo date("y");?> - <a href='http://www.softventures.com' target="_blank">Soft Ventures, Inc.</a></span>
</form>
