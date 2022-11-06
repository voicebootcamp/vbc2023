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

class plgContentMPSubscriptionForm extends CMSPlugin
{
	/**
	 * Application object.
	 *
	 * @var    JApplicationCms
	 */
	protected $app;

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
	 * Parse article and display plans if configured
	 *
	 * @param $context
	 * @param $article
	 * @param $params
	 * @param $limitstart
	 *
	 * @return true
	 */
	public function onContentPrepare($context, &$article, &$params, $limitstart)
	{
		if (!$this->app
			|| $this->app->getName() != 'site'
			|| strpos($article->text, 'mpsubscriptionform') === false)
		{
			return true;
		}

		$regex         = '#{mpsubscriptionform (\d+)}#s';
		$article->text = preg_replace_callback($regex, [&$this, 'displaySubscriptionForm'], $article->text);

		return true;
	}

	/**
	 * Replace callback function
	 *
	 * @param $matches
	 *
	 * @return string
	 * @throws Exception
	 */
	public function displaySubscriptionForm($matches)
	{
		require_once JPATH_ADMINISTRATOR . '/components/com_osmembership/loader.php';

		$planId = (int) $matches[1];

		if (!$planId)
		{
			return '';
		}

		$plan = OSMembershipHelperDatabase::getPlan($planId);

		if (!$plan)
		{
			return '';
		}

		$layout = $this->params->get('layout_type', 'default');
		OSMembershipHelper::loadLanguage();

		$Itemid = OSMembershipHelperRoute::getPlanMenuId($plan->id, $plan->category_id, OSMembershipHelper::getItemid());

		$request = ['option' => 'com_osmembership', 'view' => 'register', 'layout' => $layout, 'id' => $planId, 'limit' => 0, 'hmvc_call' => 1, 'Itemid' => $Itemid];
		$input   = new MPFInput($request);
		$config  = [
			'default_controller_class' => 'OSMembershipController',
			'default_view'             => 'plans',
			'class_prefix'             => 'OSMembership',
			'language_prefix'          => 'OSM',
			'remember_states'          => false,
			'ignore_request'           => false,
		];

		ob_start();

		//Initialize the controller, execute the task
		MPFController::getInstance('com_osmembership', $input, $config)
			->execute();

		return ob_get_clean();
	}
}
