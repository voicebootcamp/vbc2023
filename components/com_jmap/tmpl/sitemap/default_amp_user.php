<?php
/** 
 * @package JMAP::SITEMAP::components::com_jmap
 * @subpackage views
 * @subpackage sitemap
 * @subpackage tmpl
 * @author Joomla! Extensions Store 
 * @copyright (C) 2021 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );
use Joomla\CMS\Factory;
use Joomla\CMS\Filter\OutputFilter;
use JExtstore\Component\JMap\Administrator\Framework\Route\Helper as JMapRouteHelper;

$priority =  $this->sourceparams->get ( 'priority', '0.5' );
$changefreq = $this->sourceparams->get ( 'changefreq', 'daily' );
$excludePriority = $this->cparams->get ( 'disable_priority', 0 );
$excludeChangefreq = $this->cparams->get ( 'disable_changefreq', 0 );
$nullDate = Factory::getContainer()->get('DatabaseDriver')->getNullDate();

// Include common template init
include 'default_common_user.php';

if (count ( $this->source->data ) != 0) {  
	foreach ( $this->source->data as $elm ) {
		$title = isset($titleIdentifier) &&  $titleIdentifier != ''  ? $elm->{$titleIdentifier} : null;
		
		// Manage modified date if exists
		$modified = null;
		if(isset($elm->modified) && $elm->modified && $elm->modified != -1 && $elm->modified != $nullDate && !in_array($elm->modified, array('0000-00-00 00:00:00', '1000-01-01 00:00:00'))) {
			$timestamp = strtotime($elm->modified);
			$modified = gmdate('Y-m-d\TH:i:s\Z', $timestamp);
		} elseif(isset($elm->publish_up) && $elm->publish_up && $elm->publish_up != -1 && $elm->publish_up != $nullDate && !in_array($elm->publish_up, array('0000-00-00 00:00:00', '1000-01-01 00:00:00'))) {
			$timestamp = strtotime($elm->publish_up);
			$modified = gmdate('Y-m-d\TH:i:s\Z', $timestamp);
		}
		
		// Additional fields
		$additionalQueryStringFromObjectProp = null;
		$objectVars = array_diff_key(get_object_vars($elm), $arrayKeysDiff);
		// Filter URL safe alias fields id/catid
		if(isset($objectVars[$idIdentifier]) && $idURLFilter) {
			$objectVars[$idIdentifier] = OutputFilter::stringURLSafe($objectVars[$idIdentifier]);
		}
		if(isset($objectVars[$catidIdentifier]) && $catidURLFilter) {
			$objectVars[$catidIdentifier] = OutputFilter::stringURLSafe($objectVars[$catidIdentifier]);
		}
		if(is_array($objectVars) && count($objectVars)) {
			$additionalQueryStringFromObjectProp = '&' . http_build_query($objectVars);
		}
		
		if(isset($supportedRouterHelperAdapters[$targetOption]) && $supportedRouterHelperAdapters[$targetOption]) {
			include 'adapters/'.$targetOption.'.php';
		} else {
			$guessedItemid = null;
			if($guessItemid) {
				$guessedItemid = JMapRouteHelper::getItemRoute($targetOption, $targetViewName, $elm->{$idIdentifier}, $elm, $mainTable);
				if($guessedItemid) {
					$guessedItemid = '&Itemid=' . $guessedItemid;
				}
			}
			$seflink = \JMapRoute::_ ( 'index.php?option=' . $targetOption . $targetView . $additionalQueryStringFromObjectProp . $additionalQueryStringParams . $guessedItemid);
		}
		
		// Manage SEF links replacements
		if($sefLinksReplacements) {
			$seflink = str_replace($sefLinksReplacements['source'], $sefLinksReplacements['target'], $seflink);
		}
		
		// Skip outputting
		if(array_key_exists($seflink, $this->outputtedLinksBuffer)) {
			continue;
		}
		// Else store to prevent duplication
		$this->outputtedLinksBuffer[$seflink] = true;
		?>
<url>
<loc><?php echo $this->sefSuffixEnabled ? $this->liveSite . str_ireplace('.html', '.' . $this->ampSuffix . '.html', $seflink) : $this->liveSite . $seflink . '/' . $this->ampSuffix; ?></loc>
<?php if(isset($modified) && trim($modified)):?>
<lastmod><?php echo $modified; ?></lastmod>
<?php endif; ?>
<?php if(!$excludeChangefreq):?>
<changefreq><?php echo $changefreq;?></changefreq>
<?php endif;?>
<?php if(!$excludePriority):?>
<priority><?php echo $priority;?></priority>
<?php endif;?>
</url>
<?php 
	}
}