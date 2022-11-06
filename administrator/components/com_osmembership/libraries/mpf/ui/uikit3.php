<?php
/**
 * @package     MPF
 * @subpackage  UI
 *
 * @copyright   Copyright (C) 2016 - 2018 Ossolution Team, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;

class MPFUiUikit3 extends MPFUiAbstract implements MPFUiInterface
{
	/**
	 * UIKIT 3 framework classes
	 *
	 * @var array
	 */
	protected $frameworkClasses = [
		'uk-input',
		'uk-select',
		'uk-textarea',
		'uk-radio',
		'uk-checkbox',
		'uk-legend',
		'uk-range',
		'uk-fieldset',
		'uk-legend',
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
				'row-fluid'            => 'uk-container uk-grid',
				'span2'                => 'uk-width-1-6@s',
				'span3'                => 'uk-width-1-4@s',
				'span4'                => 'uk-width-1-3@s',
				'span5'                => 'uk-width-1-2@s',
				'span6'                => 'uk-width-1-2@s',
				'span7'                => 'uk-width-1-2@s',
				'span8'                => 'uk-width-2-3@s',
				'span9'                => 'uk-width-3-4@s',
				'span10'               => 'uk-width-5-6@s',
				'span12'               => 'uk-width-1-1',
				// Form classes
				'form'                 => 'uk-form-stacked',
				'form form-horizontal' => 'uk-form-horizontal',
				'form-horizontal'      => 'uk-form-horizontal',
				'control-group'        => 'control-group',
				'control-label'        => 'uk-form-label',
				'controls'             => 'uk-form-controls uk-form-controls-text eb-form-control',
				// Button classes
				'btn'                  => 'uk-button uk-button-default',
				'btn-primary'          => 'uk-button-primary',
				'btn-info'             => 'uk-button uk-button-default',
				'btn-success'          => 'uk-button-primary',
				'btn-warning'          => 'uk-button-danger',
				'btn-danger'           => 'uk-button-danger',
				'btn-inverse'          => 'uk-button-secondary',
				'btn-link'             => 'uk-button-link',
				// Image classes
				'thumbnail'            => 'thumbnail',
				'img-polaroid'         => 'img-polaroid',
				'img-rounded'          => 'img-rounded',
				'img-circle'           => 'img-circle',
				'img-responsive'       => 'img-responsive-bs2',
				// Table classes
				'table'                => 'uk-table',
				'table-striped'        => 'uk-table-striped',
				'table-bordered'       => 'uk-table-divider',
				'table-condensed'      => 'uk-table-small',
				'table-hover'          => 'uk-table-hover',
				// Badge classes
				'badge'                => 'uk-badge',
				'badge-success'        => 'uk-label-success',
				'badge-warning'        => 'uk-label-warning',
				'badge-info'           => 'uk-badge',
				'badge-danger'         => 'uk-label-danger',
				// Text classes
				'text-muted'           => 'uk-text-muted',
				'text-warning'         => 'uk-text-warning',
				'text-error'           => 'uk-text-danger',
				'text-info'            => 'uk-text-primary',
				'text-success'         => 'uk-text-success',
				// Text Alignment
				'text-left'            => 'uk-text-left',
				'text-center'          => 'uk-text-center',
				'text-right'           => 'uk-text-right',
				// Form input sizes
				'input-mini'           => 'input-mini',
				'input-small'          => 'input-small',
				'input-medium'         => 'input-medium',
				'input-large'          => 'input-large',
				'input-xlarge'         => 'input-xlarge',
				'input-xxlarge'        => 'input-xxlarge',
				// Button sizes
				'btn-mini'             => 'uk-button-small',
				'btn-small'            => 'uk-button-small',
				'btn-large'            => 'uk-button-large',
				// Responsive utilities
				'visible-phone'        => 'uk-hidden@s',
				'visible-tablet'       => 'uk-hidden@m',
				'visible-desktop'      => 'uk-hidden@l',
				'hidden-phone'         => 'uk-visible@s',
				'hidden-tablet'        => 'uk-visible@m',
				'hidden-desktop'       => 'uk-visible@l',
				// Utility classes
				'pull-left'            => 'uk-float-left',
				'pull-right'           => 'uk-float-right',
				'clearfix'             => 'uk-clearfix',
				'input-prepend'        => 'input-prepend',
				'input-append'         => 'input-append',
				'add-on'               => 'add-on',
				'nav'                  => 'uk-nav',
				'nav-pills'            => 'uk-navbar',
				'nav-stacked'          => 'nav-stacked',
				'nav-tabs'             => 'nav-tabs',
				'eb-one-half'          => 'eb-one-half',
				'eb-one-third'         => 'eb-one-third',
				'eb-two-thirds'        => 'eb-two-thirds',
				'eb-one-quarter'       => 'eb-one-quarter',
				'eb-two-quarters'      => 'eb-two-quarters',
				'eb-three-quarters'    => 'eb-three-quarters',
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
	 * @return mixed
	 */
	public function getPrependAddon($input, $addOn)
	{
		$html   = [];
		$html[] = '<div class="uk-inline">';
		$html[] = '<span class="uk-form-icon">' . $addOn . '</span>';
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
		$html[] = '<div class="uk-inline">';
		$html[] = $input;
		$html[] = '<span class="uk-form-icon">' . $addOn . '</span>';
		$html[] = '</div>';

		return implode("\n", $html);
	}
}
