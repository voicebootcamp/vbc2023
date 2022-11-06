<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
?>
<div class="ckinterface ckinterface-labels-big">
	<input id="type" name="type" class=""  value="heading" disabled type="hidden" />
	<div class="ck-title"><?php echo JText::_('CK_OPTIONS'); ?></div>
	<div>
		<span class="ckoption-label">
			<?php echo JText::_('CK_TITLE'); ?>
		</span>
		<span class="ckoption-field">
			<input id="title" name="title" class="inputbox"  value="" type="text" />
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
			<?php echo JText::_('CK_HTML_TAG'); ?>
		</span>
		<span class="ckoption-field">
			<select  id="imagealign" name="imagealign">
				<option value="none"><?php echo JText::_('CK_NONE'); ?></option>
				<option value="div">DIV</option>
				<option value="p">P</option>
				<option value="h2">H2</option>
				<option value="h3">H3</option>
				<option value="h4">H4</option>
				<option value="h5">H5</option>
				<option value="h6">H6</option>
			</select>
		</span>
		<div class="clr"></div>
	</div>
	<div>
		<span class="ckoption-label">
			<?php echo JText::_('CK_CSS_CLASS'); ?>
		</span>
		<span class="ckoption-field">
			<input id="cssclass" name="cssclass" class="inputbox"  value="" type="text" />
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