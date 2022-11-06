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

/**
 * Layout variables
 *
 * @var OSMembershipHelperBootstrap $bootstrapHelper
 * @var array                       $items
 * @var stdClass                    $config
 * @var int                         $Itemid
 */

// Load equals height script
$rootUri = Uri::root(true);

$subscribedPlanIds = OSMembershipHelperSubscription::getSubscribedPlans();

if (empty($params))
{
	$params = Factory::getApplication()->getParams();
}

if (isset($input) && $input->getInt('recommended_plan_id'))
{
	$recommendedPlanId = $input->getInt('recommended_plan_id');
}
else
{
	$recommendedPlanId = (int) $params->get('recommended_campaign_id');
}

$standardPlanBackgroundColor    = $params->get('standard_plan_color', '#00B69C');
$recommendedPlanBackgroundColor = $params->get('recommended_plan_color', '#F75500');
$showDetailsButton              = $params->get('show_details_button', 0);

if (isset($input) && $input->getInt('number_columns'))
{
	$numberColumns = $input->getInt('number_columns');
}
elseif (isset($config->number_columns))
{
	$numberColumns = $config->number_columns;
}
else
{
	$numberColumns = 3;
}

$numberColumns = min($numberColumns, 5);

if (!isset($categoryId))
{
	$categoryId = 0;
}

$span      = intval(12 / $numberColumns);
$imgClass  = $bootstrapHelper->getClassMapping('img-polaroid');
$spanClass = $bootstrapHelper->getClassMapping('span' . $span);

$i             = 0;
$numberPlans   = count($items);
$defaultItemId = $Itemid;
$rootUri       = Uri::root(true);

$dec_point     = isset($config->dec_point) ? $config->dec_point : '.';
$thousands_sep = isset($config->thousands_sep) ? $config->thousands_sep : ',';

foreach ($items as $item)
{
	$Itemid = OSMembershipHelperRoute::getPlanMenuId($item->id, $item->category_id, $defaultItemId);

	if ($item->thumb)
	{
		$imgSrc = $rootUri . '/media/com_osmembership/' . $item->thumb;
	}

	$url = Route::_('index.php?option=com_osmembership&view=plan&catid=' . $item->category_id . '&id=' . $item->id . '&Itemid=' . $Itemid);

	if ($config->use_https)
	{
		$signUpUrl = Route::_(OSMembershipHelperRoute::getSignupRoute($item->id, $Itemid), false, 1);
	}
	else
	{
		$signUpUrl = Route::_(OSMembershipHelperRoute::getSignupRoute($item->id, $Itemid));
	}

	if (!$item->short_description)
	{
		$item->short_description = $item->description;
	}

	if ($item->id == $recommendedPlanId)
	{
		$recommended = true;
		$backgroundColor = $recommendedPlanBackgroundColor;
	}
	else
	{
		$recommended = false;
		$backgroundColor = $standardPlanBackgroundColor;
	}

	if ($i % $numberColumns == 0)
	{
	?>
		<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid clearfix'); ?> osm-pricing-table-flat">
	<?php
	}
	?>
	<div class="<?php echo $spanClass; ?>">
		<div class="osm-plan osm-plan-<?php echo $item->id; ?>" style="background-color: <?php echo $backgroundColor; ?>">
			<div class="osm-plan-header">
				<h2 class="osm-plan-title">
					<?php echo $item->title; ?>
				</h2>
			</div>
			<div class="osm-plan-price">
				<p class="price">
					<?php
					if ($item->lifetime_membership)
					{
						$subscriptionLengthText = Text::_('OSM_LIFETIME');
					}
					else
					{
						$subscriptionLengthText = OSMembershipHelperSubscription::getDurationText($item->subscription_length, $item->subscription_length_unit, false);
					}

					if ($item->price > 0)
					{
						$priceParts = explode('.', $item->price);

						if ($priceParts[1] == '00' || $config->decimals === '0')
						{
							$numberDecimals = 0;
						}
						else
						{
							$numberDecimals = 2;
						}

						$symbol = $item->currency_symbol ?: $item->currency;

						if (!$symbol)
						{
							$symbol = $config->currency_symbol;
						}

						if ($config->currency_position == 0)
						{
							echo $symbol . number_format($item->price, $numberDecimals, $dec_point, $thousands_sep) . ($subscriptionLengthText ? "<sub>/$subscriptionLengthText</sub>" : '');
						}
						else
						{
							echo number_format($item->price, $numberDecimals, $dec_point, $thousands_sep) . $symbol . ($subscriptionLengthText ? "<sub>/$subscriptionLengthText</sub>" : '');
						}
					}
					else
					{
						echo Text::_('OSM_FREE') . ($subscriptionLengthText ? "<sub> /$subscriptionLengthText</sub>" : '');
					}
					?>
				</p>
			</div>
			<div class="osm-plan-short-description">
				<?php echo $item->short_description;?>
			</div>
			<ul class="osm-signup-container">
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
                            <a href="<?php echo $signUpUrl; ?>" class="btn-signup">
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
                            <a href="<?php echo $link; ?>" class="btn-signup">
								<?php echo Text::_($upgradeLanguageItem); ?>
                            </a>
                        </li>
						<?php
					}
				}

				if ($showDetailsButton)
				{
				?>
					<li>
						<a href="<?php echo $url; ?>" class="btn-signup oms-btn-details">
							<?php echo Text::_('OSM_DETAILS'); ?>
						</a>
					</li>
				<?php
				}
			?>
			</ul>
		</div>
	</div>
	<?php
	if (($i + 1) % $numberColumns == 0)
	{
	?>
		</div>
	<?php
	}
	$i++;
}

if ($i % $numberColumns != 0)
{
	echo "</div>" ;
}
