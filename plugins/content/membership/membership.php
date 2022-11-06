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
use Joomla\CMS\Plugin\CMSPlugin;

class plgContentMembership extends CMSPlugin
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
	 * @param   JForm  $form  The form to be altered.
	 * @param   array  $data  The associated data for the form.
	 *
	 * @return    boolean
	 * @since    2.1.0
	 */
	public function onContentPrepareForm($form, $data)
	{
		if (!$this->app)
		{
			return;
		}

		if ($this->app->isClient('site'))
		{
			return;
		}

		if (!($form instanceof JForm))
		{
			$this->_subject->setError('JERROR_NOT_A_FORM');

			return false;
		}

		$name = $form->getName();

		if ($name == 'com_content.article')
		{
			JForm::addFormPath(dirname(__FILE__) . '/form');
			$form->loadFile('membership', false);
		}

		Factory::getLanguage()->load('com_osmembership', JPATH_ADMINISTRATOR);

		return true;
	}

	/**
	 * @param $context
	 * @param $article
	 * @param $isNew
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function onContentAfterSave($context, $article, $isNew)
	{
		if ($context != 'com_content.article')
		{
			return true;
		}

		$articleId = $article->id;
		$data      = $this->app->input->get('jform', [], 'array');

		if ($articleId)
		{
			try
			{
				$db    = $this->db;
				$query = $db->getQuery(true);
				$query->delete('#__osmembership_articles');
				$query->where('article_id = ' . $db->Quote($articleId));
				$db->setQuery($query);
				$db->execute();

				if (!empty($data['plan_ids']))
				{
					$query->clear()
						->insert('#__osmembership_articles')
						->columns('plan_id,article_id');

					foreach ($data['plan_ids'] as $planId)
					{
						$query->values("$planId, $articleId");
					}

					$db->setQuery($query);
					$db->execute();
				}
			}
			catch (Exception $e)
			{
				$this->_subject->setError($e->getMessage());

				return false;
			}
		}
	}

	/**
	 * @param   string  $context  The context for the data
	 * @param   object  $data     The user id
	 *
	 * @return    boolean
	 * @since    2.1.0
	 */
	public function onContentPrepareData($context, $data)
	{
		if ($context != 'com_content.article' || !is_object($data))
		{
			return true;
		}

		$articleId = isset($data->id) ? $data->id : 0;

		if ($articleId > 0)
		{
			$db    = $this->db;
			$query = $db->getQuery(true)
				->select('plan_id')
				->from('#__osmembership_articles')
				->where('article_id = ' . (int) $articleId);
			$db->setQuery($query);
			$results = $db->loadColumn();
			$data->set('plan_ids', $results);
		}
	}
}
