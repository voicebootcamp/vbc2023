<?php
/**
 * @package            Joomla
 * @subpackage         Membership Pro
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2012 - 2022 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Captcha\Captcha;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;

trait OSMembershipControllerCaptcha
{
	/**
	 * Method to validate captcha
	 *
	 * @param   MPFInput  $input
	 * @param   string    $errorMessage
	 *
	 * @return bool|mixed
	 */
	protected function validateCaptcha($input, &$errorMessage = null)
	{
		$user   = Factory::getUser();
		$config = OSMembershipHelper::getConfig();

		if ($config->enable_captcha == 1 || ($config->enable_captcha == 2 && !$user->id))
		{
			$captchaPlugin = $this->app->get('captcha') ?: 'recaptcha';

			$plugin = PluginHelper::getPlugin('captcha', $captchaPlugin);

			if ($plugin)
			{
				try
				{
					return Captcha::getInstance($captchaPlugin)->checkAnswer($input->post->get('recaptcha', null, 'string'));
				}
				catch (Exception $e)
				{
					$errorMessage = $e->getMessage();

					return false;
				}
			}
		}

		return true;
	}
}
