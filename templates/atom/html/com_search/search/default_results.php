<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_search
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

?>
<div class="qx-grid qx-child-width-1-1 qx-grid-stack qx-margin-medium <?php echo $this->pageclass_sfx; ?>" qx-grid>
<?php foreach ($this->results as $result) : ?>
	<div class="qx-grid-margin">
		<article class="qx-article">
			<?php if ($result->href) : ?>
				<h2 class="qx-article-title qx-link-text"><a href="<?php echo JRoute::_($result->href); ?>"<?php if ($result->browsernav == 1) : ?> target="_blank"<?php endif; ?>>
					<?php // $result->title should not be escaped in this case, as it may ?>
					<?php // contain span HTML tags wrapping the searched terms, if present ?>
					<?php // in the title. ?>
					<?php echo $result->title; ?>
				</a></h2>
			<?php else : ?>
				<?php // see above comment: do not escape $result->title ?>
				<?php echo $result->title; ?>
			<?php endif; ?>

			<p>
				<?php echo $result->text; ?>
			</p>							
		</article>		
	</div>
<?php endforeach; ?>
</div>

<div class="qx-margin-large qx-margin-remove-bottom">
	<?php echo $this->pagination->getPagesLinks(); ?>
</div>
