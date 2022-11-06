<?php

/**
 * @copyright	Copyright (C) 2014 Cedric KEIFLIN alias ced1870
 * http://www.joomlack.fr
 * @license		GNU/GPL
 * */
defined('JPATH_BASE') or die;
jimport('joomla.filesystem.file');
jimport('joomla.form.formfield');
JFormHelper::loadFieldClass('list');

class JFormFieldCkadsmanagercategory extends JFormFieldList {

    protected $type = 'ckadsmanagercategory';

    protected function getOptions() {
        // if the component is not installed
        if (!JFolder::exists(JPATH_ROOT . '/administrator/components/com_adsmanager')) {
            // add the root item
            $option = new stdClass();
            $option->text = JText::_('MOD_MAXIMENUCK_ADSMANAGER_NOTFOUND');
            $option->value = '0';
            $options[] = $option;
            // Merge any additional options in the XML definition.
            $options = array_merge(parent::getOptions(), $options);

            return $options;
        }

        // get the categories form the helper
        $params = new JRegistry();
        require_once(JPATH_SITE . '/components/com_adsmanager/lib/core.php');
		require_once (JPATH_ADMINISTRATOR . '/components/com_adsmanager/models/category.php');

		// get the model instance from the component
		$model = JModelLegacy::getInstance('Category', 'AdsmanagerModel');

		// get the list of items
		$cats = $model->getFlatTree();

        // add the root item
        $option = new stdClass();
        $option->text = JText::_('MOD_MAXIMENUCK_ADSMANAGER_ROOTNODE');
        $option->value = '0';
        $options[] = $option;
        foreach ($cats as $cat) {
            $option = new stdClass();
            $option->text = str_repeat(" - ", $cat->level + 1) . $cat->name;
            $option->value = $cat->id;
            $options[] = $option;
        }
        // Merge any additional options in the XML definition.
        $options = array_merge(parent::getOptions(), $options);

        return $options;
    }

}
