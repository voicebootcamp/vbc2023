<?php
/**
 * Form Field class for the Joomla MPF.
 *
 * Supports a Url input.
 *
 * @package     Joomla.RAD
 * @subpackage  Form
 */

class MPFFormFieldUrl extends MPFFormFieldText
{
	/**
	 * Field Type
	 *
	 * @var string
	 */
	protected $type = 'Url';

	public function getOutput($tableLess = true, $bootstrapHelper = null)
	{
		if ($this->value && filter_var($this->value, FILTER_VALIDATE_URL))
		{
			$this->value = '<a href="' . $this->value . '">' . $this->value . '</a>';
		}

		return parent::getOutput($tableLess, $bootstrapHelper);
	}
}
