<?php
/**
 * @package		   Joomla
 * @subpackage	   Membership Pro
 * @author		   Tuan Pham Ngoc
 * @copyright	   Copyright (C) 2012 - 2022 Ossolution Team
 * @license		   GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die ;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarHelper;

if (OSMembershipHelper::isJoomla4())
{
	$tabApiPrefix = 'uitab.';
}
else
{
	$tabApiPrefix = 'bootstrap.';
}

ToolbarHelper::title(Text::_('OSM_SUBSCRIBER_EDIT'), 'generic.png');
ToolbarHelper::save('save');
ToolbarHelper::cancel('cancel');

if (Factory::getUser()->authorise('core.admin', 'com_osmembership'))
{
	ToolbarHelper::preferences('com_osmembership');
}

HTMLHelper::_('behavior.core');
?>
<form action="index.php?option=com_osmembership&view=subscriber" method="post" name="adminForm" id="adminForm" autocomplete="off" enctype="multipart/form-data" class="form form-horizontal">
	<?php
		echo HTMLHelper::_($tabApiPrefix . 'startTabSet', 'osm-profile', array('active' => 'profile-page'));
		echo HTMLHelper::_($tabApiPrefix . 'addTab', 'osm-profile', 'profile-page', Text::_('OSM_PROFILE_INFORMATION', true));
		echo $this->loadTemplate('profile');
		echo HTMLHelper::_($tabApiPrefix . 'endTab');
		echo HTMLHelper::_($tabApiPrefix . 'addTab', 'osm-profile', 'subscription-history-page', Text::_('OSM_SUBSCRIPTION_HISTORY', true));
		echo $this->loadTemplate('subscriptions_history');
		echo HTMLHelper::_($tabApiPrefix . 'endTab');

		if (count($this->plugins))
		{
			$count = 0 ;

			foreach ($this->plugins as $plugin)
			{
				$count++ ;

				if (empty($plugin['form']))
				{
					continue;
				}

				echo HTMLHelper::_($tabApiPrefix . 'addTab', 'osm-profile', 'tab_' . $count, Text::_($plugin['title'], true));
				echo $plugin['form'];
				echo HTMLHelper::_($tabApiPrefix . 'endTab');
			}
		}

		echo HTMLHelper::_($tabApiPrefix . 'endTabSet');
	?>
    <input type="hidden" name="id" value="<?php echo (int) $this->item->id; ?>"/>
	<input type="hidden" name="task" value="" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>