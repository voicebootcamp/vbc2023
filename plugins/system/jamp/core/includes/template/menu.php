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

$animateMenu = \JAmpHelper::$pluginParams->get('menu_module_animate', 1) ? 'animate' : '';
if(\JAmpHelper::$pluginParams->get('menu_module_title', '')):
	$moduleContents = ModuleHelper::renderModule(ModuleHelper::getModule(\JAmpHelper::$pluginParams->get('menu_module_name', 'menu'), \JAmpHelper::$pluginParams->get('menu_module_title', '')));
	if($moduleContents):?>
	<amp-accordion class="menu-accordion" <?php echo $animateMenu;?>>
		<section <?php echo \JAmpHelper::$pluginParams->get('menu_module_accordion_expanded', 0) ? 'expanded' : '';?>>
			<?php if(\JAmpHelper::$pluginParams->get('enable_menu_header_title', 1)):?>
				<h4><?php echo @ModuleHelper::getModule(\JAmpHelper::$pluginParams->get('menu_module_name', 'menu'), \JAmpHelper::$pluginParams->get('menu_module_title', ''))->title;?></h4>
			<?php else:?>
				<h4>&nbsp;</h4>
			<?php endif;?>
			<?php echo \JAmpHelper::transformContents($moduleContents, false, true); ?>
		</section>
	</amp-accordion>
	<?php endif;
else:
	// Fallback to the position based loading as we have no module title
	$modules = ModuleHelper::getModules(\JAmpHelper::$pluginParams->get('menu_module_name', '')); ?>
	<amp-accordion class="menu-accordion" <?php echo $animateMenu;?>>
	<?php 
	foreach($modules as $moduleInPosition):?>
		<?php if($moduleInPosition->module == 'mod_menu'):?>
			<section <?php echo \JAmpHelper::$pluginParams->get('menu_module_accordion_expanded', 0) ? 'expanded' : '';?>>
				<?php if(\JAmpHelper::$pluginParams->get('enable_menu_header_title', 1)):?>
					<h4><?php echo $moduleInPosition->title;?></h4>
				<?php else:?>
					<h4>&nbsp;</h4>
				<?php endif;?>
				<?php echo \JAmpHelper::transformContents(ModuleHelper::renderModule(ModuleHelper::getModule($moduleInPosition->name, $moduleInPosition->title)), false, true); ?>
			</section>
		<?php endif;?>
	<?php 
	endforeach;?>
	</amp-accordion>
	<?php 
endif;
