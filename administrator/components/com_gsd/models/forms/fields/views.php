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

use GSD\Helper;
use GSD\Apps;

JFormHelper::loadFieldClass('list');

class JFormFieldViews extends JFormFieldList
{
    private $selectedApp;

	/**
	 * Method to get a control group with label and input.
	 *
	 * @param   array  $options  Options to be passed into the rendering of the field
	 *
	 * @return  string  A string containing the html for the control group
	 *
	 * @since   3.2
	 */
	public function renderField($options = array())
	{
        if (!$selectedApp = $this->form->getData()->get('plugin'))
        {
            return;
        }

        $app = Apps::getApp($selectedApp);
        $supportedViews = $app->advertiseSupportedViews();

        // In case the App supports only 1 view, improve UX by pre-populating the field with the supported view and make the field read-only.
        if (count($supportedViews) == 1)
        {
            $this->value = array_keys($supportedViews)[0];
            $this->readonly = true;
        }

        // Sort alphabetically
        asort($supportedViews);

        $this->supportedViews = $supportedViews;

        return parent::renderField();
    }

    /**
     * Method to get a list of options for a list input.
     *
     * @return      array           An array of JHtml options.
     */
    protected function getOptions()
    {
        $options[] = JHTML::_('select.option', '', '- ' . JText::_('GSD_APP_VIEW_SELECT') . ' -');
        
        foreach ($this->supportedViews as $value => $label)
        {
            $options[] = JHTML::_('select.option', $value, $label);
        }

        return array_merge(parent::getOptions(), $options);
    }
}