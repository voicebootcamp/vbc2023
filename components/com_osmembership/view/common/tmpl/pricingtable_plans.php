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

$subscribedPlanIds = OSMembershipHelperSubscription::getSubscribedPlans();

if (empty($params))
{
	$params = Factory::getApplication()->getParams();
}

// Background color settings
$badgeBgColor            = $params->get('recommended_badge_background_color');
$headerBgColor           = $params->get('header_background_color');
$priceBgColor            = $params->get('price_background_color');
$recommendedPriceBgColor = $params->get('recommended_plan_price_background_color');

if (isset($input) && $input->getInt('recommended_plan_id'))
{
	$recommendedPlanId = $input->getInt('recommended_plan_id');
}
else
{
	$recommendedPlanId = (int) $params->get('recommended_campaign_id');
}

$showDetailsButton              = $params->get('show_details_button', 0);

if (isset($input) && $input->getInt('number_columns'))
{
    $numberColumns = $input->getInt('number_columns');
}
elseif (isset($config->number_columns))
{
	$numberColumns = $config->number_columns ;
}
else
{
	$numberColumns = 3 ;
}

$numberColumns = min($numberColumns, 4);

if (!isset($categoryId))
{
	$categoryId = 0;
}

$span = intval(12 / $numberColumns);

$btnClass        = $bootstrapHelper->getClassMapping('btn');
$btnPrimaryClass = $bootstrapHelper->getClassMapping('btn btn-primary');
$imgClass        = $bootstrapHelper->getClassMapping('img-polaroid');
$spanClass       = $bootstrapHelper->getClassMapping('span' . $span);

$rootUri       = Uri::root(true);
$i             = 0;
$numberPlans   = count($items);
$defaultItemId = $Itemid;

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
	}
	else
	{
		$recommended = false;
	}

	if ($recommended && $recommendedPriceBgColor)
	{
		$planPriceBackgroundColor = $recommendedPriceBgColor;
	}
	elseif ($priceBgColor)
	{
		$planPriceBackgroundColor = $priceBgColor;
	}
	else
	{
		$planPriceBackgroundColor =  '';
	}

	if ($i % $numberColumns == 0)
	{
	?>
		<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid clearfix'); ?> osm-pricing-table">
	<?php
	}
	?>
	<div class="<?php echo $spanClass; ?>">
		<div class="osm-plan<?php if ($recommended) echo ' osm-plan-recommended'; ?> osm-plan-<?php echo $item->id; ?>">
			<?php
				if ($recommended)
				{
				?>
					<p class="plan-recommended"<?php if ($badgeBgColor) echo ' style=" background-color:' . $badgeBgColor . '";'; ?>><?php echo Text::_('OSM_RECOMMENDED'); ?></p>
				<?php
				}
			?>
			<div class="osm-plan-header"<?php if ($headerBgColor) echo ' style=" background-color:' . $headerBgColor . '";'; ?>>
				<h2 class="osm-plan-title">
					<?php echo $item->title; ?>
				</h2>
			</div>
			<div class="osm-plan-price"<?php if ($planPriceBackgroundColor) echo ' style=" background-color:' . $planPriceBackgroundColor . '";'; ?>>
				<h2>
					<p class="price">
						<span>
						<?php
							if ($item->price > 0)
							{
								$symbol = $item->currency_symbol ? $item->currency_symbol : $item->currency;
								echo str_replace('.01', '.00', OSMembershipHelper::formatCurrency($item->price, $config, $symbol));
							}
							else
							{
								echo Text::_('OSM_FREE');
							}
							?>
						</span>
					</p>
				</h2>
			</div>
			<div class="osm-plan-short-description">
				<?php echo $item->short_description;?>
			</div>
			<?php
            $actions = OSMembershipHelperSubscription::getAllowedActions($item);

            if (count($actions) || $showDetailsButton)
			{
			    $language = Factory::getLanguage();
			?>
				 <ul class="osm-signup-container">
                     <?php
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
                             <a href="<?php echo $signUpUrl; ?>" class="<?php echo $btnPrimaryClass; ?> btn-singup">
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
                             <a href="<?php echo $link; ?>" class="<?php echo $btnPrimaryClass; ?> btn-singup">
			                     <?php echo Text::_($upgradeLanguageItem); ?>
                             </a>
                         </li>
	                     <?php
                     }

                     if ($showDetailsButton)
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
			<?php
			}
			?>
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
