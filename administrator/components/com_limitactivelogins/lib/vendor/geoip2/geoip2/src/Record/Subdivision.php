<?php
/* ======================================================
 # Limit Active Logins for Joomla! - v1.1.2 (pro version)
 # -------------------------------------------------------
 # For Joomla! CMS (v4.x)
 # Author: Web357 (Yiannis Christodoulou)
 # Copyright (©) 2014-2022 Web357. All rights reserved.
 # License: GNU/GPLv3, http://www.gnu.org/licenses/gpl-3.0.html
 # Website: https:/www.web357.com
 # Demo: https://limitactivelogins.web357.com/
 # Support: support@web357.com
 # Last modified: Thursday 31 March 2022, 12:05:22 PM
 ========================================================= */

namespace GeoIp2\Record;

/**
 * Contains data for the subdivisions associated with an IP address.
 *
 * This record is returned by all location databases and services besides
 * Country.
 *
 * @property-read int|null $confidence This is a value from 0-100 indicating
 * MaxMind's confidence that the subdivision is correct. This attribute is
 * only available from the Insights service and the GeoIP2 Enterprise
 * database.
 * @property-read int|null $geonameId This is a GeoName ID for the subdivision.
 * This attribute is returned by all location databases and services besides
 * Country.
 * @property-read string|null $isoCode This is a string up to three characters long
 * contain the subdivision portion of the
 * {@link * http://en.wikipedia.org/wiki/ISO_3166-2 ISO 3166-2 code}. This attribute
 * is returned by all location databases and services except Country.
 * @property-read string|null $name The name of the subdivision based on the
 * locales list passed to the constructor. This attribute is returned by all
 * location databases and services besides Country.
 * @property-read array|null $names An array map where the keys are locale codes
 * and the values are names. This attribute is returned by all location
 * databases and services besides Country.
 */
class Subdivision extends AbstractPlaceRecord
{
    /**
     * @ignore
     */
    protected $validAttributes = [
        'confidence',
        'geonameId',
        'isoCode',
        'names',
    ];
}
