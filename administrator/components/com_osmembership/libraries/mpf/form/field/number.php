<?php
/**
 * Form Field class for the Joomla MPF.
 *
 * Supports a number input.
 *
 * @package     Joomla.MPF
 * @subpackage  Form
 */

class MPFFormFieldNumber extends MPFFormFieldText
{
	/**
	 * Field Type
	 *
	 * @var string
	 */
	protected $type = 'Number';

	/**
	 * Constructor.
	 *
	 * @param   OSMembershipTableField  $row
	 * @param   string                  $value
	 * @param   string                  $fieldSuffix
	 */
	public function __construct($row, $value, $fieldSuffix)
	{
		parent::__construct($row, $value, $fieldSuffix);

		if ($row->min)
		{
			$this->attributes['min'] = $row->min;
		}

		if ($row->max)
		{
			$this->attributes['max'] = $row->max;
		}

		if ($row->step)
		{
			$this->attributes['step'] = $row->step;
		}
	}
}
