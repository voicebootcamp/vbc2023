<?php

/**
 * @copyright	Copyright (C) 2011 Cedric KEIFLIN alias ced1870
 * http://www.joomlack.fr
 * Module Maximenu CK
 * @license		GNU/GPL
 * */
defined('JPATH_BASE') or die;
jimport('joomla.filesystem.file');
jimport('joomla.form.formfield');
JFormHelper::loadFieldClass('list');

class JFormFieldCkk2category extends JFormFieldList {

    protected $type = 'ckk2category';

    protected function getOptions() {
        // if the component is not installed
        if (!JFolder::exists(JPATH_ROOT . '/administrator/components/com_k2')) {
            // add the root item
            $option = new stdClass();
            $option->text = JText::_('MOD_MAXIMENUCK_K2_NOTFOUND');
            $option->value = '0';
            $options[] = $option;
            // Merge any additional options in the XML definition.
            $options = array_merge(parent::getOptions(), $options);

            return $options;
        }

        // get the categories form the helper
        $params = new JRegistry();
        require_once JPATH_ROOT . '/plugins/maximenuck/k2/helper/helper_k2.php';
        $cats = MaximenuckHelpersourceK2::getItems($params);

        // add the root item
        $option = new stdClass();
        $option->text = JText::_('MOD_MAXIMENUCK_K2_ROOTNODE');
        $option->value = '0';
        $options[] = $option;
        foreach ($cats as $cat) {
            $option = new stdClass();
            $option->text = str_repeat(" - ", $cat->level - 1) . $cat->name;
            $option->value = $cat->id;
            $options[] = $option;
        }
        // Merge any additional options in the XML definition.
        $options = array_merge(parent::getOptions(), $options);

        return $options;
    }

}
