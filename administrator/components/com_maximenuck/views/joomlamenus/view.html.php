<?php
// No direct access
defined('_JEXEC') or die;

use Maximenuck\CKView;
use Maximenuck\CKFof;
use Maximenuck\CKText;

class MaximenuckViewJoomlamenus extends CKView {

	protected $items;

	protected $pagination;

	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null) {
		$user = JFactory::getUser();
		$authorised = ($user->authorise('core.edit', 'com_maximenuck') || (count($user->getAuthorisedCategories('com_maximenuck', 'core.edit'))));

		if ($authorised !== true)
		{
			throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'), 403);
			return false;
		}

		$this->items = $this->get('Items');
		$this->toolbar = $this->getToolbar();

		parent::display($tpl);
	}

	private function getToolbar() {
		JToolBarHelper::title('Maximenu CK - ' . CKText::_('CK_MENUS_LIST'));

		// if (CKFof::userCan('core.admin')) {
			 // JToolBarHelper::preferences('com_maximenuck');
		// }
		$bar = JToolBar::getInstance('toolbar');
		
		return $bar;
	}
}
