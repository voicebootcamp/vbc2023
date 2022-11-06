<?php
/**
 * @copyright	Copyright (C) 2011-2020 Cedric KEIFLIN alias ced1870
 * https://www.joomlack.fr
 * Module Maximenu CK
 * @license		GNU/GPL
 * */

// no direct access
defined('_JEXEC') or die('Restricted access');

$close = '<span class="maxiclose">' . JText::_('MAXICLOSE') . '</span>';
$orientation_class = ( $params->get('orientation', 'horizontal') == 'vertical' ) ? 'maximenuckv' : 'maximenuckh';
$maximenufixedclass = ($params->get('menuposition', '0') == 'bottomfixed') ? ' maximenufixed' : '';
$start = (int) $params->get('startLevel');
$direction = $langdirection == 'rtl' ? 'right' : 'left';
$addclosingdiv = false;
?>
<!-- debut Maximenu CK -->
	<div class="<?php echo $orientation_class . ' ' . $langdirection ?><?php echo $maximenufixedclass ?>" id="<?php echo $params->get('menuid', 'maximenuck'); ?>" style="z-index:<?php echo $params->get('zindexlevel', '10'); ?>;">
			<?php require dirname(__FILE__) . '/_mobile.php'; ?>
			<ul<?php echo $microdata_ul ?> class="<?php echo $params->get('moduleclass_sfx'); ?> maximenuck<?php echo $params->get('calledfromlevel') ? '2' : '' ?>">
				<?php
				include dirname(__FILE__) . '/_logo.php';

				$zindex = 12000;
				foreach ($items as $i => &$item) {
					$item->mobile_data = isset($item->mobile_data) ? $item->mobile_data : '';
					// test if need to be dropdown
					//    $stopdropdown = ($item->level > 120) ? '-nodrop' : '';
					$itemlevel = ($start > 1) ? $item->level - $start + 1 : $item->level;
					$closeHtml = (($params->get('clickclose', '0') == '1' && $params->get('behavior', 'mouseover') == 'clickclose') || stristr($item->liclass, 'clickclose') != false) ? $close : '';

					if ($params->get('calledfromlevel')) {
						$itemlevel = $itemlevel + $params->get('calledfromlevel') - 1;
					}
					$stopdropdown = $params->get('stopdropdownlevel', '0');
					$stopdropdownclass = ($stopdropdown != '0' && $item->level >= $stopdropdown) ? ' nodropdown' : '';

					$createnewrow = (isset($item->createnewrow) AND $item->createnewrow) ? '<div style="clear:both;" class="ck-column-break"></div>' : '';
					$columnstyles = isset($item->columnwidth) ? ' style="width:' . modMaximenuckHelper::testUnit($item->columnwidth) . ';float:left;' . ($item->columnwidth == 'auto' ? 'flex: 1 1 auto;' : '') . '"' : '';
					$nextcolumnstyles = isset($item->nextcolumnwidth) ? ' style="width:' . modMaximenuckHelper::testUnit($item->nextcolumnwidth) . ';float:left;' . ($item->nextcolumnwidth == 'auto' ? 'flex: 1 1 auto;' : '') . '"' : '';

					if (isset($item->colonne) AND (isset($previous) AND !$previous->deeper)) {
						echo '</ul></div>' . $createnewrow . '<div class="maximenuck2" ' . $columnstyles . '><ul class="maximenuck2">';
					}
					// for 1st level1 item with column
					if (isset($item->colonne) AND $item->level === 1 AND !isset($previous)) {
						echo $createnewrow . '<li><div class="maximenuck2" ' . $columnstyles . '><ul class="maximenuck2">';
						$addclosingdiv = true;
					}
					if (isset($item->content) AND $item->content) {
						echo '<li data-level="' . $itemlevel . '" class="maximenuck maximenuckmodule' . $stopdropdownclass . $item->classe . ' level' . $itemlevel . ' ' . $item->liclass . '" ' . $item->mobile_data . '>' . $item->content;
						$item->ftitle = '';
					}


					if ($item->ftitle != "") {
						$title = $item->anchor_title ? ' title="' . $item->anchor_title . '"' : '';
						$description = $item->desc ? '<span class="descck">' . $item->desc . '</span>' : '';
						// manage HTML encapsulation
						$classcoltitle = $item->fparams->get('maximenu_classcoltitle', '') ? ' class="' . $item->fparams->get('maximenu_classcoltitle', '') . '"' : '';
						$opentag = (isset($item->tagcoltitle) AND $item->tagcoltitle != 'none') ? '<' . $item->tagcoltitle . $classcoltitle . '>' : '';
						$closetag = (isset($item->tagcoltitle) AND $item->tagcoltitle != 'none') ? '</' . $item->tagcoltitle . '>' : '';

						$linkrollover = '';
						// manage image
						require dirname(__FILE__) . '/_image.php';

						echo '<li'. $microdata_li .' data-level="' . $itemlevel . '" class="maximenuck' . $stopdropdownclass . $item->classe . ' level' . $itemlevel . ' ' . $item->liclass . '" style="z-index : ' . $zindex . ';" ' . $item->mobile_data . '>';
						require dirname(__FILE__) . '/_itemtype.php';
					}

					if ($item->deeper) {
						// set the styles for the submenus container
						if (isset($item->submenuswidth) || $item->leftmargin || $item->topmargin || $item->colbgcolor || isset($item->submenucontainerheight)) {
							$item->styles = "style=\"";
							$item->innerstyles = "style=\"";
							if ($item->leftmargin)
								$item->styles .= "margin-".$direction.":" . modMaximenuckHelper::testUnit($item->leftmargin) . ";";
							if ($item->topmargin)
								$item->styles .= "margin-top:" . modMaximenuckHelper::testUnit($item->topmargin) . ";";
							if (isset($item->submenuswidth))
								$item->innerstyles .= "width:" . modMaximenuckHelper::testUnit($item->submenuswidth) . ";";
							if (isset($item->colbgcolor) && $item->colbgcolor)
								$item->styles .= "background:" . $item->colbgcolor . ";";
							if (isset($item->submenucontainerheight) && $item->submenucontainerheight)
								$item->innerstyles .= "height:" . modMaximenuckHelper::testUnit($item->submenucontainerheight) . ";";
							$item->styles .= "\"";
							$item->innerstyles .= "\"";
						} else {
							$item->styles = "";
							$item->innerstyles = "";
						}
						echo "\n\t<div class=\"floatck\" " . $item->styles . ">" . $closeHtml . "<div class=\"maxidrop-main\" " . $item->innerstyles . "><div class=\"maximenuck2 first \" " . $nextcolumnstyles . ">\n\t<ul class=\"maximenuck2\">";
						// if (isset($item->coltitle))
						// echo $item->coltitle;
					}
					// The next item is shallower.
					elseif ($item->shallower) {
						echo "\n\t</li>";
						echo str_repeat("\n\t</ul>\n\t</div></div></div>\n\t</li>", $item->level_diff);
					}
					// the item is the last.
					elseif ($item->is_end) {
						echo str_repeat("</li>\n\t</ul>\n\t</div></div></div>", $item->level_diff);
						echo "</li>";
					}
					// The next item is on the same level.
					else {
						//if (!isset($item->colonne))
						echo "\n\t\t</li>";
					}

					$zindex--;
					$previous = $item;
				}
				if ($addclosingdiv === true) echo '</li></div>';
				?>
            </ul>
    </div>
    <!-- fin maximenuCK -->
