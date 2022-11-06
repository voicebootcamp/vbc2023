<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2022 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

?>
<div class="control-group">
	<div class="control-label">
		<?php echo Text::_('OSM_FIRST_REMINDER_EMAIL_SUBJECT'); ?>
	</div>
	<div class="controls">
		<input type="text" name="first_reminder_email_subject" class="form-control input-xxlarge"
		       value="<?php echo $this->item->first_reminder_email_subject; ?>" size="40"/>
	</div>
</div>
<div class="control-group">
	<div class="control-label">
		<?php echo Text::_('OSM_FIRST_REMINDER_EMAIL_BODY'); ?>
	</div>
	<div class="controls">
		<?php echo $editor->display('first_reminder_email_body', $this->item->first_reminder_email_body, '100%', '250', '75', '8'); ?>
	</div>
</div>
<div class="control-group">
	<div class="control-label">
		<?php echo Text::_('OSM_SECOND_REMINDER_EMAIL_SUBJECT'); ?>
	</div>
	<div class="controls">
		<input type="text" name="second_reminder_email_subject" class="form-control input-xxlarge"
		       value="<?php echo $this->item->second_reminder_email_subject; ?>" size="40"/>
	</div>
</div>
<div class="control-group">
	<div class="control-label">
		<?php echo Text::_('OSM_SECOND_REMINDER_EMAIL_BODY'); ?>
	</div>
	<div class="controls">
		<?php echo $editor->display('second_reminder_email_body', $this->item->second_reminder_email_body, '100%', '250', '75', '8'); ?>
	</div>
</div>
<div class="control-group">
	<div class="control-label">
		<?php echo Text::_('OSM_THIRD_REMINDER_EMAIL_SUBJECT'); ?>
	</div>
	<div class="controls">
		<input type="text" name="third_reminder_email_subject" class="form-control input-xxlarge"
		       value="<?php echo $this->item->third_reminder_email_subject; ?>" size="40"/>
	</div>
</div>
<div class="control-group">
	<div class="control-label">
		<?php echo Text::_('OSM_THIRD_REMINDER_EMAIL_BODY'); ?>
	</div>
	<div class="controls">
		<?php echo $editor->display('third_reminder_email_body', $this->item->third_reminder_email_body, '100%', '250', '75', '8'); ?>
	</div>
</div>
