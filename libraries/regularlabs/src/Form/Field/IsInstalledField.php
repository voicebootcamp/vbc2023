<?php
/**
 * @package         Regular Labs Library
 * @version         22.10.1331
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            http://regularlabs.com
 * @copyright       Copyright © 2022 Regular Labs All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

namespace RegularLabs\Library\Form\Field;

defined('_JEXEC') or die;

use RegularLabs\Library\Extension as RL_Extension;
use RegularLabs\Library\Form\FormField as RL_FormField;

class IsInstalledField extends RL_FormField
{
    protected $layout = 'joomla.form.field.hidden';

    protected function getLabel()
    {
        $this->value = (int) RL_Extension::isInstalled($this->get('extension'), $this->get('extension_type', 'component'), $this->get('folder', 'system'));

        return $this->getControlGroupEnd()
            . rtrim($this->getRenderer($this->layout)->render($this->getLayoutData()), PHP_EOL)
            . $this->getControlGroupStart();
    }
}
