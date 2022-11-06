/**
 * @package   AkeebaLoginGuard
 * @copyright Copyright (c)2016-2022 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

var loginguardSmsapiTelinput;

function loginguardSmsapiInitialise()
{
	var input = document.querySelector("#loginGuardSMSAPIPhone");

	loginguardSmsapiTelinput = window.intlTelInput(input, {
		allowDropdown:         true,
		nationalMode:          false,
		separateDialCode:      true,
		initialCountry:        Joomla.getOptions('loginguard.smsapi.country', 'auto'),
		geoIpLookup:           function (success, failure) {
			$.get("https://ipinfo.io", function () {
			}, "jsonp").always(function (resp) {
				var countryCode = (resp && resp.country) ? resp.country : "us";
				success(countryCode);
			});
		},
		placeholderNumberType: "MOBILE",
		utilsScript: Joomla.getOptions('loginguard.smsapi.utilsScript', '')
	});

	document.getElementById('loginguardSmsapiSendCode').addEventListener('click', function (e) {
		e.preventDefault();

		var phone       = loginguardSmsapiTelinput.getNumber();
		window.location = Joomla.getOptions('loginguard.smsapi.actionUrl', '')
			+ '&phone=' + encodeURIComponent(phone);

		return false;
	});
}

loginguardSmsapiInitialise();