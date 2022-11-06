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

$pullLeftClass = $this->bootstrapHelper->getClassMapping('pull-left');
$isJoomla4     = OSMembershipHelper::isJoomla4();
?>
<div class="filter-search btn-group <?php echo $pullLeftClass; ?>">
    <label for="filter_search" class="element-invisible sr-only"><?php echo Text::_('OSM_FILTER_SEARCH_SUBSCRIPTIONS_DESC');?></label>
    <input type="text" name="filter_search" id="filter_search" placeholder="<?php echo Text::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->escape($this->state->filter_search); ?>" class="hasTooltip input-medium" title="<?php echo HTMLHelper::tooltipText('OSM_SEARCH_SUBSCRIPTIONS_DESC'); ?>" />
</div>
<div class="btn-group <?php echo $pullLeftClass; ?>">
    <button type="submit" class="btn<?php if ($isJoomla4) echo ' btn-primary'; ?> hasTooltip" title="<?php echo HTMLHelper::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>"><span class="<?php echo $this->bootstrapHelper->getClassMapping('icon-search'); ?>"></span></button>
    <button type="button" class="btn<?php if ($isJoomla4) echo ' btn-primary'; ?> hasTooltip" title="<?php echo HTMLHelper::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('filter_search').value='';this.form.submit();"><span class="<?php echo $this->bootstrapHelper->getClassMapping('icon-remove'); ?>"></span></button>
</div>
<div class="btn-group <?php echo $pullLeftClass; ?>">
	<?php
        echo $this->lists['plan_id'];
        echo $this->lists['subscription_type'];
        echo $this->lists['published'];

        foreach($this->filters as $filter)
        {
            echo $filter;
        }
	?>
</div>
