<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2022 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die ;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

$articleId =  $this->plan->terms_and_conditions_article_id > 0 ? $this->plan->terms_and_conditions_article_id : $this->config->article_id;

if ($articleId > 0)
{
	if (Multilanguage::isEnabled())
	{
		$associations = Associations::getAssociations('com_content', '#__content', 'com_content.item', $articleId);
		$langCode     = Factory::getLanguage()->getTag();

		if (isset($associations[$langCode]))
		{
			$article = $associations[$langCode];
		}
	}

	if (!isset($article))
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id, catid')
			->from('#__content')
			->where('id = ' . (int) $articleId);
		$db->setQuery($query);
		$article = $db->loadObject();
	}

	if ($article)
	{
		JLoader::register('ContentHelperRoute', JPATH_ROOT . '/components/com_content/helpers/route.php');

		OSMembershipHelperModal::iframeModal('.osm-modal');
		?>
		<div class="<?php echo $controlGroupClass ?> osm-terms-and-conditions-container">
			<label class="checkbox">
				<input type="checkbox" id="osm-accept-terms-conditions" name="accept_term" value="1" class="validate[required]<?php echo $this->bootstrapHelper->getFrameworkClass('uk-checkbox', 1); ?>" />
				<?php echo Text::_('OSM_ACCEPT'); ?>&nbsp;<a href="<?php echo Route::_(ContentHelperRoute::getArticleRoute($article->id, $article->catid) . '&tmpl=component&format=html'); ?>" class="osm-modal"><?php echo Text::_('OSM_TERM_AND_CONDITION'); ?></a>
			</label>
		</div>
		<?php
	}
}
