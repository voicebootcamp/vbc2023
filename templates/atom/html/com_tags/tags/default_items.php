<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_tags
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');

// JHtml::_('behavior.caption');
JHtml::_('behavior.core');

// Get the user object.
$user = JFactory::getUser();

// Check if user is allowed to add/edit based on tags permissions.
$canEdit      = $user->authorise('core.edit', 'com_tags');
$canCreate    = $user->authorise('core.create', 'com_tags');
$canEditState = $user->authorise('core.edit.state', 'com_tags');

$columns = $this->params->get('tag_columns', 1);

// Avoid division by 0 and negative columns.
if ($columns < 1)
{
	$columns = 1;
}

$bsspans = floor(12 / $columns);

if ($bsspans < 1)
{
	$bsspans = 1;
}

$bscolumns = min($columns, floor(12 / $bsspans));
$n         = count($this->items);

JFactory::getDocument()->addScriptDeclaration("
		var resetFilter = function() {
		document.getElementById('filter-search').value = '';
	}
");

JFactory::getDocument()->addStyleDeclaration('
fieldset.filters {border:none;padding-left:0;}
#filter-search {background: #f5f6f8;}
table a {color: #666666;text-decoration: none;}
select#limit {
	background: #f5f6f8;
	padding: 4px 10px;
	color: #666;
	border: 1px solid #e5e5e5;
	font-size: inherit;
}
');

?>
<form action="<?php echo htmlspecialchars(JUri::getInstance()->toString()); ?>" method="post" name="adminForm" id="adminForm">
	<?php if ($this->params->get('filter_field') || $this->params->get('show_pagination_limit')) : ?>
		<fieldset class="filters btn-toolbar">
			<?php if ($this->params->get('filter_field')) : ?>
				<div class="qx-button-group">
					<input type="text" name="filter-search" id="filter-search" value="<?php echo $this->escape($this->state->get('list.filter')); ?>" class="qx-input" onchange="document.adminForm.submit();" title="<?php echo JText::_('COM_TAGS_FILTER_SEARCH_DESC'); ?>" placeholder="<?php echo JText::_('COM_TAGS_TITLE_FILTER_LABEL'); ?>" />
					<button type="button" name="filter-search-button" qx-tooltip="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>" onclick="document.adminForm.submit();" class="qx-button qx-button-default">
						<svg xmlns="http://www.w3.org/2000/svg" height="35" version="1.1" viewBox="-1 0 136 136.21852" width="35">
							<g id="surface1">
							<path d="M 93.148438 80.832031 C 109.5 57.742188 104.03125 25.769531 80.941406 9.421875 C 57.851562 -6.925781 25.878906 -1.460938 9.53125 21.632812 C -6.816406 44.722656 -1.351562 76.691406 21.742188 93.039062 C 38.222656 104.707031 60.011719 105.605469 77.394531 95.339844 L 115.164062 132.882812 C 119.242188 137.175781 126.027344 137.347656 130.320312 133.269531 C 134.613281 129.195312 134.785156 122.410156 130.710938 118.117188 C 130.582031 117.980469 130.457031 117.855469 130.320312 117.726562 Z M 51.308594 84.332031 C 33.0625 84.335938 18.269531 69.554688 18.257812 51.308594 C 18.253906 33.0625 33.035156 18.269531 51.285156 18.261719 C 69.507812 18.253906 84.292969 33.011719 84.328125 51.234375 C 84.359375 69.484375 69.585938 84.300781 51.332031 84.332031 C 51.324219 84.332031 51.320312 84.332031 51.308594 84.332031 Z M 51.308594 84.332031 " style=" stroke:none;fill-rule:nonzero;fill:rgb(0%,0%,0%);fill-opacity:1;" />
							</g>
						</svg>						
					</button>
					<button type="reset" name="filter-clear-button" qx-tooltip="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>" class="qx-button qx-button-secondary" onclick="resetFilter(); document.adminForm.submit();">
						<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="35" height="35"
							viewBox="0 0 492 492" style="enable-background:new 0 0 492 492;fill:#ffffff;" xml:space="preserve">
						<g>
							<g>
								<path d="M300.188,246L484.14,62.04c5.06-5.064,7.852-11.82,7.86-19.024c0-7.208-2.792-13.972-7.86-19.028L468.02,7.872
									c-5.068-5.076-11.824-7.856-19.036-7.856c-7.2,0-13.956,2.78-19.024,7.856L246.008,191.82L62.048,7.872
									c-5.06-5.076-11.82-7.856-19.028-7.856c-7.2,0-13.96,2.78-19.02,7.856L7.872,23.988c-10.496,10.496-10.496,27.568,0,38.052
									L191.828,246L7.872,429.952c-5.064,5.072-7.852,11.828-7.852,19.032c0,7.204,2.788,13.96,7.852,19.028l16.124,16.116
									c5.06,5.072,11.824,7.856,19.02,7.856c7.208,0,13.968-2.784,19.028-7.856l183.96-183.952l183.952,183.952
									c5.068,5.072,11.824,7.856,19.024,7.856h0.008c7.204,0,13.96-2.784,19.028-7.856l16.12-16.116
									c5.06-5.064,7.852-11.824,7.852-19.028c0-7.204-2.792-13.96-7.852-19.028L300.188,246z"/>
							</g>
						</g>
						<g>
						</g>
						<g>
						</g>
						<g>
						</g>
						<g>
						</g>
						<g>
						</g>
						<g>
						</g>
						<g>
						</g>
						<g>
						</g>
						<g>
						</g>
						<g>
						</g>
						<g>
						</g>
						<g>
						</g>
						<g>
						</g>
						<g>
						</g>
						<g>
						</g>
						</svg>						
					</button>
				</div>
			<?php endif; ?>
			<?php if ($this->params->get('show_pagination_limit')) : ?>
				<div class="qx-button-group qx-align-right">
					<?php echo $this->pagination->getLimitBox(); ?>
				</div>
			<?php endif; ?>
			<input type="hidden" name="filter_order" value="" />
			<input type="hidden" name="filter_order_Dir" value="" />
			<input type="hidden" name="limitstart" value="" />
			<input type="hidden" name="task" value="" />
			<div class="clearfix"></div>
		</fieldset>
	<?php endif; ?>
	<?php if ($this->items == false || $n === 0) : ?>
		<p><?php echo JText::_('COM_TAGS_NO_TAGS'); ?></p>
	<?php else : ?>
		<?php foreach ($this->items as $i => $item) : ?>
			<?php if ($n === 1 || $i === 0 || $bscolumns === 1 || $i % $bscolumns === 0) : ?>
				<ul class="qx-link-text qx-subnav" qx-margin>
			<?php endif; ?>
			<?php if ((!empty($item->access)) && in_array($item->access, $this->user->getAuthorisedViewLevels())) : ?>
				<li class="cat-list-row<?php echo $i % 2; ?>">
					<h3>
						<a href="<?php echo JRoute::_(TagsHelperRoute::getTagRoute($item->id . ':' . $item->alias)); ?>" class="qx-text-large qx-text-bold">
							<?php echo $this->escape($item->title); ?>
						</a>
					</h3>
			<?php endif; ?>
			<?php if ($this->params->get('all_tags_show_tag_image') && !empty($item->images)) : ?>
				<?php $images  = json_decode($item->images); ?>
				<span class="tag-body">
					<?php if (!empty($images->image_intro)) : ?>
						<?php $imgfloat = empty($images->float_intro) ? $this->params->get('float_intro') : $images->float_intro; ?>
						<div class="pull-<?php echo htmlspecialchars($imgfloat, ENT_QUOTES, 'UTF-8'); ?> item-image">
							<img
								<?php if ($images->image_intro_caption) : ?>
									<?php echo 'class="caption"' . ' title="' . htmlspecialchars($images->image_intro_caption, ENT_QUOTES, 'UTF-8') . '"'; ?>
								<?php endif; ?>
								src="<?php echo htmlspecialchars($images->image_intro, ENT_QUOTES, 'UTF-8'); ?>"
								alt="<?php echo htmlspecialchars($images->image_intro_alt, ENT_QUOTES, 'UTF-8'); ?>" />
						</div>
					<?php endif; ?>
				</span>
			<?php endif; ?>
			<?php if (($this->params->get('all_tags_show_tag_description', 1) && !empty($item->description)) || $this->params->get('all_tags_show_tag_hits')) : ?>
				<div class="caption">
					<?php if ($this->params->get('all_tags_show_tag_description', 1) && !empty($item->description)) : ?>
						<span class="tag-body">
							<?php echo JHtml::_('string.truncate', $item->description, $this->params->get('all_tags_tag_maximum_characters')); ?>
						</span>
					<?php endif; ?>
					<?php if ($this->params->get('all_tags_show_tag_hits')) : ?>
						<span class="list-hits badge badge-info">
							<?php echo JText::sprintf('JGLOBAL_HITS_COUNT', $item->hits); ?>
						</span>
					<?php endif; ?>
				</div>
			<?php endif; ?>
			</li>
			<?php if (($i === 0 && $n === 1) || $i === $n - 1 || $bscolumns === 1 || (($i + 1) % $bscolumns === 0)) : ?>
				</ul>
			<?php endif; ?>
		<?php endforeach; ?>
	<?php endif; ?>
	<?php // Add pagination links ?>
	<?php if (!empty($this->items)) : ?>
		<?php if (($this->params->def('show_pagination', 2) == 1 || ($this->params->get('show_pagination') == 2)) && ($this->pagination->pagesTotal > 1)) : ?>
			<div class="pagination">
				<?php if ($this->params->def('show_pagination_results', 1)) : ?>
					<p class="counter pull-right">
						<?php echo $this->pagination->getPagesCounter(); ?>
					</p>
				<?php endif; ?>
				<?php echo $this->pagination->getPagesLinks(); ?>
			</div>
		<?php endif; ?>
	<?php endif; ?>
</form>
