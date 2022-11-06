jQuery(function ($) {
    $.fn.validationEngineLanguage = function () {
    };

    $.validationEngineLanguage = {
        newLang: function () {
            var rootUri = Joomla.getOptions('rootUri');
            $.validationEngineLanguage.allRules = {
                "required": { // Add your regex rules here, you can take telephone as an example
                    "regex": "none",
                    "alertText": Joomla.JText._('OSM_FIELD_REQUIRED', '* This field is required'),
                    "alertTextCheckboxMultiple": Joomla.JText._('OSM_PLEASE_SELECT_AN_OPTION', '* Please select an option'),
                    "alertTextCheckboxe": Joomla.JText._('OSM_CHECKBOX_REQUIRED', '* This checkbox is required'),
                    "alertTextDateRange": Joomla.JText._('OSM_BOTH_DATE_RANGE_FIELD_REQUIRED', '* Both date range fields are required')
                },
                "requiredInFunction": {
                    "func": function (field, rules, i, options) {
                        return (field.val() == "test") ? true : false;
                    },
                    "alertText": Joomla.JText._('OSM_FIELD_MUST_EQUAL_TEST', '* Field must equal test')
                },
                "dateRange": {
                    "regex": "none",
                    "alertText": Joomla.JText._('OSM_INVALID', '* Invalid '),
                    "alertText2": "Date Range"
                },
                "dateTimeRange": {
                    "regex": "none",
                    "alertText": Joomla.JText._('OSM_INVALID', '* Invalid '),
                    "alertText2": Joomla.JText._('OSM_DATE_TIME_RANGE', 'Date Time Range')
                },
                "minSize": {
                    "regex": "none",
                    "alertText": Joomla.JText._('OSM_MINIMUM', '* Minimum '),
                    "alertText2": Joomla.JText._('OSM_CHARACTERS_REQUIRED', ' characters required')
                },
                "maxSize": {
                    "regex": "none",
                    "alertText": Joomla.JText._('OSM_MAXIMUM', '* Maximum '),
                    "alertText2": Joomla.JText._('OSM_CHACTERS_ALLOWED', ' characters allowed')
                },
                "groupRequired": {
                    "regex": "none",
                    "alertText": Joomla.JText._('OSM_GROUP_REQUIRED','* You must fill one of the following fields')
                },
                "min": {
                    "regex": "none",
                    "alertText": Joomla.JText._('OSM_MIN', '* Minimum value is ')
                },
                "max": {
                    "regex": "none",
                    "alertText": Joomla.JText._('OSM_MAX', '* Maximum value is ')
                },
                "past": {
                    "regex": "none",
                    "alertText": Joomla.JText._('OSM_DATE_PRIOR_TO', '* Date prior to ')
                },
                "future": {
                    "regex": "none",
                    "alertText": Joomla.JText._('OSM_DATE_PAST', '* Date past ')
                },
                "maxCheckbox": {
                    "regex": "none",
                    "alertText": Joomla.JText._('OSM_MAXIMUM', '* Maximum '),
                    "alertText2": Joomla.JText._('OSM_OPTION_ALLOW', ' options allowed')
                },
                "minCheckbox": {
                    "regex": "none",
                    "alertText": Joomla.JText._('OSM_PLEASE_SELECT', '* Please select '),
                    "alertText2": " options"
                },
                "equals": {
                    "regex": "none",
                    "alertText": Joomla.JText._('OSM_FIELDS_DO_NOT_MATCH', '* Fields do not match')
                },
                "creditCard": {
                    "regex": "none",
                    "alertText": Joomla.JText._('OSM_INVALID_CREDIT_CARD_NUMBER', '* Invalid credit card number')
                },
                "phone": {
                    // credit: jquery.h5validate.js / orefalo
                    "regex": /^([\+][0-9]{1,3}[\ \.\-])?([\(]{1}[0-9]{2,6}[\)])?([0-9\ \.\-\/]{3,20})((x|ext|extension)[\ ]?[0-9]{1,4})?$/,
                    "alertText": Joomla.JText._('OSM_INVALID_PHONE_NUMBER', '* Invalid phone number')
                },
                "email": {
                    // HTML5 compatible email regex ( http://www.whatwg.org/specs/web-apps/current-work/multipage/states-of-the-type-attribute.html#    e-mail-state-%28type=email%29 )
                    "regex": /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/,
                    "alertText": Joomla.JText._('OSM_INVALID_EMAIL_ADDRESS', '* Invalid email address')
                },
                "integer": {
                    "regex": /^[\-\+]?\d+$/,
                    "alertText": Joomla.JText._('OSM_NOT_A_VALID_INTEGER', '* Not a valid integer')
                },
                "number": {
                    // Number, including positive, negative, and floating decimal. credit: orefalo
                    "regex": /^[\-\+]?((([0-9]{1,3})([,][0-9]{3})*)|([0-9]+))?([\.]([0-9]+))?$/,
                    "alertText": Joomla.JText._('OSM_INVALID_FLOATING_DECIMAL_NUMBER', '* Invalid floating decimal number')
                },
                "date": {
                    //	Check if date is valid by leap year
                    "func": function (field) {
                        var match = pattern.exec(field.val());
                        if (match == null)
                            return false;

                        var year = match[yearPartIndex + 1];
                        var month = match[monthPartIndex + 1] * 1;
                        var day = match[dayPartIndex + 1] * 1;
                        var date = new Date(year, month - 1, day); // because months starts from 0.

                        return (date.getFullYear() == year && date.getMonth() == (month - 1) && date.getDate() == day);
                    },
                    "alertText": Joomla.JText._('OSM_INVALID_DATE').replace('YYYY-MM-DD', Joomla.getOptions('humanFormat'))
                },
                "ipv4": {
                    "regex": /^((([01]?[0-9]{1,2})|(2[0-4][0-9])|(25[0-5]))[.]){3}(([0-1]?[0-9]{1,2})|(2[0-4][0-9])|(25[0-5]))$/,
                    "alertText": Joomla.JText._('OSM_INVALID_IP_ADDRESS', '* Invalid IP address')
                },
                "url": {
                    "regex": /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/,
                    "alertText": Joomla.JText._('OSM_INVALID_URL', '* Invalid URL')
                },
                "onlyNumberSp": {
                    "regex": /^[0-9\ ]+$/,
                    "alertText": Joomla.JText._('OSM_NUMBER_ONLY', '* Numbers only')
                },
                "onlyLetterSp": {
                    "regex": /^[a-zA-Z\ \']+$/,
                    "alertText": Joomla.JText._('OSM_LETTERS_ONLY', '* Letters only')
                },
                "onlyLetterNumber": {
                    "regex": /^[0-9a-zA-Z]+$/,
                    "alertText": Joomla.JText._('OSM_NO_SPECIAL_CHACTERS_ALLOWED', '* No special characters allowed')
                },
                // --- CUSTOM RULES -- Those are specific to the demos, they can be removed or changed to your likings
                "ajaxUserCall": {
                    "url": rootUri + '/index.php?option=com_osmembership&task=validator.validate_username',
                    "alertText": Joomla.JText._('OSM_INVALID_USERNAME', 'This username has been used by a different user. Please enter a new username')
                },
                "ajaxEmailCall": {
                    "url": rootUri + '/index.php?option=com_osmembership&task=validator.validate_email',
                    // you may want to pass extra data on the ajax call
                    "alertText": Joomla.JText._('OSM_INVALID_EMAIL', 'This email has been used by a different user. Please enter a new email')
                },
                "ajaxValidateGroupMemberEmail": {
                    "url": rootUri + '/index.php?option=com_osmembership&task=validator.validate_group_member_email',
                    "alertText": Joomla.JText._('OSM_INVALID_EMAIL', 'This email has been used by a different user. Please enter a new email')
                },
                "ajaxValidatePassword": {
                    "url": rootUri + '/index.php?option=com_osmembership&task=validator.validate_password',
                    "alertText": Joomla.JText._('OSM_INVALID_PASSWORD', 'The password you entered is invalid'),
                },
                //tls warning:homegrown not fielded
                "dateFormat": {
                    "regex": /^\d{4}[\/\-](0?[1-9]|1[012])[\/\-](0?[1-9]|[12][0-9]|3[01])$|^(?:(?:(?:0?[13578]|1[02])(\/|-)31)|(?:(?:0?[1,3-9]|1[0-2])(\/|-)(?:29|30)))(\/|-)(?:[1-9]\d\d\d|\d[1-9]\d\d|\d\d[1-9]\d|\d\d\d[1-9])$|^(?:(?:0?[1-9]|1[0-2])(\/|-)(?:0?[1-9]|1\d|2[0-8]))(\/|-)(?:[1-9]\d\d\d|\d[1-9]\d\d|\d\d[1-9]\d|\d\d\d[1-9])$|^(0?2(\/|-)29)(\/|-)(?:(?:0[48]00|[13579][26]00|[2468][048]00)|(?:\d\d)?(?:0[48]|[2468][048]|[13579][26]))$/,
                    "alertText": Joomla.JText._('OSM_INVALID_DATE', '* Invalid date, must be in YYYY-MM-DD format')
                },
                //tls warning:homegrown not fielded
                "dateTimeFormat": {
                    "regex": /^\d{4}[\/\-](0?[1-9]|1[012])[\/\-](0?[1-9]|[12][0-9]|3[01])\s+(1[012]|0?[1-9]){1}:(0?[1-5]|[0-6][0-9]){1}:(0?[0-6]|[0-6][0-9]){1}\s+(am|pm|AM|PM){1}$|^(?:(?:(?:0?[13578]|1[02])(\/|-)31)|(?:(?:0?[1,3-9]|1[0-2])(\/|-)(?:29|30)))(\/|-)(?:[1-9]\d\d\d|\d[1-9]\d\d|\d\d[1-9]\d|\d\d\d[1-9])$|^((1[012]|0?[1-9]){1}\/(0?[1-9]|[12][0-9]|3[01]){1}\/\d{2,4}\s+(1[012]|0?[1-9]){1}:(0?[1-5]|[0-6][0-9]){1}:(0?[0-6]|[0-6][0-9]){1}\s+(am|pm|AM|PM){1})$/,
                    "alertText": "* Invalid Date or Date Format",
                    "alertText2": Joomla.JText._('OSM_EXPECTED_FORMAT'),
                    "alertText3": "mm/dd/yyyy hh:mm:ss AM|PM or ",
                    "alertText4": "yyyy-mm-dd hh:mm:ss AM|PM"
                }
            };

        }
    };
    $.validationEngineLanguage.newLang();
});