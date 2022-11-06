<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2022 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

$rootUri = Uri::root(true);

$subscribedPlanIds = OSMembershipHelperSubscription::getSubscribedPlans();

if (!isset($categoryId))
{
	$categoryId = 0;
}

$rowFluidClass   = $bootstrapHelper->getClassMapping('row-fluid');
$span7Class      = $bootstrapHelper->getClassMapping('span7');
$span5class      = $bootstrapHelper->getClassMapping('span5');
$imgClass        = $bootstrapHelper->getClassMapping('img-polaroid');
$btnClass        = $bootstrapHelper->getClassMapping('btn');
$btnPrimaryClass = $bootstrapHelper->getClassMapping('btn btn-primary');
$clearfixClass   = $bootstrapHelper->getClassMapping('clearfix');

$nullDate      = Factory::getDbo()->getNullDate();
$defaultItemId = $Itemid;

for ($i = 0 , $n = count($items) ;  $i < $n ; $i++)
{
	$item   = $items[$i];
	$Itemid = OSMembershipHelperRoute::getPlanMenuId($item->id, $item->category_id, $defaultItemId);

	if ($item->thumb)
	{
		$imgSrc = $rootUri . '/media/com_osmembership/' . $item->thumb;
	}

	if ($item->category_id)
	{
		$url = Route::_('index.php?option=com_osmembership&view=plan&catid=' . $item->category_id . '&id=' . $item->id . '&Itemid=' . $Itemid);
	}
	else
	{
		$url = Route::_('index.php?option=com_osmembership&view=plan&id=' . $item->id . '&Itemid=' . $Itemid);
	}

	if ($config->use_https)
	{
		$signUpUrl = Route::_(OSMembershipHelperRoute::getSignupRoute($item->id, $Itemid), false, 1);
	}
	else
	{
		$signUpUrl = Route::_(OSMembershipHelperRoute::getSignupRoute($item->id, $Itemid));
	}

	$symbol = $item->currency_symbol ? $item->currency_symbol : $item->currency;
	?>
	<div class="osm-item-wrapper <?php echo $clearfixClass; ?>">
		<div class="osm-item-heading-box <?php echo $clearfixClass; ?>">
			<h3 class="osm-item-title">
				<a href="<?php echo $url; ?>" title="<?php echo $item->title; ?>">
					<?php echo $item->title; ?>
				</a>
			</h3>
		</div>
		<div class="osm-item-description <?php echo $clearfixClass; ?>">
			<div class="<?php echo $rowFluidClass; ?>">
				<div class="osm-description-details <?php echo $span7Class; ?>">
					<?php
					if ($item->thumb)
					{
					?>
						<img src="<?php echo $imgSrc; ?>" alt="<?php echo $item->title; ?>" class="osm-thumb-left <?php echo $imgClass; ?>"/>
					<?php
					}

					if ($item->short_description)
					{
						echo $item->short_description;
					}
					else
					{
						echo $item->description;
					}
					?>
				</div>
				<div class="<?php echo $span5class; ?>">
					<table class="<?php echo $bootstrapHelper->getClassMapping('table table-striped table-bordered'); ?>">
						<?php
						if ($item->setup_fee > 0)
						{
						?>
							<tr class="osm-plan-property">
								<td class="osm-plan-property-label">
									<?php echo Text::_('OSM_SETUP_FEE'); ?>:
								</td>
								<td class="osm-plan-property-value">
									<?php
									echo OSMembershipHelper::formatCurrency($item->setup_fee, $config, $symbol);
									?>
								</td>
							</tr>
						<?php
						}

						if ($item->recurring_subscription && $item->trial_duration)
						{
						?>
							<tr class="osm-plan-property">
								<td class="osm-plan-property-label">
									<?php echo Text::_('OSM_TRIAL_DURATION'); ?>:
								</td>
								<td class="osm-plan-property-value">
									<?php
									if ($item->lifetime_membership)
									{
										echo Text::_('OSM_LIFETIME');
									}
									else
									{
										echo OSMembershipHelperSubscription::getDurationText($item->trial_duration, $item->trial_duration_unit);
									}
									?>
								</td>
							</tr>

							<tr class="osm-plan-property">
								<td class="osm-plan-property-label">
									<?php echo Text::_('OSM_TRIAL_PRICE'); ?>:
								</td>
								<td class="osm-plan-property-value">
									<?php
									if ($item->trial_amount > 0)
									{
										echo OSMembershipHelper::formatCurrency($item->trial_amount, $config, $symbol);
									}
									else
									{
										echo Text::_('OSM_FREE');
									}
									?>
								</td>
							</tr>
						<?php
						}

						if (!$item->expired_date || ($item->expired_date == $nullDate))
						{
						?>
							<tr class="osm-plan-property">
								<td class="osm-plan-property-label">
									<?php echo Text::_('OSM_DURATION'); ?>:
								</td>
								<td class="osm-plan-property-value">
									<?php
									if ($item->lifetime_membership)
									{
										echo Text::_('OSM_LIFETIME');
									}
									else
									{
										echo OSMembershipHelperSubscription::getDurationText($item->subscription_length, $item->subscription_length_unit);
									}
									?>
								</td>
							</tr>
						<?php
						}
						?>
						<tr class="osm-plan-property">
							<td class="osm-plan-property-label">
								<?php echo Text::_('OSM_PRICE'); ?>:
							</td>
							<td class="osm-plan-property-value">
								<?php
								if ($item->price > 0)
								{
									echo OSMembershipHelper::formatCurrency($item->price, $config, $symbol);
								}
								else
								{
									echo Text::_('OSM_FREE');
								}
								?>
							</td>
						</tr>
					</table>
				</div>
			</div>
			<div class="osm-taskbar <?php echo $clearfixClass; ?>">
				<ul>
					<?php
					$actions = OSMembershipHelperSubscription::getAllowedActions($item);

					if (count($actions))
					{
						$language = Factory::getLanguage();

						if (in_array('subscribe', $actions))
						{
							if ($language->hasKey('OSM_SIGNUP_PLAN_' . $item->id))
							{
								$signUpLanguageItem = 'OSM_SIGNUP_PLAN_' . $item->id;
							}
							else
							{
								$signUpLanguageItem = 'OSM_SIGNUP';
							}

							if ($language->hasKey('OSM_RENEW_PLAN_' . $item->id))
							{
								$renewLanguageItem = 'OSM_RENEW_PLAN_' . $item->id;
							}
							else
							{
								$renewLanguageItem = 'OSM_RENEW';
							}
							?>
								<li>
									<a href="<?php echo $signUpUrl; ?>" class="<?php echo $btnPrimaryClass; ?>">
										<?php echo in_array($item->id, $subscribedPlanIds) ? Text::_($renewLanguageItem) : Text::_($signUpLanguageItem); ?>
									</a>
								</li>
							<?php
						}

						if (in_array('upgrade', $actions))
						{
							if ($language->hasKey('OSM_UPGRADE_PLAN_' . $item->id))
							{
								$upgradeLanguageItem = 'OSM_UPGRADE_PLAN_' . $item->id;
							}
							else
							{
								$upgradeLanguageItem = 'OSM_UPGRADE';
							}

							if (count($item->upgrade_rules) > 1)
							{
								$link = Route::_('index.php?option=com_osmembership&view=upgrademembership&to_plan_id=' . $item->id . '&Itemid=' . OSMembershipHelperRoute::findView('upgrademembership', $Itemid));
							}
							else
							{
								$upgradeOptionId = $item->upgrade_rules[0]->id;
								$link            = Route::_('index.php?option=com_osmembership&task=register.process_upgrade_membership&upgrade_option_id=' . $upgradeOptionId . '&Itemid=' . $Itemid);
							}
							?>
							<li>
								<a href="<?php echo $link; ?>" class="<?php echo $btnPrimaryClass; ?>">
									<?php echo Text::_($upgradeLanguageItem); ?>
								</a>
							</li>
							<?php
						}
					}

					if (empty($config->hide_details_button))
					{
					?>
						<li>
							<a href="<?php echo $url; ?>" class="<?php echo $btnClass; ?>">
								<?php echo Text::_('OSM_DETAILS'); ?>
							</a>
						</li>
					<?php
					}
					?>
				</ul>
			</div>
		</div>
	</div>
	<?php
}
