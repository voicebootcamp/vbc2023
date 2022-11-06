<?php
/*------------------------------------------------------------------------
# category.php - Ossolution emailss Booking
# ------------------------------------------------------------------------
# author    Ossolution team
# copyright Copyright (C) 2022 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/
// no direct access
defined('_JEXEC') or die;

class HTML_OSappscheduleCategory{
	/**
	 * List categories
	 *
	 * @param unknown_type $option
	 * @param unknown_type $rows
	 * @param unknown_type $pageNav
	 * @param unknown_type $keyword
	 */
	static function listCategories($option,$rows,$pageNav,$keyword, $lists, $children)
    {
		global $mainframe,$_jversion,$configClass,$mapClass;
		JToolBarHelper::title(JText::_('OS_MANAGE_CATEGORIES'),'folder');
		JToolBarHelper::addNew('category_add');
		if(count($rows) > 0)
		{
			JToolBarHelper::editList('category_edit');
			JToolBarHelper::deleteList(JText::_('OS_ARE_YOU_SURE_TO_REMOVE_ITEMS'),'category_remove');
			JToolBarHelper::publish('category_publish' , 'JTOOLBAR_PUBLISH', true);
			JToolBarHelper::unpublish('category_unpublish' , 'JTOOLBAR_UNPUBLISH', true);
		}
		JToolbarHelper::custom('cpanel_list','featured.png', 'featured_f2.png',JText::_('OS_DASHBOARD'),false);

        $listOrder	= $lists['filter_order'];
        $listDirn	= $lists['filter_order_Dir'];

        $saveOrder	= $listOrder == 'ordering';
        $ordering	= ($listOrder == 'ordering');

        if ($saveOrder)
        {
            $saveOrderingUrl = 'index.php?option=com_osservicesbooking&task=category_saveorderAjax';
			if (OSBHelper::isJoomla4())
			{
				\Joomla\CMS\HTML\HTMLHelper::_('draggablelist.draggable');
			}
			else
			{
				JHtml::_('sortablelist.sortable', 'categoryList', 'adminForm', strtolower($listDirn), $saveOrderingUrl, false, true);
			}
        }

        $customOptions = array(
            'filtersHidden'       => true,
            'defaultLimit'        => JFactory::getApplication()->get('list_limit', 20),
            'orderFieldSelector'  => '#filter_full_ordering'
        );

        JHtml::_('searchtools.form', '#adminForm', $customOptions);
        if (count($rows))
        {
			$ordering = array();
            foreach ($rows as $item)
            {
                $ordering[$item->parent_id][] = $item->id;
            }
        }
		?>
		<form method="POST" action="index.php?option=<?php echo $option; ?>&task=category_list" name="adminForm" id="adminForm">
			<table style="width: 100%;">
				<tr>
					<td align="right" width="100%">
						<div class="btn-group">
							<div class="input-group">
								<input type="text" 	class="<?php echo $mapClass['input-medium']; ?> search-query form-control" name="keyword" value="<?php echo $keyword; ?>" placeholder="<?php echo JText::_('OS_SEARCH');?>" />
								<button type="submit" class="btn btn-warning"><?php echo JText::_('OS_SEARCH');?></button>
								<button type="reset"  class="btn btn-info" onclick="this.form.keyword.value='';this.form.submit();"><?php echo JText::_('OS_RESET');?></button>
							</div>
                        </div>
					</td>
				</tr>
			</table>
			<table class="adminlist table table-striped" width="100%" id="categoryList">
				<thead>
					<tr>
                        <th width="2%" class="nowrap center hidden-phone">
                            <?php echo JHtml::_('searchtools.sort', '', 'ordering', @$lists['filter_order_Dir'], @$lists['filter_order'], null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
                        </th>
						<th width="3%">
							<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
						</th>
						<th width="10%">
							<?php echo JText::_('OS_PHOTO');?>
						</th>
						<th width="50%">
							<?php echo JText::_('OS_CATEGORY_NAME');?>
						</th>
						<th width="10%" style="text-align:center;">
							<?php echo JText::_('OS_PUBLISHED'); ?>
						</th>
						<th width="5%" style="text-align:center;">
							ID
						</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td width="100%" colspan="6" style="text-align:center;">
							<?php
								echo $pageNav->getListFooter();
							?>
						</td>
					</tr>
				</tfoot>
				<tbody <?php if ($saveOrder) :?> class="js-draggable" data-url="<?php echo $saveOrderingUrl; ?>" data-direction="<?php echo strtolower($lists['filter_order_Dir']); ?>" <?php endif; ?>>
				<?php
                $k = 0;
                $canChange = true;
				for ($i=0, $n=count($rows); $i < $n; $i++)
				{
					$row        = $rows[$i];
					$checked    = JHtml::_('grid.id', $i, $row->id);
                    $orderkey   = array_search($row->id, $children[$row->parent_id]);
					$link 		= JRoute::_( 'index.php?option='.$option.'&task=category_edit&cid[]='. $row->id );
					$published 	= JHTML::_('jgrid.published', $row->published, $i, 'category_');
					?>
                    <tr class="<?php echo "row$k"; ?>" sortable-group-id="<?php echo $row->parent_id; ?>" item-id="<?php echo $row->id ?>" parents="<?php echo $parentsStr ?>" level="0">
						<td align="center">
                            <?php
                            $iconClass = '';
                            if (!$canChange)
                            {
                                $iconClass = ' inactive';
                            }
                            elseif (!$saveOrder)
                            {
                                $iconClass = ' inactive tip-top hasTooltip" title="' . JHtml::tooltipText('JORDERINGDISABLED');
                            }
                            ?>
                            <span class="sortable-handler<?php echo $iconClass ?>">
							    <span class="icon-menu"></span>
						    </span>
                            <?php if ($canChange && $saveOrder) : ?>
                                <input type="text" style="display:none" name="order[]" size="5" value="<?php echo $row->ordering; ?>" />
                            <?php endif; ?>
                        </td>
						<td align="center"><?php echo $checked; ?></td>
						<td align="center">
							<?php
							if($row->category_photo != "")
							{
								?>
								<img src="<?php echo JURI::root()?>images/osservicesbooking/category/<?php echo $row->category_photo?>" width="100" class="img-polaroid" />
								<?php
							}
							else
							{
								?>
								<img src="<?php echo JURI::root()?>components/com_osservicesbooking/asset/images/no_image_available.png" width="100" class="img-polaroid" />
								<?php
							}
							?>
						</td>
						<td align="left"><a href="<?php echo $link; ?>"><?php echo $row->treename; ?></a></td>
						<td align="center" style="text-align:center;"><?php echo $published?></td>
						<td align="center" style="text-align:center;"><?php echo $row->id; ?></td>
					</tr>
					<?php
					$k = 1 - $k;	
				}
				?>
				</tbody>
			</table>
			<input type="hidden" name="option" value="<?php echo $option; ?>">
			<input type="hidden" name="task" value="category_list">
			<input type="hidden" name="boxchecked" value="0">
            <input type="hidden" name="filter_order"  id="filter_order" value="<?php echo $lists['filter_order']; ?>" />
            <input type="hidden" name="filter_order_Dir" id="filter_order_Dir" value="<?php echo $lists['filter_order_Dir']; ?>" />
            <input type="hidden" name="filter_full_ordering" id="filter_full_ordering" value="" />
		</form>
		<?php
	}
	
	/**
	 * Edit category
	 *
	 * @param unknown_type $option
	 * @param unknown_type $row
	 */
	static function editCategory($option,$row,$lists,$translatable){
		global $mainframe, $_jversion,$configClass,$languages,$jinput,$mapClass;
		$db = JFactory::getDbo();
		$jinput->set( 'hidemainmenu', 1 );
		if ($row->id){
			$title = ' ['.JText::_('OS_EDIT').']';
		}else{
			$title = ' ['.JText::_('OS_NEW').']';
		}
		JToolBarHelper::title(JText::_('OS_CATEGORIES').$title,'folder');
		JToolBarHelper::save('category_save');
		JToolBarHelper::apply('category_apply');
		JToolBarHelper::cancel('category_cancel');
		$editor = JEditor::getInstance(JFactory::getConfig()->get('editor'));
		OSBHelper::loadTooltip();
		?>
		<form method="POST" action="index.php" name="adminForm" id="adminForm" enctype="multipart/form-data">
		<?php 
		if ($translatable)
		{
			echo JHtml::_('bootstrap.startTabSet', 'translation', array('active' => 'general-page'));
				echo JHtml::_('bootstrap.addTab', 'translation', 'general-page', JText::_('OS_GENERAL', true));
		}
		?>
		<table class="admintable">
			<tr>
				<td class="key"><?php echo JText::_('OS_CATEGORY_NAME'); ?>: </td>
				<td >
					<input class="<?php echo $mapClass['input-large']; ?> ilarge required" type="text" name="category_name" id="category_name" size="40" value="<?php echo $row->category_name?>" >
				</td>
			</tr>
            <tr>
                <td class="key" style="vertical-align: top; padding-top:10px;"><?php echo JText::_('OS_PARENT_CATEGORY'); ?>: </td>
                <td >
                    <?php echo $lists['parent']; ?>
                </td>
            </tr>
			<tr>
				<td class="key" valign="top" style="padding-top:10px;"><?php echo JText::_('Photo'); ?>: </td>
				<td >
					<?php
					if($row->category_photo != "")
					{
						?>
						<img src="<?php echo JURI::root()?>images/osservicesbooking/category/<?php echo $row->category_photo?>" width="150" class="img-polaroid" />
						<div style="clear:both;"></div>
						<input type="checkbox" name="remove_photo" id="remove_photo" value="0" onclick="javascript:changeValue('remove_photo')"  /> <?php echo JText::_('OS_REMOVE');?>
						<?php
					}
					?>
					<input type="file" name="image" id="image" class="<?php echo $mapClass['input-large']; ?> ilarge" />
				</td>
			</tr>
			<tr>
				<td class="key" valign="top"><?php echo JText::_('OS_CATEGORY_DESC'); ?>: </td>
				<td>
					<?php
					echo $editor->display( 'category_description',  $row->category_description , '95%', '250', '75', '20' ) ;
					?>
				</td>
			</tr>
			<tr>
				<td class="key"><span class="hasTip" title="<?php echo JText::_('OS_SHOW_DESCRIPTION'); ?>::<?php  echo JText::_('OS_SHOW_DESCRIPTION_EXPLAIN')?>"><?php echo JText::_('OS_SHOW_DESCRIPTION'); ?>: </span></td>
				<td >
					<?php OSappscheduleConfiguration::showCheckboxfield('show_desc',(int)$row->show_desc);?>
				</td>
			</tr>
			<tr>
				<td class="key"><?php echo JText::_('OS_PUBLISHED_STATUS'); ?>: </td>
				<td >
					<?php OSappscheduleConfiguration::showCheckboxfield('published',(int)$row->published);?>
				</td>
			</tr>
		</table>
		<?php 
		if ($translatable)
		{
		?>
		<?php echo JHtml::_('bootstrap.endTab'); ?>
			<?php echo JHtml::_('bootstrap.addTab', 'translation', 'translation-page', JText::_('OS_TRANSLATION', true)); ?>		
				<div class="tab-content">			
					<?php	
						$i = 0;
						$activate_sef = $languages[0]->sef;
						echo JHtml::_('bootstrap.startTabSet', 'languagetranslation', array('active' => 'translation-page-'.$activate_sef));
						foreach ($languages as $language)
						{												
							$sef = $language->sef;
							echo JHtml::_('bootstrap.addTab', 'languagetranslation',  'translation-page-'.$sef, '<img src="'.JURI::root().'media/com_osservicesbooking/flags/'.$sef.'.png" />');
						?>
							<div class="tab-pane<?php echo $i == 0 ? ' active' : ''; ?>" id="translation-page-<?php echo $sef; ?>">													
								<table width="100%" class="admintable" style="background-color:white;">
									<tr>
										<td class="key"><?php echo JText::_('OS_CATEGORY_NAME'); ?>: </td>
										<td >
											<input class="input-large ilarge form-control" type="text" name="category_name_<?php echo $sef; ?>" id="category_name_<?php echo $sef; ?>" size="40" value="<?php echo $row->{'category_name_'.$sef};?>" />
										</td>
									</tr>
									<tr>
										<td class="key" valign="top"><?php echo JText::_('OS_CATEGORY_DESC'); ?>: </td>
										<td>
											<?php
												echo $editor->display( 'category_description_'.$sef,  $row->{'category_description_'.$sef} , '95%', '250', '75', '20' ) ;
											?>
										</td>
									</tr>
								</table>
							</div>										
						<?php				
							echo JHtml::_('bootstrap.endTabSet');
							$i++;		
						}
						echo JHtml::_('bootstrap.endTabSet');
					?>
				</div>	
			<?php
			echo JHtml::_('bootstrap.endTab');
			echo JHtml::_('bootstrap.endTabSet');
		}
		
		?>
		<input type="hidden" name="option" value="<?php echo $option?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="id" value="<?php echo (int)$row->id?>" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="MAX_FILE_SIZE" value="9000000000" />
		</form>
		<?php
	}
}
?>