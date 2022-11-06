<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_quix
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
/**
 * Quix HTML helper
 *
 * @since  3.0
 */
abstract class JHtmlCollectionType
{
	/**
	 * Cached array of the item types
	 *
	 * @var    array
	 * @since  1.6
	 */
	protected static $items = null;

	/**
	 * Get a list of the available library types
	 *
	 * @param   boolean  $all        True to include All (*)
	 * @param   boolean  $translate  True to translate All
	 *
	 * @return  string
	 *
	 * @see     JFormFieldContentLanguage
	 * @since   1.6
	 */
	public static function listLibrary()
	{
		if (empty(static::$items))
		{
			$list = array();
			$list[] = array(
				'value' => 'section',
				'text' => 'Section',
			);
			$list[] = array(
				'value' => 'layout',
				'text' => 'Layout',
			);
			

			static::$items = $list;
		}

		return static::$items;
	}
}
