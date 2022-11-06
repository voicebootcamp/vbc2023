<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2022 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die;

use Joomla\CMS\Plugin\CMSPlugin;

class PlgUserOSMembership extends CMSPlugin
{
	/**
	 * Application object.
	 *
	 * @var    JApplicationCms
	 */
	protected $app;
	/**
	 * Database object
	 *
	 * @var JDatabaseDriver
	 */
	protected $db;

	/**
	 * Constructor
	 *
	 *
	 * @param   object  $subject
	 * @param   array   $config
	 */
	public function __construct(&$subject, $config = [])
	{
		if (!file_exists(JPATH_ADMINISTRATOR . '/components/com_osmembership/osmembership.php'))
		{
			return;
		}

		parent::__construct($subject, $config);
	}

	/**
	 * Utility method to act on a user after it has been saved.
	 *
	 * This method creates a subscription record for the saved user
	 *
	 * @param   array    $user     Holds the new user data.
	 * @param   boolean  $isnew    True if a new user is stored.
	 * @param   boolean  $success  True if user was successfully stored in the database.
	 * @param   string   $msg      Message.
	 *
	 * @return  bool
	 *
	 * @since   2.6.0
	 */
	public function onUserAfterSave($user, $isnew, $success, $msg)
	{
		if (!$this->app)
		{
			return true;
		}

		// If the user wasn't stored we don't resync
		if (!$success)
		{
			return false;
		}

		// If the user isn't new we don't sync
		if (!$isnew)
		{
			return false;
		}

		// Ensure the user id is really an int
		$userId = (int) $user['id'];

		// If the user id appears invalid then bail out just in case
		if (empty($userId))
		{
			return false;
		}

		$planId = $this->params->get('plan_id', 0);

		if (empty($planId))
		{
			return false;
		}

		if ($this->app->input->getCmd('option') == 'com_osmembership')
		{
			return false;
		}

		// If user has existing subscription of this plan, no need for creating it
		$db    = $this->db;
		$query = $db->getQuery(true)
			->select('COUNT(*)')
			->from('#__osmembership_subscribers')
			->where('user_id = ' . $userId)
			->where('plan_id = ' . $planId)
			->where(('(published >= 1 OR payment_method LIKE "os_offline%")'));
		$db->setQuery($query);
		$total = $db->loadResult();

		if ($total)
		{
			return false;
		}

		// Create subscription record
		require_once JPATH_ADMINISTRATOR . '/components/com_osmembership/loader.php';

		// Initial basic data for the subscription record
		$name = $user['name'];
		$pos  = strpos($name, ' ');

		if ($pos !== false)
		{
			$firstName = substr($name, 0, $pos);
			$lastName  = substr($name, $pos + 1);
		}
		else
		{
			$firstName = $name;
			$lastName  = '';
		}

		$data = [
			'plan_id'    => $planId,
			'user_id'    => $userId,
			'first_name' => $firstName,
			'last_name'  => $lastName,
			'email'      => $user['email'],
		];

		$model = new OSMembershipModelApi();

		try
		{
			$model->store($data);
		}
		catch (Exception $e)
		{
			// Ignore error for now
		}

		return true;
	}
}
