<?php
/*------------------------------------------------------------------------
# plugin.php - Ossolution emailss Booking
# ------------------------------------------------------------------------
# author    Ossolution team
# copyright Copyright (C) 2022 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/
// no direct access
defined('_JEXEC') or die;

class HTML_OSappschedulePlugin{
	/**
	 * List plugins
	 *
	 * @param unknown_type $option
	 * @param unknown_type $rows
	 * @param unknown_type $pageNav
	 */
	static function listPlugins($option,$rows,$pageNav,$lists)
	{
		global $mainframe,$configClass,$jinput, $mapClass;
		JToolBarHelper::title(JText::_('OS_MANAGE_PAYMENT_PLUGINS'),'list');
		JToolBarHelper::deleteList(JText::_('OS_ARE_YOU_SURE_TO_REMOVE_ITEMS'),'plugin_remove');
		JToolBarHelper::publish('plugin_publish', 'JTOOLBAR_PUBLISH', true);
		JToolBarHelper::unpublish('plugin_unpublish', 'JTOOLBAR_UNPUBLISH', true);
		JToolbarHelper::custom('cpanel_list','featured.png', 'featured_f2.png',JText::_('OS_DASHBOARD'),false);
		$ordering = ($lists['order'] == 'ordering');

		$listOrder	= $lists['order'];
        $listDirn	= $lists['order_Dir'];

        $saveOrder	= $listOrder == 'ordering';
        $ordering	= ($listOrder == 'ordering');

        if ($saveOrder)
        {
            $saveOrderingUrl = 'index.php?option=com_osservicesbooking&task=plugin_saveorderAjax';
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

		if (count($rows))
        {
			$ordering = array();
            foreach ($rows as $item)
            {
                $ordering[0][] = $item->id;
            }
        }
		?>
		<form action="index.php?option=com_osservicesbooking&task=plugin_list&type=0" method="post" name="adminForm" enctype="multipart/form-data" id="adminForm">
		<table width="100%">
			<tr>
				<td align="left"  width="100%">
					<input type="text" placeholder="<?php echo JText::_('OS_SEARCH');?>" name="keyword" id="keyword" value="<?php echo $jinput->get('keyword','','string') ;?>" class="input-medium search-query form-control" onchange="document.adminForm.submit();" />
                    <div class="btn-group">
                        <button onclick="this.form.submit();" class="btn btn-warning"><?php echo JText::_( 'OS_SEARCH' ); ?></button>
                        <button onclick="document.getElementById('keyword').value='';this.form.submit();" class="btn btn-info"><?php echo JText::_( 'OS_RESET' ); ?></button>
                        </div>
				</td>	
			</tr>
		</table>
		<div id="editcell">
			<table class="adminlist table table-striped">
			<thead>
				<tr>
					<th width="3%" class="nowrap center hidden-phone">
						<?php echo JHtml::_('searchtools.sort', '', 'ordering', @$lists['order_Dir'], @$lists['order'], null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
					</th>
					<th width="20">
                        <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
					</th>
					<th class="title">
						<?php echo JText::_('OS_PLUGIN_NAME'); ?>
					</th>
					<th class="title" width="20%">
						<?php echo JText::_('OS_PLUGIN_TITLE'); ?>
					</th>			
					<th class="title">
						<?php echo JText::_('OS_PLUGIN_AUTHOR'); ?>
					</th>			
					<th class="title">
						<?php echo JText::_('OS_PLUGIN_EMAIL'); ?>
					</th>	
					<th style="text-align:center;">
						<?php echo JText::_('OS_PUBLISHED'); ?>
					</th>											
					<th style="text-align:center;">
						<?php echo JHTML::_('grid.sort', JText::_('OS_ID') , 'id', $lists['order_Dir'], $lists['order'] ); ?>
					</th>
				</tr>		
			</thead>
			<tfoot>
				<tr>
					<td colspan="9">
						<?php echo $pageNav->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
			<tbody <?php if ($saveOrder) :?> class="js-draggable" data-url="<?php echo $saveOrderingUrl; ?>" data-direction="<?php echo strtolower($lists['order_Dir']); ?>" <?php endif; ?>>
			<?php
			$k = 0;
			$canChange = true;
			for ($i=0, $n=count( $rows ); $i < $n; $i++)
			{
				$row	= $rows[$i];
				$link 	= JRoute::_( 'index.php?option=com_osservicesbooking&task=plugin_edit&cid[]='. $row->id );
				$checked 	= JHTML::_('grid.id',   $i, $row->id );				
				$published 	= JHTML::_('jgrid.published', $row->published, $i, 'plugin_' );
				?>
				<tr class="<?php echo "row$k"; ?>" sortable-group-id="0" item-id="<?php echo $row->id ?>" parents="<?php echo $parentsStr ?>" level="0">
					<td class="order nowrap center hidden-phone" style="text-align:center;">
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
							<input type="text" style="display:none;" name="order[]" value="<?php echo $row->ordering; ?>" />
						<?php endif; ?>
					</td>
					<td>
						<?php echo $checked; ?>
					</td>	
					<td>
						<a href="<?php echo $link; ?>">
							<?php echo $row->name; ?>
						</a>
					</td>
					<td>
						<?php echo $row->title; ?>
					</td>												
					<td>
						<?php echo $row->author; ?>
					</td>
					<td align="center">
						<?php echo $row->author_email;?>
					</td>
					<td align="center" style="text-align:center;">
						<?php echo $published ; ?>
					</td>					
					<td align="center"  style="text-align:center;">
						<?php echo $row->id; ?>
					</td>
				</tr>
				<?php
				$k = 1 - $k;
			}
			?>
			</tbody>
			</table>
			<table class="adminform" style="margin-top: 50px;width:100%;">
				<tr>
					<td>
						<fieldset class="form-horizontal options-form">
							<legend><?php echo JText::_('OS_INSTALL_NEW_PLUGIN'); ?></legend>
							<table>
								<tr>
									<td>
										<input type="file" name="plugin_package" id="plugin_package" size="50" class="inputbox form-control" /> <input type="button" class="btn btn-info" value="<?php echo JText::_('OS_INSTALL'); ?>" onclick="installPlugin();" />
									</td>
								</tr>
							</table>					
						</fieldset>
					</td>
				</tr>		
			</table>
			</div>
			<input type="hidden" name="option" value="com_osservicesbooking" />
			<input type="hidden" name="task" value="plugin_list" id="task" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="filter_order" value="<?php echo $lists['order'];?>" />
			<input type="hidden" name="filter_order_Dir" value="<?php echo $lists['order_Dir'];?>" />
			<input type="hidden" name="filter_full_ordering" id="filter_full_ordering" value="" />
			<?php echo JHTML::_( 'form.token' ); ?>				 
			<script type="text/javascript">
				function installPlugin() {
					var form = document.adminForm ;
					if (form.plugin_package.value =="") {
						alert("<?php echo JText::_('OS_CHOOSE_PLUGIN'); ?>");
						return ;	
					}
					
					form.task.value = 'plugin_install' ;
					form.submit();
				}
			</script>
		</form>
		<?php
	}
	
	
	/**
	 * Edit plugin
	 *
	 * @param unknown_type $option
	 * @param unknown_type $item
	 * @param unknown_type $params
	 */
	static function editPlugin($option,$item,$lists,$form){
		global $mainframe, $mapClass;
		OSBHelper::loadTooltip();
		if($item->id > 0){
			$type = "[".JText::_('OS_EDIT')."]";
		}else{
			$type = "[".JText::_('OS_ADD')."]";
		}
		JToolBarHelper::title(JText::_('OS_PLUGIN')." ".$type,'folder');
		JToolBarHelper::save('plugin_save');
		JToolBarHelper::apply('plugin_apply');
		JToolBarHelper::cancel('plugin_gotolist');
		?>
		<script language="javascript" type="text/javascript">
			<?php
				if (version_compare(JVERSION, '1.6.0', 'ge')) {
				?>
					Joomla.submitbutton = function(pressbutton)
					{
						var form = document.adminForm;
						if (pressbutton == 'plugin.cancel') {
							Joomla.submitform(pressbutton, form);
							return;				
						} else {
							//Validate the entered data before submitting													
							Joomla.submitform(pressbutton, form);
						}								
					}
				<?php	
				} else {
				?>
				 function submitbutton(pressbutton) {
						var form = document.adminForm;
						if (pressbutton == 'cancel_plugin') {
							submitform( pressbutton );
							return;				
						} else {
							submitform( pressbutton );
						}
					}	
				<?php	
				}
			?>	
		</script>
		<form action="index.php" method="post" name="adminForm" id="adminForm">
		<div class="<?php echo $mapClass['row-fluid'];?>">
			<div class="<?php echo $mapClass['span7'];?>">
				<fieldset class="form-horizontal options-form">
					<legend><?php echo JText::_('OS_PLUGIN_DETAIL'); ?></legend>
						<table class="admintable adminform">
							<tr>
								<td width="100" align="right" class="key">
									<?php echo  JText::_('OS_NAME'); ?>
								</td>
								<td>
									<?php echo $item->name ; ?>
								</td>
							</tr>
							<tr>
								<td width="100" align="right" class="key">
									<?php echo  JText::_('OS_TITLE'); ?>
								</td>
								<td>
									<input class="form-control input-medum ilarge" type="text" name="title" id="title" size="40" maxlength="250" value="<?php echo $item->title;?>" />
								</td>
							</tr>					
							<tr>
								<td class="key">
									<?php echo JText::_('OS_AUTHOR'); ?>
								</td>
								<td>
									<input class="form-control input-medum ilarge" type="text" name="author" id="author" size="40" maxlength="250" value="<?php echo $item->author;?>" />
								</td>
							</tr>
							<tr>
								<td class="key">
									<?php echo JText::_('OS_CREATION_DATE'); ?>
								</td>
								<td>
									<?php echo $item->creation_date; ?>
								</td>
							</tr>
							<tr>
								<td class="key">
									<?php echo JText::_('OS_COPYRIGHT') ; ?>
								</td>
								<td>
									<?php echo $item->copyright; ?>
								</td>
							</tr>	
							<tr>
								<td class="key">
									<?php echo JText::_('OS_LICENSE'); ?>
								</td>
								<td>
									<?php echo $item->license; ?>
								</td>
							</tr>							
							<tr>
								<td class="key">
									<?php echo JText::_('OS_AUTHOR_EMAIL'); ?>
								</td>
								<td>
									<?php echo $item->author_email; ?>
								</td>
							</tr>
							<tr>
								<td class="key">
									<?php echo JText::_('OS_AUTHOR_URL'); ?>
								</td>
								<td>
									<?php echo $item->author_url; ?>
								</td>
							</tr>				
							<tr>
								<td class="key">
									<?php echo JText::_('OS_VERSION'); ?>
								</td>
								<td>
									<?php echo $item->version; ?>
								</td>
							</tr>
							<tr>
								<td class="key">
									<?php echo JText::_('OS_DESCRIPTION'); ?>
								</td>
								<td>
									<?php echo $item->description; ?>
								</td>
							</tr>
							<tr>
								<td class="key">
									<?php echo JText::_('OS_ACCESS'); ?>
								</td>
								<td>
									<?php					
										echo $lists['access'];					
									?>						
								</td>
							</tr>
							<tr>
								<td class="key">
									<?php echo JText::_('OS_PUBLISHED_STATE'); ?>
								</td>
								<td>
									<?php OSappscheduleConfiguration::showCheckboxfield('published',(int)$item->published);?>
								</td>
							</tr>
					</table>
				</fieldset>				
			</div>						
			<div class="<?php echo $mapClass['span5'];?> payment-plugin-form">
				<fieldset class="form-horizontal options-form">
					<legend><?php echo JText::_('OS_PLUGIN_PARAMETERS'); ?></legend>
					<?php
					$fieldSets = $form->getFieldsets();

					if (count($fieldSets) >= 2)
					{
						echo JHtml::_('bootstrap.startTabSet', 'payment-plugin-params', ['active' => 'basic']);

						foreach ($fieldSets as $fieldSet)
						{
							echo JHtml::_('bootstrap.addTab', 'payment-plugin-params', $fieldSet->name, $fieldSet->label);

							foreach ($form->getFieldset($fieldSet->name) as $field)
							{
								echo $field->renderField();
							}

							echo JHtml::_('bootstrap.endTab');
						}

						echo JHtml::_('bootstrap.endTabSet');
					}
					else
					{
						foreach ($form->getFieldset('basic') as $field)
						{
							echo $field->renderField();
						}
					}					
					?>				
				</fieldset>				
			</div>
		</div>
		<input type="hidden" name="option" value="com_osservicesbooking" />
		<input type="hidden" name="cid[]" value="<?php echo $item->id; ?>" />
		<input type="hidden" name="id" value="<?php echo $item->id; ?>" />
		<input type="hidden" name="task" value="" />
		</form>
		<?php
	}
}
?>