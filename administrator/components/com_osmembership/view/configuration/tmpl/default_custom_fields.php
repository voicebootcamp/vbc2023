<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2022 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die;

use Joomla\CMS\Editor\Editor;

$customFields = file_get_contents(JPATH_ROOT . '/components/com_osmembership/fields.xml');

if ($editorPlugin && !OSMembershipHelper::isJoomla4())
{
	echo Editor::getInstance($editorPlugin)->display('custom_fields', $customFields, '100%', '550', '75', '8', false, null, null, null, array('syntax' => 'xml'));
}
else
{
	if (OSMembershipHelper::isJoomla4())
	{
		$cssClass = 'form-control';
	}
	else
	{
		$cssClass = 'input-xxlarge';
	}
	?>
		<textarea name="custom_fields" rows="20" class="<?php echo $cssClass; ?>" style="width: 100%;"><?php echo $customFields; ?></textarea>
	<?php
}

