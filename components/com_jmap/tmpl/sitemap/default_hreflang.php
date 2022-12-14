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
use Joomla\CMS\Uri\Uri;
echo "<?xml version='1.0' encoding='UTF-8'?>" . PHP_EOL;
if($this->xslt) {
	echo "<?xml-stylesheet type='text/xsl' href='" . Uri::root() . "components/com_jmap/xslt/xml-hreflang-sitemap.xsl'?>" . PHP_EOL;
}
?>
<urlset xmlns:xhtml="http://www.w3.org/1999/xhtml" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php
foreach ( $this->data as $source ) {	
	// Strategy pattern source type template visualization
	if ($source->type) {
		$this->source = $source;
		$this->sourceparams = $source->params;
		$this->asCategoryTitleField = $this->findAsCategoryTitleField($source);
		if($this->sourceparams->get('hreflanginclude', 1)) {
			$subTemplateName = $this->_layout . '_hreflang_' . $source->type . '.php';
			if (file_exists ( JPATH_COMPONENT_SITE . '/tmpl/sitemap/' . $subTemplateName )) {
				echo $this->loadTemplate ( 'hreflang_' . $source->type );
			}
		}
	}
}
?>
</urlset>