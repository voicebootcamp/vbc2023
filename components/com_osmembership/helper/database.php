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

class OSMembershipHelperDatabase
{
	/**
	 * Get category data from database
	 *
	 * @param $id
	 *
	 * @return mixed
	 */
	public static function getCategory($id)
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true)
			->select('*')
			->from('#__osmembership_categories')
			->where('id=' . (int) $id);

		if ($fieldSuffix = OSMembershipHelper::getFieldSuffix())
		{
			self::getMultilingualFields($query, ['title', 'description'], $fieldSuffix);
		}

		$db->setQuery($query);

		return $db->loadObject();
	}

	/**
	 * Get category data from database
	 *
	 * @param   int     $id
	 * @param   string  $fieldSuffix
	 *
	 * @return mixed
	 */
	public static function getPlan($id, $fieldSuffix = null)
	{
		$db = Factory::getDbo();

		if ($fieldSuffix === null)
		{
			$fieldSuffix = OSMembershipHelper::getFieldSuffix();
		}

		$query = $db->getQuery(true)
			->select('*')
			->from('#__osmembership_plans')
			->where('id=' . (int) $id);

		if ($fieldSuffix)
		{
			self::getMultilingualFields($query, ['title', 'short_description', 'description'], $fieldSuffix);
		}

		$db->setQuery($query);

		return $db->loadObject();
	}

	/**
	 * Get all published subscription plans
	 *
	 * @param   string  $key
	 *
	 * @return mixed
	 */
	public static function getAllPlans($key = '')
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true)
			->select('*')
			->from('#__osmembership_plans')
			->where('published = 1');

		if ($fieldSuffix = OSMembershipHelper::getFieldSuffix())
		{
			self::getMultilingualFields($query, ['title', 'short_description', 'description'], $fieldSuffix);
		}

		$db->setQuery($query);

		return $db->loadObjectList($key);
	}

	/**
	 * Helper method to get fields from database table in case the site is multilingual
	 *
	 * @param   JDatabaseQuery  $query
	 * @param   array           $fields
	 * @param   string          $fieldSuffix
	 */
	public static function getMultilingualFields(JDatabaseQuery $query, $fields = [], $fieldSuffix = '')
	{
		$db = Factory::getDbo();

		foreach ($fields as $field)
		{
			$alias  = $field;
			$dotPos = strpos($field, '.');

			if ($dotPos !== false)
			{
				$alias = substr($field, $dotPos + 1);
			}

			$query->select($db->quoteName($field . $fieldSuffix, $alias));
		}
	}

	/**
	 * Method to get a upgrade rule base on given id
	 *
	 * @param   int  $id
	 *
	 * @return stdClass|null
	 */
	public static function getUpgradeRule($id)
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true)
			->select('*')
			->from('#__osmembership_upgraderules')
			->where('id = ' . (int) $id);
		$db->setQuery($query);

		return $db->loadObject();
	}

	/**
	 * Method to get a renew option
	 *
	 * @param   int  $id
	 *
	 * @return stdClass|null
	 */
	public static function getRenewOption($id)
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true)
			->select('*')
			->from('#__osmembership_renewrates')
			->where('id = ' . (int) $id);
		$db->setQuery($query);

		return $db->loadObject();
	}
}
