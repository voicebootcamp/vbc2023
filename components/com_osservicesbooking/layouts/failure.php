<h1 class="eb_title"><?php echo JText::_('OS_REGISTRATION_FAILURE'); ?></h1>
<table width="100%">
    <tr>
        <td colspan="2" align="left">
            <?php echo  JText::_('OS_FAILURE_MESSAGE'); ?>
        </td>
    </tr>
    <tr>
        <td valign="top">
            <?php echo JText::_('OS_REASON'); ?>
        </td>
        <td>
            <p class="info"><?php echo $reason; ?></p>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <input type="button" class="btn btn-primary" value="<?php echo JText::_('OS_BACK'); ?>" onclick="window.history.go(-1);" />
        </td>
    </tr>
</table>