<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2022 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

HTMLHelper::_('bootstrap.tooltip', '.hasTooltip', ['html' => true, 'sanitize' => false]);

$showAvatar              = $this->params->get('show_avatar', 1);
$showSubscriptionDate    = $this->params->get('show_subscription_date', 1);
$showSubscriptionEndDate = $this->params->get('show_subscription_end_date', 0);
$showMembershipId        = $this->params->get('show_membership_id', 0);
$hideEmptyFields         = $this->params->get('hide_empty_fields', 0);

$bootstrapHelper = OSMembershipHelperBootstrap::getInstance();
$rowFluidClass   = $bootstrapHelper->getClassMapping('row-fluid');
$span3Class      = $bootstrapHelper->getClassMapping('span3');
$span9Class      = $bootstrapHelper->getClassMapping('span9');
$imgCircle       = $bootstrapHelper->getClassMapping('img-circle');

$fields     = $this->fields;
$item       = $this->item;
$fieldsData = $this->data;

if (!$item->avatar)
{
	$item->avatar = 'no_avatar.jpg';
}
?>
<div id="osm-members-list" class="osm-container">
    <h1 class="osm-page-title"><?php echo Text::_('OSM_MEMBER_PROFILE') ; ?></h1>
	<div class="<?php echo $rowFluidClass; ?>">
		<?php
			if ($showAvatar)
			{
			?>
				<div class="<?php echo $span3Class; ?> avatar-container">
					<img class="oms-avatar <?php echo $imgCircle; ?>" src="<?php echo Uri::root(true) . '/media/com_osmembership/avatars/' . $item->avatar; ?>"/>
				</div>
				<div class="<?php echo $span9Class; ?>">
			<?php
			}
		?>
			<table class="table table-stripped osm-profile-data">
				<tr>
					<td class="osm-profile-field-title">
						<?php echo Text::_('OSM_NAME'); ?>:
					</td>
					<td class="osm-profile-field-value">
						<?php echo rtrim($item->first_name . ' ' . $item->last_name); ?>
					</td>
				</tr>
				<?php
                if ($showMembershipId)
                {
                ?>
                    <tr>
                        <td class="osm-profile-field-title">
                            <?php echo Text::_('OSM_MEMBERSHIP_ID'); ?>:
                        </td>
                        <td class="osm-profile-field-value">
                            <?php echo OSMembershipHelper::formatMembershipId($item, $this->config); ?>
                        </td>
                    </tr>
                <?php
                }

                if ($showSubscriptionDate)
				{
				?>
					<tr>
						<td class="osm-profile-field-title">
							<?php echo Text::_('OSM_SUBSCRIPTION_DATE'); ?>:
						</td>
						<td class="osm-profile-field-value">
							<?php echo HTMLHelper::_('date', $item->created_date, $this->config->date_format); ?>
						</td>
					</tr>
				<?php
				}

				if ($showSubscriptionEndDate)
				{
				?>
                    <tr>
                        <td class="osm-profile-field-title">
							<?php echo Text::_('OSM_SUBSCRIPTION_END_DATE'); ?>:
                        </td>
                        <td class="osm-profile-field-value">
							<?php echo HTMLHelper::_('date', $item->plan_subscription_to_date, $this->config->date_format); ?>
                        </td>
                    </tr>
				<?php
				}

				foreach($fields as $field)
				{
				   switch (strtolower($field->fieldtype))
                   {
                       case 'heading':
                            ?>
                           <tr>
                               <td colspan="2"><h3 class="osm-heading"><?php echo Text::_($field->title) ; ?></h3></td>
                           </tr>
                            <?php
                           break;
                       case 'message':
                            ?>
                           <tr>
                               <td colspan="2">
                                   <p class="osm-message"><?php echo $field->description ; ?></p>
                               </td>
                           </tr>
                            <?php
                           break;
                       default:
	                       if ($field->is_core)
	                       {
		                       $fieldValue = $item->{$field->name};
	                       }
                           elseif (isset($fieldsData[$field->name]))
	                       {
		                       $fieldValue = $fieldsData[$field->name];
	                       }
	                       else
	                       {
		                       $fieldValue = '';
	                       }

	                       if (is_string($fieldValue) && is_array(json_decode($fieldValue)))
	                       {
		                       $fieldValue = implode(', ', array_filter(json_decode($fieldValue)));
	                       }
                           elseif ($field->fieldtype == 'Date' && $fieldValue)
	                       {
		                       try
		                       {
			                       $fieldValue = HTMLHelper::_('date', $fieldValue, $this->config->date_format, null);
		                       }
		                       catch (Exception $e)
		                       {
			                       // Do-nothing
		                       }
	                       }
	                       elseif ($field->fieldtype == 'Textarea')
                           {
                               $fieldValue = nl2br($fieldValue);
                           }

						   if ($hideEmptyFields && strlen($fieldValue) == 0)
						   {
							   break;
						   }

	                       // Make the link and email click-able
	                       if (filter_var($fieldValue, FILTER_VALIDATE_URL))
	                       {
		                       $fieldValue = '<a href="' . $fieldValue . '" target="_blank">' . $fieldValue . '<a/>';
	                       }
                           elseif (filter_var($fieldValue, FILTER_VALIDATE_EMAIL))
	                       {
		                       $fieldValue = '<a href="mailto:' . $fieldValue . '">' . $fieldValue . '<a/>';
	                       }

	                       if (!$fieldValue)
	                       {
	                       	    break;
	                       }
	                       ?>
                           <tr>
                               <td class="osm-profile-field-title">
			                       <?php echo $field->title; ?>:
                               </td>
                               <td class="osm-profile-field-value">
			                       <?php echo $fieldValue; ?>
                               </td>
                           </tr>
	                       <?php
                            break;
                   }
				}
				?>
			</table>
		<?php
			if ($showAvatar)
			{
			?>
				</div>
			<?php
			}
		?>
	</div>
</div>