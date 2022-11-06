<?php
/**
 * @package     MPF
 * @subpackage  UI
 *
 * @copyright   Copyright (C) 2016 - 2018 Ossolution Team, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;

class MPFUiBootstrap4 extends MPFUiAbstract implements MPFUiInterface
{
	/**
	 * Twitter Bootstrap 4 framework classes
	 *
	 * @var array
	 */
	protected $frameworkClasses = [
		'form-control',
		'form-check-input',
	];

	/**
	 * Constructor
	 *
	 * @param   array  $classMaps
	 */
	public function __construct($classMaps = [])
	{
		if (empty($classMaps))
		{
			$classMaps = [
				// Grid
				'row-fluid'       => 'row',
				'span1'           => 'col-md-1',
				'span2'           => 'col-md-2',
				'span3'           => 'col-md-3',
				'span4'           => 'col-md-4',
				'span5'           => 'col-md-5',
				'span6'           => 'col-md-6',
				'span7'           => 'col-md-7',
				'span8'           => 'col-md-8',
				'span9'           => 'col-md-9',
				'span10'          => 'col-md-10',
				'span11'          => 'col-md-11',
				'span12'          => 'col-md-12',
				// Form classes
				'form'            => 'form',
				'form-horizontal' => 'form-horizontal',
				'control-group'   => 'form-group form-row',
				'control-label'   => 'col-md-3 form-control-label',
				'controls'        => 'col-md-9 eb-form-control',
				// Button classes
				'btn'             => 'btn btn-secondary',
				'btn-primary'     => 'btn-primary',
				'btn-info'        => 'btn-info',
				'btn-success'     => 'btn-success',
				'btn-warning'     => 'btn-warning',
				'btn-danger'      => 'btn-danger',
				'btn-inverse'     => 'btn-primary',
				'btn-link'        => 'btn-link',
				// Image classes
				'thumbnail'       => 'img-thumbnail',
				'img-polaroid'    => 'img-thumbnail',
				'img-rounded'     => 'img-thumbnail rounded',
				'img-circle'      => 'img-thumbnail rounded-circle',
				'img-responsive'  => 'img-fluid',
				// Table classes
				'table'           => 'table',
				'table-striped'   => 'table-striped',
				'table-bordered'  => 'table-bordered',
				'table-condensed' => 'table-sm',
				'table-hover'     => 'table-hover',
				// Badge classes
				'badge'           => 'badge',
				'badge-success'   => 'badge-success',
				'badge-warning'   => 'badge-warning',
				'badge-info'      => 'badge-info',
				'badge-danger'    => 'badge-danger',
				// Text classes
				'text-muted'      => 'text-muted',
				'text-warning'    => 'text-warning',
				'text-error'      => 'text-error',
				'text-info'       => 'text-info',
				'text-success'    => 'text-success',
				// Text Alignment
				'text-left'       => 'text-left',
				'text-center'     => 'text-center',
				'text-right'      => 'text-right',
				// Form input sizes
				'input-mini'      => 'input-mini',
				'input-small'     => 'input-small',
				'input-medium'    => 'input-medium',
				'input-large'     => 'input-large',
				'input-xlarge'    => 'input-xlarge',
				'input-xxlarge'   => 'input-xxlarge',
				// Button sizes
				'btn-mini'        => 'btn-xs',
				'btn-small'       => 'btn-sm',
				'btn-large'       => 'btn-lg',
				// Responsive utilities
				'visible-phone'   => 'd-block d-sm-none',
				'visible-tablet'  => 'visible-sm',
				'visible-desktop' => 'd-block d-md-none',
				'hidden-phone'    => 'd-none d-sm-block d-md-table-cell',
				'hidden-tablet'   => 'd-sm-none',
				'hidden-desktop'  => 'd-md-none hidden-lg',
				// Utility classes
				'pull-left'       => 'float-left',
				'pull-right'      => 'float-right',
				'clearfix'        => 'clearfix',
				'input-prepend'   => 'input-group-prepend',
				'input-append'    => 'input-group-append',
				'add-on'          => 'input-group-text',
				'nav'             => 'nav',
				'nav-pills'       => 'nav-pills',
				'nav-stacked'     => 'nav-stacked',
				'nav-tabs'        => 'nav-tabs',
				'icon-publish'    => 'fa fa-check',
				'icon-unpublish'  => 'fa fa-times',
			];
		}

		$this->classMaps = $classMaps;
	}

	/**
	 * Method to render input with prepend add-on
	 *
	 * @param   string  $input
	 * @param   string  $addOn
	 *
	 * @return string
	 */
	public function getPrependAddon($input, $addOn)
	{
		$html   = [];
		$html[] = '<div class="osm-addon-container input-group">';
		$html[] = '<div class="input-group-prepend">';
		$html[] = '<span class="input-group-text">' . $addOn . '</span>';
		$html[] = '</div>';
		$html[] = $input;
		$html[] = '</div>';

		return implode("\n", $html);
	}

	/**
	 * Method to render input with append add-on
	 *
	 * @param   string  $input
	 * @param   string  $addOn
	 *
	 * @return string
	 */
	public function getAppendAddon($input, $addOn)
	{
		$html   = [];
		$html[] = '<div class="osm-addon-container input-group">';
		$html[] = $input;
		$html[] = '<div class="input-group-append">';
		$html[] = '<span class="input-group-text">' . $addOn . '</span>';
		$html[] = '</div>';
		$html[] = '</div>';

		return implode("\n", $html);
	}
}
