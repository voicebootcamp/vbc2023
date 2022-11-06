<?php
/**
 * @package    com_quix
 * @author     ThemeXpert <info@themexpert.com>
 * @copyright  Copyright (C) 2015. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    1.0.0
 */

// No direct access
defined('_JEXEC') or die;

$app = JFactory::getApplication();
if ($app->isClient('site')) {
    JSession::checkToken('get') or die(JText::_('JINVALID_TOKEN'));
}

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.framework', true);
JHtml::_('formbehavior.chosen', 'select');

$function  = $app->input->getCmd('function', 'jSelectPage');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirection  = $this->escape($this->state->get('list.direction'));
$actionUrl = 'index.php?option=com_quix&view=collections&layout=modal&tmpl=component&function='
             . $function
             . '&'
             . JSession::getFormToken() . '=1';

?>

<form
      action="<?php echo JRoute::_($actionUrl); ?>"
      method="post"
      name="adminForm" id="adminForm"
>
        <div id="filter-bar" class="btn-toolbar">
            <div class="filter-search input-append pull-left">
                <label for="filter_search"
                       class="element-invisible">
                    <?php echo JText::_('JSEARCH_FILTER'); ?>
                </label>
                <input type="text" name="filter_search" id="filter_search"
                   placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>"
                   value="<?php echo $this->escape($this->state->get('filter.search')); ?>"
                   title="<?php echo JText::_('JSEARCH_FILTER'); ?>"
               />

                <button class="btn hasTooltip" type="submit"
                    title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>">
                    <i class="icon-search"></i>
                </button>
                <button class="btn hasTooltip" id="clear-search-button" type="button"
                    title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>"
                    onClick="document.getElementById('filter_search').value = '';this.form.submit();"
                >
                    <i class="icon-remove"></i>
                </button>
            </div>
            <div class="btn-group pull-right hidden-phone">
                <label for="limit"
                       class="element-invisible">
                    <?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?>
                </label>
                <?php echo $this->pagination->getLimitBox(); ?>
            </div>


        </div>
        <div class="clearfix"></div>

        <ul class="qx-collection-list">
        <?php foreach ($this->items as $i => $collection) : ?>
            <?php if ($collection->language && JLanguageMultilang::isEnabled()) {
                $tag = strlen($collection->language);
                if ($tag === 5) {
                    $lang = substr($collection->language, 0, 2);
                } elseif ($tag === 6) {
                    $lang = substr($collection->language, 0, 3);
                } else {
                    $lang = "";
                }

                if (is_object($lang)) {
                    $lang = $lang->get('tag');
                }
            } elseif (!JLanguageMultilang::isEnabled()) {
                $lang = "";
            }

            ?>
            <li>
                <span class="qx-collection-title">
                  <?php echo $collection->title ?>
                  <label class="label label-notice"><?php echo ucfirst($collection->builder) ?></label>
                </span>

                <span class="label label-shortcode">[quix id='<?php echo $collection->id ?>']</span>

                <span class="label label-<?php echo $collection->type ?>">
                  <?php echo $collection->type ?>
                </span>
                <a class="qx-insert-shortcode btn pull-right"
                href="javascript:void(0);"
                onclick="selectItem(<?php echo $collection->id ?>)">
                    Insert
                </a>
            </li>
        <?php endforeach; ?>
        </ul>

        <div class="center text-center">
            <br><br>
            <?php echo $this->pagination->getListFooter(); ?>
        </div>

        <input type="hidden" name="task" value=""/>
        <input type="hidden" name="boxchecked" value="0"/>
        <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
        <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirection; ?>"/>
        <?php echo JHtml::_('form.token'); ?>
</form>
<script>
  function selectItem(id){
    if (window.parent) window.parent.<?php echo $this->escape($function);?>(id);
  }
</script>
<style>
  body{
    font-family: sans-serif;
  }
  .qx-collection-list{ list-style:none; padding:0; margin:0 10px; }
  .qx-collection-list li{
    background : #fff;
    padding: 10px 1.1rem 13px; margin: 10px 0;
    border-radius: 2px;
    box-shadow: 0 2px 5px 0 rgba(0,0,0,0.16),0 2px 10px 0 rgba(0,0,0,0.12);
  }
  .qx-collection-title{ width: 40%; float: left; }
  .label{
    background: #fafbfb;
    padding: 3px 7px;
    font-size: 10px; color: #999;
    border: 1px solid #eee;
    border-radius: 2px;
    text-shadow: none;
  }
  .label-section,
  .label-layout{ color: #fff; text-transform: uppercase; letter-spacing: .5px; }
  .label-section{ background: #9575cd; border-color: #9575cd;}
  .label-layout{ background: #26a69a; border-color: #23b3a5; padding: 3px 10px; }
  .pull-right{ float: right; }
  .btn{
    border-radius: 2px;
    display: inline-block;
    height: 28px;
    line-height: 28px;
    outline: 0;
    padding: 0 2rem;
    text-transform: uppercase;
    color: #222;
    background-color: #26a69a;
    text-align: center;
    text-shadow: none;
    font-size: 11px;
    letter-spacing: .5px;
    text-decoration: none;
    margin-left: 5px;
    transition: all 0.2s linear;
  }
  .btn:hover{
    color: #fff;
    font-weight: bold;
    background-color: #2bbbad;
    box-shadow: 0 5px 11px 0 rgba(0,0,0,0.18),0 4px 15px 0 rgba(0,0,0,0.15);
    background-position: 0 30px;
  }
  .btn--new{ margin: 5px 0 5px 10px; padding: .3rem 2rem; }
  .btn--edit{ background: #00bcd4; }
  .btn--edit:hover{ background-color: #4dd0e1; }
</style>
