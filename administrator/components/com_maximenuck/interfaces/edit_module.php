<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
?>
<div class="ckinterface ckinterface-labels-big">
	<input id="type" name="type" class=""  value="module" disabled type="hidden" />
	<input name="title" class="inputbox"  value="" type="hidden" />
	<div class="ck-title"><?php echo JText::_('CK_OPTIONS'); ?></div>
	<div>
		<span class="ckoption-label">
			<?php echo JText::_('CK_MODULE_ID'); ?>
		</span>
		<span class="ckoption-field">
			<input name="id" class=""  value="" disabled type="text" />
			<a class="ckbutton" href="javascript:void(0)" onclick="CKBox.open({id: 'ckitemsselectpopup', url: MAXIMENUCK.BASE_URL + '&view=items&tmpl=component&type=module&returnFunc=ckUpdateItemId'});" ><?php echo JText::_('CK_SELECT'); ?></a>
		</span>
		<div class="clr"></div>
	</div>
	<div>
		<span class="ckoption-label">
			<?php echo JText::_('CK_EDIT dans popup'); ?>
		</span>
		<span class="ckoption-field">
			<input id="itemeditbutton" name="itemeditbutton" class="inputbox"  value="" type="text" />
		</span>
		<div class="clr"></div>
	</div>
	<div>
		<span class="ckoption-label">
			<?php echo JText::_('CK_LI_CSS_CLASS'); ?>
		</span>
		<span class="ckoption-field">
			<input id="liclass" name="liclass" class="inputbox"  value="" type="text" />
		</span>
		<div class="clr"></div>
	</div>
	<div class="ck-title"><?php echo JText::_('CK_RESPONSIVE_SETTINGS'); ?></div>
	<div>
		<span class="ckoption-label">
			<?php echo JText::_('CK_MOBILE_ENABLE'); ?>
		</span>
		<span class="ckoption-field">
			<select class="inputbox" type="list" value="1" name="mobile" id="mobile" style="width:auto;">
				<option value="1"><?php echo JText::_('JYES'); ?></option>
				<option value="0"><?php echo JText::_('JNO'); ?></option>
			</select>
		</span>
		<div class="clr"></div>
	</div>
	<div>
		<span class="ckoption-label">
			<?php echo JText::_('CK_DESKTOP_ENABLE'); ?>
		</span>
		<span class="ckoption-field">
			<select class="inputbox" type="list" value="1" name="desktop" id="desktop" style="width:auto;">
				<option value="1"><?php echo JText::_('JYES'); ?></option>
				<option value="0"><?php echo JText::_('JNO'); ?></option>
			</select>
		</span>
		<div class="clr"></div>
	</div>
</div>
<script>
//ckLoadEdition();
//ckInitColorPickers();
//ckInitOptionsTabs();
//ckInitAccordions();
</script>