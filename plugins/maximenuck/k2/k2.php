<?php
/**
 * @copyright	Copyright (C) 2020 Cédric KEIFLIN alias ced1870
 * https://www.template-creator.com
 * https://www.joomlack.fr
 * @license		GNU/GPL
 * */
 
defined('_JEXEC') or die('Restricted access');
jimport('joomla.event.plugin');

class plgMaximenuckK2 extends JPlugin {

	private $type = 'k2';

	private $shallLoad = true;

	function __construct(&$subject, $params) {
		// does not load if the component is not installed
		$this->shallLoad = file_exists(JPATH_ROOT . '/administrator/components/com_k2');
		if (! $this->shallLoad)
			return;

		parent::__construct($subject, $params);
	}

	/* 
	 * Initiate the lugin load
	 *
	 * Return mixed
	 */
	function registerListeners() {
		if ($this->shallLoad === true) {
			parent::registerListeners();
		} else {
			return false;
		}
	}

	/* 
	 * Send the infos in the source list to add the type in the plugin options
	 *
	 * Return string the source type
	 */
	public function onMaximenuckGetSourceName() {
		$this->loadLanguage();
		return $this->type;
	}

	/* 
	 * Send the infos in the source list to add the type in the plugin options
	 *
	 * Return string the source type
	 */
	public function onMaximenuckGetTypeName() {
		$this->loadLanguage();
		return $this->type;
	}

	/* 
	 * Display the html code for the item to be used into the frontend page
	 * @param string the item object from simple_html_dom
	 * 
	 * Return String the html code
	 */
	public function onMaximenuckRenderItemK2($item) {
		require_once(__DIR__ . '/helper/helper_' . $this->type . '.php');
		$k2 = MaximenuckHelpersourceK2::getItems($item->params);
		$html = '<ul class="maximenuck2">';
		foreach ($k2 as $item) {
			$item->level = $item->level;
			$item->type = 'k2';

			$html .= Maximenuck\Helperfront::getHtmlItem($item);
		}
		$html .= '</ul>';

		return $html;
	}
}