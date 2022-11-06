<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2022 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

class OSMembershipControllerValidator extends MPFController
{
	/**
	 * Validate username, make sure it is allowed. In Joomla, username must be unique for each user
	 */
	public function validate_username()
	{
		$db         = Factory::getDbo();
		$query      = $db->getQuery(true);
		$userId     = Factory::getUser()->id;
		$username   = $this->input->getString('fieldValue');
		$validateId = $this->input->getString('fieldId');
		$query->select('COUNT(*)')
			->from('#__users')
			->where('username = ' . $db->quote($username));

		if ($userId > 0)
		{
			$query->where('id != ' . (int) $userId);
		}

		$db->setQuery($query);
		$total        = $db->loadResult();
		$arrayToJs    = [];
		$arrayToJs[0] = $validateId;

		if ($total)
		{
			$arrayToJs[1] = false;
		}
		else
		{
			$arrayToJs[1] = true;
		}

		echo json_encode($arrayToJs);

		$this->app->close();
	}

	/**
	 * Validate email, make sure it is valid before continue processing subscription
	 * In Joomla, each user must have an unique email address for account registration
	 */
	public function validate_email()
	{
		$user         = Factory::getUser();
		$config       = OSMembershipHelper::getConfig();
		$email        = $this->input->get('fieldValue', '', 'string');
		$validateId   = $this->input->getString('fieldId');
		$arrayToJs    = [];
		$arrayToJs[0] = $validateId;
		$arrayToJs[1] = true;

		if ($this->app->isClient('site') && $config->registration_integration && !$user->id)
		{
			$db    = Factory::getDbo();
			$query = $db->getQuery(true);
			$query->select('COUNT(*)')
				->from('#__users')
				->where('email = ' . $db->quote($email));
			$db->setQuery($query);
			$total = $db->loadResult();

			if ($total)
			{
				$arrayToJs[1] = false;
			}
		}

		echo json_encode($arrayToJs);

		$this->app->close();
	}

	/**
	 * Validate email, make sure it is valid before continue processing subscription
	 * In Joomla, each user must have an unique email address for account registration
	 */
	public function validate_group_member_email()
	{
		$db         = Factory::getDbo();
		$query      = $db->getQuery(true);
		$email      = $this->input->get('fieldValue', '', 'string');
		$validateId = $this->input->get('fieldId', '', 'string');
		$query->select('COUNT(*)')
			->from('#__users')
			->where('email = ' . $db->quote($email));
		$db->setQuery($query);
		$total        = $db->loadResult();
		$arrayToJs    = [];
		$arrayToJs[0] = $validateId;

		if (!$total)
		{
			$arrayToJs[1] = true;
		}
		else
		{
			$arrayToJs[1] = false;
		}

		echo json_encode($arrayToJs);

		$this->app->close();
	}

	/**
	 * Validate password to ensure that password is trong
	 */
	public function validate_password()
	{
		//Load language from user component
		$lang = Factory::getLanguage();
		$tag  = $lang->getTag();

		if (!$tag)
		{
			$tag = 'en-GB';
		}

		$lang->load('com_users', JPATH_ROOT, $tag);
		$value            = $this->input->get('fieldValue', '', 'none');
		$validateId       = $this->input->get('fieldId', '', 'none');
		$params           = ComponentHelper::getParams('com_users');
		$minimumIntegers  = $params->get('minimum_integers');
		$minimumSymbols   = $params->get('minimum_symbols');
		$minimumUppercase = $params->get('minimum_uppercase');
		$validPassword    = true;
		$errorMessage     = '';

		if (!empty($minimumIntegers))
		{
			$nInts = preg_match_all('/[0-9]/', $value, $imatch);

			if ($nInts < $minimumIntegers)
			{
				$errorMessage  = Text::plural('COM_USERS_MSG_NOT_ENOUGH_INTEGERS_N', $minimumIntegers);
				$validPassword = false;
			}
		}

		if ($validPassword && !empty($minimumSymbols))
		{
			$nsymbols = preg_match_all('[\W]', $value, $smatch);

			if ($nsymbols < $minimumSymbols)
			{
				$errorMessage  = Text::plural('COM_USERS_MSG_NOT_ENOUGH_SYMBOLS_N', $minimumSymbols);
				$validPassword = false;
			}
		}

		if ($validPassword && !empty($minimumUppercase))
		{
			$nUppercase = preg_match_all("/[A-Z]/", $value, $umatch);

			if ($nUppercase < $minimumUppercase)
			{
				$errorMessage  = Text::plural('COM_USERS_MSG_NOT_ENOUGH_UPPERCASE_LETTERS_N', $minimumUppercase);
				$validPassword = false;
			}
		}

		$arrayToJs    = [];
		$arrayToJs[0] = $validateId;

		if (!$validPassword)
		{
			$arrayToJs[1] = false;
			$arrayToJs[2] = $errorMessage;
		}
		else
		{
			$arrayToJs[1] = true;
		}

		echo json_encode($arrayToJs);

		$this->app->close();
	}
}
