<?php
/**
 * @package    com_quix
 * @author     ThemeXpert <info@themexpert.com>
 * @copyright  Copyright (C) 2015. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    3.0.0
 */
// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text as JText;
use Joomla\Registry\Registry;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('jquery.framework');
JHtml::_('bootstrap.framework');

$user          = JFactory::getUser();
$userId        = $user->get('id');
$listOrder     = $this->state->get('list.ordering');
$listDirection = $this->state->get('list.direction');
$canOrder      = $user->authorise('core.edit.state', 'com_quix');
$saveOrder     = $listOrder === 'a.`ordering`';
if ($saveOrder && ! empty($this->items)) {
    if (JVERSION >= 4) {
        $saveOrderingUrl = 'index.php?option=com_quix&task=pages.saveOrderAjax&tmpl=component&'.JSession::getFormToken().'=1';
    } else {
        $saveOrderingUrl = 'index.php?option=com_quix&task=pages.saveOrderAjax&tmpl=component';
    }

    JHtml::_('sortablelist.sortable', 'qx-table', 'adminForm', strtolower($listDirection), $saveOrderingUrl);
}
$sortFields = $this->getSortFields();

$link = JRoute::_(JUri::root().'index.php?option=com_quix&task=page.add&quixlogin=true');
?>

<?php /* Load toolbar */
echo QuixHelperLayout::getToolbar('pages'); ?>

