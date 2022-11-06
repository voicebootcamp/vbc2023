<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2022 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Uri\Uri;

class plgContentMPRestriction extends CMSPlugin
{
	/**
	 * Application object.
	 *
	 * @var    JApplicationCms
	 */
	protected $app;

	/**
	 * Database object.
	 *
	 * @var    JDatabaseDriver
	 */
	protected $db;

	/**
	 * Constructor
	 *
	 * @param   object &$subject  The object to observe
	 * @param   array   $config   An optional associative array of configuration settings.
	 */
	public function __construct($subject, array $config = [])
	{
		if (!file_exists(JPATH_ADMINISTRATOR . '/components/com_osmembership/osmembership.php'))
		{
			return;
		}

		parent::__construct($subject, $config);
	}

	/**
	 * @param     $context
	 * @param     $row
	 * @param     $params
	 * @param     $page
	 *
	 * @return bool
	 */
	public function onContentPrepare($context, &$row, &$params, $page = 0)
	{
		if (!$this->app
			|| $this->app->getName() != 'site'
			|| strpos($row->text, 'mprestriction') === false)
		{
			return true;
		}

		// Search for this tag in the content
		$regex     = '#{mprestriction ids="(.*?)"}(.*?){/mprestriction}#s';
		$row->text = preg_replace_callback($regex, [&$this, 'processRestriction'], $row->text);

		return true;
	}

	public function processRestriction($matches)
	{
		// Require library + register autoloader
		require_once JPATH_ADMINISTRATOR . '/components/com_osmembership/loader.php';

		$document = Factory::getDocument();
		$rootUri  = Uri::base(true);

		$document->addStylesheet($rootUri . '/media/com_osmembership/assets/css/style.css');

		$customCssFile = JPATH_ROOT . '/media/com_osmembership/assets/css/custom.css';

		if (file_exists($customCssFile) && filesize($customCssFile) > 0)
		{
			$document->addStylesheet($rootUri . '/media/com_osmembership/assets/css/custom.css');
		}

		$message     = OSMembershipHelper::getMessages();
		$fieldSuffix = OSMembershipHelper::getFieldSuffix();

		if (strlen($message->{'content_restricted_message' . $fieldSuffix}))
		{
			$restrictedText = $message->{'content_restricted_message' . $fieldSuffix};
		}
		else
		{
			$restrictedText = $message->content_restricted_message;
		}

		$requiredPlanIds = $matches[1];
		$protectedText   = $matches[2];

		// Super admin should see all text
		$user = Factory::getUser();
		$db   = $this->db;

		if ($user->authorise('core.admin', 'com_osmembership'))
		{
			return $protectedText;
		}

		$activePlanIds = OSMembershipHelperSubscription::getActiveMembershipPlans();

		if (substr($requiredPlanIds, 0, 1) == '!')
		{
			$requiredPlanIds = substr($requiredPlanIds, 1);

			if ($requiredPlanIds == '*')
			{
				if (count($activePlanIds) == 1 && $activePlanIds[0] == 0)
				{
					return $protectedText;
				}
			}
			else
			{
				$requiredPlanIds = explode(',', $requiredPlanIds);

				if (count(array_intersect($requiredPlanIds, $activePlanIds)) == 0)
				{
					return $protectedText;
				}
				else
				{
					return '';
				}
			}
		}
		else
		{
			if ($requiredPlanIds == '*')
			{
				$query = $db->getQuery(true)
					->select('id')
					->from('#__osmembership_plans')
					->where('published = 1')
					->order('ordering');
				$db->setQuery($query);
				$planIds = $db->loadColumn();
			}
			else
			{
				$planIds = explode(',', $requiredPlanIds);
			}

			$redirectUrl = OSMembershipHelper::callOverridableHelperMethod('Helper', 'getPluginRestrictionRedirectUrl', [$this->params, $planIds]);

			// Store URL of this page to redirect user back after user logged in if they have active subscription of this plan
			$session = Factory::getSession();
			$session->set('osm_return_url', Uri::getInstance()->toString());
			$session->set('required_plan_ids', $planIds);

			$query = $db->getQuery(true)
				->select('title')
				->from('#__osmembership_plans')
				->where('id IN (' . implode(',', $planIds) . ')');
			$db->setQuery($query);
			$planTitles = implode(', ', $db->loadColumn());

			$restrictedText = str_replace('[SUBSCRIPTION_URL]', $redirectUrl, $restrictedText);
			$restrictedText = str_replace('[PLAN_TITLES]', $planTitles, $restrictedText);

			$restrictedText = HTMLHelper::_('content.prepare', $restrictedText);

			if (count($activePlanIds) == 1 && $activePlanIds[0] == 0)
			{
				return '<div id="restricted_info">' . $restrictedText . '</div>';
			}
			elseif ($requiredPlanIds == '*')
			{
				return $protectedText;
			}
			else
			{
				$requiredPlanIds = explode(',', $requiredPlanIds);

				if (count(array_intersect($requiredPlanIds, $activePlanIds)))
				{
					return $protectedText;
				}
				else
				{
					return '<div id="restricted_info">' . $restrictedText . '</div>';
				}
			}
		}
	}
}
