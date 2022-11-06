<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Maximenuck\CKFof;

$categories = JHtml::_('category.options', 'com_content');
?>
<div class="ckinterface ckinterface-labels-big">
	<input id="type" name="type" class="" value="joomshopping" disabled type="hidden" />
	<input id="title" name="title" class="" value="<?php echo JText::_('MAXIMENUCK_JOOMSHOPPING_TYPE_SHORT'); ?>" disabled type="hidden" />
	<input id="desc" name="desc" class="" value="" disabled type="hidden" />
	<input id="thirdparty" name="thirdparty" class="" value="1" disabled type="hidden" />
	<div class="ck-title"><?php echo JText::_('CK_OPTIONS'); ?></div>
	<div>
		<span class="ckoption-label">
			<?php echo JText::_('JCATEGORY'); ?>
		</span>
		<span class="ckoption-field">
			<select id="catid" name="catid" style="min-width:175px;">
				<?php 
				echo '<option value="0">' . JText::_('CK_ALL') . '</option>';
				foreach ($categories as $cat) {
					echo '<option value="' . $cat->value . '">' . $cat->text . '</option>';
				}
				?>
			</select>
		</span>
		<div class="clr"></div>
	</div>
	<div>
		<span class="ckoption-label">
			<?php echo JText::_('MAXIMENUCK_JOOMSHOPPING_CATEGORY_FIELD_CATDEPTH_LABEL'); ?>
		</span>
		<span class="ckoption-field">
			<input class="inputbox" type="text" name="levels" id="levels" placeholder="1" />
		</span>
		<div class="clr"></div>
	</div>
	<div>
		<span class="ckoption-label">
			<?php echo JText::_('MAXIMENUCK_JOOMSHOPPING_CATEGORY_FIELD_ARTICLEORDERING_LABEL'); ?>
		</span>
		<span class="ckoption-field">
			<select class="inputbox" type="list" value="1" name="show_child_category_joomshopping" id="show_child_category_joomshopping" style="width:175px;">
				<option value="a.ordering"><?php echo JText::_('MAXIMENUCK_JOOMSHOPPING_CATEGORY_OPTION_ORDERING_VALUE'); ?>
				</option>
				<option value="fp.ordering"><?php echo JText::_('MAXIMENUCK_JOOMSHOPPING_CATEGORY_OPTION_ORDERINGFEATURED_VALUE'); ?></option>
				<option value="a.hits"><?php echo JText::_('MAXIMENUCK_JOOMSHOPPING_CATEGORY_OPTION_HITS_VALUE'); ?>
				</option>
				<option value="a.title"><?php echo JText::_('JGLOBAL_TITLE'); ?>
				</option>
				<option value="a.id"><?php echo JText::_('MAXIMENUCK_JOOMSHOPPING_CATEGORY_OPTION_ID_VALUE'); ?>
				</option>
				<option value="a.alias"><?php echo JText::_('JFIELD_ALIAS_LABEL'); ?>
				</option>
				<option value="a.created"><?php echo JText::_('MAXIMENUCK_JOOMSHOPPING_CATEGORY_OPTION_CREATED_VALUE'); ?>
				</option>
				<option value="modified"><?php echo JText::_('MAXIMENUCK_JOOMSHOPPING_CATEGORY_OPTION_MODIFIED_VALUE'); ?>
				</option>
				<option value="publish_up"><?php echo JText::_('MAXIMENUCK_JOOMSHOPPING_CATEGORY_OPTION_STARTPUBLISHING_VALUE'); ?>
				</option>
				<option value="a.publish_down"><?php echo JText::_('MAXIMENUCK_JOOMSHOPPING_CATEGORY_OPTION_FINISHPUBLISHING_VALUE'); ?>
				</option>
			</select>
		</span>
		<div class="clr"></div>
	</div>
	<div class="ck-title"><?php echo JText::_('CK_RESPONSIVE_SETTINGS'); ?></div>
	<div>
		<span class="ckoption-label">
			<?php echo JText::_('CK_MOBILE_ENABLE'); ?>
		</span>
		<span class="ckoption-field">
			<select class="inputbox" type="list" value="1" name="mobile" id="mobile" style="width:auto;">
				<option value="1"><?php echo JText::_('JYES'); ?></option>
				<option value="0"><?php echo JText::_('JNO'); ?></option>
			</select>
		</span>
		<div class="clr"></div>
	</div>
	<div>
		<span class="ckoption-label">
			<?php echo JText::_('CK_DESKTOP_ENABLE'); ?>
		</span>
		<span class="ckoption-field">
			<select class="inputbox" type="list" value="1" name="desktop" id="desktop" style="width:auto;">
				<option value="1"><?php echo JText::_('JYES'); ?></option>
				<option value="0"><?php echo JText::_('JNO'); ?></option>
			</select>
		</span>
		<div class="clr"></div>
	</div>
</div>
<script>
//ckLoadEdition();
//ckInitColorPickers();
//ckInitOptionsTabs();
//ckInitAccordions();
</script>