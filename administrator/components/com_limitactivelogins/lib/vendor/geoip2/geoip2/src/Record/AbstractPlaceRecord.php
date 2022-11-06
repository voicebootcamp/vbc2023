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

abstract class AbstractPlaceRecord extends AbstractRecord
{
    private $locales;

    /**
     * @ignore
     *
     * @param mixed $record
     * @param mixed $locales
     */
    public function __construct($record, $locales = ['en'])
    {
        $this->locales = $locales;
        parent::__construct($record);
    }

    /**
     * @ignore
     *
     * @param mixed $attr
     */
    public function __get($attr)
    {
        if ($attr === 'name') {
            return $this->name();
        }

        return parent::__get($attr);
    }

    /**
     * @ignore
     *
     * @param mixed $attr
     */
    public function __isset($attr)
    {
        if ($attr === 'name') {
            return $this->firstSetNameLocale() === null ? false : true;
        }

        return parent::__isset($attr);
    }

    private function name()
    {
        $locale = $this->firstSetNameLocale();

        return $locale === null ? null : $this->names[$locale];
    }

    private function firstSetNameLocale()
    {
        foreach ($this->locales as $locale) {
            if (isset($this->names[$locale])) {
                return $locale;
            }
        }

        return null;
    }
}
