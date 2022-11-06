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
 * This class provides the GeoIP2 Connection-Type model.
 *
 * @property-read string|null $connectionType The connection type may take the
 *     following values: "Dialup", "Cable/DSL", "Corporate", "Cellular".
 *     Additional values may be added in the future.
 * @property-read string $ipAddress The IP address that the data in the model is
 *     for.
 */
class ConnectionType extends AbstractModel
{
    protected $connectionType;
    protected $ipAddress;

    /**
     * @ignore
     *
     * @param mixed $raw
     */
    public function __construct($raw)
    {
        parent::__construct($raw);

        $this->connectionType = $this->get('connection_type');
        $this->ipAddress = $this->get('ip_address');
    }
}
