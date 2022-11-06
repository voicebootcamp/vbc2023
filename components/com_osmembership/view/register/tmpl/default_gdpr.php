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
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

if ($this->config->show_privacy_policy_checkbox)
{
	if ($this->config->privacy_policy_url)
	{
		$link = $this->config->privacy_policy_url;
	}
	elseif ($this->config->privacy_policy_article_id > 0)
	{
		$privacyArticleId = $this->config->privacy_policy_article_id;

		if (Multilanguage::isEnabled())
		{
			$associations = JLanguageAssociations::getAssociations('com_content', '#__content', 'com_content.item', $privacyArticleId);
			$langCode     = Factory::getLanguage()->getTag();

			if (isset($associations[$langCode]))
			{
				$privacyArticle = $associations[$langCode];
			}
		}

		if (!isset($privacyArticle))
		{
			$db    = Factory::getDbo();
			$query = $db->getQuery(true);
			$query->select('id, catid')
				->from('#__content')
				->where('id = ' . (int) $privacyArticleId);
			$db->setQuery($query);
			$privacyArticle = $db->loadObject();
		}

		if ($privacyArticle)
		{
			JLoader::register('ContentHelperRoute', JPATH_ROOT . '/components/com_content/helpers/route.php');

			$link = Route::_(ContentHelperRoute::getArticleRoute($privacyArticle->id, $privacyArticle->catid) . '&tmpl=component&format=html');
		}
		else
		{
			$link = '';
		}
	}
	else
	{
		$link = '';
	}
	?>
    <div class="<?php echo $controlGroupClass ?> osm-privacy-policy">
        <div class="<?php echo $controlLabelClass; ?>">
            <label class="checkbox">
                <input type="checkbox" name="agree_privacy_policy" value="1" class="validate[required] checkbox<?php echo $this->bootstrapHelper->getFrameworkClass('uk-checkbox', 1); ?>" data-errormessage="<?php echo Text::_('OSM_AGREE_PRIVACY_POLICY_ERROR');?>" />
                <?php
                if ($link)
                {
                    if ($this->config->privacy_policy_url)
                    {
                    ?>
                        <a href="<?php echo $link; ?>" target="_blank"><?php echo Text::_('OSM_PRIVACY_POLICY');?></a>
                    <?php
                    }
                    else
                    {
						OSMembershipHelperModal::iframeModal('.osm-modal');
                    ?>
                        <a href="<?php echo $link; ?>" class="osm-modal"><?php echo Text::_('OSM_PRIVACY_POLICY');?></a>
                    <?php
                    }
                }
                else
                {
                    echo Text::_('OSM_PRIVACY_POLICY');
                }
                ?>
            </label>
        </div>
        <div class="<?php echo $controlsClass; ?>">
			<?php
			$agreePrivacyPolicyMessage = Text::_('OSM_AGREE_PRIVACY_POLICY_MESSAGE');

			if (strlen($agreePrivacyPolicyMessage))
			{
			?>
                <div class="osm-privacy-policy-message alert alert-info"><?php echo $agreePrivacyPolicyMessage;?></div>
			<?php
			}
			?>
        </div>
    </div>
	<?php
}

if ($this->config->show_subscribe_newsletter_checkbox && ($this->action != 'renew' || !$this->config->hide_newsletter_checkbox_on_renewal))
{
?>
    <div class="<?php echo $controlGroupClass ?> osm-subscribe-to-newsletter-container">
        <label class="checkbox" for="subscribe_to_newsletter">
            <input type="checkbox" name="subscribe_to_newsletter" id="subscribe_to_newsletter" value="1"<?php echo $this->bootstrapHelper->getFrameworkClass('uk-checkbox', 3); ?> />
            <?php echo Text::_('OSM_JOIN_NEWSLETTER'); ?>
        </label>
    </div>
<?php
}
