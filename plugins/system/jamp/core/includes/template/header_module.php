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
use Joomla\CMS\Helper\ModuleHelper;

if(\JAmpHelper::$pluginParams->get('header_module_title', '')):
	if(\JAmpHelper::$pluginParams->get('enable_header_module_title', 1)):?>
		<div class="header_module_title"><?php echo @ModuleHelper::getModule(\JAmpHelper::$pluginParams->get('header_module_name', ''), \JAmpHelper::$pluginParams->get('header_module_title', ''))->title;?></div>
	<?php else:?>
		<div>&nbsp;</div>
	<?php endif;?>
	
	<div class="header_module_content">
		<?php echo \JAmpHelper::transformContents(ModuleHelper::renderModule(ModuleHelper::getModule(\JAmpHelper::$pluginParams->get('header_module_name', ''), \JAmpHelper::$pluginParams->get('header_module_title', ''))));?>
	</div>
<?php else:
	// Fallback to the position based loading as we have no module title
	$modules = ModuleHelper::getModules(\JAmpHelper::$pluginParams->get('header_module_name', ''));
	foreach($modules as $moduleInPosition):
		if(\JAmpHelper::$pluginParams->get('enable_header_module_title', 1)):?>
			<div class="header_module_title"><?php echo $moduleInPosition->title;?></div>
		<?php else:?>
			<div>&nbsp;</div>
		<?php endif;?>
		
		<div class="header_module_content">
			<?php echo \JAmpHelper::transformContents(ModuleHelper::renderModule(ModuleHelper::getModule($moduleInPosition->name, $moduleInPosition->title)));?>
		</div>
	<?php 
	endforeach;
endif;?>