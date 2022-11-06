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
 * Contains data about your account.
 *
 * This record is returned by all location services and databases.
 *
 * @property-read int|null $queriesRemaining The number of remaining queries you
 * have for the service you are calling.
 */
class MaxMind extends AbstractRecord
{
    /**
     * @ignore
     */
    protected $validAttributes = ['queriesRemaining'];
}
