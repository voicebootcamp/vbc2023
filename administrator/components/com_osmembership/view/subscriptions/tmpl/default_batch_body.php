<?php
/**
 * @package           Joomla
 * @subpackage        Membership Pro
 * @author            Tuan Pham Ngoc
 * @copyright         Copyright (C) 2012 - 2022 Ossolution Team
 * @license           GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Editor\Editor;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

$config  = OSMembershipHelper::getConfig();
$editor  = Editor::getInstance($config->get('editor') ?: Factory::getApplication()->get('editor'));
$message = OSMembershipHelper::getMessages();

$bootstrapHelper = OSMembershipHelperHtml::getAdminBootstrapHelper();
?>
<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?> form form-horizontal">
    <div class="control-group">
        <div class="control-label">
			<?php echo Text::_('OSM_REPLY_TO_EMAIL'); ?>
        </div>
        <div class="controls">
            <input type="email" name="reply_to_email" value="" size="70" class="input-xxlarge form-control" />
        </div>
    </div>
    <div class="control-group">
        <div class="control-label">
			<?php echo Text::_('OSM_BCC_EMAIL'); ?>
        </div>
        <div class="controls">
            <input type="email" name="bcc_email" value="" size="70" class="input-xxlarge form-control" />
        </div>
    </div>
    <div class="control-group">
        <div class="control-label">
			<?php echo Text::_('OSM_ATTACHMENT'); ?>
        </div>
        <div class="controls">
            <input type="file" name="attachment" value="" size="70" class="form-control input-xxlarge" />
        </div>
    </div>
	<div class="control-group">
		<div class="control-label">
			<?php echo Text::_('OSM_EMAIL_SUBJECT'); ?>
		</div>
		<div class="controls">
			<input type="text" name="subject" value="" size="70" class="input-xxlarge form-control" />
		</div>
	</div>
	<div class="control-group">
		<div class="control-label">
			<?php echo Text::_('OSM_EMAIL_MESSAGE'); ?>
            <p><strong><?php echo Text::_('OSM_AVAILABLE_TAGS'); ?> : [PLAN_TITLE], [FIRST_NAME], [LAST_NAME], [ADDRESS] ...,[CREATED_DATE],[FROM_DATE], [TO_DATE]</strong></p>
		</div>
		<div class="controls">
			<?php echo $editor->display('message', $message->mass_mail_template, '100%', '250', '75', '10'); ?>
		</div>
	</div>
</div>

