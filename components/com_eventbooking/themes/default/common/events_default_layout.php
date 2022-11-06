<?php
/**
 * @package        	Joomla
 * @subpackage		Event Booking
 * @author  		Tuan Pham Ngoc
 * @copyright    	Copyright (C) 2010 - 2022 Ossolution Team
 * @license        	GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die ;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Uri\Uri;

$return                  = base64_encode(Uri::getInstance()->toString());
$eventPropertiesPosition = (int) $this->params->get('event_properties_position', 0);

if (!$this->config->get('show_register_buttons', 1))
{
	$hideRegisterButtons = true;
}
else
{
	$hideRegisterButtons = false;
}

$linkThumbToEvent = $this->config->get('link_thumb_to_event_detail_page', 1);

/* @var EventbookingHelperBootstrap $bootstrapHelper */
$bootstrapHelper         = $this->bootstrapHelper;
$activeCategoryId        = $this->categoryId;

if ($eventPropertiesPosition === 0)
{
	$eventDescriptionClass = $bootstrapHelper->getClassMapping('span7');
	$eventPropertiesClass  = $bootstrapHelper->getClassMapping('span5');
}
else
{
	$eventDescriptionClass = $bootstrapHelper->getClassMapping('clearfix');
	$eventPropertiesClass  = $bootstrapHelper->getClassMapping('clearfix');
}

$rowFluidClass     = $bootstrapHelper->getClassMapping('row-fluid');
$btnClass          = $bootstrapHelper->getClassMapping('btn');
$btnPrimaryClass   = $bootstrapHelper->getClassMapping('btn-primary');
$iconPencilClass   = $bootstrapHelper->getClassMapping('icon-pencil');
$iconOkClass       = $bootstrapHelper->getClassMapping('icon-ok');
$iconRemoveClass   = $bootstrapHelper->getClassMapping('icon-remove');
$iconDownloadClass = $bootstrapHelper->getClassMapping('icon-download');
$clearfixClass     = $bootstrapHelper->getClassMapping('clearfix');

EventbookingHelperData::prepareDisplayData($this->items, $activeCategoryId, $this->config, $this->Itemid);
?>
<div id="eb-events">
	<?php
		for ($i = 0 , $n = count($this->items) ;  $i < $n ; $i++)
		{
			$event = $this->items[$i];

			$layoutData = [
				'item'                => $event,
				'config'              => $this->config,
				'isMultipleDate'      => $event->is_multiple_date,
				'canRegister'         => $event->can_register,
				'registrationOpen'    => $event->registration_open,
				'return'              => $return,
				'showInviteFriend'    => false,
				'waitingList'         => $event->waiting_list,
				'ssl'                 => (int) $this->config->use_https,
				'Itemid'              => $this->Itemid,
				'btnClass'            => $btnClass,
				'iconOkClass'         => $iconOkClass,
				'iconRemoveClass'     => $iconRemoveClass,
				'iconDownloadClass'   => $iconDownloadClass,
				'iconPencilClass'     => $iconPencilClass,
				'hideRegisterButtons' => $hideRegisterButtons,
			];

			$registerButtons = EventbookingHelperHtml::loadCommonLayout('common/buttons.php', $layoutData);

			$layoutData = [
				'item'           => $event,
				'config'         => $this->config,
				'location'       => $event->location,
				'showLocation'   => $this->config->show_location_in_category_view,
				'isMultipleDate' => $event->is_multiple_date,
				'nullDate'       => $this->nullDate,
				'Itemid'         => $this->Itemid,
			];

			$eventProperties = EventbookingHelperHtml::loadCommonLayout('common/event_properties.php', $layoutData);

			$cssClasses = ['eb-category-' . $event->category_id, 'eb-event'];

			if ($event->featured)
			{
				$cssClasses[] = 'eb-featured-event';
			}

			if ($event->published == 2)
			{
				$cssClasses[] = 'eb-cancelled-event';
			}

			$cssClasses[] = 'clearfix';
		?>
			<div class="<?php echo implode(' ', $cssClasses); ?>">
				<div class="eb-box-heading <?php echo $clearfixClass; ?>">
					<h2 class="eb-event-title pull-left">
						<?php
						if ($this->config->hide_detail_button !== '1')
						{
						?>
							<a href="<?php echo $event->url; ?>" title="<?php echo $event->title; ?>" class="eb-event-title-link">
								<?php echo $event->title; ?>
							</a>
						<?php
						}
						else
						{
						?>
							<?php echo $event->title; ?>
						<?php
						}
						?>
					</h2>
				</div>
				<div class="eb-description <?php echo $clearfixClass; ?>">
				<?php
				if (in_array($this->config->get('register_buttons_position', 0), array(1, 2)))
				{
				?>
					<div class="eb-taskbar eb-register-buttons-top <?php echo $clearfixClass; ?>">
						<ul>
							<?php
							echo $registerButtons;

							if ($this->config->hide_detail_button !== '1' || $event->is_multiple_date)
							{
							?>
								<li>
									<a class="<?php echo $btnClass; ?>" href="<?php echo $event->url; ?>">
										<?php echo $event->is_multiple_date ? Text::_('EB_CHOOSE_DATE_LOCATION') : Text::_('EB_DETAILS');?>
									</a>
								</li>
							<?php
							}
							?>
						</ul>
					</div>
					<?php
				}

				if ($eventPropertiesPosition === 0)
				{
				?>
					<div class="<?php echo $rowFluidClass; ?>">
				<?php
				}

				if ($eventPropertiesPosition == 1)
				{
				?>
					<div class="eb-event-properties-table <?php echo $eventPropertiesClass; ?>">
						<?php echo $eventProperties; ?>
					</div>
				<?php
				}
				?>
				<div class="eb-description-details <?php echo $eventDescriptionClass; ?>">
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

					echo $event->short_description;
					?>
				</div>
				<?php
				if (in_array($eventPropertiesPosition, [0, 2]))
				{
				?>
					<div class="eb-event-properties-table <?php echo $eventPropertiesClass; ?>">
						<?php echo $eventProperties; ?>
					</div>
				<?php
				}

				if ($eventPropertiesPosition == 0)
				{
				?>
				 </div>
				<?php
				}

				if ($this->config->display_ticket_types && !empty($event->ticketTypes))
				{
					echo EventbookingHelperHtml::loadCommonLayout('common/tickettypes.php', array('ticketTypes' => $event->ticketTypes, 'config' => $this->config, 'event' => $event));
				?>
					<div class="<?php echo $clearfixClass; ?>"></div>
				<?php
				}

				// Event message to tell user that they already registered, need to login to register or don't have permission to register...
				echo EventbookingHelperHtml::loadCommonLayout('common/event_message.php', array('config' => $this->config, 'event' => $event));

				if (in_array($this->config->get('register_buttons_position', 0), array(0, 2)))
				{
				?>
					<div class="eb-taskbar <?php echo $clearfixClass; ?>">
						<ul>
							<?php
							echo $registerButtons;

							if ($this->config->hide_detail_button !== '1' || $event->is_multiple_date)
							{
							?>
								<li>
									<a class="<?php echo $btnClass; ?>" href="<?php echo $event->url; ?>">
										<?php echo $event->is_multiple_date ? Text::_('EB_CHOOSE_DATE_LOCATION') : Text::_('EB_DETAILS');?>
									</a>
								</li>
							<?php
							}
							?>
						</ul>
					</div>
				<?php
				}
				?>
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
