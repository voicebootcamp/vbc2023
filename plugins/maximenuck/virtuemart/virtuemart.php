<?php
/**
 * @copyright	Copyright (C) 2020 Cédric KEIFLIN alias ced1870
 * https://www.template-creator.com
 * https://www.joomlack.fr
 * @license		GNU/GPL
 * */
 
defined('_JEXEC') or die('Restricted access');
jimport('joomla.event.plugin');

class plgMaximenuckVirtuemart extends JPlugin {

	private $type = 'virtuemart';

	private $shallLoad = true;

	function __construct(&$subject, $params) {
		// does not load if the component is not installed
		$this->shallLoad = file_exists(JPATH_SITE . '/administrator/components/com_virtuemart');
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
	public function onMaximenuckRenderItemVirtuemart($item) {
		require_once(__DIR__ . '/helper/helper_' . $this->type . '.php');
		$virtuemart = MaximenuckHelpersourceVirtuemart::getItems($item->params);
		$html = '<ul class="maximenuck2">';
		foreach ($virtuemart as $article) {
			$article->level = $item->level;
			$article->type = 'article';

			$html .= Maximenuck\Helperfront::getHtmlItem($article);
		}
		$html .= '</ul>';

		return $html;
	}
}