<?php
/*------------------------------------------------------------------------
# review.html.php - Ossolution Services Booking
# ------------------------------------------------------------------------
# author    Ossolution team
# copyright Copyright (C) 2019 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/
// no direct access
defined('_JEXEC') or die;


class HTML_OSappscheduleReview{
    /**
     * Extra field list HTML
     *
     * @param unknown_type $option
     * @param unknown_type $rows
     * @param unknown_type $pageNav
     * @param unknown_type $lists
     */
    static function review_list($option,$rows,$pageNav,$lists){
        global $mainframe,$configClass;
        $db = JFactory::getDbo();
        JHtml::_('behavior.multiselect');
        JToolBarHelper::title(JText::_('OS_MANAGE_REVIEWS'),'comment');
        JToolBarHelper::addNew('review_add');
        if(count($rows) > 0){
            JToolBarHelper::editList('review_edit');
            JToolBarHelper::deleteList(JText::_('OS_ARE_YOU_SURE_TO_REMOVE_ITEMS'),'review_remove');
            JToolBarHelper::publish('review_publish', 'JTOOLBAR_PUBLISH', true);
            JToolBarHelper::unpublish('review_unpublish', 'JTOOLBAR_UNPUBLISH', true);
        }
        JToolbarHelper::custom('cpanel_list','featured.png', 'featured_f2.png',JText::_('OS_DASHBOARD'),false);
        $ordering = ($lists['order'] == 'ordering');
        ?>

        <form method="POST" action="index.php?option=<?php echo $option; ?>&task=review_list" name="adminForm" id="adminForm">
            <table class="adminlist table table-striped" width="100%">
                <thead>
                <tr>
                    <th width="3%">#</th>
                    <th width="2%">
                        <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
                    </th>
                    <th width="5%">
                        <?php echo JHTML::_('grid.sort',   JText::_('OS_USER'), 'b.username', @$lists['order_Dir'], @$lists['order'] ,'review_list'); ?>
                    </th>
                    <th width="10%">
                        <?php echo JHTML::_('grid.sort',   JText::_('OS_NAME'), 'a.name', @$lists['order_Dir'], @$lists['order'] ,'review_list'); ?>
                    </th>
                    <th width="15%" style="text-align:left;">
                        <?php echo JHTML::_('grid.sort',   JText::_('OS_TITLE'), 'a.comment_title', @$lists['order_Dir'], @$lists['order'] ,'review_list'); ?>
                    </th>
                    <th width="10%" style="text-align:center;">
                        <?php echo JHTML::_('grid.sort',   JText::_('OS_DATE'), 'a.comment_date', @$lists['order_Dir'], @$lists['order'] ,'review_list'); ?>
                    </th>
                    <th width="10%" style="text-align:center;">
                        <?php echo JHTML::_('grid.sort',   JText::_('OS_RATING'), 'a.rating', @$lists['order_Dir'], @$lists['order'] ,'review_list'); ?>
                    </th>
                    <th width="15%" style="text-align:center;">
                        <?php echo JHTML::_('grid.sort',   JText::_('OS_EMPLOYEE')."/".JText::_('OS_SERVICE'), '', @$lists['order_Dir'], @$lists['order'] ,'review_list'); ?>
                    </th>
                    <th width="5%" style="text-align:center;">
                        <?php echo JText::_('OS_IP_ADDRESS'); ?>
                    </th>
                    <th width="5%" style="text-align:center;">
                        <?php echo JText::_('OS_PUBLISHED'); ?>
                    </th>
                    <th width="5%" style="text-align:center;">
                        <?php echo JHTML::_('grid.sort',   JText::_('OS_ID'), 'id', @$lists['order_Dir'], @$lists['order'] ); ?>
                    </th>
                </tr>
                </thead>
                <tfoot>
                <tr>
                    <td width="100%" colspan="11" style="text-align:center;">
                        <?php
                        echo $pageNav->getListFooter();
                        ?>
                    </td>
                </tr>
                </tfoot>
                <tbody>
                <?php
                $k = 0;
                $db = JFactory::getDbo();
                for ($i=0, $n=count($rows); $i < $n; $i++) {
                    $row = $rows[$i];
                    $checked = JHtml::_('grid.id', $i, $row->id);
                    $link 		= JRoute::_( 'index.php?option='.$option.'&task=review_edit&cid[]='. $row->id );
                    $published 	= JHTML::_('jgrid.published', $row->published, $i, 'review_');
                    ?>
                    <tr class="<?php echo "row$k"; ?>">
                        <td align="center"><?php echo $pageNav->getRowOffset( $i ); ?></td>
                        <td align="center"><?php echo $checked; ?></td>
                        <td align="left">
                            <a href="<?php echo $link; ?>"><?php echo $row->username; ?></a>
                        </td>
                        <td align="left">
                            <a href="<?php echo $link; ?>"><?php echo $row->name; ?></a>
                        </td>
                        <td align="left">
                            <?php echo $row->comment_title; ?>
                        </td>
                        <td style="text-align:center;">
                            <?php echo $row->comment_date; ?>
                        </td>
                        <td style="text-align:center;">
                            <?php
                            if($row->rating > 0){
                                for($j=1;$j<=$row->rating;$j++){
                                    ?>
                                    <i class="icon-star" style="color:orange;"></i>
                                    <?php
                                }
                            }
                            for($j=$row->rating + 1;$j<=5 ;$j++){
                                ?>
                                <i class="icon-star" style="color:#CCC;"></i>
                                <?php
                            }
                            ?>
                        </td>
                        <td style="text-align:center;">
                            <?php
                            $db->setQuery("Select employee_name from #__app_sch_employee where id = '$row->eid'");
                            echo $db->loadResult();
                            echo "/";
                            $db->setQuery("Select service_name from #__app_sch_services where id = '$row->sid'");
                            echo $db->loadResult();
                            ?>
                        </td>
                        <td style="text-align:center;">
                            <?php
                            echo $row->ip_address;
                            ?>
                        </td>
                        <td align="center" style="text-align:center;"><?php echo $published; ?></td>
                        <td align="center" style="text-align:center;"><?php echo $row->id; ?></td>
                    </tr>
                    <?php
                    $k = 1 - $k;
                }
                ?>
                </tbody>
            </table>
            <input type="hidden" name="option" value="<?php echo $option; ?>" />
            <input type="hidden" name="task" value="review_list"  />
            <input type="hidden" name="boxchecked" value="0" />
            <input type="hidden" name="filter_order" value="<?php echo $lists['order'];?>" />
            <input type="hidden" name="filter_order_Dir" value="<?php echo $lists['order_Dir'];?>" />
        </form>
        <?php
    }

    /**
     * This static function is used to show Review Add/modify form
     * @param $row
     * @param $lists
     */
    static function review_modify($row,$lists){
        global $mainframe,$configClass,$jinput;
        $db = JFactory::getDbo();
        $version 	= new JVersion();
        $_jversion	= $version->RELEASE;
        $mainframe 	= JFactory::getApplication();
        $jinput->set( 'hidemainmenu', 1 );
        if ($row->id){
            $title = ' ['.JText::_('OS_EDIT').']';
        }else{
            $title = ' ['.JText::_('OS_NEW').']';
        }
        JToolBarHelper::title(JText::_('OS_REVIEW').$title,'folder');
        JToolBarHelper::save('review_save');
        JToolBarHelper::apply('review_apply');
        JToolBarHelper::cancel('review_cancel');
		$document	= JFactory::getDocument();
		$document->addScript(JUri::root(true).'/media/com_osservicesbooking/assets/js/admin-review-default.js');
		JText::script('OS_PLEASE_ENTER_COMMENT_TITLE', true);
		JText::script('OS_PLEASE_SELECT_SERVICE_EMPLOYEE', true);
        ?>
        <?php
        if (version_compare(JVERSION, '3.5', 'ge')){
            ?>
            <script src="<?php echo JUri::root()?>media/jui/js/fielduser.min.js" type="text/javascript"></script>
        <?php } ?>
        <form method="post" name="adminForm" id="adminForm" action="index.php?option=com_osservicesbooking">
        <table class="admintable">
            <tr>
                <td class="key"><?php echo JText::_('OS_AUTHOR'); ?>: </td>
                <td >
                    <?php
                    if($row->id == 0){
                        $user = JFactory::getUser();
                        $row->user_id = $user->id;
                    }
                    echo OSappscheduleReview::getUserInput($row->user_id);
                    ?>
                </td>
            </tr>
            <tr>
                <td class="key"><?php echo JText::_('OS_NAME'); ?>: </td>
                <td >
                    <input type="text" name="name" id="name" class="input-medium" value="<?php echo $row->name;?>"/>
                </td>
            </tr>
            <tr>
                <td class="key"><?php echo JText::_('OS_SERVICE')."/ ".JText::_('OS_EMPLOYEE'); ?>: </td>
                <td >
                    <?php
                    echo $lists['service'];
                    ?>
                </td>
            </tr>
            <tr>
                <td class="key"><?php echo JText::_('OS_RATING'); ?>: </td>
                <td >
                    <?php echo $lists['rating'];?>
                </td>
            </tr>
            <tr>
                <td class="key"><?php echo JText::_('OS_CREATED_DATE'); ?>: </td>
                <td >
                    <?php
                    echo JHtml::_('calendar',$row->comment_date,'comment_date','comment_date','%Y-%m-%d','class="input-small"');
                    ?>
                </td>
            </tr>
            <tr>
                <td class="key"><?php echo JText::_('OS_COMMENT_TITLE'); ?>: </td>
                <td >
                    <input type="text" name="comment_title" id="comment_title" class="input-xlarge" value="<?php echo $row->comment_title;?>"/>
                </td>
            </tr>
            <tr>
                <td class="key"><?php echo JText::_('OS_COMMENT'); ?>: </td>
                <td >
                    <textarea rows="5" style="width: 550px;" name="comment_content" id="comment_content"><?php echo $row->comment_content; ?></textarea>
                </td>
            </tr>
            <tr>
                <td class="key">
                    <?php echo JText::_('OS_IP_ADDRESS'); ?>
                </td>
                <td>
                    <input type="text" name="ip_address" id="ip_address" size="40" value="<?php echo $row->ip_address; ?>" class="input-small">
                </td>
            </tr>
            <tr>
                <td class="key">
                    <?php echo JText::_('OS_PUBLISHED')?>
                </td>
                <td>
                    <?php
                    echo $lists['published'];
                    ?>
                </td>
            </tr>
            <tr>
        </table>
        <input type="hidden" name="option"  value="com_osservicesbooking" />
        <input type="hidden" name="task"    value="" />
        <input type="hidden" name="id"      value="<?php echo $row->id?>" />
        <input type="hidden" name="approved" id="approved" value="<?php echo $row->approved;?>" />
        </form>
        <?php
    }
}
?>