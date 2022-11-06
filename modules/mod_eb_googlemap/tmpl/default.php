<?php
/**
 * @package        Joomla
 * @subpackage     Event Booking
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2010 - 2022 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

if (EventbookingHelper::isValidMessage($params->get('pre_text')))
{
	echo $params->get('pre_text');
}
?>
<div id="map<?php echo $module->id;?>" style="position:relative; width: <?php echo $width; ?>%; height: <?php echo $height?>px"></div>
<?php
if (EventbookingHelper::isValidMessage($params->get('post_text')))
{
	echo $params->get('post_text');
}
