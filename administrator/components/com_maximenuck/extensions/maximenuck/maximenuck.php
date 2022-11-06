<?php
// no direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

class plgSystemMaximenuck extends JPlugin {

	public $pluginPath;

	/*
	 * Constructor
	 */
	function __construct(&$subject, $config) {
		$this->pluginPath = '/plugins/system/maximenuck';

		parent :: __construct($subject, $config);
	}

	/**
	 * @param       JForm   The form to be altered.
	 * @param       array   The associated data for the form.
	 * @return      boolean
	 */
	public function onContentPrepareForm($form, $data) {

		// load language files for the maximenu plugin if needed
		if (isset($data->request['option'])) {
			if ($data->request['option'] == 'com_maximenuck' && $data->request['view'] == 'sources') {
				$plugin = $data->request['layout'];
				// loads the language files from the frontend
				$lang	= JFactory::getLanguage();
				$lang->load('plg_maximenuck_' . $plugin, JPATH_SITE . '/plugins/maximenuck/' . $plugin, $lang->getTag(), false);
				$lang->load('plg_maximenuck_' . $plugin, JPATH_SITE, $lang->getTag(), false);
			}
		}

		if (
			// condition for the module - add source of plugins
			(
			$form->getName() != 'com_modules.module'
			&& $form->getName() != 'com_advancedmodules.module'
			&& $form->getName() != 'com_config.modules' // for frontend edition
			|| ($form->getName() == 'com_modules.module' && $data && @$data->module != 'mod_maximenuck')
			|| ($form->getName() == 'com_advancedmodules.module' && $data && $data->module != 'mod_maximenuck')
			// for frontend edition
			|| ($form->getName() == 'com_config.modules' && $data && @$data->module != 'mod_maximenuck')
			)
			// for menu item params
			&& $form->getName() != 'com_menus.item' 
			&& $form->getName() != 'com_menumanagerck.itemedition'
		)
			return;

		// menu item params
		if ($form->getName() == 'com_menus.item' 
			|| $form->getName() == 'com_menumanagerck.itemedition')	{
			JForm::addFormPath(JPATH_SITE . '/plugins/system/maximenuck/params');
			JForm::addFieldPath(JPATH_ROOT . '/administrator/components/com_maximenuck/elements');

			// get the language
			$lang = JFactory::getLanguage();
			$langtag = $lang->getTag(); // returns fr-FR or en-GB
			$this->loadLanguage();

			// menu item options
			if ($form->getName() == 'com_menus.item' || $form->getName() == 'com_menumanagerck.itemedition') {
				$form->loadFile('advanced_itemparams_maximenuck', false);
			}
		} else {
			// check that we are editing the maximenuck module
			$id = JFactory::getApplication()->input->get('id',0, 'int');
			if ($id) {
				$q = "SELECT module FROM #__modules WHERE id = " . $id;
				$db = JFactory::getDbo();
				$db->setQuery($q);
				$module = $db->loadResult();

				if ($module != 'mod_maximenuck') return;
			}

			$this->loadLanguage();

			// module options
			if (
					$form->getName() == 'com_modules.module' || $form->getName() == 'com_advancedmodules.module'
					// for frontend edition
					|| $form->getName() == 'com_config.modules'
			) {
				

				// load the custom plugins
				require_once(JPATH_ADMINISTRATOR . '/components/com_maximenuck/helpers/ckfof.php');
				Maximenuck\CKFof::importPlugin('maximenuck');
				$sources = Maximenuck\CKFof::triggerEvent('onMaximenuckGetSourceName');

				if (! empty($sources)) {
					foreach ($sources as $source) {
						$this->loadLanguage();
						JForm::addFormPath(JPATH_SITE . '/plugins/maximenuck/' . strtolower($source) . '/params');
						$form->loadFile(strtolower($source) . '_params', false);
					}
				}
			}
		}
	}

	/**
	 * Function to remove old parameters on frontend
	 */
	function onAfterRender() {
		$app = JFactory::getApplication();
		$document = JFactory::getDocument();
		$doctype = $document->getType();

		// si pas en frontend, on sort
		if ($app->isClient('administrator')) {
			return false;
		}

		// si pas HTML, on sort
		if ($doctype !== 'html') {
			return;
		}

		// renvoie les donnees dans la page
		// get the page code
		if (version_compare(JVERSION, '4') >= 0) {
			$body = JFactory::getApplication()->getBody(); 
		} else {
			$body = JResponse::getBody();
		}
		$regex = "#{maximenu}(.*?){/maximenu}#s"; // masque de recherche
		$body = preg_replace($regex, '', $body);
		if (version_compare(JVERSION, '4') >= 0) {
			JFactory::getApplication()->setBody($body); 
		} else {
			JResponse::setBody($body);
		}
	}
}