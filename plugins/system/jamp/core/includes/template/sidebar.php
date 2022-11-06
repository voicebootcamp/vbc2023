<?php
/**
 * @package JAMP::plugins::system
 * @subpackage jamp
 * @subpackage core
 * @subpackage includes
 * @subpackage template
 * @author Joomla! Extensions Store
 * @copyright (C)2016 Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined ( '_JEXEC' ) or die ();
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Helper\ModuleHelper;

$sidebarOpenIcon = \JAmpHelper::$pluginParams->get('sidebar_open_icon', 'sidebar_open_white');
$sidebarCloseIcon = \JAmpHelper::$pluginParams->get('sidebar_close_icon', 'sidebar_close_black');
$sidebarSide = (\JAmpHelper::$pluginParams->get('sidebar_side', 'left') == 'right') ? 'side="right"' : null;
$sidebarSideClass = $sidebarSide ? 'trigger-right' : '';

if(\JAmpHelper::$pluginParams->get('sidebar_module_title', '')):
?>
<amp-sidebar <?php echo $sidebarSide;?> id='sidebar' layout="nodisplay">
      <amp-img id="sidebar-close" class='close <?php echo $sidebarSideClass;?>' src="<?php echo Uri::root(false) . 'plugins/system/jamp/core/images/' . $sidebarCloseIcon . '.png'?>" width="32" height="32" alt="Close" on="tap:sidebar.close" role="button" tabindex="0"></amp-img>
      <div class="topheader">
      	<?php if(\JAmpHelper::$pluginParams->get('show_sidebar_title', 1)):?>
      	<?php echo @ModuleHelper::getModule(\JAmpHelper::$pluginParams->get('sidebar_module_name', ''), \JAmpHelper::$pluginParams->get('sidebar_module_title', ''))->title;?>
      	<?php endif;?>
      </div>
      <hr/>
      <section>
      	<?php echo \JAmpHelper::transformContents(ModuleHelper::renderModule(ModuleHelper::getModule(\JAmpHelper::$pluginParams->get('sidebar_module_name', ''), \JAmpHelper::$pluginParams->get('sidebar_module_title', ''))));?>
      </section>
</amp-sidebar>
<a id="sidebar-trigger" class="button <?php echo $sidebarSideClass;?>" on='tap:sidebar.toggle'>
	<amp-img src="<?php echo Uri::root(false) . 'plugins/system/jamp/core/images/' . $sidebarOpenIcon . '.png'?>" width="32" height="32" alt="navigation"></amp-img>
</a>
<?php else: 
// Fallback to the position based loading as we have no module title
$modules = ModuleHelper::getModules(\JAmpHelper::$pluginParams->get('sidebar_module_name', '')); ?>
<amp-sidebar <?php echo $sidebarSide;?> id='sidebar' layout="nodisplay">
      <amp-img id="sidebar-close" class='close <?php echo $sidebarSideClass;?>' src="<?php echo Uri::root(false) . 'plugins/system/jamp/core/images/' . $sidebarCloseIcon . '.png'?>" width="32" height="32" alt="Close" on="tap:sidebar.close" role="button" tabindex="0"></amp-img>
	  <?php foreach($modules as $moduleInPosition):?>
	      <div class="topheader">
	      	<?php if(\JAmpHelper::$pluginParams->get('show_sidebar_title', 1)):?>
	      	<?php echo $moduleInPosition->title;?>
	      	<?php endif;?>
	      </div>
	      <hr/>
	      <section>
	      	<?php echo \JAmpHelper::transformContents(ModuleHelper::renderModule(ModuleHelper::getModule($moduleInPosition->name, $moduleInPosition->title)));?>
	      </section>
      <?php endforeach;?>
</amp-sidebar>
<a id="sidebar-trigger" class="button <?php echo $sidebarSideClass;?>" on='tap:sidebar.toggle'>
	<amp-img src="<?php echo Uri::root(false) . 'plugins/system/jamp/core/images/' . $sidebarOpenIcon . '.png'?>" width="32" height="32" alt="navigation"></amp-img>
</a>
<?php endif;?>