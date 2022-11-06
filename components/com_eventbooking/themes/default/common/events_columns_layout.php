<?php
/**
 * @package        	Joomla
 * @subpackage		Event Booking
 * @author  		Tuan Pham Ngoc
 * @copyright    	Copyright (C) 2010 - 2022 Ossolution Team
 * @license        	GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

Factory::getDocument()->addScript(Uri::root(true) . '/media/com_eventbooking/assets/js/responsive-auto-height.min.js', [], ['defer' => true]);

/* @var EventbookingHelperBootstrap $bootstrapHelper */
$bootstrapHelper = $this->bootstrapHelper;

$return           = base64_encode(Uri::getInstance()->toString());
$linkThumbToEvent = $this->config->get('link_thumb_to_event_detail_page', 1);
$timeFormat       = $this->config->event_time_format ?: 'g:i a';
$dateFormat       = $this->config->date_format;
$numberColumns    = $this->params->get('number_columns', 2) ?: 2;

$rowFluidClass     = $bootstrapHelper->getClassMapping('row-fluid');
$btnClass          = $bootstrapHelper->getClassMapping('btn');
$btnInverseClass   = $bootstrapHelper->getClassMapping('btn-inverse');
$iconOkClass       = $bootstrapHelper->getClassMapping('icon-ok');
$iconRemoveClass   = $bootstrapHelper->getClassMapping('icon-remove');
$iconPencilClass   = $bootstrapHelper->getClassMapping('icon-pencil');
$iconDownloadClass = $bootstrapHelper->getClassMapping('icon-download');
$iconCalendarClass = $bootstrapHelper->getClassMapping('icon-calendar');
$iconMapMakerClass = $bootstrapHelper->getClassMapping('icon-map-marker');
$clearfixClass     = $bootstrapHelper->getClassMapping('clearfix');
$btnPrimaryClass   = $bootstrapHelper->getClassMapping('btn-primary');
$btnBtnPrimary     = $bootstrapHelper->getClassMapping('btn btn-primary');

$span             = 'span' . intval(12 / $numberColumns);
$span             = $bootstrapHelper->getClassMapping($span);
$numberEvents     = count($this->items);
$activeCategoryId = $this->categoryId;

