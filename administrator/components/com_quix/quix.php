<?php
/**
 * @package    com_quix
 * @author     ThemeXpert <info@themexpert.com>
 * @copyright  Copyright (C) 2015. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    3.0.0
 */

// No direct access
defined('_JEXEC') or die;

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_quix'))
{
  throw new JAccessExceptionNotallowed(JText::_('JERROR_ALERTNOAUTHOR'), 403);
}

try
{
  $controller = JControllerLegacy::getInstance('Quix');
  $controller->execute(JFactory::getApplication()->input->get('task'));
  $controller->redirect();
}
catch (Exception $e)
{
  JErrorPage::render($e);
}
