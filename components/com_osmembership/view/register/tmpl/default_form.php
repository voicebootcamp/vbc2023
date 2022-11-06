<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2022 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die ;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\Text;

/**@var OSMembershipHelperBootstrap $bootstrapHelper **/
$bootstrapHelper   = $this->bootstrapHelper;
$controlGroupClass = $bootstrapHelper->getClassMapping('control-group');
$inputPrependClass = $bootstrapHelper->getClassMapping('input-prepend');
$inputAppendClass  = $bootstrapHelper->getClassMapping('input-append');
$addOnClass        = $bootstrapHelper->getClassMapping('add-on');
$controlLabelClass = $bootstrapHelper->getClassMapping('control-label');
$controlsClass     = $bootstrapHelper->getClassMapping('controls');

if ($this->config->enable_avatar && empty($this->avatar))
{
?>
	<div id="field_upload_profile_avatar" class="<?php echo $controlGroupClass ?>">
		<div class="<?php echo $controlLabelClass; ?>">
			<label for="profile_avatar"><?php echo  Text::_('OSM_AVATAR') ?></label>
		</div>
		<div class="<?php echo $controlsClass; ?>">
			<input type="file" name="profile_avatar" accept="image/*">
		</div>
	</div>
<?php
}

if (isset($this->lists['show_on_members_list']))
{
?>
    <div id="field_show_on_members_list_control" class="<?php echo $controlGroupClass; ?>">
        <div class="<?php echo $controlLabelClass; ?>">
            <label><?php echo Text::_('OSM_SHOW_ON_MEMBERS_LIST'); ?></label>
        </div>
        <div class="<?php echo $controlsClass; ?>">
			<?php echo $this->lists['show_on_members_list']; ?>
        </div>
    </div>
<?php
}

if (!$this->userId && $this->config->registration_integration)
{
	$params = ComponentHelper::getParams('com_users');
	$passwordValidationRules = 'validate[required';

	if ($minimumLength = $params->get('minimum_length', 4))
    {
        $passwordValidationRules .= ",minSize[$minimumLength]";
    }

    $passwordValidationRules .= ',ajax[ajaxValidatePassword]]';

	if (empty($this->config->use_email_as_username))
	{
	?>
		<div id="field_username" class="<?php echo $controlGroupClass ?>">
			<div class="<?php echo $controlLabelClass; ?>">
				<?php echo OSMembershipHelperHtml::getFieldLabel('username1', Text::_('OSM_USERNAME'), Text::_('OSM_USERNAME_TOOLTIP'), true); ?>
			</div>
			<div class="<?php echo $controlsClass; ?>">
				<input type="text" name="username" id="username1" class="validate[required,minSize[2],ajax[ajaxUserCall]] form-control<?php echo $bootstrapHelper->getFrameworkClass('uk-input', 1); ?>" value="<?php echo $this->escape($this->input->post->getUsername('username')); ?>" size="15" autocomplete="off"/>
			</div>
		</div>
	<?php
	}
	else
	{
		echo $fields['email']->getControlGroup($bootstrapHelper, true);
		unset($fields['email']);

		// Do a bit magic here so that confirm email field will be displayed right after email field
        if (isset($fields['confirm_email']))
        {
	        echo $fields['confirm_email']->getControlGroup($bootstrapHelper, true);
	        unset($fields['confirm_email']);
        }
	}

	if (empty($this->config->auto_generate_password))
    {
    ?>
        <div id="field_password" class="<?php echo $controlGroupClass ?>">
            <div class="<?php echo $controlLabelClass; ?>">
			    <?php echo OSMembershipHelperHtml::getFieldLabel('password1', Text::_('OSM_PASSWORD'), Text::_('OSM_PASSWORD_TOOLTIP'), true); ?>
            </div>
            <div class="<?php echo $controlsClass; ?>">
                <input value="" class="<?php echo $passwordValidationRules; ?> form-control<?php echo $bootstrapHelper->getFrameworkClass('uk-input', 1); ?>" type="password" name="password1" id="password1" autocomplete="off"/>
            </div>
        </div>
        <div id="field_password2" class="<?php echo $controlGroupClass ?>">
            <div class="<?php echo $controlLabelClass; ?>">
                <label for="password2">
				    <?php echo  Text::_('OSM_RETYPE_PASSWORD') ?>
                    <span class="required">*</span>
                </label>
            </div>
            <div class="<?php echo $controlsClass; ?>">
                <input value="" class="validate[required,equals[password1]] form-control<?php echo $bootstrapHelper->getFrameworkClass('uk-input', 1); ?>" type="password" name="password2" id="password2" />
            </div>
        </div>
    <?php
    }
}

foreach ($fields as $field)
{
    /* @var MPFFormField $field */
	echo $field->getControlGroup($bootstrapHelper, true);
}
