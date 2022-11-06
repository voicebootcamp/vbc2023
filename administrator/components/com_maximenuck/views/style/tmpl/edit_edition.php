<?php
// No direct access
defined('_JEXEC') or die('Restricted access');

use Maximenuck\CKFof;
use Maximenuck\CKFramework;
use Maximenuck\Helper;
use Maximenuck\Style;

require_once(MAXIMENUCK_PATH . '/helpers/defines.js.php');
require_once(MAXIMENUCK_PATH . '/helpers/style.php');

CKFramework::load();
CKFramework::loadFaIconsInline();
Helper::loadCkbox();

$this->imagespath = MAXIMENUCK_MEDIA_URI .'/images';
$this->colorpicker_class = 'color {required:false,pickerPosition:\'top\',pickerBorder:2,pickerInset:3,hash:true}';

CKFof::addStylesheet(MAXIMENUCK_MEDIA_URI . '/assets/admin.css');
CKFof::addScript(MAXIMENUCK_MEDIA_URI . '/assets/jscolor/jscolor.js');
CKFof::addScript(MAXIMENUCK_MEDIA_URI . '/assets/admin.js');
CKFof::addScript(MAXIMENUCK_MEDIA_URI . '/assets/style.js');

$layout = $this->input->get('layout', '', 'string');
$popupclass = ($layout === 'modal') ? 'ckpopupwizard' : '';
$preview_width = ($this->params->get('orientation', 'horizontal') == 'vertical') ? 'width:200px;' : 'width:300px;';
$theme = $this->params->get('theme', 'blank');
$themecss = file_get_contents(JPATH_ROOT . '/modules/mod_maximenuck/themes/' . $theme . '/css/maximenuck.php');
$themecss = str_replace('<?php echo $id; ?>', 'maximenuck_previewmodule', $themecss);
$moduleId = $this->input->get('frommoduleid', 0, 'int');
?>
<style>
#stylescontainer {
	display: flex;
}

#stylescontainerleft, #stylescontainerright {
	/*float :left;*/
	width: auto;
	padding: 10px;
	box-sizing: border-box;
}

#stylescontainerleft {
	width: 810px;
}

#stylescontainerright {
	width: calc(100% - 850px);
	min-width: 400px;
}

body.contentpane {
	padding-top: 66px;
}

#maximenuck_previewmodule.maximenuckh {
	min-width: 600px;
}

.istopfixed {
	width: 600px;
	right: 10px;
}
</style>

<div class="menustylescustom" data-prefix="menustyles" data-rule="[menustyles]"></div>
<div class="menustylescustom" data-prefix="level1itemnormalstyles" data-rule="[level1itemnormalstyles]"></div>
<div class="menustylescustom" data-prefix="level1itemdescstyles" data-rule="[level1itemdescstyles]"></div>
<div class="menustylescustom" data-prefix="level1itemhoverstyles" data-rule="[level1itemhoverstyles]"></div>
<div class="menustylescustom" data-prefix="level1itemactivestyles" data-rule="[level1itemactivestyles]"></div>
<div class="menustylescustom" data-prefix="level1itemparentstyles" data-rule="[level1itemparentstyles]"></div>
<div class="menustylescustom" data-prefix="level2menustyles" data-rule="[level2menustyles]"></div>
<div class="menustylescustom" data-prefix="level2itemnormalstyles" data-rule="[level2itemnormalstyles]"></div>
<div class="menustylescustom" data-prefix="level2itemhoverstyles" data-rule="[level2itemhoverstyles]"></div>
<div class="menustylescustom" data-prefix="level2itemactivestyles" data-rule="[level2itemactivestyles]"></div>
<div class="menustylescustom" data-prefix="level1itemnormalstylesicon" data-rule="[level1itemnormalstylesicon]"></div>
<div class="menustylescustom" data-prefix="level1itemhoverstylesicon" data-rule="[level1itemhoverstylesicon]"></div>
<div class="menustylescustom" data-prefix="level2itemnormalstylesicon" data-rule="[level2itemnormalstylesicon]"></div>
<div class="menustylescustom" data-prefix="level2itemhoverstylesicon" data-rule="[level2itemhoverstylesicon]"></div>
<div class="menustylescustom" data-prefix="level3menustyles" data-rule="[level3menustyles]"></div>
<div class="menustylescustom" data-prefix="level3itemnormalstyles" data-rule="[level3itemnormalstyles]"></div>
<div class="menustylescustom" data-prefix="level3itemhoverstyles" data-rule="[level3itemhoverstyles]"></div>
<div class="menustylescustom" data-prefix="headingstyles" data-rule="[headingstyles]"></div>
<div class="menustylescustom" data-prefix="fancystyles" data-rule="[fancystyles]"></div>

