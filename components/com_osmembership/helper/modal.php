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
use Joomla\CMS\Uri\Uri;

abstract class OSMembershipHelperModal
{
	/**
	 * @param   string  $selector
	 * @param   string  $containerClass
	 * @param   string  $iframeHeight
	 */
	public static function iframeModal($selector = '', $containerClass = 'osm-modal-container', $iframeHeight = '480px')
	{
		static $scriptLoaded = false;

		static $loadedSelectors = [];

		if ($scriptLoaded === false)
		{
			$rootUri = Uri::root(true);
			Factory::getDocument()->addScript($rootUri . '/media/com_osmembership/assets/js/tingle/tingle.min.js')
				->addStyleSheet($rootUri . '/media/com_osmembership/assets/js/tingle/tingle.min.css');
			$scriptLoaded = true;
		}

		// Sometime, we just only want to load modal script
		if (empty($selector))
		{
			return;
		}

		if (isset($loadedSelectors[$selector]))
		{
			return;
		}

		$script = <<<SCRIPT
			document.addEventListener('DOMContentLoaded', function () {
		        [].slice.call(document.querySelectorAll('$selector')).forEach(function (link) {
		            link.addEventListener('click', function (e) {
		            	e.preventDefault();
		                var modal = new tingle.modal({
		                	cssClass: ['$containerClass'],
		                    onClose: function () {
		                        modal.destroy();
		                    }
		                });		                
		                modal.setContent('<iframe width="100%" height="$iframeHeight" src="' + link.href + '" frameborder="0" allowfullscreen></iframe>');
		                modal.open();
		            });
		        });
		    });
SCRIPT;

		Factory::getDocument()->addScriptDeclaration($script);

		$loadedSelectors[$selector] = true;
	}
}