EventbookingHelperData::prepareDisplayData($this->items, $activeCategoryId, $this->config, $this->Itemid);
?>
<div id="eb-events" class="<?php echo $rowFluidClass . ' ' . $clearfixClass; ?> eb-columns-layout-container">
	<?php
		$rowCount = 0;

		for ($i = 0 ;  $i < $numberEvents ; $i++)
		{
			$event = $this->items[$i];

			if ($i % $numberColumns == 0)
			{
				$rowCount++;
				$newRowClass = ' eb-first-child-of-new-row';
			}
			else
			{
				$newRowClass = '';
			}

			$cssClasses = ['eb-event-wrapper', 'eb-category-' . $event->category_id];

			if ($event->featured)
			{
				$cssClasses[] = 'eb-featured-event';
			}

			if ($event->published == 2)
			{
				$cssClasses[] = 'eb-cancelled-event';
			}

			$cssClasses[] = 'eb-event-box';
			$cssClasses[] = 'eb-event-' . $event->id;
			$cssClasses[] = $clearfixClass;
		?>
		<div class="<?php echo $span . $newRowClass; ?> eb-row-<?php echo $rowCount; ?>">
			<div class="<?php echo implode(' ', $cssClasses); ?>">
				<?php
				if (!empty($event->thumb_url))
				{
					if ($linkThumbToEvent)
					{
					?>
						<a href="<?php echo $event->url; ?>"><img src="<?php echo $event->thumb_url; ?>" class="eb-thumb-left" alt="<?php echo $event->title; ?>"/></a>
					<?php
					}
					else
					{
					?>
						<a href="<?php echo $event->image_url; ?>" class="eb-modal"><img src="<?php echo $event->thumb_url; ?>" class="eb-thumb-left" alt="<?php echo $event->title; ?>"/></a>
					<?php
					}
				}
				?>
				<h2 class="eb-event-title-container">
					<?php
					if ($this->config->hide_detail_button !== '1')
					{
					?>
						<a class="eb-event-title" href="<?php echo $event->url; ?>"><?php echo $event->title; ?></a>
					<?php
					}
					else
					{
						echo $event->title;
					}
					?>
				</h2>
				<div class="eb-event-date-time <?php echo $clearfixClass; ?>">
					<i class="<?php echo $iconCalendarClass; ?>"></i>
					<?php
					if ($event->event_date != EB_TBC_DATE)
					{
						echo HTMLHelper::_('date', $event->event_date, $dateFormat, null);
					}
					else
					{
						echo Text::_('EB_TBC');
					}

					if (strpos($event->event_date, '00:00:00') === false)
					{
					?>
						<span class="eb-time"><?php echo HTMLHelper::_('date', $event->event_date, $timeFormat, null) ?></span>
					<?php
					}

					if ($event->event_end_date != $this->nullDate)
					{
						if (strpos($event->event_end_date, '00:00:00') === false)
						{
							$showTime = true;
						}
						else
						{
							$showTime = false;
						}

						$startDate =  HTMLHelper::_('date', $event->event_date, 'Y-m-d', null);
						$endDate   = HTMLHelper::_('date', $event->event_end_date, 'Y-m-d', null);

						if ($startDate == $endDate)
						{
							if ($showTime)
							{
							?>
								-<span class="eb-time"><?php echo HTMLHelper::_('date', $event->event_end_date, $timeFormat, null) ?></span>
							<?php
							}
						}
						else
						{
							echo " - " . HTMLHelper::_('date', $event->event_end_date, $dateFormat, null);

							if ($showTime)
							{
							?>
								<span class="eb-time"><?php echo HTMLHelper::_('date', $event->event_end_date, $timeFormat, null) ?></span>
							<?php
							}
						}
					}
					?>
				</div>
				<div class="eb-event-location-price <?php echo $rowFluidClass . ' ' . $clearfixClass; ?>">
					<?php
					if ($event->location_id)
					{
					?>
						<div class="eb-event-location <?php echo $bootstrapHelper->getClassMapping('span9'); ?>">
							<i class="<?php echo $iconMapMakerClass; ?>"></i>
							<?php
							if ($event->location_address)
							{
							?>
								<a href="<?php echo Route::_('index.php?option=com_eventbooking&view=map&location_id=' . $event->location_id . '&tmpl=component'); ?>" class="eb-colorbox-map"><span><?php echo $event->location_name ; ?></span></a>
							<?php
							}
							else
							{
								echo $event->location_name;
							}
							?>
						</div>
					<?php
					}

					if ($event->priceDisplay)
					{
					?>
						<div class="eb-event-price <?php echo $btnPrimaryClass . ' ' . $bootstrapHelper->getClassMapping('span3 pull-right'); ?>">
							<span class="eb-individual-price"><?php echo $event->priceDisplay; ?></span>
						</div>
					<?php
					}
					?>
				</div>
				<div class="eb-event-short-description <?php echo $clearfixClass; ?>">
					<?php echo $event->short_description; ?>
				</div>
				<?php
					// Event message to tell user that they already registered, need to login to register or don't have permission to register...
					echo EventbookingHelperHtml::loadCommonLayout('common/event_message.php', array('config' => $this->config, 'event' => $event));
				?>
				<div class="eb-taskbar <?php echo $clearfixClass; ?>">
					<ul>
						<?php
						if ($this->config->get('show_register_buttons', 1) && !$event->is_multiple_date)
						{
							if ($event->can_register)
							{
								echo EventbookingHelperHtml::loadCommonLayout('common/buttons_register.php', ['item' => $event, 'config' => $this->config, 'Itemid' => $this->Itemid]);
							}
							elseif ($event->waiting_list && $event->registration_type != 3 && !EventbookingHelperRegistration::isUserJoinedWaitingList($event->id))
							{
								echo EventbookingHelperHtml::loadCommonLayout('common/buttons_waiting_list.php', ['item' => $event, 'config' => $this->config, 'Itemid' => $this->Itemid]);
							}
						}

						if ($this->config->hide_detail_button !== '1' || $event->is_multiple_date)
						{
						?>
							<li>
								<a class="<?php echo $btnClass ?>" href="<?php echo $event->url; ?>">
									<?php echo $event->is_multiple_date ? Text::_('EB_CHOOSE_DATE_LOCATION') : Text::_('EB_DETAILS');?>
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
	?>
</div>
<?php

// Add Google Structured Data
PluginHelper::importPlugin('eventbooking');
Factory::getApplication()->triggerEvent('onDisplayEvents', [$this->items]);

$equalHeightScript[] = 'window.addEventListener("load", function() {';

for ($i = 1; $i <= $rowCount; $i++)
{
	$equalHeightScript[] = 'new ResponsiveAutoHeight(".eb-row-' . $i . ' .eb-event-wrapper");';
}

$equalHeightScript[] = '});';

Factory::getDocument()->addScriptDeclaration(implode("\r\n", $equalHeightScript));
