<?php
if (QuixAppHelper::checkQuixIsBuilderMode()) {
    return;
}

if (!defined('QX_ELEMENT_FORM')) {
    JFactory::getApplication()->allowCache(false);
    define('QX_ELEMENT_FORM', true);
    JHtml::_('script', 'system/core.js', false, true);

    $_SESSION['quix_form_captcha'] = [
        'first_number' => rand(1, 10),
        'second_number' => rand(1, 10)
    ];
}
