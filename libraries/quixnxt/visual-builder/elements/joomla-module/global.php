<?php
if (!function_exists('getJoomlaModulesList')) {
    function getJoomlaModulesList()
    {
        JModelLegacy::addIncludePath(JPATH_SITE . '/administrator/components/com_modules/models', 'ModulesModel');

      // Get an instance of the generic articles model
        $model = JModelLegacy::getInstance('Modules', 'ModulesModel', [ 'ignore_request' => true ]);

      // Set the filters based on the module params
        $model->setState('list.start', 0);
        $model->setState('list.limit', 9999);
    
      // Access filter
      // $access = ! JComponentHelper::getParams( 'com_modules' )->get( 'show_noauth' );
      // $model->setState( 'filter.access', $access );
        $model->setState('filter.state', 1);

      // Set ordering
        $model->setState('list.ordering', 'a.ordering');

        $model->setState('list.direction', 'ASC');

      // Retrieve Content
        $items = $model->getItems();
    
        return $items;
    }
}
