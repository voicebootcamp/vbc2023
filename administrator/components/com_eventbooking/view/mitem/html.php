<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2022 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

class EventbookingViewMitemHtml extends RADViewItem
{
	/**
	 * Buttons which will be hidden on edit message item
	 *
	 * @var array
	 */
	protected $hideButtons = ['save2new', 'save2copy'];

	/**
	 * Prepare data for the view
	 *
	 * @throws Exception
	 */
	protected function prepareView()
	{
		parent::prepareView();

		if (!$this->item->id)
		{
			throw new Exception('Message item not found', 404);
		}

		$this->message = EventbookingHelper::getMessages();
	}
}
