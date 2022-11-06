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
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

/**
 * HTML View class for Membership Pro component
 *
 * @static
 * @package        Joomla
 * @subpackage     Membership Pro
 */
class OSMembershipViewMemberHtml extends MPFViewHtml
{
	/**
	 * Member data
	 *
	 * @var stdClass
	 */
	protected $item;

	/**
	 * Model state
	 *
	 * @var MPFModelState
	 */
	protected $state;

	/**
	 * Fields which will be displayed on member page
	 *
	 * @var array
	 */
	protected $fields;

	/**
	 * Member data array
	 *
	 * @var array
	 */
	protected $data;

	/**
	 * Active menu item parameters
	 *
	 * @var \Joomla\Registry\Registry
	 */
	protected $params;

	/**
	 * Component config
	 *
	 * @var MPFConfig
	 */
	protected $config;

	/**
	 * Display member
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	public function display()
	{
		if (!Factory::getUser()->authorise('core.viewmembers', 'com_osmembership'))
		{
			$app = Factory::getApplication();
			$app->enqueueMessage(Text::_('OSM_NOT_ALLOW_TO_VIEW_MEMBERS'));
			$app->redirect(Uri::root(), 403);
		}

		/* @var OSMembershipModelMember $model */
		$model = $this->getModel();
		$item  = $model->getData();
		$state = $model->getState();

		if (!$item)
		{
			throw new Exception(sprintf('Member ID %d does not exist in the system', $state->get('id')));
		}

		$fields = OSMembershipHelper::getProfileFields($item->plan_id, true);

		for ($i = 0, $n = count($fields); $i < $n; $i++)
		{
			if (!$fields[$i]->show_on_profile || in_array($fields[$i]->name, ['first_name', 'last_name']))
			{
				unset($fields[$i]);
			}
		}

		$fields = array_values($fields);

		$this->item   = $item;
		$this->state  = $state;
		$this->fields = $fields;
		$this->data   = OSMembershipHelper::getProfileData($item, $item->plan_id, $fields);
		$this->config = OSMembershipHelper::getConfig();
		$this->params = Factory::getApplication()->getParams();

		foreach ($fields as $field)
		{
			if ($field->is_core)
			{
				continue;
			}

			if (isset($this->data[$field->name]))
			{
				$fieldValue = $this->data[$field->name];
			}
			else
			{
				$fieldValue = '';
			}

			$this->item->{$field->name} = $fieldValue;
		}
		
		// Force to use default layout
		$this->setLayout('default');

		parent::display();
	}
}
