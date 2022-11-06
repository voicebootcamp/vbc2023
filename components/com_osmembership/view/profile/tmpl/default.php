<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2022 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die ;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

HTMLHelper::_('behavior.core');

if (OSMembershipHelper::isJoomla4())
{
	$tabApiPrefix   = 'uitab.';
	$containerClass = ' osm-container-j4';
}
else
{
	$tabApiPrefix   = 'bootstrap.';
	$containerClass = '';
}

OSMembershipHelperJquery::validateForm();

$document = Factory::getDocument();
$document->addScriptDeclaration('var siteUrl = "' . OSMembershipHelper::getSiteUrl() . '";');
$document->addScript(Uri::root(true) . '/media/com_osmembership/js/site-profile-default.min.js');
Text::script('OSM_CANCEL_SUBSCRIPTION_CONFIRM', true);

if ($this->config->use_https)
{
	$ssl = 1;
}
else
{
	$ssl = 0;
}

$fields = $this->form->getFields();

if (isset($fields['state']))
{
	$selectedState = $fields['state']->value;
}
else
{
	$selectedState = '';
}

$document->addScriptOptions('selectedState', $selectedState);

/* @var OSMembershipHelperBootstrap $bootstrapHelper*/
$bootstrapHelper = $this->bootstrapHelper;

// Get mapping classes, make them ready for using
$rowFluidClass     = $bootstrapHelper->getClassMapping('row-fluid');
$controlGroupClass = $bootstrapHelper->getClassMapping('control-group');
$inputPrependClass = $bootstrapHelper->getClassMapping('input-group');
$addOnClass        = $bootstrapHelper->getClassMapping('add-on');
$controlLabelClass = $bootstrapHelper->getClassMapping('control-label');
$controlsClass     = $bootstrapHelper->getClassMapping('controls');
$btnClass          = $bootstrapHelper->getClassMapping('btn');

$fieldSuffix = OSMembershipHelper::getFieldSuffix();
?>
<div id="osm-profile-page" class="osm-container<?php echo $containerClass; ?>">
<?php
	if ($this->params->get('show_page_heading', 1))
    {
	    if ($this->input->getInt('hmvc_call'))
	    {
		    $hTag = 'h2';
	    }
	    else
	    {
		    $hTag = 'h1';
	    }
    ?>
        <<?php echo $hTag; ?> class="osm-page-title"><?php echo Text::_('OSM_USER_PROFILE'); ?></<?php echo $hTag; ?>>
    <?php
    }

	if (OSMembershipHelper::isValidMessage($this->params->get('intro_text')))
	{
	?>
		<div class="osm-description osm-page-intro-text <?php echo $this->bootstrapHelper->getClassMapping('clearfix'); ?>">
			<?php echo HTMLHelper::_('content.prepare', $this->params->get('intro_text')); ?>
		</div>
	<?php
	}
