<?php
/**
 * @package    com_quix
 * @author     ThemeXpert <info@themexpert.com>
 * @copyright  Copyright (C) 2015. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text as JText;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

$user          = JFactory::getUser();
$userId        = $user->get('id');
$listOrder     = $this->state->get('list.ordering');
$listDirection = $this->state->get('list.direction');
$canOrder      = $user->authorise('core.edit.state', 'com_quix');
$saveOrder     = $listOrder == 'a.`ordering`';
if ($saveOrder) {
    $saveOrderingUrl = 'index.php?option=com_quix&task=collections.saveOrderAjax&tmpl=component';
    JHtml::_('sortablelist.sortable', 'pageList', 'adminForm', strtolower($listDirection), $saveOrderingUrl);
}

$sortFields        = $this->getSortFields();
$filter_collection = $this->app->getUserStateFromRequest('com_quix.themes.filter.collection', 'filter_collection', 'all', 'string');

$layout = new JLayoutFile('blocks.toolbar');
echo $layout->render(['active' => 'collections.'.$filter_collection]);
?>
<div class="quix qx-container qx-margin-medium-top qx-text-small">
  <form action="<?php echo JRoute::_('index.php?option=com_quix&view=collections'); ?>" method="post" name="adminForm"
        id="adminForm">

    <div id="page-filter" class="qx-margin-small-bottom">
      <div class="qx-gird-small qx-grid" qx-grid>
        <!-- search -->
        <div class="qx-width-1-3@s qx-flex">

          <a href="#" class="qx-button qx-button-primary" qx-toggle="target: #new-template">
            <span class="qxuicon-plus qx-margin-small-right"></span> New Template
          </a>
            <?php if ($this->canDo->get('core.edit.state')): ?>
              <a
                      href="javascript::void(0);"
                      id="toolbar-trash"
                      onclick="if (document.adminForm.boxchecked.value == 0) { alert(Joomla.JText._('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST')); } else { Joomla.submitbutton('collections.trash'); }"
                      class="qx-button qx-button-danger qx-margin-small-left qx-hidden"
                      qx-tooltip="title: Trash your item"
              >
                <span class="qxuicon-trash"></span>
              </a>
            <?php endif; ?>

            <?php if ($this->state->get('filter.state') === '-2' && $this->canDo->get('core.delete')): ?>
              <a
                      href="javascript::void(0);"
                      id="toolbar-remove"
                      onclick="if (document.adminForm.boxchecked.value == 0) { alert(Joomla.JText._('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST')); } else { Joomla.submitbutton('collections.delete'); }"
                      class="qx-button qx-button-danger qx-margin-small-left qx-hidden"
                      qx-tooltip="title: Remove your item permanently."
              >
                <span class="qxuicon-trash-alt"></span>
              </a>
            <?php endif; ?>
        </div>
        <!-- Filter and item limit -->
        <div class="qx-width-expand@s qx-flex qx-flex-right">
          <div class="qx-visibel@s">
            <input class="qx-input" type="text" name="filter_search" id="filter_search"
                   placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>"
                   value="<?php echo $this->escape($this->state->get('filter.search')); ?>"
                   title="<?php echo JText::_('JSEARCH_FILTER'); ?>" />
          </div>

          <div class="qx-visible@s">
            <button class="qx-button qx-button-default qx-margin-small-right" type="submit"
                    title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>">
              <span class="qxuicon-search"></span>
            </button>
          </div>

          <div class="qx-visible@s">
            <label for="sortTable" class="element-invisible"><?php echo JText::_('JOPTION_SELECT_PUBLISHED'); ?></label>
            <select name="filter_published" id="filter_published" class="qx-select" onchange="this.form.submit()">
              <option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED'); ?></option>
                <?php echo JHtml::_(
                    'select.options',
                    JHtml::_('jgrid.publishedOptions'),
                    'value',
                    'text',
                    $this->state->get('filter.state'),
                    true
                ); ?>
            </select>
          </div>

          <div class="qx-visible@s qx-margin-small-left">
            <label for="limit" class="element-invisible">
                <?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?>
            </label>
            <select name="limit" id="limit" class="qx-select" onchange="Joomla.submitform();">
              <option value=""><?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?></option>
                <?php
                $limits = array();
                for ($i = 5; $i <= 30; $i += 5) {
                    $limits[] = \JHtml::_('select.option', "$i");
                }
                $limits[] = \JHtml::_('select.option', '50', \JText::_('J50'));
                $limits[] = \JHtml::_('select.option', '100', \JText::_('J100'));
                $limits[] = \JHtml::_('select.option', '0', \JText::_('JALL'));
                echo JHtml::_('select.options', $limits, 'value', 'text', $this->state->get('list.limit'), true);
                ?>
            </select>
          </div>
        </div>
      </div>
    </div>

      <?php if (count($this->items)) : ?>
        <table id="qx-table" class="qx-table qx-table-hover qx-box-shadow-hover-small">
          <thead>
          <tr>
              <?php if (isset($this->items[0]->ordering)) : ?>
                <th width="1%" class="nowrap center qx-visible@s">
                    <?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'a.`ordering`', $listDirection, $listOrder, null, 'asc',
                        'JGRID_HEADING_ORDERING'); ?>
                </th>
              <?php endif; ?>
            <th width="1%" class="qx-visible@s">
              <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>"
                     onclick="Joomla.checkAll(this)" />
            </th>

            <th width="1%"></th>

            <th width="40%">
                <?php echo JHtml::_('grid.sort', 'COM_QUIX_PAGES_TITLE', 'a.`title`', $listDirection, $listOrder); ?>
            </th>

            <th></th>

            <th width="10%" class='left'>
                <?php echo JText::_('COM_QUIX_COLLECTION_SHORTCODE'); ?>
            </th>

            <th width="10%" class='left'>
                <?php echo JText::_('COM_QUIX_COLLECTION_TYPE'); ?>
            </th>

              <?php if (isset($this->items[0]->id)) : ?>
                <th width="1%" class="nowrap center qx-visible@s">
                    <?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.`id`', $listDirection, $listOrder); ?>
                </th>
              <?php endif; ?>
          </tr>
          </thead>

          <tbody>
          <?php foreach ($this->items as $i => $item) :
              $ordering = ($listOrder == 'a.ordering');
              $canCreate = $user->authorise('core.create', 'com_quix');
              $canEdit = $user->authorise('core.edit', 'com_quix');
              $canCheckin = $user->authorise('core.manage', 'com_quix');
              $canChange = $user->authorise('core.edit.state', 'com_quix');
              ?>
            <tr class="qx-background-default row<?php echo $i % 2; ?>">
                <?php if (isset($this->items[0]->ordering)) : ?>
                  <td class="order nowrap center qx-visible@s">
                      <?php
                      if ($canChange) :
                          $disableClassName = '';
                          $disabledLabel = '';
                          if ( ! $saveOrder) :
                              $disabledLabel    = JText::_('JORDERINGDISABLED');
                              $disableClassName = 'inactive tip-top';
                          endif;
                          switch ($item->state) {
                              case 1:
                                  $status_text  = 'P';
                                  $status_class = 'primary';
                                  break;
                              case 2:
                                  $status_text  = 'A';
                                  $status_class = 'secondary';
                                  break;
                              default:
                                  $status_text  = 'U';
                                  $status_class = 'danger';
                                  break;
                          }
                          ?>
                        <span class="sortable-handler hasTooltip <?php echo $disableClassName ?>"
                              title="<?php echo $disabledLabel ?>">
              <i class="icon-menu"></i>
            </span>
                        <input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->ordering; ?>"
                               class="width-20 text-area-order " />
                      <?php else : ?>
                        <span class="sortable-handler inactive">
              <i class="icon-menu"></i>
            </span>
                      <?php endif; ?>
                  </td>
                <?php endif; ?>
              <td class="qx-visible@s">
                  <?php echo JHtml::_('grid.id', $i, $item->id); ?>
              </td>
                <?php if (isset($this->items[0]->state)) : ?>
                    <?php $item->state = (int) $item->state; ?>
                  <td>

                    <div class="qx-button-group">
                      <a
                              class="qx-button qx-button-small qx-button-<?php echo $status_class ?>"
                              qx-tooltip="title: Click to <?php echo $item->state === 1 ? 'Unpublish' : 'Publish' ?>"
                              href="javascript:void(0);"
                              onclick="return Joomla.listItemTask('cb<?php echo $i; ?>','collections.<?php echo $item->state === 1 ? 'unpublish' : 'publish' ?>')">
                          <?php echo $status_text; ?>
                      </a>
                      <div class="qx-inline">
                        <button class="qx-button qx-button-default qx-button-small" type="button"><span
                                  class="qxuicon-ellipsis-v"></span></button>
                        <div class="qx-dropdown" qx-dropdown="mode:click">
                          <ul class="qx-nav qx-dropdown-nav">
                            <li>
                              <a href="javascript://"
                                 onclick="Joomla.listItemTask('cb<?php echo $i; ?>', 'collections.duplicate')">
                                <span class="qxuicon-copy qx-margin-small-right" aria-hidden="true"></span>Duplicate Item
                              </a>
                            </li>
                              <?php if ($item->state != 2): ?>
                                <li>
                                  <a href="javascript://"
                                     onclick="Joomla.listItemTask('cb<?php echo $i; ?>', 'collections.archive')">
                                    <span class="qxuicon-archive qx-margin-small-right" aria-hidden="true"></span>Archive Item
                                  </a>
                                </li>
                              <?php endif; ?>
                          </ul>
                        </div>
                      </div>
                    </div>
                  </td>
                <?php endif; ?>
              <td>
                  <?php if (isset($item->checked_out) && $item->checked_out && ($canEdit || $canChange)) : ?>
                      <?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'collections.', $canCheckin); ?>
                  <?php endif; ?>
                  <?php if ($canEdit) : ?>
                      <?php
                      if ($item->builder == 'classic') {
                          $link = 'index.php?option=com_quix&task=collection.edit&id='.(int) $item->id;
                      } else {
                          $link = JUri::root().'index.php?option=com_quix&task=collection.edit&id='.(int) $item->id.'&quixlogin=true';
                      } ?>
                    <a <?php echo($item->builder == 'frontend' ? 'target="_blank"' : ''); ?> href="<?php echo $link ?>">
                        <?php echo $this->escape($item->title); ?>
                    </a>
                  <?php else : ?>
                      <?php echo $this->escape($item->title); ?>
                  <?php endif; ?>

                  <?php echo($item->builder == 'classic' ? '<span class="qx-label">Classic</span>' : ''); ?>

                  <?php echo(($item->type != 'layout' and $item->type != 'section') ? '<span class="qx-label qx-label-'.$item->type.'">'.ucfirst($item->type).'</span>' : ''); ?>
                  <?php if ($item->builder !== 'classic'): ?>
                    <div class="qx-text-meta"><small>Version: <?php echo $item->builder_version; ?> <a
                                href="index.php?option=com_quix&task=config.reverseVersion&type=collections&id=<?php echo $item->id.'&'.JSession::getFormToken().'=1'; ?>"
                                qx-tooltip="Fix wrong version number"><i
                                  class="qxuicon-first-aid"></i></a></small></div>
                  <?php endif; ?>
              </td>
              <td>
                <a class="qx-button qx-button-text" target="_blank"
                   href="<?php echo JUri::root().'index.php?option=com_quix&view=collection&&id='.$item->id; ?>">
                  <i class="qxuicon-eye qx-margin-small-right"></i>Preview
                </a>
              </td>

              <td>
            <span class="qx-label">
              [quix id='<?php echo $item->id; ?>']
            </span>
              </td>

              <td>
                  <?php echo ucfirst($item->type); ?>
              </td>

                <?php if (isset($this->items[0]->id)) : ?>
                  <td class="center qx-visible@s">
                      <?php echo (int) $item->id; ?>
                  </td>
                <?php endif; ?>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
          <?php echo $this->pagination->getListFooter(); ?>

      <?php else : ?>
        <div class="qx-card qx-card-default qx-card-body qx-text-center">
          <h3 class="qx-margin-remove-top qx-card-title">No Templates Found</h3>
          <p>Create your first re-useable template and use it anywhere inside Joomla!</p>
        </div>
      <?php endif; ?>


      <?php echo QuixHelper::getFooterLayout(); ?>

    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirection; ?>" />
      <?php echo JHtml::_('form.token'); ?>
  </form>

    <?php echo $this->loadTemplate('new'); ?>
</div>
<script type="text/javascript">
    Joomla.orderTable = function() {
        let table = document.getElementById('sortTable');
        let direction = document.getElementById('directionTable');
        let order = table.options[table.selectedIndex].value;
        let newDirection;
        if (order != '<?php echo $listOrder; ?>') {
            newDirection = 'asc';
        }
        else {
            newDirection = direction.options[direction.selectedIndex].value;
        }
        Joomla.tableOrdering(order, newDirection, '');
    };
</script>

