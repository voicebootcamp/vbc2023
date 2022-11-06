<?php
// No direct access
defined('_JEXEC') or die;

use Maximenuck\CKView;
use Maximenuck\CKFof;
use Maximenuck\CKText;
use Maximenuck\Helper;

class MaximenuckViewMaximenus extends CKView {

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

		Helper::checkDbIntegrity();

		$this->items = $this->get('Items');
		$this->toolbar = $this->getToolbar();

		// $this->input->set('tmpl', 'component');
		// $this->input->set('layout', 'modal');

		parent::display();
	}

	private function getToolbar() {
		JToolBarHelper::title('Maximenu CK - ' . CKText::_('CK_MENUS'));
		// Get the toolbar object instance
		$bar = JToolBar::getInstance('toolbar');
		if (CKFof::userCan('create')) {
			JToolBarHelper::addNew('maximenu.add', 'CK_NEW');
			JToolBarHelper::custom('maximenu.copy', 'copy', 'copy', 'CK_COPY');
			// Render the popup button
//				$html = '<button class="btn btn-small btn-success" onclick="CKBox.open({handler:\'iframe\', fullscreen: true, url:\'' . JUri::root(true) . '/administrator/index.php?option=com_maximenuck&view=maximenu&layout=modal&tmpl=component&id=0\'})">
//						<span class="icon-new icon-white"></span>
//						' . JText::_('JTOOLBAR_NEW') . '
//						</button>';
//				$bar->appendButton('Custom', $html);
			
		}
		if (CKFof::userCan('edit')) {
			JToolBarHelper::custom('maximenu.edit', 'edit', 'edit', 'CK_EDIT');
			JToolBarHelper::trash('maximenu.delete', 'CK_REMOVE');
		}

		return $bar;
	}
}
