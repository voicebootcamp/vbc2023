<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
?>
<div class="ckinterface ckinterface-labels-big">
	<input id="type" name="type" class=""  value="menuitem" disabled type="hidden" />
	<div class="ck-title"><?php echo JText::_('CK_OPTIONS'); ?></div>
	<div>
		<span class="ckoption-label">
			<?php echo JText::_('CK_MENU_ITEM_ID'); ?>
		</span>
		<span class="ckoption-field">
			<input name="id" class=""  value="" disabled type="text" />
			<a class="ckbutton" href="javascript:void(0)" onclick="CKBox.open({id: 'ckitemsselectpopup', url: MAXIMENUCK.BASE_URL + '&view=items&tmpl=component&type=menuitem&returnFunc=ckUpdateItemId'});" ><?php echo JText::_('CK_SELECT'); ?></a>
		</span>
		<div class="clr"></div>
	</div>
	<div>
		<span class="ckoption-label">
			<?php echo JText::_('CK_TITLE'); ?>
		</span>
		<span class="ckoption-field">
			<input name="title" class="inputbox"  value="" type="text" />
		</span>
		<div class="clr"></div>
	</div>
	<div>
		<span class="ckoption-label">
			<?php echo JText::_('CK_DESCRIPTION'); ?>
		</span>
		<span class="ckoption-field">
			<input id="desc" name="desc" class="inputbox"  value="" type="text" />
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
			<?php echo JText::_('CK_ACCESSKEY'); ?>
		</span>
		<span class="ckoption-field">
			<input id="accesskey" name="accesskey" class="inputbox"  value="" type="text" />
		</span>
		<div class="clr"></div>
	</div>
	<div>
		<span class="ckoption-label">
			<?php echo JText::_('CK_ANCHOR'); ?>
		</span>
		<span class="ckoption-field">
			<input id="anchor" name="anchor" class="inputbox"  value="" type="text" />
		</span>
		<div class="clr"></div>
	</div>
	<div>
		<span class="ckoption-label">
			<?php echo JText::_('CK_REL_ATTRIBUTE'); ?>
		</span>
		<span class="ckoption-field">
			<input id="relattr" name="relattr" class="inputbox"  value="" type="text" />
		</span>
		<div class="clr"></div>
	</div>
	<div>
		<span class="ckoption-label">
			<?php echo JText::_('CK_URL_SUFFIX'); ?>
		</span>
		<span class="ckoption-field">
			<input id="urlsuffix" name="urlsuffix" class="inputbox"  value="" type="text" />
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
	<div class="ck-title"><?php echo JText::_('CK_IMAGE'); ?></div>
	<div>
		<span class="ckoption-label">
			<?php echo JText::_('CK_IMAGE'); ?>
		</span>
		<span class="ckoption-field">
			<input id="imageurl" name="imageurl" class="inputbox"  value="" type="text" />
			<a class="ckbutton" href="javascript:void(0)" onclick="CKBox.open({handler: 'iframe', id: 'ckimagemanager', url: MAXIMENUCK.BASE_URL + '&view=browse&type=image&func=ckSelectFile&field=imageurl&tmpl=component'})" ><?php echo JText::_('CK_SELECT'); ?></a>
			<a class="ckbutton" href="javascript:void(0)" onclick="$ck('#imageurl').attr('value', '');"><?php echo JText::_('CK_CLEAN'); ?></a>
		</span>
		<div class="clr"></div>
	</div>
	<div>
		<span class="ckoption-label">
			<?php echo JText::_('CK_IMAGE_ALIGN'); ?>
		</span>
		<span class="ckoption-field">
			<select  id="imagealign" name="imagealign">
				<option value="moduledefault"><?php echo JText::_('CK_MODULEDEFAULT'); ?></option>
				<option value="default"><?php echo JText::_('CK_DEFAULT'); ?></option>
				<option value="top"><?php echo JText::_('CK_TOP'); ?></option>
				<option value="bottom"><?php echo JText::_('CK_BOTTOM'); ?></option>
				<option value="lefttop"><?php echo JText::_('CK_TOPLEFT'); ?></option>
				<option value="leftmiddle"><?php echo JText::_('CK_MIDDLELEFT'); ?></option>
				<option value="leftbottom"><?php echo JText::_('CK_BOTTOMLEFT'); ?></option>
				<option value="righttop"><?php echo JText::_('CK_TOPRIGHT'); ?></option>
				<option value="rightmiddle"><?php echo JText::_('CK_MIDDLERIGHT'); ?></option>
				<option value="rightbottom"><?php echo JText::_('CK_BOTTOMRIGHT'); ?></option>
			</select>
		</span>
		<div class="clr"></div>
	</div>
	<div>
		<span class="ckoption-label">
			<?php echo JText::_('CK_IMAGE_WIDTH'); ?>
		</span>
		<span class="ckoption-field">
			<input id="imagewidth" name="imagewidth" class="inputbox"  value="" type="text" />
		</span>
		<div class="clr"></div>
	</div>
	<div class="ck-title"><?php echo JText::_('CK_ICON'); ?></div>
	<div>
		<span class="ckoption-label">
			<?php echo JText::_('CK_ICON'); ?>
		</span>
		<span class="ckoption-field">
			<input id="iconclass" name="iconclass" class="inputbox"  value="" type="text" placeholder="Ex : fa fa-eye"/>
			<a class="ckbutton" href="javascript:void(0)" onclick="CKBox.open({handler: 'iframe', id: 'ckiconmanager', url: MAXIMENUCK.BASE_URL + '&view=icons&func=ckSelectFaIcon&field=iconclass&tmpl=component'})" ><?php echo JText::_('CK_SELECT'); ?></a>
			<a class="ckbutton" href="javascript:void(0)" onclick="$ck('#iconclass').attr('value', '');"><?php echo JText::_('CK_CLEAN'); ?></a>
		</span>
		<div class="clr"></div>
	</div>
	<div>
		<span class="ckoption-label">
			<?php echo JText::_('CK_ICON_POSITION'); ?>
		</span>
		<span class="ckoption-field">
			<input id="iconpos" name="iconpos" class="inputbox"  value="" type="text" />
		</span>
		<div class="clr"></div>
	</div>
	<div>
		<span class="ckoption-label">
			<?php echo JText::_('CK_ICON_SIZE'); ?>
		</span>
		<span class="ckoption-field">
			<input id="iconsize" name="iconsize" class="inputbox"  value="" type="text" />
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