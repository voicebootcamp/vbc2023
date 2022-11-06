<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');

//JHtml::_('behavior.caption');
// If the page class is defined, add to class as suffix.
// It will be a separate class if the user starts it with a space
?>
<div class="blog-featured<?php echo $this->pageclass_sfx; ?>" itemscope itemtype="https://schema.org/Blog">
<?php if ($this->params->get('show_page_heading') != 0) : ?>
	<h1 class="qx-heading-small qx-margin-remove-top qx-margin-large"><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
<?php endif; ?>
<?php if ($this->params->get('page_subheading')) : ?>
	<h2>
		<?php echo $this->escape($this->params->get('page_subheading')); ?>
	</h2>
<?php endif; ?>
<?php $leadingcount = 0; ?>
<?php if (!empty($this->lead_items)) : ?>
<div class="qx-child-width-1-1 qx-grid-row-large" qx-grid>
	<?php foreach ($this->lead_items as &$item) : ?>
		<article class="qx-article leading-<?php echo $leadingcount; ?><?php echo $item->state == 0 ? ' system-unpublished' : null; ?> clearfix"
			itemprop="blogPost" itemscope itemtype="https://schema.org/BlogPosting">
			<meta property="name" content="<?php echo $this->escape($item->title) ?>">
			<meta property="author" typeof="Person" content="<?php echo $this->escape($item->author) ?>">
			<meta property="dateModified" content="<?php echo $this->escape($item->modified, 'c') ?>">
			<meta property="datePublished" content="<?php echo $this->escape($item->publish_up, 'c') ?>">
			<meta class="qx-margin-remove-adjacent" property="articleSection" content="<?php echo $this->escape($item->category_title) ?>">
			<?php
				$this->item = &$item;
				echo $this->loadTemplate('item');
			?>
		</article>
		<?php
			$leadingcount++;
		?>
	<?php endforeach; ?>
</div>
<?php endif; ?>
<?php
	$introcount = count($this->intro_items);
	$counter = 0;
?>
<?php if (!empty($this->intro_items)) : ?>
	<?php foreach ($this->intro_items as $key => &$item) : ?>

		<?php
		$key = ($key - $leadingcount) + 1;
		$rowcount = (((int) $key - 1) % (int) $this->columns) + 1;
		$row = $counter / $this->columns;

		if ($rowcount === 1) : ?>

		<div class="qx-child-width-1-<?php echo (int) $this->columns; ?>@m qx-child-width-1-1 qx-grid-row-large" qx-grid>
		<?php endif; ?>
			<article class="qx-article column-<?php echo $rowcount; ?><?php echo $item->state == 0 ? ' system-unpublished' : null; ?> qx-margin-remove-top"
				itemprop="blogPost" itemscope itemtype="https://schema.org/BlogPosting">
			<?php
					$this->item = &$item;
					echo $this->loadTemplate('item');
			?>
			</article>
			<?php $counter++; ?>

			<?php if (($rowcount == $this->columns) or ($counter == $introcount)) : ?>

		</div>
		<?php endif; ?>

	<?php endforeach; ?>
<?php endif; ?>

<?php if (!empty($this->link_items)) : ?>
	<div class="items-more">
	<?php echo $this->loadTemplate('links'); ?>
	</div>
<?php endif; ?>

<?php if ($this->params->def('show_pagination', 2) == 1  || ($this->params->get('show_pagination') == 2 && $this->pagination->pagesTotal > 1)) : ?>
	<div class="pagination">

		<?php if ($this->params->def('show_pagination_results', 1)) : ?>
			<p class="counter pull-right">
				<?php echo $this->pagination->getPagesCounter(); ?>
			</p>
		<?php endif; ?>
				<?php echo $this->pagination->getPagesLinks(); ?>
	</div>
<?php endif; ?>

</div>
