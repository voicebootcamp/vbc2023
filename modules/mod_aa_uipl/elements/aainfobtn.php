<?php
/*------------------------------------------------------------------------
# AA User IP and Location
# ------------------------------------------------------------------------
# author    AA Extensions https://aaextensions.com/
# Copyright (C) 2018 AA Extensions. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: https://aaextensions.com/
-------------------------------------------------------------------------*/
defined('_JEXEC') or die;


/**
 * Form Field class for AA Joomla! Extensions.
 * Adds a main website button with a link to the main website
 */
class JFormFieldAAInfoBtn extends JFormField
{
    protected $type = 'aainfobtn';

    protected function getLabel()
    {
        return '';
    }

    protected function getInput()
    {
        $aainfobtnLink = 'https://aaextensions.com/joomla-extensions/';

        $document = JFactory::getDocument();

        // Add styles
        $style = 'a.aa_info_button[target="_blank"]::before {'
            . 'content: ""!important;'
            . '}';
        $style .= 'a.aa_info_button {'
            . 'margin-left: 9px;'
            . '}';


        $version = new JVersion;

        if ($version->isCompatible('4.0')) {
            $document->addStyleDeclaration($style);
        }

        $aainfobtn = '<div class="btn-wrapper" id="toolbar-pro"><a href="' . $aainfobtnLink . '" title="AA Joomla Extensions" target="_blank" class="aa_info_button"><button class="btn btn-small btn-inverse"><span class="icon-cube icon-white" aria-hidden="true"></span> Other Joomla Extensions</button></a></div>';

        $document = JFactory::getDocument();
        $scriptDeclaration = 'jQuery(function($){$("#toolbar").append(\'' . $aainfobtn . '\');});';
        $document->addScriptDeclaration($scriptDeclaration);

        return '';
    }
}