<div class="quix qx-container qx-margin-medium-top qx-text-small">

  <script type="text/javascript">
      Joomla.orderTable = function() {
          let table = document.getElementById('sortTable');
          let direction = document.getElementById('directionTable');
          let order = table.options[table.selectedIndex].value;
          let dirn;
          if (order !== '<?php echo $listOrder; ?>') {
              dirn = 'asc';
          }
          else {
              dirn = direction.options[direction.selectedIndex].value;
          }

          Joomla.tableOrdering(order, dirn, '');
      };
  </script>

  <form action="<?php echo JRoute::_('index.php?option=com_quix&view=pages'); ?>" method="post" name="adminForm"
        id="adminForm">

    <div class="qx-margin-small-bottom">
      <div class="qx-grid qx-gird-small" qx-grid>
        <!-- new -->

        <div class="qx-width-1-3@s qx-flex">
          <a
                  href="<?php echo $link; ?>"
                  target="_blank"
                  id="js-new-page-prompt"
                  class="qx-button qx-button-primary"
                  qx-tooltip="title: Create New Page"
          >
            <span class="qxuicon-plus qx-margin-small-right"></span>New Page
          </a>

            <?php if ($this->canDo->get('core.edit.state')): ?>
              <a
                      href="javascript::void(0);"
                      id="toolbar-trash"
                      onclick="if (document.adminForm.boxchecked.value == 0) { alert(Joomla.JText._('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST')); } else { Joomla.submitbutton('pages.trash'); }"
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
                      onclick="if (document.adminForm.boxchecked.value == 0) { alert(Joomla.JText._('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST')); } else { Joomla.submitbutton('pages.delete'); }"
                      class="qx-button qx-button-danger qx-margin-small-left qx-hidden"
                      qx-tooltip="title: Remove your item permanently."
              >
                <span class="qxuicon-trash-alt"></span>
              </a>
            <?php endif; ?>

        </div>

        <div class="qx-width-expand@s qx-flex qx-flex-right">
          <!-- Filter and item limit -->
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
                    <?php echo JHtml::_(
                        'grid.sort',
                        '<i class="icon-menu-2"></i>',
                        'a.`ordering`',
                        $listDirection,
                        $listOrder,
                        null,
                        'asc',
                        'JGRID_HEADING_ORDERING'
                    ); ?>
                </th>
              <?php endif; ?>

            <th width="1%" class="qx-visible@s">
              <input type="checkbox" class="qx-checkbox" name="checkall-toggle" value=""
                     title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
            </th>

              <?php if (isset($this->items[0]->state)) : ?>
                <th width="60px" class="nowrap qx-text-center">
                    <?php echo JHtml::_('grid.sort', 'JSTATUS', 'a.`state`', $listDirection, $listOrder); ?>
                </th>
              <?php endif; ?>

            <th>
                <?php echo JHtml::_('grid.sort', 'COM_QUIX_PAGES_TITLE', 'a.`title`', $listDirection, $listOrder); ?>
            </th>
            <!-- Action buttons  -->
            <th class="center qx-visible@m"></th>

            <th width="1%" class="center qx-visible@m">
                <?php echo 'SEO' ?>
            </th>

            <th width="1%" class="nowrap center qx-visible@m">
                <?php echo JHtml::_('searchtools.sort', 'JGLOBAL_HITS', 'a.hits', $listDirection, $listOrder); ?>
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
              $registry = new Registry;
              $metadata = $registry->loadString($item->metadata);
              $seoScore = $metadata->get('seo_score', 0);

              $params = $registry->loadString($item->params);
              // $image_optimized = $params->get('image_optimized', false);

              $ordering   = ($listOrder === 'a.ordering');
              $canCreate  = $user->authorise('core.create', 'com_quix');
              $canEdit    = $user->authorise('core.edit', 'com_quix');
              $canCheckin = $user->authorise('core.manage', 'com_quix');
              $canChange  = $user->authorise('core.edit.state', 'com_quix');

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
                        <span class="sortable-handler <?php echo $disableClassName ?>"> <i class="icon-menu"></i> </span>
                        <input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->ordering; ?>" />
                      <?php else : ?>
                        <span class="sortable-handler inactive"><i class="icon-menu"></i></span>
                      <?php endif; ?>
                  </td>
                <?php endif; ?>

              <td class="qx-visible@s">
                  <?php echo JHtml::_('grid.id', $i, $item->id); ?>
              </td>

                <?php if (isset($this->items[0]->state)) : ?>
                  <td>
                      <?php $item->state = (int) $item->state; ?>
                    <div class="qx-button-group">
                      <a
                              class="qx-button qx-button-small qx-button-<?php echo $status_class ?>"
                              qx-tooltip="title: Click to <?php echo $item->state === 1 ? 'Unpublish' : 'Publish' ?>"
                              ref="javascript:void(0);"
                              onclick="return Joomla.listItemTask('cb<?php echo $i; ?>','pages.<?php echo $item->state === 1 ? 'unpublish' : 'publish' ?>')">
                          <?php echo $status_text; ?>
                      </a>
                      <div class="qx-inline">
                        <button class="qx-button qx-button-default qx-button-small" type="button"><span
                                  class="qxuicon-ellipsis-v"></span></button>
                        <div class="qx-dropdown" qx-dropdown="mode:click">
                          <ul class="qx-nav qx-dropdown-nav">
                            <li>
                                <?php
                                if (isset($item->checked_out) && $item->checked_out && ($canEdit || $canChange)) :
                                    $text = $item->editor.'<br />'.JHtml::_('date', $item->checked_out_time,
                                            JText::_('DATE_FORMAT_LC')).'<br />'.JHtml::_('date', $item->checked_out_time, 'H:i');
                                    ?>
                                  <a
                                          class="qx-text-primary"
                                          href="javascript:void(0);"
                                          onclick="return Joomla.listItemTask('cb<?php echo $i; ?>','pages.checkin')"
                                          data-title="<?php echo $text; ?>">
                                    <span class="qxuicon-lock-open" aria-hidden="true"></span>
                                    Unlock Page
                                  </a>
                                <?php endif; ?>
                            </li>
                            <li>
                              <a href="javascript://" onclick="Joomla.listItemTask('cb<?php echo $i; ?>', 'pages.duplicate')">
                                <span class="qxuicon-copy" aria-hidden="true"></span>
                                Duplicate Page
                              </a>
                            </li>
                              <?php if ($item->state != 2): ?>
                                <li>
                                  <a href="javascript://" onclick="Joomla.listItemTask('cb<?php echo $i; ?>', 'pages.archive')">
                                    <span class="qxuicon-archive" aria-hidden="true"></span>
                                    Archive Page
                                  </a>
                                </li>
                              <?php endif; ?>

                            <li class="qx-nav-divider"></li>

                            <li>
                              <a href="javascript://" onclick="Joomla.listItemTask('cb<?php echo $i; ?>', 'pages.clearCache')">
                                <span class="qxuicon-trash-alt" aria-hidden="true"></span>
                                Clear Page Cache
                              </a>
                            </li>
                            <li>
                              <a href="javascript://" onclick="Joomla.listItemTask('cb<?php echo $i; ?>', 'pages.resetHits')">
                                <span class="qxuicon-calculator" aria-hidden="true"></span>
                                Reset Hits
                              </a>
                            </li>
                          </ul>
                        </div>
                      </div>
                    </div>
                  </td>
                <?php endif; ?>

              <td class="item-title">
                  <?php if ($canEdit) : ?>
                      <?php
                      if ($item->builder === 'classic') {
                          $link = 'index.php?option=com_quix&task=page.edit&id='.(int) $item->id;
                      } else {
                          $link = JUri::root().'index.php?option=com_quix&task=page.edit&id='.(int) $item->id.'&quixlogin=true';
                      } ?>
                    <a <?php echo($item->builder === 'frontend' ? 'target="_blank"' : ''); ?>
                            href="<?php echo JRoute::_($link); ?>">
                        <?php echo $this->escape($item->title); ?>
                      <!--<span-->
                      <!--  class="qx-margin-small-left qx-label qx-label---><?php //echo ($image_optimized) ? 'success' : 'danger'; ?><!--"-->
                      <!--  qx-tooltip="title: Image">-->
                      <!--  --><?php //echo ($image_optimized) ? '<span class="qxuicon-check"></span>' : '<span class="qxuicon-times"></span>' ?>
                      <!--</span>-->
                    </a>
                  <?php else : ?>
                      <?php echo $this->escape($item->title); ?>
                  <?php endif; ?>
                  <?php echo($item->builder === 'classic' ? '<span class="qx-label qx-label-warning">Classic</span>' : ''); ?>

                <div class="qx-text-meta">
                  <small>Access: <?php echo $item->access_level; ?></small>
                  <small class="qx-margin-small-left">Lang: <?php echo JLayoutHelper::render('joomla.content.language', $item); ?></small>
                    <?php if ($item->builder !== 'classic'): ?>
                      <small class="qx-margin-small-left">Version: <?php echo $item->builder_version; ?> <a
                                href="index.php?option=com_quix&task=config.reverseVersion&type=pages&id=<?php echo $item->id.'&'.JSession::getFormToken().'=1'; ?>"
                                qx-tooltip="Fix wrong version number"><i
                                  class="qxuicon-first-aid"></i></a></small>
                    <?php endif; ?>
                </div>
              </td>

              <td class="center qx-visible@m">
                <a
                        class="qx-button qx-button-text qx-button-small"
                        target="_blank"
                        qx-tooltip="title: Preview Page"
                        href="<?php echo JUri::root().'index.php?option=com_quix&view=page&id='.$item->id; ?>">
                  <span class="qxuicon-external-link"></span> Preview
                </a>
                <a
                        class="qx-button qx-button-primary qx-button-small qx-margin-small-left"
                        target="_blank"
                        qx-tooltip="title: Edit with v4 Builder"
                        href="<?php echo JRoute::_($link); ?>">
                  <span class="qxuicon-bolt"></span> Edit
                </a>
              </td>

              <td class="qx-visible@m center">
                  <?php
                  $status = ($seoScore <= 80) ? 'warning' : 'success';
                  ?>
                <label class="qx-label qx-label-<?php echo $status; ?>">
                    <?php echo $seoScore; ?>
                </label>
              </td>

              <td class="qx-visible@m">
                  <?php echo (int) $item->hits; ?>
              </td>

              <td class="qx-visible@s">
                  <?php echo (int) $item->id; ?>
              </td>

            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>

          <?php echo $this->pagination->getListFooter(); ?>

      <?php else : ?>
        <div class="qx-alert qx-alert-primary">
          <h3>No Page found!</h3>
          <p>Create your first Quix page and experience the new visual builder.</p>
        </div>
      <?php endif; ?>

      <?php echo QuixHelper::getFooterLayout(); ?>

    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirection; ?>" />
      <?php echo JHtml::_('form.token'); ?>
  </form>
</div>