?>
<form action="<?php echo Route::_('index.php?option=com_osmembership&Itemid=' . $this->Itemid) ?>" method="post" name="osm_form" id="osm_form" autocomplete="off" enctype="multipart/form-data" class="<?php echo $bootstrapHelper->getClassMapping('form form-horizontal'); ?>">
	<?php
    $numberTabs = 0;
    $pluginExists = false;

    foreach ($this->plugins as $plugin)
    {
        if (!empty($plugin['form']))
        {
            $pluginExists = true;
            $numberTabs = 1;
            break;
        }
    }

    if ($this->params->get('show_edit_profile', 1))
    {
        $numberTabs++;
    }

    if ($this->params->get('show_my_subscriptions', 1))
    {
        $numberTabs++;
    }

    if ($this->params->get('show_subscriptions_history', 1))
    {
        $numberTabs++;
    }

	$showTabs = $numberTabs > 1;

    if ($this->params->get('show_edit_profile', 1))
    {
        $activeTab = 'profile-page';
    }
    elseif ($this->params->get('show_my_subscriptions', 1))
    {
        $activeTab = 'my-subscriptions-page';
    }
    else
    {
        $activeTab = 'subscription-history-page';
    }

	if ($showTabs)
    {
	    echo HTMLHelper::_($tabApiPrefix . 'startTabSet', 'osm-profile', array('active' => $activeTab));
    }

    if ($this->params->get('show_edit_profile', 1))
    {
        if ($showTabs)
        {
	        echo HTMLHelper::_($tabApiPrefix . 'addTab', 'osm-profile', 'profile-page', Text::_('OSM_EDIT_PROFILE', true));
        }

	    $profileLayoutData = array(
		    'controlGroupClass' => $controlGroupClass,
		    'controlLabelClass' => $controlLabelClass,
		    'controlsClass' => $controlsClass,
		    'bootstrapHelper' => $bootstrapHelper,
		    'btnClass' => $btnClass,
	    );

	    echo $this->loadTemplate('profile', $profileLayoutData);

	    if ($showTabs)
	    {
		    echo HTMLHelper::_($tabApiPrefix . 'endTab');
	    }
    }

    if ($this->params->get('show_my_subscriptions', 1))
    {
        if ($showTabs)
        {
	        echo HTMLHelper::_($tabApiPrefix . 'addTab', 'osm-profile', 'my-subscriptions-page', Text::_('OSM_MY_SUBSCRIPTIONS', true));
        }

	    echo $this->loadTemplate('subscriptions');

        if ($showTabs)
        {
	        echo HTMLHelper::_($tabApiPrefix . 'endTab');
        }
    }

	if ($this->params->get('show_subscriptions_history', 1))
    {
        if ($showTabs)
        {
	        echo HTMLHelper::_($tabApiPrefix . 'addTab', 'osm-profile', 'subscription-history-page', Text::_('OSM_SUBSCRIPTION_HISTORY', true));
        }

	    $layoutData = array(
		    'showPagination' => false,
	    );
	    echo $this->loadCommonLayout('common/tmpl/subscriptions_history.php', $layoutData);

	    if ($showTabs)
        {
	        echo HTMLHelper::_($tabApiPrefix . 'endTab');
        }
    }

	if ($pluginExists)
	{
		$count = 0 ;

		foreach ($this->plugins as $plugin)
		{
			$count++ ;

			if (empty($plugin['form']))
			{
				continue;
			}

			echo HTMLHelper::_($tabApiPrefix . 'addTab', 'osm-profile', 'tab_' . $count, Text::_($plugin['title'], true));
			echo $plugin['form'];
			echo HTMLHelper::_($tabApiPrefix . 'endTab');
		}
	}

	if ($showTabs)
    {
	    echo HTMLHelper::_($tabApiPrefix . 'endTabSet');
    }
	?>
	<div class="clearfix"></div>
	<input type="hidden" name="cid[]" value="<?php echo $this->item->id; ?>" />
	<input type="hidden" name="task" value="profile.update" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>

<?php
// Renew Membership
if ($this->params->get('show_renew_options', 1) && $this->item->group_admin_id == 0 && count($this->planIds))
{
?>
	<form action="<?php echo Route::_('index.php?option=com_osmembership&task=register.process_renew_membership&Itemid=' . $this->Itemid, false, $ssl); ?>" method="post" name="osm_form_renew" id="osm_form_renew" autocomplete="off" class="<?php echo $bootstrapHelper->getClassMapping('form form-horizontal'); ?>">
		<h2 class="osm-form-heading"><?php echo Text::_('OSM_RENEW_MEMBERSHIP'); ?></h2>
		<?php echo $this->loadCommonLayout('common/tmpl/renew_options.php');?>
	</form>
<?php
}

// Upgrade Membership
if ($this->params->get('show_upgrade_options', 1) && $this->item->group_admin_id == 0 && !empty($this->upgradeRules))
{
?>
	<form action="<?php echo Route::_('index.php?option=com_osmembership&task=register.process_upgrade_membership&Itemid=' . $this->Itemid, false, $ssl); ?>" method="post" name="osm_form_update_membership" id="osm_form_update_membership" autocomplete="off" class="<?php echo $bootstrapHelper->getClassMapping('form form-horizontal'); ?>">
		<h2 class="osm-form-heading"><?php echo Text::_('OSM_UPGRADE_MEMBERSHIP'); ?></h2>
		<?php
			echo $this->loadCommonLayout('common/tmpl/upgrade_options.php');
		?>
		<div class="form-actions">
			<input type="submit" class="<?php echo $bootstrapHelper->getClassMapping('btn btn-primary'); ?>" value="<?php echo Text::_('OSM_PROCESS_UPGRADE'); ?>"/>
		</div>
	</form>
<?php
}
?>

<form action="<?php echo Route::_('index.php?option=com_osmembership&task=register.process_cancel_subscription&Itemid=' . $this->Itemid, false, $ssl); ?>" method="post" name="osm_form_cancel_subscription" id="osm_form_cancel_subscription" autocomplete="off" class="form form-horizontal">
	<input type="hidden" name="subscription_id" value="" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>

<form name="osm_logout_form" id="osm_logout_form" action="<?php echo Route::_('index.php?option=com_users&task=user.logout'); ?>" method="post">
    <input type="hidden" name="return" value="<?php echo base64_encode(Uri::root()); ?>" />
    <?php echo HTMLHelper::_('form.token'); ?>
</form>
</div>