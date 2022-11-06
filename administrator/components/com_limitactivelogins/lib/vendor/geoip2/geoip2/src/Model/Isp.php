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

namespace GeoIp2\Model;

/**
 * This class provides the GeoIP2 ISP model.
 *
 * @property-read int|null $autonomousSystemNumber The autonomous system number
 *     associated with the IP address.
 * @property-read string|null $autonomousSystemOrganization The organization
 *     associated with the registered autonomous system number for the IP
 *     address.
 * @property-read string|null $isp The name of the ISP associated with the IP
 *     address.
 * @property-read string|null $organization The name of the organization associated
 *     with the IP address.
 * @property-read string $ipAddress The IP address that the data in the model is
 *     for.
 */
class Isp extends AbstractModel
{
    protected $autonomousSystemNumber;
    protected $autonomousSystemOrganization;
    protected $isp;
    protected $organization;
    protected $ipAddress;

    /**
     * @ignore
     *
     * @param mixed $raw
     */
    public function __construct($raw)
    {
        parent::__construct($raw);
        $this->autonomousSystemNumber = $this->get('autonomous_system_number');
        $this->autonomousSystemOrganization =
            $this->get('autonomous_system_organization');
        $this->isp = $this->get('isp');
        $this->organization = $this->get('organization');

        $this->ipAddress = $this->get('ip_address');
    }
}
