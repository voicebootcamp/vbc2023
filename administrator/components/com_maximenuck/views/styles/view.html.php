<?php
// No direct access
defined('_JEXEC') or die('Restricted access');

use Maximenuck\CKView;
use Maximenuck\CKFof;
use Maximenuck\Helper;


class MaximenuckViewStyles extends CKView {

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
		JToolBarHelper::title('Maximenu CK - ' . JText::_('CK_STYLES'));
		// Get the toolbar object instance
		$bar = JToolBar::getInstance('toolbar');
		if (CKFof::userCan('create')) {
			// JToolBarHelper::addNew('style.add', 'CK_NEW');
			if ($this->input->get('layout') == 'modal') {
				JToolBarHelper::addNew('style.add', 'CK_NEW');
			} else {
				// Render the popup button
				$html = '<button class="btn btn-small btn-success" onclick="CKBox.open({handler:\'iframe\', fullscreen: true, url:\'' . JUri::root(true) . '/administrator/index.php?option=com_maximenuck&view=style&layout=edit&tmpl=component&id=0\'})">
						<span class="icon-new icon-white"></span>
						' . JText::_('CK_NEW') . '
						</button>';
				$bar->appendButton('Custom', $html);
			}
			JToolBarHelper::custom('style.copy', 'copy', 'copy', 'CK_COPY');
		}
		if (CKFof::userCan('edit')) {
			JToolBarHelper::custom('style.edit', 'edit', 'edit', 'CK_EDIT');
		}
		if (CKFof::userCan('delete')) {
			JToolBarHelper::trash('style.delete', 'CK_REMOVE');
		}

		return $bar;
	}
}
