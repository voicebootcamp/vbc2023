<?php
/**
 * @package        Joomla
 * @subpackage     Events Booking
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2010 - 2022 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Uri\Uri;

$rootUri  = Uri::root(true);
$document = Factory::getDocument()
	->addStyleSheet($rootUri . '/modules/mod_eb_advslider/assets/splide/css/themes/' . $params->get('theme', 'splide-default.min.css'))
	->addStyleSheet($rootUri . '/modules/mod_eb_advslider/assets/css/styles.css')
	->addScript($rootUri . '/modules/mod_eb_advslider/assets/splide/js/splide.min.js');

EventbookingHelper::loadComponentCssForModules();

$config = EventbookingHelper::getConfig();
$return     = base64_encode(Uri::getInstance()->toString());
$timeFormat = $config->event_time_format ?: 'g:i a';
$dateFormat = $config->date_format;

/* @var EventbookingHelperBootstrap $bootstrapHelper */
$bootstrapHelper = EventbookingHelperBootstrap::getInstance();
$rowFluidClass     = $bootstrapHelper->getClassMapping('row-fluid');
$btnClass          = $bootstrapHelper->getClassMapping('btn');
$btnInverseClass   = $bootstrapHelper->getClassMapping('btn-inverse');
$iconOkClass       = $bootstrapHelper->getClassMapping('icon-ok');
$iconRemoveClass   = $bootstrapHelper->getClassMapping('icon-remove');
$iconPencilClass   = $bootstrapHelper->getClassMapping('icon-pencil');
$iconDownloadClass = $bootstrapHelper->getClassMapping('icon-download');
$iconCalendarClass = $bootstrapHelper->getClassMapping('icon-calendar');
$iconMapMakerClass = $bootstrapHelper->getClassMapping('icon-map-marker');
$clearfixClass     = $bootstrapHelper->getClassMapping('clearfix');
$btnPrimaryClass   = $bootstrapHelper->getClassMapping('btn-primary');
$btnBtnPrimary     = $bootstrapHelper->getClassMapping('btn btn-primary');

$linkThumbToEvent   = $config->get('link_thumb_to_event_detail_page', 1);


$activeCategoryId = 0;

EventbookingHelperData::prepareDisplayData($rows, $activeCategoryId, $config, $itemId);

if (EventbookingHelper::isValidMessage($params->get('pre_text')))
{
	echo $params->get('pre_text');
}
?>
<div class="eb-slider-container splide">
    <div class="splide__track">
        <ul class="splide__list">
	        <?php
	        foreach ($rows as $event)
	        {
		        require ModuleHelper::getLayoutPath('mod_eb_advslider', 'default_item');
	        }
	        ?>
        </ul>
    </div>
</div>
<?php
if (EventbookingHelper::isValidMessage($params->get('post_text')))
{
	echo $params->get('post_text');
}
?>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var splide = new Splide('.splide', <?php echo json_encode($sliderSettings) ?>);
        splide.mount();
    });
</script>