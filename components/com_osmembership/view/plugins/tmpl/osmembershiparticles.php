<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2022 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

/**
 * Layout variables
 * -----------------
 * @var   array $items
 */

JLoader::register('ContentHelperRoute', JPATH_ROOT . '/components/com_content/helpers/route.php');

$displayedArticleIds = [];
$bootstrapHelper     = OSMembershipHelperBootstrap::getInstance();
$centerClass         = $bootstrapHelper->getClassMapping('center');
?>
<table class="adminlist <?php echo $bootstrapHelper->getClassMapping('table table-striped table-bordered'); ?>"
       id="adminForm">
	<thead>
	<tr>
		<th class="title"><?php echo Text::_('OSM_TITLE'); ?></th>
		<th class="title"><?php echo Text::_('OSM_CATEGORY'); ?></th>
		<th class="<?php echo $centerClass; ?>"><?php echo Text::_('OSM_HITS'); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php
	foreach ($items as $item)
	{
		if (in_array($item->id, $displayedArticleIds))
		{
			continue;
		}

		$displayedArticleIds[] = $item->id;

		$articleLink = Route::_(ContentHelperRoute::getArticleRoute($item->id, $item->catid));
		?>
		<tr>
			<td><a href="<?php echo $articleLink ?>"><?php echo $item->title; ?></a></td>
			<td><?php echo $item->category_title; ?></td>
			<td class="<?php echo $centerClass; ?>">
				<?php echo $item->hits; ?>
			</td>
		</tr>
		<?php
	}
	?>
	</tbody>
</table>