<?php
/*------------------------------------------------------------------------
# emails.html.php - Ossolution Services Booking
# ------------------------------------------------------------------------
# author    Ossolution team
# copyright Copyright (C) 2022 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/
// no direct access
defined('_JEXEC') or die;

class HTML_OSappscheduleEmails{
	static function emailListForm($option,$rows){
		global $mainframe;
		JToolBarHelper::title(JText::_('OS_MANAGE_EMAIL_TEMPLATES'),'envelope');
        JToolBarHelper::publish('emails_publish', 'JTOOLBAR_PUBLISH', true);
        JToolBarHelper::unpublish('emails_unpublish', 'JTOOLBAR_UNPUBLISH', true);
		JToolBarHelper::cancel('goto_index');
		JToolbarHelper::custom('cpanel_list','featured.png', 'featured_f2.png',JText::_('OS_DASHBOARD'),false);
		?>
		<form method="POST" action="index.php" name="adminForm" id="adminForm">
		<table width="100%" class="adminlist table table-striped">
			<thead>
				<tr>
                    <th width="2%">#</th>
                    <th width="3%">
                        <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
                    </th>
					<th width="35%">
						<?php echo JText::_('OS_EMAIL_KEY');?>
					</th>
					<th width="50%">
						<?php echo JText::_('OS_EMAIL_SUBJECT');?>
					</th>
                    <th width="10%" style="text-align:center;">
                        <?php echo JText::_('OS_PUBLISHED'); ?>
                    </th>
				</tr>
			</thead>
			<tbody>
				<?php
				$k = 0;
				for ($i=0, $n=count($rows); $i < $n; $i++) {
					$row = $rows[$i];
                    $checked = JHtml::_('grid.id', $i, $row->id);
					$link 		= JRoute::_( 'index.php?option='.$option.'&task=emails_edit&cid[]='. $row->id );
                    $published 	= JHTML::_('jgrid.published', $row->published, $i, 'emails_');
					?>
					<tr class="<?php echo "row$k"; ?>">
                        <td align="center"><?php echo $i + 1; ?></td>
                        <td align="center"><?php echo $checked; ?></td>
						<td align="left">
							<a href="<?php echo $link?>">
								<?php echo $row->email_key?>
							</a>
						</td>
						<td align="left">
							<a href="<?php echo $link?>">
								<?php echo $row->email_subject?>
							</a>
						</td>
                        <td align="center" style="text-align:center;"><?php echo $published?></td>
					</tr>
					<?php
					$k = 1 - $k;	
				}
				?>
			</tbody>
		</table>
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="emails_list" />
		<input type="hidden" name="boxchecked" value="0" />
		</form>
		<?php
	}
	
	static function editEmailTemplate($option,$row,$translatable){
		global $mainframe,$languages;
		JToolBarHelper::title(JText::_('OS_EMAIL_TEMPLATE')." <small>[Edit]</small>");
		JToolBarHelper::save('emails_save');
		JToolBarHelper::apply('emails_apply');
		JToolBarHelper::cancel('emails_gotolist');
		$editor = JEditor::getInstance(JFactory::getConfig()->get('editor'));
		?>
		<form method="POST" action="index.php" name="adminForm" id="adminForm">
		
		<?php 
		if ($translatable)
		{
			echo JHtml::_('bootstrap.startTabSet', 'translation', array('active' => 'general-page'));
				echo JHtml::_('bootstrap.addTab', 'translation', 'general-page', JText::_('OS_GENERAL', true));
		}
		?>
			<table cellpadding="0" cellspacing="0" width="100%" class="admintable">
				<tr>
					<td class="key">
						<?php echo JText::_('OS_EMAIL_SUBJECT')?>
					</td>
					<td>
						<input type="text" class="inputbox form-control" size="50" value="<?php echo $row->email_subject?>" name="email_subject">
					</td>
				</tr>
				<tr>
					<td class="key" valign="top" style="padding-top:5px;">
						<?php echo JText::_('OS_EMAIL_CONTENT')?>
					</td>
					<td>
						<?php echo $editor->display('email_content',stripslashes($row->email_content), '95%', '250', '75', '20' ,false) ?>
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
										<td class="key"><?php echo JText::_('OS_EMAIL_SUBJECT'); ?></td>
										<td >
											<input type="text" name="email_subject_<?php echo $sef; ?>" id="email_subject_<?php echo $sef; ?>" value="<?php echo $row->{'email_subject_'.$sef}?>" class="input-xlarge form-control" />
										</td>
									</tr>
									<tr>
										<td class="key" valign="top"><?php echo JText::_('OS_EMAIL_CONTENT'); ?></td>
										<td >
											<?php
											echo $editor->display( 'email_content_'.$sef,  stripslashes($row->{'email_content_'.$sef}) , '95%', '250', '75', '20' ) ;
											?>
										</td>
									</tr>
								</table>
							</div>										
						<?php				
							
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
		</form>		
		<?php
	}
}
?>