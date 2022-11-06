<?php
/**
 * @package    	SeoSiteAttributes
 * @author    	ThemeXpert http://www.themexpert.com
 * @copyright  	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license  	GNU General Public License version 3 or later; see LICENSE.txt
 * @since    	1.0.0
 */
defined('_JEXEC') or die;

class PlgSystemSeositeattributes extends JPlugin
{
	/**
	 * Load the language file on instantiation. Note this is only available in Joomla 3.1 and higher.
	 * If you want to support 3.0 series you must override the constructor
	 *
	 * @var    boolean
	 * @since  3.1
	 */
	protected $autoloadLanguage = true;

	/**
	 * onBeforeRender
	 *
	 * @return bollian
	 */
	public function onBeforeRender()
	{
		// add for admin only menu options
		if (JFactory::getApplication()->isClient('administrator'))
		{
			$input = JFactory::getApplication()->input;
			$option = $input->get('option', '');
			$view = $input->get('view', '');
			if('com_quix' == $option && ('pages' == $view or 'collections' == $view or 'integrations' == $view or 'dashboard' == $view or 'elements' == $view or 'filemanager' == $view or 'help' == $view)){
				$pluginid = $this->getPluginId('seositeattributes','system','plugin');
				$link = JRoute::_("index.php?option=com_plugins&client_id=0&task=plugin.edit&extension_id=".$pluginid);

				$toolbar = JToolBar::getInstance('toolbar');
				$toolbar->appendButton('Custom', "<a href='".$link ."' target='_blank' class='btn hasPopover' data-title='".JText::_('PLG_SYSTEM_SEOSITEATTRIBUTES')."' data-content='".JText::_('PLG_SYSTEM_SEOSITEATTRIBUTES_INTRO')."' data-placement='bottom'>".JText::_('PLG_SYSTEM_SEOSITEATTRIBUTES_TITLE')."</a>");
			}
			return;
		}

		// Use only for front-end site
		$type = $this->params->get('type', 'Organization');
		$customtype = $this->params->get('customtype');
		$name = $this->params->get('name', JFactory::getConfig()->get('sitename'));
		$url = $this->params->get('url', JUri::root());

		$sameAsFacebook = $this->params->get('sameAsFacebook');
		$sameAsTwitter = $this->params->get('sameAsTwitter');
		$sameAsGPlus = $this->params->get('sameAsGPlus');
		$sameAsInstagram = $this->params->get('sameAsInstagram');
		$sameAsYoutube = $this->params->get('sameAsYoutube');
		$sameAsLinkedIn = $this->params->get('sameAsLinkedIn');
		$sameAsMyspace = $this->params->get('sameAsMyspace');

		$sameAsPinterest = $this->params->get('sameAsPinterest');
		$sameAsSoundCloud = $this->params->get('sameAsSoundCloud');
		$sameAsTumblr = $this->params->get('sameAsTumblr');

		$logo = $this->params->get('logo');

		$telephone = $this->params->get('telephone');
		$contactType = $this->params->get('contactType', 'customer support');
		$areaServed = $this->params->get('areaServed');
		$contactOption = $this->params->get('contactOption');
		$availableLanguage = $this->params->get('availableLanguage');

		$streetAddress = $this->params->get('streetAddress');
		$addressLocality = $this->params->get('addressLocality');
		$addressRegion = $this->params->get('addressRegion');
		$postalCode = $this->params->get('postalCode');
		$addressCountry = $this->params->get('addressCountry');
		$latitude = $this->params->get('latitude');
		$longitude = $this->params->get('longitude');

		$structured_markup = array();
		$sameAs = array();

		$structured_markup['@context'] = 'http://schema.org';

		if (empty($customtype))
		{
			$structured_markup['@type'] = $type;
		}
		else
		{
			$structured_markup['@type'] = $customtype;
		}


		$structured_markup['name'] = $name;
		$structured_markup['url'] = $url;

		if (!empty($logo))
		{
			$logo_url = JUri::root() . $logo;

			if (!empty($logo_url))
			{
				$structured_markup['logo'] = $logo_url;
			}
		}

		if ($sameAsFacebook)
		{
			$sameAs[] = $sameAsFacebook;
		}

		if ($sameAsTwitter)
		{
			$sameAs[] = $sameAsTwitter;
		}

		if ($sameAsGPlus)
		{
			$sameAs[] = $sameAsGPlus;
		}

		if ($sameAsInstagram)
		{
			$sameAs[] = $sameAsInstagram;
		}

		if ($sameAsYoutube)
		{
			$sameAs[] = $sameAsYoutube;
		}

		if ($sameAsLinkedIn)
		{
			$sameAs[] = $sameAsLinkedIn;
		}

		if ($sameAsMyspace)
		{
			$sameAs[] = $sameAsMyspace;
		}

		if ($sameAsPinterest)
		{
			$sameAs[] = $sameAsPinterest;
		}

		if ($sameAsSoundCloud)
		{
			$sameAs[] = $sameAsSoundCloud;
		}

		if ($sameAsTumblr)
		{
			$sameAs[] = $sameAsTumblr;
		}

		if (!empty($sameAs))
		{
			$structured_markup['sameAs'] = $sameAs;
		}

		if (!empty($telephone))
		{
			$contactPoint = array(
				'@type' => "ContactPoint",
				'telephone' => $telephone
			);

			if (!empty($contactType))
			{
				$contactPoint['contactType'] = $contactType;
			}

			if (!empty($areaServed))
			{
				$areaServed = explode(",", $areaServed);
				$contactPoint['areaServed'] = $areaServed;
			}

			if (!empty($contactOption))
			{
				$contactPoint['contactOption'] = $contactOption;
			}

			if (!empty($availableLanguage))
			{
				$availableLanguage = explode(",", $availableLanguage);
				$contactPoint['availableLanguage'] = $availableLanguage;
			}

			$structured_markup['contactPoint'] = $contactPoint;
		}


		if ( (!empty($streetAddress))
			&& (!empty($addressLocality))
			&& (!empty($addressRegion))
			&& (!empty($postalCode))
			&& (!empty($addressCountry)) )
		{
			$address = array(
				'@type' => 'PostalAddress',
				'streetAddress' => $streetAddress,
				'addressLocality' => $addressLocality,
				'addressRegion' => $addressRegion,
				'postalCode' => $postalCode,
				'addressCountry' => $addressCountry
			);

			$structured_markup['address'] = $address;
		}

		if ( (!empty($latitude))
			&& (!empty($longitude)) )
		{
			$geo = array(
				'@type' => 'GeoCoordinates',
				'latitude' => $latitude,
				'longitude' => $longitude
			);

			$structured_markup['geo'] = $geo;
		}

		JFactory::getDocument()->addScriptDeclaration(json_encode($structured_markup), 'application/ld+json');

		return;
	}

	/*
	* method getPluginId
	* used to get plugin for use
	* @element : joomla plugin element name
	* @folder : joomla plugin folder name
	* @type : joomla plugin type
	* @return extension_id
	*/
	function getPluginId($element,$folder, $type)
	{
		static $pluginIDSSA;

		// Function has already run
	    if ( $pluginIDSSA !== null ) return $pluginIDSSA;

	    $pluginIDSSA = false;

	    $db = JFactory::getDBO();
	    $query = $db->getQuery(true);
	    $query
	        ->select($db->quoteName('a.extension_id'))
	        ->from($db->quoteName('#__extensions', 'a'))
	        ->where($db->quoteName('a.element').' = '.$db->quote($element))
	        ->where($db->quoteName('a.folder').' = '.$db->quote($folder))
	        ->where($db->quoteName('a.type').' = '.$db->quote($type));
	    $db->setQuery($query);
	    $db->execute();
	    if($db->getNumRows()){
	        $pluginIDSSA = $db->loadResult();
	    }

	    return $pluginIDSSA;
	}
}
