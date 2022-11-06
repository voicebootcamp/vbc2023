<?php
/**
 * @package            Joomla
 * @subpackage         OS Services Booking
 * @author             Dang Thuc Dam
 * @copyright          Copyright (C) 2012 - 2018 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */
class OsbHelperBootstrap
{
	/**
	 * Twitter bootstrap version, default 2
	 * @var string
	 */
	protected $bootstrapVersion;

	/**
	 * The class mapping to map between twitter bootstrap 2 and twitter bootstrap 3
	 * @var string
	 */
	protected static $classMaps;

	/**
	 * Constructor, initialize the classmaps array
	 *
	 * @param int $bootstrapVersion
	 */
	public function __construct($bootstrapVersion)
	{
		if (empty($bootstrapVersion))
		{
			$bootstrapVersion = 2;
		}
		if(!OSBHelper::isJoomla4() && JFactory::getApplication()->isClient('administrator'))
		{
			$bootstrapVersion = 2;
		}
		elseif(OSBHelper::isJoomla4() && JFactory::getApplication()->isClient('administrator'))
		{
			$bootstrapVersion = 4;
		}
		$this->bootstrapVersion = $bootstrapVersion;
		// The static class map
		if ($bootstrapVersion == 2)
		{
			self::$classMaps = array(
				'row-fluid'       => 'row-fluid',
				'span1'           => 'span1',
				'span2'           => 'span2',
				'span3'           => 'span3',
				'span4'           => 'span4',
				'span5'           => 'span5',
				'span6'           => 'span6',
				'span7'           => 'span7',
				'span8'           => 'span8',
				'span9'           => 'span9',
				'span10'          => 'span10',
				'span11'          => 'span11',
				'span12'          => 'span12',
				'btn'             => 'btn',
				'btn-mini'        => 'btn-mini',
				'btn-small'       => 'btn-small',
				'btn-large'       => 'btn-large',
				'btn-inverse'     => 'btn-inverse',
				'visible-phone'   => 'visible-phone',
				'visible-tablet'  => 'visible-tablet',
				'visible-desktop' => 'visible-desktop',
				'hidden-phone'    => 'hidden-phone',
				'hidden-tablet'   => 'hidden-tablet',
				'hidden-desktop'  => 'hidden-desktop',
				'control-group'   => 'control-group',
				'input-prepend'   => 'input-prepend',
				'input-append '   => 'input-append',
				'add-on'          => 'add-on',
				'img-polaroid'    => 'img-polaroid',
				'control-label'   => 'control-label',
				'controls'        => 'controls',
				'icon-location'	  => 'icon-location',
				'icon-map-marker'	  => 'icon-location icon-map-marker',
                'input-small'     => 'input-small',
                'input-medium'    => 'input-medium',
                'input-large'     => 'input-large',
				'input-mini'	  => 'input-mini',
                'icon-tag'        => 'icon-tag',
                'icon-phone'      => 'icon-phone',
                'icon-mail'       => 'icon-mail',
				'inputbox'		  => 'form-control',
			);
		}
		elseif ($bootstrapVersion == 3)
		{
			self::$classMaps = array(
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
				'btn'             => 'btn btn-default',
				'btn-mini'        => 'btn-xs',
				'btn-small'       => 'btn-sm',
				'btn-large'       => 'btn-lg',
				'btn-inverse'     => 'btn-primary',
				'visible-phone'   => 'visible-xs',
				'visible-tablet'  => 'visible-sm',
				'visible-desktop' => 'visible-md visible-lg',
				'hidden-phone'    => 'hidden-xs',
				'hidden-tablet'   => 'hidden-sm',
				'hidden-desktop'  => 'hidden-md hidden-lg',
				'control-group'   => 'form-group',
				'input-prepend'   => 'input-group',
				'input-append '   => 'input-group',
				'add-on'          => 'input-group-addon',
				'img-polaroid'    => 'img-thumbnail',
				'control-label'   => 'col-sm-3 control-label',
				'controls'        => 'col-sm-9',
				'icon-location'	  => 'icon-location',
                'input-small'     => 'input-small',
                'input-medium'    => 'input-medium',
                'input-large'     => 'input-large',
				'input-mini'	  => 'input-mini',
                'icon-tag'        => 'icon-tag',
                'icon-phone'      => 'icon-phone',
                'icon-mail'       => 'icon-mail',
				'inputbox'		  => 'form-control',
			);
        }
        elseif ($bootstrapVersion == 4)
        {
            self::$classMaps = array(
                'row-fluid'       => 'row',
                'span1'           => 'col-lg-1',
                'span2'           => 'col-lg-2',
                'span3'           => 'col-lg-3',
                'span4'           => 'col-lg-4',
                'span5'           => 'col-lg-5',
                'span6'           => 'col-lg-6',
                'span7'           => 'col-lg-7',
                'span8'           => 'col-lg-8',
                'span9'           => 'col-lg-9',
                'span10'          => 'col-lg-10',
                'span11'          => 'col-lg-11',
                'span12'          => 'col-lg-12',
                'pull-left'       => 'float-left',
                'pull-right'      => 'float-right',
                'btn'             => 'btn btn-secondary',
                'btn-mini'        => 'btn-xs',
                'btn-small'       => 'btn-sm',
                'btn-large'       => 'btn-lg',
                'btn-inverse'     => 'btn-primary',
                'visible-phone'   => 'd-block d-sm-none',
                'visible-tablet'  => 'visible-sm',
                'visible-desktop' => 'd-block d-md-none',
                'hidden-phone'    => 'd-none d-sm-block',
                'hidden-tablet'   => 'd-sm-none',
                'hidden-desktop'  => 'd-md-none hidden-lg',
                'control-group'   => 'form-group form-row row',
                'input-prepend'   => 'input-group-prepend',
                'input-append'    => 'input-group-append',
                'add-on'          => 'input-group-text',
                'img-polaroid'    => 'img-thumbnail',
                'control-label'   => 'col-md-3 form-control-label',
                'controls'        => 'col-md-9',
                'img-rounded'	  => 'rounded',
                'btn'			  => 'btn btn-light',
                'input-small'     => 'form-control ',
                'input-medium'    => 'form-control ',
                'input-large'     => 'form-control ',
				'input-mini'      => 'form-control ',
				'inputbox'		  => 'form-control',
                'icon-tag'        => 'fa fa-tag',
                'icon-phone'      => 'fa fa-phone',
                'icon-mail'       => 'fa fa-envelope',
            );
        }
		elseif ($bootstrapVersion == 6)
        {
            self::$classMaps = array(
                'row-fluid'          => 'row',
				'span1'              => 'col-md-1',
				'span2'              => 'col-md-2',
				'span3'              => 'col-md-3',
				'span4'              => 'col-md-4',
				'span5'              => 'col-md-5',
				'span6'              => 'col-md-6',
				'span7'              => 'col-md-7',
				'span8'              => 'col-md-8',
				'span9'              => 'col-md-9',
				'span10'             => 'col-md-10',
				'span11'             => 'col-md-11',
				'span12'             => 'col-md-12',
                'pull-left'       => 'float-left',
                'pull-right'      => 'float-right',
                'btn'             => 'btn btn-secondary',
                'btn-mini'        => 'btn-xs',
                'btn-small'       => 'btn-sm',
                'btn-large'       => 'btn-lg',
                'btn-inverse'     => 'btn-primary',
                'visible-phone'   => 'd-block d-sm-none',
                'visible-tablet'  => 'visible-sm',
                'visible-desktop' => 'd-block d-md-none',
                'hidden-phone'    => 'd-none d-sm-block',
                'hidden-tablet'   => 'd-sm-none',
                'hidden-desktop'  => 'd-md-none hidden-lg',
                'control-group'   => 'form-group form-row row',
                'input-prepend'   => 'input-group-prepend',
                'input-append'    => 'input-group-append',
                'add-on'          => 'input-group-text',
                'img-polaroid'    => 'img-thumbnail',
                'control-label'   => 'col-md-3 form-control-label',
                'controls'        => 'col-md-9',
                'img-rounded'	  => 'rounded',
                'btn'			  => 'btn btn-light',
                'input-small'     => 'form-control ',
                'input-medium'    => 'form-control ',
                'input-large'     => 'form-control ',
				'input-mini'      => 'form-control ',
				'inputbox'		  => 'form-control',
                'icon-tag'        => 'fa fa-tag',
                'icon-phone'      => 'fa fa-phone',
                'icon-mail'       => 'fa fa-envelope',
            );
        }
		elseif($bootstrapVersion == 5)
		{
			self::$classMaps = array(
				'row-fluid'                                      => 'uk-container uk-grid',
				'span2'                                          => 'uk-width-1-6@s',
				'span3'                                          => 'uk-width-1-4@s',
				'span4'                                          => 'uk-width-1-3@s',
				'span5'                                          => 'uk-width-1-2@s',
				'span6'                                          => 'uk-width-1-2@s',
				'span7'                                          => 'uk-width-1-2@s',
				'span8'                                          => 'uk-width-2-3@s',
				'span9'                                          => 'uk-width-3-4@s',
				'span10'                                         => 'uk-width-5-6@s',
				'span12'                                         => 'uk-width-1-1',
				'pull-left'                                      => 'uk-float-left',
				'pull-right'                                     => 'uk-float-right',
				'clearfix'                                       => 'uk-clearfix',
				'btn'                                            => 'uk-button uk-button-default',
				'btn-primary'                                    => 'uk-button-primary',
				'btn-mini'                                       => 'uk-button uk-button-default uk-button-small',
				'btn-small'                                      => 'uk-button uk-button-default uk-button-small',
				'btn-large'                                      => 'uk-button uk-button-default uk-button-large',
				'btn-inverse'                                    => 'uk-button-primary',
				'hidden-phone'                                   => 'uk-visible@s',
				'form form-horizontal'                           => 'uk-form-horizontal',
				'control-group'                                  => 'control-group',
				'control-label'                                  => 'uk-form-label',
				'controls'                                       => 'uk-form-controls uk-form-controls-text',
				'input-tiny'                                     => 'uk-input uk-form-width-xsmall',
				'input-small'                                    => 'uk-input uk-form-width-small',
				'input-medium'                                   => 'uk-input uk-form-width-medium',
				'input-large'                                    => 'uk-input uk-form-width-large',
				'center'                                         => 'uk-text-center',
				'text-center'                                    => 'uk-text-center',
				'row-fluid clearfix'                             => 'uk-container uk-grid uk-clearfix',
				'btn btn-primary'                                => 'uk-button uk-button-default uk-button-primary',
				'table table-striped table-bordered'             => 'uk-table uk-table-striped uk-table-bordered',
				'table table-bordered table-striped'             => 'uk-table uk-table-bordered uk-table-striped',
				'table table-striped table-bordered table-hover' => 'uk-table uk-table-striped uk-table-bordered uk-table-hover',
				'nav'                                            => 'uk-nav',
				'nav-pills'                                      => 'uk-navbar',
				'badge badge-info'                               => 'uk-badge',
			);
		}
	}

	/**
	 * Get the mapping of a given class
	 *
	 * @param $class The input class
	 *
	 * @return string The mapped class
	 */
	public function getClassMapping($class)
	{
		if (isset(self::$classMaps[$class]))
		{
			return self::$classMaps[$class];
		}

		// Handle icon class
		if (strpos($class, 'icon-') !== false)
		{
			if ($this->bootstrapVersion == 2)
			{
				return $class;
			}
			else
			{
				$icon = substr($class, 5);

				return "glyphicon glyphicon-$icon fa fa-$icon";
			}
		}

		// Not found, this class is for twitter bootstrap 3 only

		if ($this->bootstrapVersion == 3 || $this->bootstrapVersion == 4)
		{
			return $class;
		}
		else
		{
			return;
		}

	}
}
