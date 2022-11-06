<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2022 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

if (OSMembershipHelper::isJoomla4())
{
	$tabApiPrefix = 'uitab.';
}
else
{
	$tabApiPrefix = 'bootstrap.';
}

$rootUri = Uri::root(true);
echo HTMLHelper::_($tabApiPrefix . 'startTabSet', 'invoice-translation', array('active' => 'invoice-translation-' . $this->languages[0]->sef));

foreach ($this->languages as $language)
{
	$sef = $language->sef;
	echo HTMLHelper::_($tabApiPrefix . 'addTab', 'invoice-translation', 'invoice-translation-' . $sef, $language->title . ' <img src="' . $rootUri . '/media/mod_languages/images/' . $language->image . '.gif" />');
	?>
		<div class="control-group">
			<div class="control-label">
				<?php echo OSMembershipHelperHtml::getFieldLabel('invoice_format_' . $sef, Text::_('OSM_INVOICE_FORMAT'), Text::_('OSM_INVOICE_FORMAT_EXPLAIN')); ?>
			</div>
			<div class="controls">
				<?php echo $editor->display('invoice_format_' . $sef, $config->{'invoice_format_' . $sef}, '100%', '550', '75', '8');?>
			</div>
		</div>
	<?php
	echo HTMLHelper::_($tabApiPrefix . 'endTab');
}

echo HTMLHelper::_($tabApiPrefix . 'endTabSet');
