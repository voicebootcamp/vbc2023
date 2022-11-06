/**
 * JAMP MOBILE DETECTION frontend class based on device width
 * 
 * @package JAMP::plugins::system
 * @subpackage js
 * @author Joomla! Extensions Store
 * @copyright (C)2020 Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 */
(function($) {
	var MobileDetection = function() {
		// Check if a session storage width has been previously set
		var sessionWidth = window.sessionStorage.getItem('jamp_device_width');
		var activeRedirect = false;
		var injectLoader = function() {
			$('body').append('<div id="jamp_loader"></div>');
		}
		
		// Perform operation if the session width is not set, AKA first request, or if the session width is different than the current width
		switch(jamp_redirect_width_value) {
			case 'outerWidth':
				var windowWidth = window.outerWidth;
				break;
			
			case 'innerWidth':
				var windowWidth = window.innerWidth;
				break;
				
			case 'jQueryWidth':
				var windowWidth = jQuery(window).width();
				break;
		}
		
		// Debug width
		console.log(windowWidth);
		
		if(!sessionWidth || sessionWidth != windowWidth) {
			// Always store the current device width in the session storage
			window.sessionStorage.setItem('jamp_device_width', windowWidth);
			
			if(windowWidth <= jamp_redirect_devicewidth) {
				// Check if this is a page with a real reL=amphtml page to redirect to
				var relAmpHtml = $('link[rel=amphtml]').attr('href');
				
				if(relAmpHtml) {
					// Flag the redirect phase
					activeRedirect = true;
					
					// Check if the loader effect must be injected
					if(jamp_redirect_loader_effect) {
						injectLoader();
					}
					
					// Perform the redirect
					window.location.href = relAmpHtml;
				}
			}
		} 

		// If there is not the need of a reload, for example the width changed but not trepassing the threshold, make the website immediately visible
		if(jamp_redirect_hide_website && !activeRedirect) {
			document.querySelector('html').style.visibility = 'visible';
		}
	}
	
  // On DOM Ready
  $(function () {
	  setTimeout(function() {
		  window.JAmpMobileDetection = new MobileDetection();
	  }, jamp_redirect_session_delay);
  });
})(jQuery);