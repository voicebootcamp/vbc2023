<?php
/**
 * @package    com_quix
 * @author     ThemeXpert <info@themexpert.com>
 * @copyright  Copyright (C) 2015. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    3.0.0
 */

defined('_JEXEC') or die;

use Joomla\CMS\Exception\ExceptionHandler;
use Joomla\CMS\Factory;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Uri\Uri;
// Version warning
if (PHP_VERSION_ID < 50600)
{
  $lang         = Factory::getLanguage();
  $extension    = 'com_quix';
  $base_dir     = JPATH_ADMINISTRATOR;
  $language_tag = 'en-GB';
  $reload       = true;
  $lang->load($extension, $base_dir, $language_tag, $reload);

  $layout = new FileLayout(
    'toolbar.phpwarning',
    JPATH_ADMINISTRATOR . '/components/com_quix/layouts'
  );
  echo $layout->render([]);

  return true;
}

// Get App instance
try
{
  $app   = Factory::getApplication();
  $input = $app->input;

  if ($input->get('view', '') === 'form')
  {
    $user = Factory::getUser();
    // Can create in any category (component permission) or at least in one category
    $canCreateRecords = $user->authorise('core.create', 'com_quix')
      || count($user->getAuthorisedCategories('com_quix', 'core.create')) > 0;

    $canEditRecords = $user->authorise('core.edit', 'com_quix')
      || count($user->getAuthorisedCategories('com_quix', 'core.edit')) > 0;

    // Instead of checking edit on all records, we can use **same** check as the form editing view
    $values           = (array) $app->getUserState('com_quix.edit.form.id');
    $isEditingRecords = count($values);

    $hasAccess = $canCreateRecords || $isEditingRecords || $canEditRecords;

    if (!$hasAccess)
    {
      $app->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'warning');
      if ($user->get('guest'))
      {
        $return                = base64_encode(Uri::getInstance());
        $login_url_with_return = 'index.php?option=com_users&view=login&return='.$return;
        $app->redirect($login_url_with_return, 403);
      }
      else
      {
        $app->setHeader('status', 403, true);
        $app->redirect('index.php', 403);
      }
    }
  }

  $controller = BaseController::getInstance('Quix');
  $controller->execute($app->input->get('task'));
  $controller->redirect();
}
catch (Exception $e)
{
  ExceptionHandler::render($e);
}
