<?php
/**
 * @name		Maximenu CK params
 * @package		com_maximenuck
 * @copyright	Copyright (C) 2014. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Cedric Keiflin - http://www.template-creator.com - http://www.joomlack.fr
 */

defined('_JEXEC') or die;
// get the layout of the module
$layout = trim($this->params->get('layout', 'default'), "_:");
?>
<div class="ckrow" style="margin-left:200px;">
	<label><?php echo JText::_('CK_ORIENTATION'); ?></label>
	<img class="ckicon" src="<?php echo $this->imagespath ?>/shape_rotate_clockwise.png" />
	<div class="ckbutton-group">
		<input class="orientation" type="radio" value="horizontal" id="orientationhorizontal" name="orientation" onclick="ckChangeMenuOrientation('horizontal')" />
		<label class="ckbutton first" for="orientationhorizontal"><?php echo JText::_('CK_HORIZONTAL'); ?></label>
		<input class="orientation" type="radio" value="vertical" id="orientationvertical" name="orientation" onclick="ckChangeMenuOrientation('vertical')"/>
		<label class="ckbutton"  for="orientationvertical"><?php echo JText::_('CK_VERTICAL'); ?></label>
	</div>
</div>
<hr />
<?php
$path = JPATH_ROOT . '/modules/mod_maximenuck/tmpl';
$files = JFolder::files($path, '.php');
natsort($files);
$i = 1;
echo '<div class="clearfix" style="min-height:35px;margin: 0 5px;">';
foreach ($files as $file) {
	// don't take the sublayout
	if (substr($file,0,1) == '_') {
		continue;
	} 
	$thumb_title = '';
	$file = JFile::stripExt($file);
	if ($file === 'deault2') continue;
	if ( file_exists($path . '/' . $file. '.png') ) {
		$thumb = Juri::root(true) . '/modules/mod_maximenuck/tmpl/' . $file . '.png';
	} else {
		$thumb = Juri::root(true) . '/administrator/components/com_maximenuck/images/what.png style="display:block;margin:0 auto;" width="90 height="90';
		$thumb_title = JText::_('CK_LAYOUT_PREVIEW_NOT_FOUND');
	}

	$active = ( $layout == $file ) ? 'selected' : '';
	echo '<div class="layoutthumb ' . $active . '" data-name="' . $file . '" onclick="ckChangeLayout(\'' . $file . '\')">'
		. '<img src="' . $thumb . '" style="margin:0;padding:0;" title="' . $thumb_title . '" class="cktip" />'
		. '<div class="layoutname">' . $file . '</div>'
		. '</div>';
	$i++;
}
echo '</div>';