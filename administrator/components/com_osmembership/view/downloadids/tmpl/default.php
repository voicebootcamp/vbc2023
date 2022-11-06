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

if (!OSMembershipHelper::isJoomla4())
{
	HTMLHelper::_('formbehavior.chosen', 'select');
}

$config = OSMembershipHelper::getConfig();
?>
<form action="index.php?option=com_osmembership&view=downloadids" method="post" name="adminForm" id="adminForm">
    <div id="filter-bar" class="btn-toolbar">
        <div class="filter-search btn-group pull-left">
            <label for="filter_search" class="element-invisible"><?php echo Text::_('OSM_FILTER_SEARCH_DOWNLOAD_IDS_DESC');?></label>
            <input type="text" name="filter_search" inputmode="search" id="filter_search" placeholder="<?php echo Text::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->escape($this->state->filter_search); ?>" class="hasTooltip form-control" title="<?php echo HTMLHelper::tooltipText('OSM_SEARCH_DOWNLOAD_IDS_DESC'); ?>" />
        </div>
        <div class="btn-group pull-left">
            <button type="submit" class="btn hasTooltip" title="<?php echo HTMLHelper::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>"><span class="icon-search"></span></button>
            <button type="button" class="btn hasTooltip" title="<?php echo HTMLHelper::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('filter_search').value='';this.form.submit();"><span class="icon-remove"></span></button>
        </div>
        <div class="filter-select pull-right">
            <?php echo $this->lists['filter_state']; ?>
        </div>
    </div>
    <div class="clearfix"></div>
    <table class="adminlist table table-striped">
        <thead>
        <tr>
            <th width="20">
                <input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
            </th>
            <th class="title">
                <?php echo HTMLHelper::_('grid.sort', Text::_('OSM_DOWNLOAD_ID'), 'tbl.download_id', $this->state->filter_order_Dir, $this->state->filter_order); ?>
            </th>
            <th class="title">
                <?php echo HTMLHelper::_('grid.sort', Text::_('OSM_DOMAIN'), 'tbl.domain', $this->state->filter_order_Dir, $this->state->filter_order); ?>
            </th>
            <th class="title" nowrap="nowrap">
                <?php echo HTMLHelper::_('grid.sort', Text::_('OSM_CREATED_DATE'), 'tbl.created_date', $this->state->filter_order_Dir, $this->state->filter_order); ?>
            </th>
            <th class="title" nowrap="nowrap">
                <?php echo HTMLHelper::_('grid.sort', Text::_('OSM_USERNAME'), 'u.username', $this->state->filter_order_Dir, $this->state->filter_order); ?>
            </th>
            <th class="title" nowrap="nowrap">
                <?php echo HTMLHelper::_('grid.sort', Text::_('Published'), 'tbl.published', $this->state->filter_order_Dir, $this->state->filter_order); ?>
            </th>
            <th width="3%" class="title" nowrap="nowrap">
                <?php echo HTMLHelper::_('grid.sort', Text::_('OSM_ID'), 'tbl.id', $this->state->filter_order_Dir, $this->state->filter_order); ?>
            </th>
        </tr>
        </thead>
        <tfoot>
        <tr>
            <td colspan="7">
                <?php echo $this->pagination->getListFooter(); ?>
            </td>
        </tr>
        </tfoot>
        <tbody>
        <?php
        $k = 0;
        for ($i=0, $n=count($this->items); $i < $n; $i++)
        {
            $row       = $this->items[$i];
            $checked   = HTMLHelper::_('grid.id', $i, $row->id);
	        $published = HTMLHelper::_('jgrid.published', $row->published, $i, 'downloadid.');
            ?>
            <tr class="<?php echo "row$k"; ?>">
                <td>
                    <?php echo $checked; ?>
                </td>
                <td>
                    <?php echo $row->download_id ?>
                </td>
                <td>
                    <?php echo $row->domain ; ?>
                </td>
                <td>
                    <?php echo HTMLHelper::_('date', $row->created_date, $config->date_format); ?>
                </td>
                <td>
                    <?php echo $row->username ; ?>
                </td>
                <td class="center">
                    <?php echo $published; ?>
                </td>
                <td class="center">
                    <?php echo $row->id; ?>
                </td>
            </tr>
            <?php
            $k = 1 - $k;
        }
        ?>
        </tbody>
    </table>
	<?php $this->renderFormHiddenVariables(); ?>
</form>