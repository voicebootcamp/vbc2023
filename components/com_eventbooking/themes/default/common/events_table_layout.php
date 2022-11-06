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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;

$cols                = 2;
$linkThumbToEvent    = $this->config->get('link_thumb_to_event_detail_page', 1);
$showAddEventsButton = false;

/* @var EventbookingHelperBootstrap $bootstrapHelper */
$bootstrapHelper  = $this->bootstrapHelper;
$hiddenPhoneClass = $bootstrapHelper->getClassMapping('hidden-phone');
$btnClass         = $bootstrapHelper->getClassMapping('btn');
$btnPrimary       = $bootstrapHelper->getClassMapping('btn btn-primary');

$activeCategoryId = $this->categoryId;

EventbookingHelperData::prepareDisplayData($this->items, $activeCategoryId, $this->config, $this->Itemid);
?>
<table class="<?php echo $bootstrapHelper->getClassMapping('table table-striped table-bordered table-condensed'); ?> eb-responsive-table">
	<thead>
		<tr>
		<?php
			if ($this->config->show_image_in_table_layout)
			{
				$cols++;
			?>
				<th class="<?php echo $hiddenPhoneClass; ?> eb-event-image-column">
					<?php echo Text::_('EB_EVENT_IMAGE'); ?>
				</th>
			<?php
			}
		?>
		<th>
			<?php echo Text::_('EB_EVENT'); ?>
		</th>
		<th class="date_col eb-event-date-column">
			<?php echo Text::_('EB_EVENT_DATE'); ?>
		</th>
		<?php
			if ($this->config->show_event_end_date_in_table_layout)
			{
				$cols++;
			?>
				<th class="date_col eb-event-end-date-column">
					<?php echo Text::_('EB_EVENT_END_DATE'); ?>
				</th>
			<?php
			}

			if ($this->config->show_location_in_category_view)
			{
				$cols++;
			?>
				<th class="location_col eb-event-location-column">
					<?php echo Text::_('EB_LOCATION'); ?>
				</th>
			<?php
			}

			if ($this->config->show_price_in_table_layout)
			{
				$cols++;
			?>
				<th class="table_price_col eb-event-price-column">
					<?php echo Text::_('EB_INDIVIDUAL_PRICE'); ?>
				</th>
			<?php
			}

			if ($this->config->show_capacity)
			{
				$cols++;
			?>
				<th class="capacity_col eb-event-capacity-column">
					<?php echo Text::_('EB_CAPACITY'); ?>
				</th>
			<?php
			}

			if ($this->config->show_registered)
			{
				$cols++;
			?>
				<th class="registered_col eb-event-registered-column">
					<?php echo Text::_('EB_REGISTERED'); ?>
				</th>
			<?php
			}

			if ($this->config->show_available_place)
			{
				$cols++;
			?>
				<th class="center available-place-col eb-event-available-place-column">
					<?php echo Text::_('EB_AVAILABLE_PLACE'); ?>
				</th>
			<?php
			}

			if ($this->config->get('show_register_buttons', 1) && $this->params->get('display_events_type') !== '3')
			{
				$cols++;
			?>
				<th class="center actions-col eb-register-buttons-column">
					<?php echo Text::_('EB_REGISTER'); ?>
				</th>
			<?php
			}
			?>
		</tr>
	</thead>
	<tbody>
	<?php
		for ($i = 0, $n = count($this->items); $i < $n; $i++)
		{
			$item = $this->items[$i];

			// Fake is_multiple_date to show register button for parent event on children events page
			if (!empty($displayChildrenEvents))
			{
				$item->is_multiple_date = false;
			}

			$cssClasses = ['eb-category-' . $item->category_id];

			if ($item->featured)
			{
				$cssClasses[] = 'eb-featured-event';
			}

			if ($item->published == 2)
			{
				$cssClasses[] = 'eb-cancelled-event';
			}
		?>
			<tr class="<?php echo implode(' ', $cssClasses); ?>">
				<?php
					if ($this->config->show_image_in_table_layout)
					{
					?>
						<td class="eb-image-column <?php echo $hiddenPhoneClass; ?>">
						<?php
							if (!empty($item->thumb_url))
							{
								if ($linkThumbToEvent)
								{
								?>
									<a href="<?php echo $item->url; ?>"><img src="<?php echo $item->thumb_url; ?>" class="eb-thumb-left" alt="<?php echo $item->title; ?>"/></a>
								<?php
								}
								else
								{
								?>
									<a href="<?php echo $item->image_url; ?>" class="eb-modal"><img src="<?php echo $item->thumb_url; ?>" class="eb-thumb-left" alt="<?php echo $item->title; ?>"/></a>
								<?php
								}
							}
							else
							{
								echo ' ';
							}
						?>
					</td>
					<?php
					}
				?>
				<td class="tdno<?php echo $i; ?>" data-content="<?php echo Text::_('EB_EVENT'); ?>">
					<?php
						if ($this->config->hide_detail_button !== '1')
						{
						?>
							<a href="<?php echo $item->url;?>" class="eb-event-link"><?php echo $item->title ; ?></a>
						<?php
						}
						else
						{
							echo $item->title;
						}
					?>
				</td>
				<td class="tdno<?php echo $i; ?>" data-content="<?php echo Text::_('EB_EVENT_DATE'); ?>">
					<?php
						if ($item->event_date == EB_TBC_DATE)
						{
							echo Text::_('EB_TBC');
						}
						elseif($item->event_date != $this->nullDate)
						{
							if (strpos($item->event_date, '00:00:00') !== false)
							{
								$dateFormat = $this->config->date_format;
							}
							else
							{
								$dateFormat = $this->config->event_date_format;
							}

							echo HTMLHelper::_('date', $item->event_date, $dateFormat, null);
						}
					?>
				</td>
				<?php
					if ($this->config->show_event_end_date_in_table_layout)
					{
					?>
						<td class="tdno<?php echo $i; ?>" data-content="<?php echo Text::_('EB_EVENT_END_DATE'); ?>">
							<?php
								if ($item->event_end_date == EB_TBC_DATE)
								{
									echo Text::_('EB_TBC');
								}
								elseif($item->event_end_date != $this->nullDate)
								{
									if (strpos($item->event_end_date, '00:00:00') !== false)
									{
										$dateFormat = $this->config->date_format;
									}
									else
									{
										$dateFormat = $this->config->event_date_format;
									}

									echo HTMLHelper::_('date', $item->event_end_date, $dateFormat, null);
								}
							?>
						</td>
					<?php
					}

					if ($this->config->show_location_in_category_view)
					{
					?>
					<td class="tdno<?php echo $i; ?>" data-content="<?php echo Text::_('EB_LOCATION'); ?>">
						<?php
							if ($item->location_id)
							{
								if ($item->location_address)
								{
									$location = $item->location;

									if ($location->image || EventbookingHelper::isValidMessage($location->description))
									{
									?>
										<a href="<?php echo Route::_('index.php?option=com_eventbooking&view=map&location_id=' . $item->location_id . '&Itemid=' . $this->Itemid); ?>"><?php echo $item->location_name ; ?></a>
									<?php
									}
									else
									{
									?>
										<a href="<?php echo Route::_('index.php?option=com_eventbooking&view=map&location_id=' . $item->location_id . '&Itemid=' . $this->Itemid . '&tmpl=component'); ?>" class="eb-colorbox-map"><?php echo $item->location_name ; ?></a>
									<?php
									}
								}
								else
								{
									echo $item->location_name;
								}
							}
							else
							{
								echo ' ';
							}
						?>
					</td>
					<?php
					}

					if ($this->config->show_price_in_table_layout)
					{
						if ($item->price_text || $item->individual_price > 0 || $this->config->show_price_for_free_event)
						{
							$showPrice = true;
						}
						else
						{
							$showPrice = false;
						}
					?>
						<td class="tdno<?php echo $i; ?>" data-content="<?php echo Text::_('EB_INDIVIDUAL_PRICE'); ?>">
							<?php
							if ($showPrice)
							{
								echo $item->priceDisplay;
							}
							?>
						</td>
					<?php
					}

					if ($this->config->show_capacity)
					{
					?>
						<td class="center tdno<?php echo $i; ?>" data-content="<?php echo Text::_('EB_CAPACITY'); ?>">
							<?php
								if ($item->event_capacity)
								{
									echo $item->event_capacity ;
								}
								elseif ($this->config->show_capacity != 2)
								{
									echo Text::_('EB_UNLIMITED') ;
								}
							?>
						</td>
					<?php
					}

					if ($this->config->show_registered)
					{
					?>
						<td class="center tdno<?php echo $i; ?>" data-content="<?php echo Text::_('EB_REGISTERED'); ?>">
							<?php
								if ($item->registration_type != 3)
								{
									echo $item->total_registrants ;
								}
								else
								{
									echo ' ';
								}

							?>
						</td>
					<?php
					}

					if ($this->config->show_available_place)
					{
					?>
						<td class="center tdno<?php echo $i; ?>" data-content="<?php echo Text::_('EB_AVAILABLE_PLACE'); ?>">
							<?php
								if ($item->event_capacity)
								{
									echo max($item->event_capacity - $item->total_registrants, 0);
								}
							?>
						</td>
					<?php
					}

					if ($this->config->get('show_register_buttons', 1) && $this->params->get('display_events_type') !== '3')
					{
					?>
						<td class="center">
							<?php
							if (!$item->is_multiple_date && ($item->waiting_list || $item->can_register || ($item->registration_type != 3 && $this->config->display_message_for_full_event)))
							{
								if ($item->can_register)
								{
									?>
									<div class="eb-taskbar">
										<ul>
											<?php
											if ($this->config->multiple_booking && $this->config->enable_add_multiple_events_to_cart)
											{
												$showCheckbox = true;

												if (!trim($item->registration_handle_url))
												{
													$showAddEventsButton = true;
												}
											}
											else
											{
												$showCheckbox = false;
											}

											echo EventbookingHelperHtml::loadCommonLayout('common/buttons_register.php', ['item' => $item, 'config' => $this->config, 'Itemid' => $this->Itemid, 'showCheckbox' => $showCheckbox]);
											?>
										</ul>
									</div>
									<?php
								}
								elseif ($item->registration_start_date != $this->nullDate && $item->registration_start_minutes < 0)
								{
									if (strpos($item->registration_start_date, '00:00:00') !== false)
									{
										$dateFormat = $this->config->date_format;
									}
									else
									{
										$dateFormat = $this->config->event_date_format;
									}

									echo Text::sprintf('EB_REGISTRATION_STARTED_ON', HTMLHelper::_('date', $item->registration_start_date, $dateFormat, null));
								}
								elseif($item->waiting_list && $item->registration_type != 3 && ! EventbookingHelperRegistration::isUserJoinedWaitingList($item->id))
								{
									if ($item->waiting_list_capacity == 0)
									{
										$numberWaitingListAvailable =  1000; // Fake number
									}
									else
									{
										$numberWaitingListAvailable = max($item->waiting_list_capacity - EventbookingHelperRegistration::countNumberWaitingList($item), 0);
									}
									?>
									<div class="eb-taskbar">
										<ul>
											<?php echo EventbookingHelperHtml::loadCommonLayout('common/buttons_waiting_list.php', ['item' => $item, 'config' => $this->config, 'Itemid' => $this->Itemid]); ?>
										</ul>
									</div>
									<?php
								}
								else
								{
									// Event message to tell user that they already registered, need to login to register or don't have permission to register...
									echo EventbookingHelperHtml::loadCommonLayout('common/event_message.php', array('config' => $this->config, 'event' => $item));
								}
							}

							if ($item->is_multiple_date)
							{
							?>
								<div class="eb-taskbar">
									<ul>
										<li>
											<a class="<?php echo $btnPrimary; ?>" href="<?php echo Route::_(EventbookingHelperRoute::getEventRoute($item->id, $this->categoryId, $this->Itemid));?>"><?php echo Text::_('EB_CHOOSE_DATE_LOCATION');  ?></a>
										</li>
									</ul>
								</div>
							<?php
							}
							?>
						</td>
					<?php
					}
				?>
			</tr>
		<?php
		}

		if ($showAddEventsButton)
		{
		?>
			<tr>
				<td colspan="<?php echo $cols ?>" style="text-align: right;"><input type="button" class="<?php echo $btnPrimary; ?>" onclick="addSelectedEventsToCart();" value="<?php echo Text::_('EB_ADD_EVENTS_TO_CART'); ?>" /></td>
			</tr>
		<?php
		}
	?>
	</tbody>
</table>
<?php
if ($showAddEventsButton)
{
	EventbookingHelperHtml::renderaddEventsToCartHiddenForm($this->Itemid);
}

// Add Google Structured Data
PluginHelper::importPlugin('eventbooking');
Factory::getApplication()->triggerEvent('onDisplayEvents', [$this->items]);