<div id="ckpopupstyleswizard" class="<?php echo $popupclass; ?>">
<input type="hidden" id="id" name="id" value="<?php echo $this->item->id; ?>" />
	<input type="hidden" id="layoutcss" name="layoutcss" value="<?php echo $this->item->layoutcss; ?>" />
	<input type="hidden" id="params" name="params" value="<?php echo htmlspecialchars($this->item->params); ?>" />
	<input type="hidden" id="returnFunc" name="returnFunc" value="<?php echo htmlspecialchars($this->input->get('returnFunc', '', 'cmd')); ?>" />
	<input type="hidden" id="frommoduleid" name="frommoduleid" value="<?php echo $moduleId; ?>" />
	<?php
	// detection for IE
	if(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== FALSE || strpos($_SERVER['HTTP_USER_AGENT'], 'Trident/7.0; rv:11.0') !== FALSE ) { ?>
	<div class="errorck" style="margin:0 10px;">
		<?php echo JText::_('CK_PLEASE_DO_NOT_USE_IE'); ?>
	</div>
	<?php } ?>
	
	<div id="stylescontainer" style="min-width: 1300px;" class="animateck">
		<div id="stylescontainerleft" class="ckinterface">
		<?php if ($moduleId > 0) { 
			$this->item->name = $this->item->name ? $this->item->name : 'moduleid' . $moduleId;
		?>
			<input type="hidden" id="name" name="name" value="<?php echo $this->item->name; ?>" />
		<?php } else { ?>
			<label for="name" style="display: inline-block;"><?php echo JText::_('CK_NAME'); ?></label>
			<input type="text" id="name" name="name" value="<?php echo $this->item->name; ?>" />
		<?php } ?>
			<div id="styleswizard_options" class="styleswizard">
				<div class="ckinterfacetablink current" data-level="1" data-tab="tab_mainmenu" data-group="main"><?php echo JText::_('CK_MAINMENU'); ?></div>
				<div class="ckinterfacetablink" data-level="1" data-tab="tab_submenu" data-group="main"><?php echo JText::_('CK_SUBMENU'); ?></div>
				<div class="ckinterfacetablink" data-level="1" data-tab="tab_subsubmenu" data-group="main"><?php echo JText::_('CK_SUBSUBMENU'); ?></div>
				<div class="ckinterfacetablink" data-level="1" data-tab="tab_fancy" data-group="main"><?php echo JText::_('CK_FANCY'); ?></div>
				<div class="ckinterfacetablink" data-level="1" data-tab="tab_layout" data-group="main"><?php echo JText::_('CK_LAYOUT'); ?></div>
				<div class="ckinterfacetablink" data-level="1" data-tab="tab_customcss" data-group="main"><?php echo JText::_('CK_CUSTOM_CSS'); ?></div>
				<div class="ckinterfacetablink" data-level="1" data-tab="tab_presets" data-group="main"><?php echo JText::_('CK_PRESETS'); ?></div>
				<div class="ckclr"></div>
				<div class="ckinterfacetab current hascol" data-level="1" id="tab_mainmenu" data-group="main">
					<div class="ckcol_left">
						<div class="ckinterfacetablink current" data-tab="tab_menustyles" data-group="mainmenu"><?php echo JText::_('CK_MENUBAR'); ?></div>
						<div class="ckinterfacetablink" data-tab="tab_level1itemnormalstyles" data-group="mainmenu"><?php echo JText::_('CK_MENULINK'); ?></div>
						<div class="ckinterfacetablink" data-tab="tab_level1itemdescstyles" data-group="mainmenu"><?php echo JText::_('CK_MENULINK_DESC'); ?></div>
						<div class="ckinterfacetablink" data-tab="tab_level1itemhoverstyles" data-group="mainmenu"><?php echo JText::_('CK_MENULINK_HOVER'); ?></div>
						<div class="ckinterfacetablink" data-tab="tab_level1itemactivestyles" data-group="mainmenu"><?php echo JText::_('CK_MENULINK_ACTIVE'); ?></div>
						<div class="ckinterfacetablink" data-tab="tab_level1itemactivehoverstyles" data-group="mainmenu"><?php echo JText::_('CK_MENULINK_ACTIVE_HOVER'); ?></div>
						<div class="ckinterfacetablink" data-tab="tab_level1itemparentarrow" data-group="mainmenu"><?php echo JText::_('CK_PARENT_ARROW'); ?></div>
						<div class="ckinterfacetablink" data-tab="tab_level1itemparentstyles" data-group="mainmenu"><?php echo JText::_('CK_MENULINK_PARENT'); ?></div>
						<div class="ckinterfacetablink" data-tab="tab_level1itemicon" data-group="mainmenu"><?php echo JText::_('CK_ITEM_ICON'); ?></div>
					</div>
					<div class="ckcol_right">
						<div class="ckinterfacetab current" id="tab_menustyles" data-group="mainmenu">
							<div class="ckheading"><?php echo JText::_('CK_TEXT_LABEL'); ?></div>
							<?php include dirname(__FILE__) . '/edit_render_tab_menustyles.php' ?>
							<div class="ckheading"><?php echo JText::_('CK_APPEARANCE_LABEL'); ?></div>
							<?php
							$this->interface->createBackgroundColor('menustyles');
							$this->interface->createBackgroundImage('menustyles');
							$this->interface->createBorders('menustyles');
							$this->interface->createRoundedCorners('menustyles');
							$this->interface->createShadow('menustyles');
							$this->interface->createTextShadow('menustyles');
							?>
							<div class="ckheading"><?php echo JText::_('CK_DIMENSIONS_LABEL'); ?></div>
							<?php
							$this->interface->createMargins('menustyles');
							?>
						</div>
						<div class="ckinterfacetab" id="tab_level1itemnormalstyles" data-group="mainmenu">
							<div class="ckheading"><?php echo JText::_('CK_APPEARANCE_LABEL'); ?></div>
							<?php
							$this->interface->createBackgroundColor('level1itemnormalstyles');
							$this->interface->createBackgroundImage('level1itemnormalstyles');
							$this->interface->createBorders('level1itemnormalstyles');
							$this->interface->createRoundedCorners('level1itemnormalstyles');
							$this->interface->createShadow('level1itemnormalstyles');
							$this->interface->createTextShadow('level1itemnormalstyles');
							?>
							<div class="ckheading"><?php echo JText::_('CK_DIMENSIONS_LABEL'); ?></div>
							<?php
							$this->interface->createMargins('level1itemnormalstyles');
							?>
						</div>
						<div class="ckinterfacetab" id="tab_level1itemdescstyles" data-group="mainmenu">
							<div class="ckheading"><?php echo JText::_('CK_TEXT_LABEL'); ?></div>
							<?php
							$this->interface->createTextShadow('level1itemdescstyles');
							?>
							<div class="ckheading"><?php echo JText::_('CK_DIMENSIONS_LABEL'); ?></div>
							<?php
							$this->interface->createMargins('level1itemdescstyles');
							?>
						</div>
						<div class="ckinterfacetab" id="tab_level1itemhoverstyles" data-group="mainmenu">
							<div class="ckheading"><?php echo JText::_('CK_APPEARANCE_LABEL'); ?></div>
							<?php
							$this->interface->createBackgroundColor('level1itemhoverstyles');
							$this->interface->createBackgroundImage('level1itemhoverstyles');
							$this->interface->createBorders('level1itemhoverstyles');
							$this->interface->createRoundedCorners('level1itemhoverstyles');
							$this->interface->createShadow('level1itemhoverstyles');
							$this->interface->createTextShadow('level1itemhoverstyles');
							?>
							<div class="ckheading"><?php echo JText::_('CK_DIMENSIONS_LABEL'); ?></div>
							<?php
							$this->interface->createMargins('level1itemhoverstyles');
							?>
						</div>
						<div class="ckinterfacetab" id="tab_level1itemactivestyles" data-group="mainmenu">
							<div class="ckrow" style="margin-left:200px;">
								<label></label>
								<div class="ckbutton-group">
									<input checked class="level1itemactivestyles" type="radio" value="1" id="level1itemactivestylesidemhoveryes" name="level1itemactivestylesidemhover"  />
									<label class="ckbutton first" for="level1itemactivestylesidemhoveryes" onclick="$ck('#level1itemactivestyleswrap').hide()"><?php echo JText::_('CK_ACTIVE_SYLES_IDEM_HOVER'); ?></label>
									<input class="level1itemactivestyles" type="radio" value="0" id="level1itemactivestylesidemhoverno" name="level1itemactivestylesidemhover" />
									<label class="ckbutton"  for="level1itemactivestylesidemhoverno" onclick="$ck('#level1itemactivestyleswrap').show()"><?php echo JText::_('CK_ACTIVE_SYLES_CUSTOM'); ?></label>
								</div>
							</div>
							<div id="level1itemactivestyleswrap">
									<div class="ckheading"><?php echo JText::_('CK_TEXT_LABEL'); ?></div>
									<div class="ckrow">
										<label for="level1itemactivestylesfontsize"><?php echo JText::_('CK_TITLEFONTSTYLES_LABEL'); ?></label>
										<img class="ckicon" src="<?php echo $this->imagespath ?>/style.png" />
										<input type="text" id="level1itemactivestylesfontsize" name="level1itemactivestylesfontsize" class="level1itemactivestyles cktip" style="width:45px;" title="<?php echo JText::_('CK_FONTSIZE_DESC'); ?>" />
										<img class="ckicon" src="<?php echo $this->imagespath ?>/color.png" />
										<span><?php echo JText::_('CK_NORMAL'); ?></span>
										<input type="text" id="level1itemactivestylescolor" name="level1itemactivestylescolor" class="level1itemactivestyles cktip <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_FONTCOLOR_DESC'); ?>" />

										</div>
									<div class="ckrow">
										<label for="level1itemactivestylesdescfontsize"><?php echo JText::_('CK_DESCFONTSTYLES_LABEL'); ?></label>
										<img class="ckicon" src="<?php echo $this->imagespath ?>/style.png" />
										<input type="text" id="level1itemactivestylesdescfontsize" name="level1itemactivestylesdescfontsize" class="level1itemactivestyles cktip" style="width:45px;" title="<?php echo JText::_('CK_FONTSIZE_DESC'); ?>" />
										<img class="ckicon" src="<?php echo $this->imagespath ?>/color.png" />
										<span><?php echo JText::_('CK_NORMAL'); ?></span>
										<input type="text" id="level1itemactivestylesdesccolor" name="level1itemactivestylesdesccolor" class="level1itemactivestyles cktip <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_FONTCOLOR_DESC'); ?>" />
									</div>

									<div class="ckheading"><?php echo JText::_('CK_APPEARANCE_LABEL'); ?></div>
									<?php
									$this->interface->createBackgroundColor('level1itemactivestyles');
									$this->interface->createBackgroundImage('level1itemactivestyles');
									$this->interface->createBorders('level1itemactivestyles');
									$this->interface->createRoundedCorners('level1itemactivestyles');
									$this->interface->createShadow('level1itemactivestyles');
									$this->interface->createTextShadow('level1itemactivestyles');
									?>
									<div class="ckheading"><?php echo JText::_('CK_DIMENSIONS_LABEL'); ?></div>
									<?php
									$this->interface->createMargins('level1itemactivestyles');
									?>
							</div>
						</div>
						<div class="ckinterfacetab" id="tab_level1itemactivehoverstyles" data-group="mainmenu">
							<div class="ckheading"><?php echo JText::_('CK_TEXT_LABEL'); ?></div>
							<div class="ckrow">
								<label for="level1itemactivehoverstylesfontsize"><?php echo JText::_('CK_TITLEFONTSTYLES_LABEL'); ?></label>
								<img class="ckicon" src="<?php echo $this->imagespath ?>/style.png" />
								<input type="text" id="level1itemactivehoverstylesfontsize" name="level1itemactivehoverstylesfontsize" class="level1itemactivehoverstyles cktip" style="width:45px;" title="<?php echo JText::_('CK_FONTSIZE_DESC'); ?>" />
								<img class="ckicon" src="<?php echo $this->imagespath ?>/color.png" />
								<span><?php echo JText::_('CK_NORMAL'); ?></span>
								<input type="text" id="level1itemactivehoverstylescolor" name="level1itemactivehoverstylescolor" class="level1itemactivehoverstyles cktip <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_FONTCOLOR_DESC'); ?>" />

								</div>
							<div class="ckrow">
								<label for="level1itemactivehoverstylesdescfontsize"><?php echo JText::_('CK_DESCFONTSTYLES_LABEL'); ?></label>
								<img class="ckicon" src="<?php echo $this->imagespath ?>/style.png" />
								<input type="text" id="level1itemactivehoverstylesdescfontsize" name="level1itemactivehoverstylesdescfontsize" class="level1itemactivehoverstyles cktip" style="width:45px;" title="<?php echo JText::_('CK_FONTSIZE_DESC'); ?>" />
								<img class="ckicon" src="<?php echo $this->imagespath ?>/color.png" />
								<span><?php echo JText::_('CK_NORMAL'); ?></span>
								<input type="text" id="level1itemactivehoverstylesdesccolor" name="level1itemactivehoverstylesdesccolor" class="level1itemactivehoverstyles cktip <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_FONTCOLOR_DESC'); ?>" />
							</div>

							<div class="ckheading"><?php echo JText::_('CK_APPEARANCE_LABEL'); ?></div>
							<?php
							$this->interface->createBackgroundColor('level1itemactivehoverstyles');
							$this->interface->createBackgroundImage('level1itemactivehoverstyles');
							$this->interface->createBorders('level1itemactivehoverstyles');
							$this->interface->createRoundedCorners('level1itemactivehoverstyles');
							$this->interface->createShadow('level1itemactivehoverstyles');
							$this->interface->createTextShadow('level1itemactivehoverstyles');
							?>
							<div class="ckheading"><?php echo JText::_('CK_DIMENSIONS_LABEL'); ?></div>
							<?php
							$this->interface->createMargins('level1itemactivehoverstyles');
							?>
						</div>
						<div class="ckinterfacetab" id="tab_level1itemparentarrow" data-group="mainmenu">
							<?php include dirname(__FILE__) . '/edit_render_tab_level1itemparentarrow.php' ?>
						</div>
						<div class="ckinterfacetab" id="tab_level1itemparentstyles" data-group="mainmenu">
							<div class="ckheading"><?php echo JText::_('CK_APPEARANCE_LABEL'); ?></div>
							<?php
							$this->interface->createBorders('level1itemparentstyles');
							$this->interface->createRoundedCorners('level1itemparentstyles');
							$this->interface->createShadow('level1itemparentstyles');
							?>
							<div class="ckheading"><?php echo JText::_('CK_DIMENSIONS_LABEL'); ?></div>
							<?php
							$this->interface->createMargins('level1itemparentstyles');
							?>
						</div>
						<div class="ckinterfacetab" id="tab_level1itemicon" data-group="mainmenu">
							<?php include dirname(__FILE__) . '/edit_render_tab_level1itemicon.php' ?>
						</div>
					</div>
					<div style="clear:both;"></div>
				</div>
				<div class="ckinterfacetab hascol" data-level="1" id="tab_submenu" data-group="main">
					<div class="ckcol_left">
						<div class="ckinterfacetablink current" data-tab="tab_level2menustyles" data-group="submenu"><?php echo JText::_('CK_SUBMENU'); ?></div>
						<div class="ckinterfacetablink" data-tab="tab_level2itemnormalstyles" data-group="submenu"><?php echo JText::_('CK_SUBMENULINK'); ?></div>
						<div class="ckinterfacetablink" data-tab="tab_level2itemhoverstyles" data-group="submenu"><?php echo JText::_('CK_SUBMENULINK_HOVER'); ?></div>
						<div class="ckinterfacetablink" data-tab="tab_level2itemactivestyles" data-group="submenu"><?php echo JText::_('CK_SUBMENULINK_ACTIVE'); ?></div>
						<div class="ckinterfacetablink" data-tab="tab_level2itemparentarrow" data-group="submenu"><?php echo JText::_('CK_PARENT_ARROW'); ?></div>
						<div class="ckinterfacetablink" data-tab="tab_level2heading" data-group="submenu"><?php echo JText::_('CK_COLUMN_HEADING'); ?></div>
						<div class="ckinterfacetablink" data-tab="tab_level2itemicon" data-group="submenu"><?php echo JText::_('CK_ITEM_ICON'); ?></div>
					</div>
					<div class="ckcol_right">
						<div class="ckinterfacetab current" id="tab_level2menustyles" data-group="submenu">
							<?php include dirname(__FILE__) . '/edit_render_tab_level2menustyles.php' ?>
							<div class="ckheading"><?php echo JText::_('CK_APPEARANCE_LABEL'); ?></div>
							<?php
							$this->interface->createBackgroundColor('level2menustyles');
							$this->interface->createBackgroundImage('level2menustyles');
							$this->interface->createBorders('level2menustyles');
							$this->interface->createRoundedCorners('level2menustyles');
							$this->interface->createShadow('level2menustyles');
							$this->interface->createTextShadow('level2menustyles');
							?>
						</div>
						<div class="ckinterfacetab" id="tab_level2itemnormalstyles" data-group="submenu">
							<div class="ckheading"><?php echo JText::_('CK_APPEARANCE_LABEL'); ?></div>
							<?php
							$this->interface->createBackgroundColor('level2itemnormalstyles');
							$this->interface->createBackgroundImage('level2itemnormalstyles');
							$this->interface->createBorders('level2itemnormalstyles');
							$this->interface->createRoundedCorners('level2itemnormalstyles');
							$this->interface->createShadow('level2itemnormalstyles');
							$this->interface->createTextShadow('level2itemnormalstyles');
							?>
							<div class="ckheading"><?php echo JText::_('CK_DIMENSIONS_LABEL'); ?></div>
							<?php
							$this->interface->createMargins('level2itemnormalstyles');
							?>
						</div>
						<div class="ckinterfacetab" id="tab_level2itemhoverstyles" data-group="submenu">
							<div class="ckheading"><?php echo JText::_('CK_APPEARANCE_LABEL'); ?></div>
							<?php
							$this->interface->createBackgroundColor('level2itemhoverstyles');
							$this->interface->createBackgroundImage('level2itemhoverstyles');
							$this->interface->createBorders('level2itemhoverstyles');
							$this->interface->createRoundedCorners('level2itemhoverstyles');
							$this->interface->createShadow('level2itemhoverstyles');
							$this->interface->createTextShadow('level2itemhoverstyles');
							?>
							<div class="ckheading"><?php echo JText::_('CK_DIMENSIONS_LABEL'); ?></div>
							<?php
							$this->interface->createMargins('level2itemhoverstyles');
							?>
						</div>
						<div class="ckinterfacetab" id="tab_level2itemactivestyles" data-group="submenu">
							<div class="ckrow" style="margin-left:200px;">
								<label></label>
								<div class="ckbutton-group">
									<input checked class="level2itemactivestyles" type="radio" value="1" id="level2itemactivestylesidemhoveryes" name="level2itemactivestylesidemhover"  />
									<label class="ckbutton first" for="level2itemactivestylesidemhoveryes" onclick="$ck('#level2itemactivestyleswrap').hide()"><?php echo JText::_('CK_ACTIVE_SYLES_IDEM_HOVER'); ?></label>
									<input class="level2itemactivestyles" type="radio" value="0" id="level2itemactivestylesidemhoverno" name="level2itemactivestylesidemhover" />
									<label class="ckbutton"  for="level2itemactivestylesidemhoverno" onclick="$ck('#level2itemactivestyleswrap').show()"><?php echo JText::_('CK_ACTIVE_SYLES_CUSTOM'); ?></label>
								</div>
							</div>
					<div id="level1itemactivestyleswrap">
							<div class="ckheading"><?php echo JText::_('CK_TEXT_LABEL'); ?></div>
							<div class="ckrow">
								<label for="level2itemactivestylesfontsize"><?php echo JText::_('CK_TITLEFONTSTYLES_LABEL'); ?></label>
								<img class="ckicon" src="<?php echo $this->imagespath ?>/style.png" />
								<input type="text" id="level2itemactivestylesfontsize" name="level2itemactivestylesfontsize" class="level2itemactivestyles cktip" style="width:45px;" title="<?php echo JText::_('CK_FONTSIZE_DESC'); ?>" />
								<img class="ckicon" src="<?php echo $this->imagespath ?>/color.png" />
								<span><?php echo JText::_('CK_NORMAL'); ?></span>
								<input type="text" id="level2itemactivestylescolor" name="level2itemactivestylescolor" class="level2itemactivestyles cktip <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_FONTCOLOR_DESC'); ?>" />

								</div>
							<div class="ckrow">
								<label for="level2itemactivestylesdescfontsize"><?php echo JText::_('CK_DESCFONTSTYLES_LABEL'); ?></label>
								<img class="ckicon" src="<?php echo $this->imagespath ?>/style.png" />
								<input type="text" id="level2itemactivestylesdescfontsize" name="level2itemactivestylesdescfontsize" class="level2itemactivestyles cktip" style="width:45px;" title="<?php echo JText::_('CK_FONTSIZE_DESC'); ?>" />
								<img class="ckicon" src="<?php echo $this->imagespath ?>/color.png" />
								<span><?php echo JText::_('CK_NORMAL'); ?></span>
								<input type="text" id="level2itemactivestylesdesccolor" name="level2itemactivestylesdesccolor" class="level2itemactivestyles cktip <?php echo $this->colorpicker_class; ?>" title="<?php echo JText::_('CK_FONTCOLOR_DESC'); ?>" />
							</div>
							<div class="ckheading"><?php echo JText::_('CK_APPEARANCE_LABEL'); ?></div>
							<?php
							$this->interface->createBackgroundColor('level2itemactivestyles');
							$this->interface->createBackgroundImage('level2itemactivestyles');
							$this->interface->createBorders('level2itemactivestyles');
							$this->interface->createRoundedCorners('level2itemactivestyles');
							$this->interface->createShadow('level2itemactivestyles');
							$this->interface->createTextShadow('level2itemactivestyles');
							?>
							<div class="ckheading"><?php echo JText::_('CK_DIMENSIONS_LABEL'); ?></div>
							<?php
							$this->interface->createMargins('level2itemactivestyles');
							?>
					</div>
						</div>
						<div class="ckinterfacetab" id="tab_level2itemparentarrow" data-group="submenu">
							<?php include dirname(__FILE__) . '/edit_render_tab_level2itemparentarrow.php' ?>
						</div>
						<div class="ckinterfacetab" id="tab_level2heading" data-group="submenu">
							<div class="ckheading"><?php echo JText::_('CK_TEXT_LABEL'); ?></div>
							<?php
							$this->interface->createText('headingstyles');
							?>
							<div class="ckheading"><?php echo JText::_('CK_APPEARANCE_LABEL'); ?></div>
							<?php
							$this->interface->createBackgroundColor('headingstyles');
							$this->interface->createBackgroundImage('headingstyles');
							$this->interface->createBorders('headingstyles');
							$this->interface->createRoundedCorners('headingstyles');
							$this->interface->createShadow('headingstyles');
							$this->interface->createTextShadow('headingstyles');
							?>
							<div class="ckheading"><?php echo JText::_('CK_DIMENSIONS_LABEL'); ?></div>
							<?php
							$this->interface->createMargins('headingstyles');
							?>
						</div>
						<div class="ckinterfacetab" id="tab_level2itemicon" data-group="submenu">
							<?php include dirname(__FILE__) . '/edit_render_tab_level2itemicon.php' ?>
						</div>
					</div>
					<div style="clear:both;"></div>
				</div>
				<div class="ckinterfacetab hascol" data-level="1" id="tab_subsubmenu" data-group="main">
					<div class="ckcol_left">
						<div class="ckinterfacetablink current" data-tab="tab_level3menustyles" data-group="subsubmenu"><?php echo JText::_('CK_SUBSUBMENU'); ?></div>
					</div>
					<div class="ckcol_right">
						<div class="ckinterfacetab current" id="tab_level3menustyles" data-group="subsubmenu">
							<div class="ckheading"><?php echo JText::_('CK_DIMENSIONS_LABEL'); ?></div>
							<?php
							$this->interface->createMargins('level3menustyles', false, true);
							?>
							<?php include dirname(__FILE__) . '/edit_render_tab_level3menustyles.php' ?>
							<div class="ckheading"><?php echo JText::_('CK_APPEARANCE_LABEL'); ?></div>
							<?php
							$this->interface->createBackgroundColor('level3menustyles');
							$this->interface->createBackgroundImage('level3menustyles');
							$this->interface->createBorders('level3menustyles');
							$this->interface->createRoundedCorners('level3menustyles');
							$this->interface->createShadow('level3menustyles');
							$this->interface->createTextShadow('level3menustyles');
							?>
						</div>
					</div>
					<div style="clear:both;"></div>
				</div>
				<div class="ckinterfacetab hascol" data-level="1" id="tab_fancy" data-group="main">
					<div class="ckcol_left">
						<div class="ckinterfacetablink current" data-tab="tab_fancycursor" data-group="fancy"><?php echo JText::_('CK_FANCY'); ?></div>
					</div>
					<div class="ckcol_right">
						<div class="ckinterfacetab current" id="tab_fancycursor" data-group="fancy">
							<div class="ckheading"><?php echo JText::_('CK_APPEARANCE_LABEL'); ?></div>
							<?php
							$this->interface->createBackgroundColor('fancystyles');
							$this->interface->createBackgroundImage('fancystyles');
							$this->interface->createBorders('fancystyles');
							$this->interface->createRoundedCorners('fancystyles');
							$this->interface->createShadow('fancystyles');
							?>
							<div class="ckheading"><?php echo JText::_('CK_DIMENSIONS_LABEL'); ?></div>
							<?php
							$this->interface->createMargins('fancystyles');
							$this->interface->createDimensions('fancystyles');
							?>
						</div>
					</div>
					<div style="clear:both;"></div>
				</div>
				<div class="ckinterfacetab" data-level="1" id="tab_layout" data-group="main">
					<?php include dirname(__FILE__) . '/edit_layout.php' ?>
				</div>
				<div class="ckinterfacetab" data-level="1" id="tab_customcss" data-group="main">
					<div id="customcssbuttons">
						<div class="customcssbutton btn" data-rule="|ID| { }"><?php echo JText::_('CK_MENUBAR'); ?></div>
						<div class="customcssbutton btn" data-rule="|ID| li.maximenuck.level1 { }"><?php echo JText::_('CK_MENULINK'); ?></div>
						<div class="customcssbutton btn" data-rule="|ID| li.maximenuck.level1:hover { }"><?php echo JText::_('CK_MENULINK_HOVER'); ?></div>
						<div class="customcssbutton btn" data-rule="|ID| li.maximenuck.level1.active { }"><?php echo JText::_('CK_MENULINK_ACTIVE'); ?></div>
						<div class="customcssbutton btn" data-rule="|ID| li.maximenuck div.floatck { }"><?php echo JText::_('CK_SUBMENU'); ?></div>
						<div class="customcssbutton btn" data-rule="|ID| ul.maximenuck2 li.maximenuck { }"><?php echo JText::_('CK_SUBMENULINK'); ?></div>
						<div class="customcssbutton btn" data-rule="|ID| ul.maximenuck2 li.maximenuck:hover { }"><?php echo JText::_('CK_SUBMENULINK_HOVER'); ?></div>
						<div class="customcssbutton btn" data-rule="|ID| ul.maximenuck2 li.maximenuck.active { }"><?php echo JText::_('CK_SUBMENULINK_ACTIVE'); ?></div>
						<div class="customcssbutton btn" data-rule="|ID| li.maximenuck div.floatck div.floatck { }"><?php echo JText::_('CK_SUBSUBMENU'); ?></div>
					</div>
					<textarea id="customcss" style="width: calc(100% - 20px);margin:10px;min-height:400px;box-sizing:border-box;"><?php echo $this->item->customcss; ?></textarea>
				</div>
				<div class="ckinterfacetab hascol" data-level="1" id="tab_presets" data-group="main">
					<input type="hidden" id="theme" name="theme" value="" />
					<?php echo $this->loadTemplate('themes'); ?>
				</div>
			</div>
			
		</div>
		<div id="stylescontainerright">
			<div id="previewarea">
				<style class="ckstyletheme">
					<?php echo $themecss ?>
				</style>
				<div class="ckstylesheet">
					<div class="ckstylesheet">
						<?php /*<link type="text/css" href="<?php echo JUri::root(true); ?>/modules/mod_maximenuck/themes/<?php echo $this->params->get('theme', 'blank'); ?>/css/maximenuck.php?monid=maximenuck_previewmodule" rel="stylesheet"> */ ?>
					</div>
				</div>
				<div class="ckgfontstylesheet"></div>
				<div class="ckstyle"></div>
				<div class="inner" style="<?php echo $preview_width; ?>">
					<?php echo $this->loadTemplate('render_menu_module'); ?>
				</div>
			</div>
			
		</div>
	
	
	<div style="clear:both;"></div>
</div>
<script language="javascript" type="text/javascript">
	MAXIMENUCK.CKCSSREPLACEMENT = new Object();
	<?php foreach (Style::getCssReplacement() as $tag => $rep) { ?>
	MAXIMENUCK.CKCSSREPLACEMENT['<?php echo $tag ?>'] = '<?php echo $rep ?>';
	<?php } ?>

	jQuery(document).ready(function($){
		// window.setInterval("ckKeepAlive()", 600000);

		CKBox.initialize({});
		CKBox.assign($('a.modal'), {
			parse: 'rel'
		});
		CKApi.Tooltip('.cktip');

		// manage the tabs
		ckInitTabs();
		// launch the preview when the user do a change
		$('#styleswizard_options input,#styleswizard_options select,#styleswizard_options textarea').change(function() {
			ckPreviewStyles(); // B/C preview_stylesparams
		});

		ckApplyStylesparams();
		ckSetFloatingOnPreview();
		// ckPlayAnimationPreview();
		
		$ck('.customcssbutton').click(function() {
			$ck('#customcss').val($ck('#customcss').val() + $ck(this).attr('data-rule'));
		});
	});
</script>
