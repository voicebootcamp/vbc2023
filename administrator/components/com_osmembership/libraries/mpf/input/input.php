<?php
/**
 * @package     MPF
 * @subpackage  Input
 *
 * @copyright   Copyright (C) 2016 Ossolution Team, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;
define('MPF_INPUT_ALLOWRAW', 2);
define('MPF_INPUT_ALLOWHTML', 4);

use Joomla\CMS\Filter\InputFilter;

/**
 * Extends JInput class to allow getting raw data from Input object. This can be removed when we don't provide support for Joomla 2.5.x
 *
 * @package       MPF
 * @subpackage    Input
 * @since         1.0
 */
class MPFInput extends JInput
{
	/**
	 * Keep a reference of original input object
	 *
	 * @var JInput
	 */
	protected $input;

	/**
	 * Constructor.
	 *
	 * @param   array  $source   Source data (Optional, default is $_REQUEST)
	 * @param   array  $options  Array of configuration parameters (Optional)
	 */
	public function __construct($source = null, array $options = [])
	{
		if ($source instanceof JInput)
		{
			$this->input = $source;

			$reflection = new ReflectionClass($source);
			$property   = $reflection->getProperty('data');
			$property->setAccessible(true);
			$source = $property->getValue($source);
		}

		if (!isset($options['filter']))
		{
			if (version_compare(JVERSION, '4.0.0-dev', 'ge'))
			{
				//Set default filter so that getHtml can be returned properly
				$options['filter'] = InputFilter::getInstance([], [], 1, 1);
			}
			else
			{
				$options['filter'] = InputFilter::getInstance(null, null, 1, 1);
			}
		}

		parent::__construct($source, $options);
	}

	/**
	 * Get data from the input
	 *
	 * @param   int  $mask
	 *
	 * @return array
	 */
	public function getData($mask = MPF_INPUT_ALLOWHTML)
	{
		if ($mask & 2)
		{
			return $this->data;
		}

		return $this->filter->clean($this->data, '');
	}

	/**
	 * Magic method to get an input object
	 *
	 * @param   mixed  $name  Name of the input object to retrieve.
	 *
	 * @return  JInput  The request input object
	 *
	 * @since   11.1
	 */
	public function __get($name)
	{
		if (isset($this->inputs[$name]))
		{
			return $this->inputs[$name];
		}

		$className = 'JInput' . ucfirst($name);

		if (class_exists($className))
		{
			$this->inputs[$name] = new $className(null, $this->options);

			return $this->inputs[$name];
		}

		$superGlobal = '_' . strtoupper($name);

		if (isset($GLOBALS[$superGlobal]))
		{
			$this->inputs[$name] = new MPFInput($GLOBALS[$superGlobal], $this->options);

			return $this->inputs[$name];
		}

	}

	/**
	 * Check to see if a variable is avaialble in the input or not
	 *
	 * @param   string  $name  the variable name
	 *
	 * @return boolean
	 */
	public function has($name)
	{
		if (isset($this->data[$name]))
		{
			return true;
		}

		return false;
	}

	/**
	 * Override set method to make change back to Joomla Input Object
	 *
	 * @param   string  $name
	 * @param   mixed   $value
	 */
	public function set($name, $value)
	{
		parent::set($name, $value);

		// Store change back to the original Joomla Input object if provided
		if ($this->input)
		{
			$this->input->set($name, $value);
		}
	}
}
