<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2022 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

/**
 * Membership Pro controller
 *
 * @package        Joomla
 * @subpackage     Membership Pro
 */
class OSMembershipControllerMessage extends OSMembershipController
{
	public function save()
	{
		$model = $this->getModel();
		$data  = $this->input->getData(MPF_INPUT_ALLOWRAW);
		$model->store($data);
		$task = $this->getTask();
		$msg  = Text::_('Messages were successfully saved');
		if ($task == 'apply')
		{
			$this->setRedirect('index.php?option=com_osmembership&view=message', $msg);
		}
		else
		{
			$this->setRedirect('index.php?option=com_osmembership&view=' . $this->config['default_view'], $msg);
		}
	}

	/**
	 * Redirect back to default view afters users cancel an action
	 */
	public function cancel()
	{
		$this->setRedirect('index.php?option=com_osmembership&view=' . $this->config['default_view']);
	}
}
