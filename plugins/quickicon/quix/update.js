/**
 * @copyright	Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

 var ajax_quix_structure = {};


jQuery(document).ready(function() {
	var ajax_quix_structure = {
		success: function(data, textStatus, jqXHR) {
			var link = jQuery('#plg_quickicon_quix').find('span.j-links-link');

			try {
				var updateInfoList = jQuery.parseJSON(data);
			} catch (e) {
				// An error occurred
				link.html(plg_quickicon_quix_text.ERROR);
			}

			if (updateInfoList) {
				// No updates
				link.html(plg_quickicon_quix_text.UPTODATE);
			} else {
				// An error occurred
				link.html(plg_quickicon_quix_text.ERROR);
			}
		},
		error: function(jqXHR, textStatus, errorThrown) {
			// An error occurred
			jQuery('#plg_quickicon_quix').find('span.j-links-link').html(plg_quickicon_quix_text.ERROR);
		},
		url: plg_quickicon_quix_ajax_url
	};

	ajax_object = new jQuery.ajax(ajax_quix_structure);
});
