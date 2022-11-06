<?php
// No direct access
defined('_JEXEC') or die;
require_once MAXIMENUCK_PATH . '/controller.php';

use Maximenuck\CKFof;

class MaximenuckControllerInterface extends MaximenuckController {

	/**
	 * Load the needed interface
	 * 
	 * @return void
	 */
	public function load() {
		// security check
		CKFof::checkAjaxToken(false);

		$layout = $this->input->get('layout', '', 'cmd');
		if (! $layout) return;

		$this->imagespath = MAXIMENUCK_MEDIA_URI . '/images/menustyles/';
		$plugin = str_replace('edit_', '', $layout);

		// loads the language files from the frontend
		$lang	= JFactory::getLanguage();
		$lang->load('plg_maximenuck_' . $plugin, JPATH_SITE . '/plugins/maximenuck/' . $plugin, $lang->getTag(), false);
		$lang->load('plg_maximenuck_' . $plugin, JPATH_SITE, $lang->getTag(), false);

		if (file_exists(MAXIMENUCK_PATH . '/interfaces/' . $layout . '.php')) {
			require_once(MAXIMENUCK_PATH . '/interfaces/' . $layout . '.php');
		} else {
			require_once(JPATH_SITE . '/plugins/maximenuck/' . $plugin . '/layouts/' . $layout . '.php');
		}
		exit;
	}
}