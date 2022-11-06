<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_menu
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$id = '';

if ($tagId = $params->get('tag_id', 'main_menu'))
{
	$id = ' id="' . $tagId . '"';
}
// The menu class is deprecated. Use nav instead
?>
<a href="#offcanvas-nav" qx-toggle class="qx-navbar-toggle qx-hidden@m">
	<span qx-navbar-toggle-icon></span>
</a>

<div id="offcanvas-nav" qx-offcanvas="overlay: true" class="qx-visible qx-hidden@m">
	<div class="qx-offcanvas-bar">
		<button class="qx-offcanvas-close" type="button" qx-close></button>

		<ul class="qx-nav qx-nav-default menu<?php echo $class_sfx; ?> mod-list"<?php echo $id; ?>>
			<?php foreach ($list as $i => &$item)
			{
				$class = 'item-' . $item->id;

				if ($item->id == $default_id)
				{
					$class .= ' qx-default';
				}

				if ($item->id == $active_id || ($item->type === 'alias' && $item->params->get('aliasoptions') == $active_id))
				{
					$class .= ' qx-current';
				}

				if (in_array($item->id, $path))
				{
					$class .= ' qx-active';
				}
				elseif ($item->type === 'alias')
				{
					$aliasToId = $item->params->get('aliasoptions');

					if (count($path) > 0 && $aliasToId == $path[count($path) - 1])
					{
						$class .= ' qx-active';
					}
					elseif (in_array($aliasToId, $path))
					{
						$class .= ' alias-parent-active';
					}
				}

				if ($item->type === 'separator')
				{
					$class .= ' qx-divider';
				}

				if ($item->deeper)
				{
					$class .= ' qx-deeper';
				}

				if ($item->parent)
				{
					$class .= ' qx-parent';
				}

				echo '<li class="' . $class . '">';

				switch ($item->type) :
					case 'separator':
					case 'component':
					case 'heading':
					case 'url':
						require JModuleHelper::getLayoutPath('mod_menu', 'default_' . $item->type);
						break;

					default:
						require JModuleHelper::getLayoutPath('mod_menu', 'default_url');
						break;
				endswitch;

				// The next item is deeper.
				if ($item->deeper)
				{
					echo '<ul class="qx-nav-sub">';
				}
				// The next item is shallower.
				elseif ($item->shallower)
				{
					echo '</li>';
					echo str_repeat('</ul></li>', $item->level_diff);
				}
				// The next item is on the same level.
				else
				{
					echo '</li>';
				}
			}
			?></ul>
	</div>
</div>

<!-- Display from medium devices-->
<nav class="qx-navbar-container" qx-navbar>
	<div class="qx-navbar-left">
		<ul class="qx-navbar-nav qx-visible@m menu<?php echo $class_sfx; ?> mod-list"<?php echo $id; ?>>
		<?php foreach ($list as $i => &$item)
		{
			$class = 'item-' . $item->id;

			if ($item->id == $default_id)
			{
				$class .= ' qx-default';
			}

			if ($item->id == $active_id || ($item->type === 'alias' && $item->params->get('aliasoptions') == $active_id))
			{
				$class .= ' qx-current';
			}

			if (in_array($item->id, $path))
			{
				$class .= ' qx-active';
			}
			elseif ($item->type === 'alias')
			{
				$aliasToId = $item->params->get('aliasoptions');

				if (count($path) > 0 && $aliasToId == $path[count($path) - 1])
				{
					$class .= ' qx-active';
				}
				elseif (in_array($aliasToId, $path))
				{
					$class .= ' alias-parent-active';
				}
			}

			if ($item->type === 'separator')
			{
				$class .= ' qx-divider';
			}

			if ($item->deeper)
			{
				$class .= ' qx-deeper';
			}

			if ($item->parent)
			{
				$class .= ' qx-parent';
			}

			echo '<li class="' . $class . '">';

			switch ($item->type) :
				case 'separator':
				case 'component':
				case 'heading':
				case 'url':
					require JModuleHelper::getLayoutPath('mod_menu', 'default_' . $item->type);
					break;

				default:
					require JModuleHelper::getLayoutPath('mod_menu', 'default_url');
					break;
			endswitch;

			// The next item is deeper.
			if ($item->deeper)
			{
				echo '<div class="qx-navbar-dropdown" qx-dropdown><ul class="qx-nav qx-navbar-dropdown-nav">';
			}
			// The next item is shallower.
			elseif ($item->shallower)
			{
				echo '</li>';
				echo str_repeat('</ul></div></li>', $item->level_diff);
			}
			// The next item is on the same level.
			else
			{
				echo '</li>';
			}
		}
		?></ul>
	</div>
</nav>
