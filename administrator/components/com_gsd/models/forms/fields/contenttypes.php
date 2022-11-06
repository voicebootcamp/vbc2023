<?php

/**
 * @package         Google Structured Data
 * @version         5.1.6 Pro
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die('Restricted access');

require_once JPATH_PLUGINS . '/system/nrframework/helpers/fieldlist.php';

class JFormFieldContentTypes extends NRFormFieldList
{
    /**
     * Method to get a list of options for a list input.
     *
     * @return      array           An array of JHtml options.
     */
    protected function getOptions()
    {
        $contentTypes = GSD\Helper::getContentTypes();

        if ($this->get("showselect", 'false') === 'true')
        {
            $options[] = JHTML::_('select.option', '', '- ' . JText::_('GSD_CONTENT_TYPE_SELECT') . ' -');
        }

        foreach ($contentTypes as $contentType)
        {
            $options[] = JHTML::_('select.option', $contentType, JText::_('GSD_' . strtoupper($contentType)));
        }

        return array_merge(parent::getOptions(), $options);
    }

    protected function getInput()
    {
        if (defined('nrJ4'))
        {
            $this->class .= ' d-i-b width-auto';
        }
        
        if (!$this->get('showhelp', false))
        {
            return parent::getInput();
        }

        $this->doc->addScriptDeclaration('
            document.addEventListener("DOMContentLoaded", function() {
                const select = document.querySelector("#' . $this->id . '");

                select.addEventListener("change", function(e) {
                    gsd_set_help_value(e.target.value);
                });
                
                // set initialvalue to help URL
                gsd_set_help_value(select.value);

                function gsd_set_help_value(content_type) {
                    href = "https://www.tassos.gr/joomla-extensions/google-structured-data-markup/docs/" + content_type.replace("_", "") + "-schema";
                    document.querySelector(".contentTypeHelp").href = href;
                }
            });
        ');

        return '
            <div class="d-flex gap-1"> ' . parent::getInput() . '
                <a class="btn btn-secondary contentTypeHelp" target="_blank" title="' . JText::_('GSD_CONTENTTYPE_HELP') . '">
                    <span class="icon-help" style="margin-right:0;"></span>
                </a>
            </div>
        ';
    }
}