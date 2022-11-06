<?php

defined('_JEXEC') or die('Restricted access');
/*
	preflight which is executed before install and update
	install
	update
	uninstall
	postflight which is executed after install and update
	*/

class com_maximenuckInstallerScript {

	function install($parent) {
		
	}
	
	function update($parent) {
		
	}

	function uninstall($parent) {
		// disable all plugins and modules
		$db = JFactory::getDbo();
		$db->setQuery("UPDATE `#__modules` SET `published` = 0 WHERE `module` LIKE '%maximenuck%'");
		$db->execute();

		$db->setQuery("UPDATE `#__extensions` SET `enabled` = 0 WHERE `type` = 'plugin' AND `element` LIKE '%maximenuck%' AND `folder` NOT LIKE '%maximenuck%'");
		$db->execute();
		return true;
	}

	function preflight($type, $parent) {

		return true;
	}

	// run on install and update
	function postflight($type, $parent) {
		// install modules and plugins
		jimport('joomla.installer.installer');
		$db = JFactory::getDbo();
		$status = array();
		$src_ext = dirname(__FILE__).'/administrator/extensions';
		$installer = new JInstaller;

		// module
		$result = $installer->install($src_ext.'/mod_maximenuck');
		$status[] = array('name'=>'Maximenu CK - Module','type'=>'module', 'result'=>$result);

		// system plugin
		$result = $installer->install($src_ext.'/maximenuck');
		$status[] = array('name'=>'System - Maximenu CK','type'=>'plugin', 'result'=>$result);
		// system plugin must be enabled for user group limits and private areas
		$db->setQuery("UPDATE #__extensions SET enabled = '1' WHERE `element` = 'maximenuck' AND `type` = 'plugin'");
		$db->execute();

		// maximenuck plugin
		$plugins_src_ext = $src_ext . '/plugins';
		$plugins = JFolder::folders($plugins_src_ext);
		$ordering = 1;
		foreach ($plugins as $plugin) {
			$result = $installer->install($plugins_src_ext . '/' . $plugin);
			$status[] = array('name' => 'Maximenu CK - ' . $plugin, 'type' => 'plugin', 'result' => $result);
			// auto enable the plugin
			$db->setQuery("UPDATE #__extensions SET enabled = '1', ordering = '" . $ordering . "' WHERE `element` = '" . $plugin . "' AND `type` = 'plugin' AND `folder` = 'maximenuck'");
			$db->execute();
			$ordering++;
		}

		// disable the old update site
		$db->setQuery("UPDATE #__update_sites SET enabled = '0' WHERE `location` = 'http://update.joomlack.fr/mod_maximenuck_update.xml'");
		$result3 = $db->execute();
		// disable the old update site
		$db->setQuery("UPDATE #__update_sites SET enabled = '0' WHERE `location` = 'http://update.joomlack.fr/com_maximenuck_update.xml'");
		$result4 = $db->execute();
		// disable the light update site
		$db->setQuery("UPDATE #__update_sites SET enabled = '0' WHERE `location` = 'https://update.joomlack.fr/maximenuck_light_update.xml'");
		$result3 = $db->execute();

		foreach ($status as $statu) {
			if ($statu['result'] == true) {
				$alert = 'success';
				$icon = 'icon-ok';
				$text = 'Successful';
			} else {
				$alert = 'warning';
				$icon = 'icon-cancel';
				$text = 'Failed';
			}
			echo '<div class="alert alert-' . $alert . '"><i class="icon ' . $icon . '"></i>Installation and activation of the <b>' . $statu['type'] . ' ' . $statu['name'] . '</b> : ' . $text . '</div>';
		}

		// check for table creation
		require_once JPATH_ROOT . '/administrator/components/com_maximenuck/helpers/helper.php';
		Maximenuck\Helper::checkDbIntegrity();

		return true;
	}
}
