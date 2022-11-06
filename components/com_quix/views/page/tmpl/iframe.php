<?php
/**
 * @version    1.8.0
 * @package    com_quix
 * @author     ThemeXpert <info@themexpert.com>
 * @copyright  Copyright (C) 2015. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access

defined('_JEXEC') or die;

$input = JFactory::getApplication()->input;;
$pageid = $input->get('id');
$tmploutput = $input->get('tmploutput', false);

$preview = '';
if ($this->item->builder == 'classic') {
    jimport( 'quix.vendor.autoload' );
    $preview = '&preview=true';

    // Load assets
    JFactory::getDocument()->addScript(JUri::root(true) . '/libraries/quix/assets/js/cookies.js');
    JFactory::getDocument()->addScript(JUri::root(true) . '/libraries/quix/assets/js/quix-toolbar.js');
    JFactory::getDocument()->addStylesheet(JUri::root(true) . '/libraries/quix/assets/css/quix-classic.css');
}

?>
<div class="qx-responsive-preview__window">
	<iframe
		src="<?php echo JUri::root() . '/index.php?option=com_quix' . $preview . '&view=page&id=' . $pageid . ($tmploutput ? '&tmpl=component' : '');?>"
		frameborder="0">
	</iframe>
</div>

<div class="qx-responsive-toolbar">
	<ul class="qx-devices">
		<li data-device="desktop" class="active" data-toggle="tooltip" title="Desktop">
			<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px"
				y="0px" viewBox="0 0 470 470" xml:space="preserve" width="16px" height="16px">
				<path
					d="M432.5,12.5h-395C16.822,12.5,0,29.323,0,50v300c0,20.677,16.822,37.5,37.5,37.5h145v55H130c-4.142,0-7.5,3.358-7.5,7.5     c0,4.142,3.358,7.5,7.5,7.5h210c4.142,0,7.5-3.358,7.5-7.5c0-4.142-3.358-7.5-7.5-7.5h-52.5v-55h145     c20.678,0,37.5-16.823,37.5-37.5V50C470,29.323,453.178,12.5,432.5,12.5z M272.5,442.5h-75v-55h75V442.5z M455,350     c0,12.406-10.093,22.5-22.5,22.5h-395C25.093,372.5,15,362.406,15,350v-22.5h440V350z M455,312.5H15V50     c0-12.406,10.093-22.5,22.5-22.5h395c12.407,0,22.5,10.094,22.5,22.5V312.5z" />
				<path
					d="M432.5,42.5h-395c-4.142,0-7.5,3.358-7.5,7.5v240c0,4.142,3.358,7.5,7.5,7.5h325c4.142,0,7.5-3.358,7.5-7.5     c0-4.142-3.358-7.5-7.5-7.5H45v-225h380v225h-32.5c-4.142,0-7.5,3.358-7.5,7.5c0,4.142,3.358,7.5,7.5,7.5h40     c4.142,0,7.5-3.358,7.5-7.5V50C440,45.858,436.642,42.5,432.5,42.5z" />
			</svg>
		</li>
		<li data-device="tablet" data-toggle="tooltip" title="Tablet-768px">
			<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px"
				y="0px" viewBox="0 0 470 470" xml:space="preserve" width="16px" height="16px">
				<path
					d="M432.5,47.5h-395C16.822,47.5,0,64.323,0,85v300c0,20.677,16.822,37.5,37.5,37.5h395c20.678,0,37.5-16.823,37.5-37.5V85     C470,64.323,453.178,47.5,432.5,47.5z M455,385c0,12.406-10.094,22.5-22.5,22.5h-395C25.093,407.5,15,397.406,15,385V85     c0-12.406,10.093-22.5,22.5-22.5h395c12.406,0,22.5,10.094,22.5,22.5V385z" />
				<path
					d="M402.5,107.5h-335c-4.142,0-7.5,3.358-7.5,7.5v240c0,4.142,3.358,7.5,7.5,7.5h265c4.143,0,7.5-3.358,7.5-7.5     c0-4.142-3.357-7.5-7.5-7.5H75v-225h320v225h-32.5c-4.143,0-7.5,3.358-7.5,7.5c0,4.142,3.357,7.5,7.5,7.5h40     c4.143,0,7.5-3.358,7.5-7.5V115C410,110.858,406.643,107.5,402.5,107.5z" />
				<circle cx="235" cy="85" r="7.5" />
			</svg>
		</li>
		<li data-device="phone" data-toggle="tooltip" title="Phone-480px">
			<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px"
				y="0px" viewBox="0 0 470 470" xml:space="preserve" width="16px" height="16px">
				<path
					d="M340,0H130c-20.678,0-37.5,16.823-37.5,37.5v395c0,20.677,16.822,37.5,37.5,37.5h210c20.678,0,37.5-16.823,37.5-37.5     v-395C377.5,16.823,360.678,0,340,0z M362.5,365H330c-4.142,0-7.5,3.358-7.5,7.5c0,4.142,3.358,7.5,7.5,7.5h32.5v52.5     c0,12.406-10.093,22.5-22.5,22.5H130c-12.407,0-22.5-10.094-22.5-22.5V380H300c4.142,0,7.5-3.358,7.5-7.5     c0-4.142-3.358-7.5-7.5-7.5H107.5V105h255V365z M362.5,90h-255V37.5c0-12.406,10.093-22.5,22.5-22.5h210     c12.407,0,22.5,10.094,22.5,22.5V90z" />
				<path
					d="M235,395c-12.407,0-22.5,10.094-22.5,22.5S222.593,440,235,440s22.5-10.094,22.5-22.5S247.407,395,235,395z M235,425     c-4.136,0-7.5-3.365-7.5-7.5s3.364-7.5,7.5-7.5s7.5,3.365,7.5,7.5S239.136,425,235,425z" />
				<path
					d="M265,45h-60c-4.142,0-7.5,3.358-7.5,7.5c0,4.142,3.358,7.5,7.5,7.5h60c4.142,0,7.5-3.358,7.5-7.5     C272.5,48.358,269.142,45,265,45z" />
			</svg>
		</li>
		<li data-toggle="tooltip" data-title="Toggle view component mode">
			<?php
            $uri = JUri::getInstance();
            if ($uri->getVar('tmploutput', false) == true) {
                $uri->setVar('tmploutput', false);
            } else {
                $uri->setVar('tmploutput', true);
            }
             ?>
			<a href="<?php echo $uri->toString();?>">
				<svg viewBox="0 0 17 16" xmlns="http://www.w3.org/2000/svg" version="1.1" width="16px" height="16px">
					<defs></defs>
					<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
						<g transform="translate(1.000000, 1.000000)" fill="#867e92">
							<path
								d="M15.8992941,11.815 L13.8738824,8.48 C13.6847059,8.168 13.2931765,8.079 13.0014118,8.281 C12.7077647,8.48 12.6249412,8.896 12.8141176,9.207 L14.2117647,11.564 L11.216,11.564 L11.2169412,10.125 C11.0964706,9.996 10.9072941,9.988 10.7943529,10.108 L8.75388235,11.935 C8.64188235,12.056 8.64847059,12.257 8.76894118,12.386 L10.7924706,13.861 C10.9138824,13.989 11.1030588,13.996 11.2150588,13.875 L11.2150588,12.849 L15.3694118,12.849 C15.6,12.849 15.8127059,12.715 15.9228235,12.499 C16.0329412,12.284 16.0244706,12.021 15.8992941,11.815 L15.8992941,11.815 Z"
								class="si-glyph-fill"></path>
							<path
								d="M4.94305882,11.608 L1.86070588,11.608 L3.54070588,8.856 L4.42352941,9.512 C4.59670588,9.482 4.71341176,9.316 4.68423529,9.142 L4.54870588,6.316 C4.52047059,6.143 4.35670588,6.031 4.18352941,6.062 L1.66870588,6.894 C1.49741176,6.929 1.38164706,7.095 1.41082353,7.266 L2.48188235,8.065 L0.181647059,11.866 C0.0517647059,12.078 0.0432941176,12.349 0.157176471,12.57 C0.271058824,12.794 0.488470588,12.931 0.725647059,12.931 L4.99952941,12.931 C5.35717647,12.931 5.64705882,12.621 5.64705882,12.24 C5.64705882,11.859 5.30164706,11.608 4.94305882,11.608 L4.94305882,11.608 Z"
								class="si-glyph-fill"></path>
							<path
								d="M9.02211765,5.617 L11.4795294,6.459 C11.6376471,6.491 11.7901176,6.372 11.8221176,6.193 L11.9792941,3.477 C12.0094118,3.298 11.9077647,3.129 11.7515294,3.098 L10.8517647,3.748 L8.80282353,0.378 C8.57129412,-0.004 7.97552941,-0.005 7.744,0.379 L5.84,3.512 C5.65082353,3.824 5.73458824,4.238 6.02729412,4.439 C6.32,4.64 6.71058824,4.551 6.89976471,4.239 L8.27388235,1.978 L9.80894118,4.503 L8.79623529,5.235 C8.76329412,5.415 8.864,5.585 9.02211765,5.617 L9.02211765,5.617 Z"
								class="si-glyph-fill"></path>
						</g>
					</g>
				</svg>
			</a>
		</li>
	</ul>
</div>
<script>
	jQuery(document).ready(function() {
		jQuery('[data-toggle="tooltip"]').tooltip()
	});
</script>
