<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2022 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Uri\Uri;

class EventbookingViewRegistrantsHtml extends RADViewList
{
	use EventbookingViewRegistrants;

	/**
	 * Prepare view data for displaying
	 *
	 * @throws Exception
	 */
	protected function prepareView()
	{
		parent::prepareView();

		$user = Factory::getUser();

		if (!$user->authorise('eventbooking.registrantsmanagement', 'com_eventbooking'))
		{
			if ($user->get('guest'))
			{
				$this->requestLogin();
			}
			else
			{
				$app = Factory::getApplication();
				$app->enqueueMessage(Text::_('NOT_AUTHORIZED'), 'error');
				$app->redirect(Uri::root(), 403);
			}
		}

		$this->prepareViewData();
		$this->coreFields = EventbookingHelperRegistration::getPublishedCoreFields();

		$this->findAndSetActiveMenuItem();

		$this->addToolbar();
	}

	/**
	 * Override addToolbar method to add custom csv export function
	 * @see RADViewList::addToolbar()
	 */
	protected function addToolbar()
	{
		$this->hideButtons = $this->params->get('hide_buttons', []);

		if (!EventbookingHelperAcl::canDeleteRegistrant())
		{
			$this->hideButtons[] = 'delete';
		}

		parent::addToolbar();

		if (!in_array('cancel_registrations', $this->hideButtons))
		{
			ToolbarHelper::custom('cancel_registrations', 'cancel', 'cancel', 'EB_CANCEL_REGISTRATIONS', true);
		}

		if ($this->config->activate_checkin_registrants)
		{
			if (!in_array('checkin_multiple_registrants', $this->hideButtons))
			{
				ToolbarHelper::checkin('checkin_multiple_registrants');
			}

			if (!in_array('check_out', $this->hideButtons))
			{
				ToolbarHelper::unpublish('check_out', Text::_('EB_CHECKOUT'), true);
			}
		}

		if (!in_array('batch_mail', $this->hideButtons))
		{
			$bar = Toolbar::getInstance('toolbar');

			if (EventbookingHelper::isJoomla4())
			{
				$bar->popupButton('batch-sms')
					->text('EB_MASS_MAIL')
					->selector('collapseModal')
					->listCheck(true);
			}
			else
			{
				$layout = new FileLayout('joomla.toolbar.batch');
				$dhtml  = $layout->render(['title' => Text::_('EB_MASS_MAIL')]);
				$bar->appendButton('Custom', $dhtml, 'batch-sms');
			}
		}

		if (!in_array('resend_email', $this->hideButtons))
		{
			ToolbarHelper::custom('resend_email', 'envelope', 'envelope', 'EB_RESEND_EMAIL', true);
		}

		if (!in_array('export', $this->hideButtons))
		{
			ToolbarHelper::custom('export', 'download', 'download', 'EB_EXPORT_REGISTRANTS', false);
		}

		if (!in_array('export_pdf', $this->hideButtons))
		{
			ToolbarHelper::custom('export_pdf', 'download', 'download', 'EB_EXPORT_PDF', false);
		}

		if ($this->config->activate_certificate_feature)
		{
			if (!in_array('download_certificates', $this->hideButtons))
			{
				ToolbarHelper::custom('download_certificates', 'download', 'download', 'EB_DOWNLOAD_CERTIFICATES', true);
			}

			if (!in_array('send_certificates', $this->hideButtons))
			{
				ToolbarHelper::custom('send_certificates', 'envelope', 'envelope', 'EB_SEND_CERTIFICATES', true);
			}
		}

		if ($this->config->activate_waitinglist_feature && !in_array('request_payment', $this->hideButtons))
		{
			ToolbarHelper::custom('request_payment', 'envelope', 'envelope', 'EB_REQUEST_PAYMENT', true);
		}
	}
}
