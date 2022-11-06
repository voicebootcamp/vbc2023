<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$tparams = $this->item->params;

/**
 * Marker_class: Class based on the selection of text, none, or icons
 * jicon-text, jicon-none, jicon-icon
 */
?>
<div class="contact-address qx-width-1-1 qx-width-2-3@m" itemprop="address" itemscope itemtype="https://schema.org/PostalAddress">
	<?php if ($this->contact->con_position && $tparams->get('show_position')) : ?>
		<div class="contact-position">
			<div itemprop="jobTitle">
				<strong><?php echo JText::_('COM_CONTACT_POSITION'); ?>: </strong><?php echo $this->contact->con_position; ?>
			</div>
		</div>
	<?php endif; ?>

	<div class="qx-list qx-margin-small">
		<div>
		<?php if (($this->params->get('address_check') > 0) &&
			($this->contact->address || $this->contact->suburb  || $this->contact->state || $this->contact->country || $this->contact->postcode)) : ?>
				<strong class="<?php echo $this->params->get('marker_class'); ?>">
					<?php //echo $this->params->get('marker_address'); ?> Address: 
				</strong>

			<?php if ($this->contact->address && $this->params->get('show_street_address')) : ?>
					<span class="contact-street" itemprop="streetAddress">
						<?php echo nl2br($this->contact->address); ?>
					</span>
			<?php endif; ?>

			<?php if ($this->contact->suburb && $this->params->get('show_suburb')) : ?>

					<span class="contact-suburb" itemprop="addressLocality">
						<?php echo $this->contact->suburb; ?>
					</span>
			<?php endif; ?>
			<?php if ($this->contact->state && $this->params->get('show_state')) : ?>

					<span class="contact-state" itemprop="addressRegion">
						<?php echo $this->contact->state; ?>
					</span>
			<?php endif; ?>
			<?php if ($this->contact->postcode && $this->params->get('show_postcode')) : ?>

					<span class="contact-postcode" itemprop="postalCode">
						<?php echo $this->contact->postcode; ?>

					</span>
			<?php endif; ?>
			<?php if ($this->contact->country && $this->params->get('show_country')) : ?>

				<span class="contact-country" itemprop="addressCountry">
					<?php echo $this->contact->country; ?>
				</span>
			<?php endif; ?>
		<?php endif; ?>
		</div>

	<?php if ($this->contact->email_to && $this->params->get('show_email')) : ?>
		<div>
			<strong class="<?php echo $this->params->get('marker_class'); ?>" itemprop="email">
				<?php //echo nl2br($this->params->get('marker_email')); ?> E-mail: 
			</strong>
			<span class="contact-emailto">
				<?php echo $this->contact->email_to; ?>
			</span>			
		</div>
	<?php endif; ?>

	<?php if ($this->contact->telephone && $this->params->get('show_telephone')) : ?>
		<div>
			<strong class="<?php echo $this->params->get('marker_class'); ?>">
				<?php //echo $this->params->get('marker_telephone'); ?> Telephone: 
			</strong>
			<span class="contact-telephone" itemprop="telephone">
				<?php echo $this->contact->telephone; ?>
			</span>			
		</div>
	<?php endif; ?>
	<?php if ($this->contact->fax && $this->params->get('show_fax')) : ?>
		<div>
			<strong class="<?php echo $this->params->get('marker_class'); ?>">
				<?php //echo $this->params->get('marker_fax'); ?> Fax: 
			</strong>
			<span class="contact-fax" itemprop="faxNumber">
			<?php echo $this->contact->fax; ?>
			</span>			
		</div>
	<?php endif; ?>
	<?php if ($this->contact->mobile && $this->params->get('show_mobile')) : ?>
		<div>
			<strong class="<?php echo $this->params->get('marker_class'); ?>">
				<?php //echo $this->params->get('marker_mobile'); ?> Mobile: 
			</strong>
			<span class="contact-mobile" itemprop="telephone">
				<?php echo $this->contact->mobile; ?>
			</span>			
		</div>
	<?php endif; ?>
	<?php if ($this->contact->webpage && $this->params->get('show_webpage')) : ?>
		<div>
			<strong class="<?php echo $this->params->get('marker_class'); ?>">
				Website: 
			</strong>
			<span class="contact-webpage">
				<a href="<?php echo $this->contact->webpage; ?>" target="_blank" rel="noopener noreferrer" itemprop="url" class="qx-link-text">
				<?php echo JStringPunycode::urlToUTF8($this->contact->webpage); ?></a>
			</span>			
		</div>
	<?php endif; ?>

	<?php if ($tparams->get('allow_vcard')) : ?>
		<div>		
			<strong><?php echo JText::_('COM_CONTACT_DOWNLOAD_INFORMATION_AS'); ?></strong>
			<a href="<?php echo JRoute::_('index.php?option=com_contact&amp;view=contact&amp;id=' . $this->contact->id . '&amp;format=vcf'); ?>" class="qx-link-text">
			<?php echo JText::_('COM_CONTACT_VCARD'); ?></a>
		</div>
	<?php endif; ?>
	</div>
</div>
