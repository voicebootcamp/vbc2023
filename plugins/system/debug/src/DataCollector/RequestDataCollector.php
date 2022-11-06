<?php

/**
 * @package     Joomla.Plugin
 * @subpackage  System.Debug
 *
 * @copyright   (C) 2018 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Plugin\System\Debug\DataCollector;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Collects info about the request content while redacting potentially secret contents
 *
 * @since  4.2.4
 */
class RequestDataCollector extends \DebugBar\DataCollector\RequestDataCollector
{
    const PROTECTED_KEYS = "/password|passwd|pwd|secret|token/i";

    /**
     * Called by the DebugBar when data needs to be collected
     *
     * @since  4.2.4
     *
     * @return array
     */
    public function collect()
    {
        $vars = array('_GET', '_POST', '_SESSION', '_COOKIE', '_SERVER');
        $returnData = array();

        foreach ($vars as $var) {
            if (isset($GLOBALS[$var])) {
                $key = "$" . $var;

                $data = $GLOBALS[$var];

                array_walk_recursive($data, static function(&$value, $key) {
                    if (!preg_match(self::PROTECTED_KEYS, $key)) {
                        return;
                    }

                    $value = '***redacted***';
                });

                if ($this->isHtmlVarDumperUsed()) {
                    $returnData[$key] = $this->getVarDumper()->renderVar($data);
                } else {
                    $returnData[$key] = $this->getDataFormatter()->formatVar($data);
                }
            }
        }

        return $returnData;
    }
}
