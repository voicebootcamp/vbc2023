<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2022 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Form\Form;

$form                = Form::getInstance('upgrade_options', JPATH_ADMINISTRATOR . '/components/com_osmembership/view/plan/forms/upgrade_options.xml');
$formData['upgrade_options'] = [];

foreach ($this->upgradeRules as $upgradeOption)
{
	$formData['upgrade_options'][] = [
		'id'         => $upgradeOption->id,
		'to_plan_id' => $upgradeOption->to_plan_id,
		'price'      => $upgradeOption->price,
		'upgrade_prorated' => $upgradeOption->upgrade_prorated,
		'published'  => $upgradeOption->published,
	];
}

$form->bind($formData);

foreach ($form->getFieldset() as $field)
{
	echo $field->input;
}
