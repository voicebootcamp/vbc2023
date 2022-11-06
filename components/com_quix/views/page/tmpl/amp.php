<?php
/**
* @package    com_quix
* @author     ThemeXpert <info@themexpert.com>
* @copyright  Copyright (C) 2015. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
* @version    1.0.0
*/
// No direct access
defined('_JEXEC') or die;
// System
$siteName = JFactory::getConfig()->get('sitename');
$uri = JUri::getInstance(true);
$uri->delVar('format', 'amp');
?>
<!doctype html>
<html ⚡="">

<head>
	<meta charset="utf-8">
	<title><?php echo $siteName; ?>
	</title>
	<link rel="canonical"
		href="<?php echo htmlspecialchars($uri->toString()); ?>">
	<meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1">
	<meta name="amp-google-client-id-api" content="googleanalytics">

	<style amp-boilerplate="">
		body {
			-webkit-animation: -amp-start 8s steps(1, end) 0s 1 normal both;
			-moz-animation: -amp-start 8s steps(1, end) 0s 1 normal both;
			-ms-animation: -amp-start 8s steps(1, end) 0s 1 normal both;
			animation: -amp-start 8s steps(1, end) 0s 1 normal both
		}

		@-webkit-keyframes -amp-start {
			from {
				visibility: hidden
			}

			to {
				visibility: visible
			}
		}

		@-moz-keyframes -amp-start {
			from {
				visibility: hidden
			}

			to {
				visibility: visible
			}
		}

		@-ms-keyframes -amp-start {
			from {
				visibility: hidden
			}

			to {
				visibility: visible
			}
		}

		@-o-keyframes -amp-start {
			from {
				visibility: hidden
			}

			to {
				visibility: visible
			}
		}

		@keyframes -amp-start {
			from {
				visibility: hidden
			}

			to {
				visibility: visible
			}
		}
	</style><noscript>
		<style amp-boilerplate="">
			body {
				-webkit-animation: none;
				-moz-animation: none;
				-ms-animation: none;
				animation: none
			}
		</style>
	</noscript>

	<script async src="https://cdn.ampproject.org/v0.js"></script>
	<script async custom-element="amp-youtube" src="https://cdn.ampproject.org/v0/amp-youtube-0.1.js" async=""></script>
	<script async custom-element="amp-iframe" src="https://cdn.ampproject.org/v0/amp-iframe-0.1.js" async=""></script>
	<script async custom-element="amp-form" src="https://cdn.ampproject.org/v0/amp-form-0.1.js" async=""></script>
	<script async custom-element="amp-sidebar" src="https://cdn.ampproject.org/v0/amp-sidebar-0.1.js" async=""></script>
	<script async custom-element="amp-video" src="https://cdn.ampproject.org/v0/amp-video-0.1.js"></script>

	<link href="https://fonts.googleapis.com/css?family=Playfair+Display:400,700%7CLora%7CLato" rel="stylesheet">

	<style amp-custom>
		<?php include JPATH_SITE . '/media/quix/assets/amp.css';?>
		.ampstart-headerbar {
			background:
				<?php echo $this->config->get('header_bg', '#ffffff', 'string'); ?>
			;
			color:
				<?php echo $this->config->get('header_color', '#222', 'string'); ?>
			;
		}

		body {
			font-family: Lora, serif;
		}
	</style>
</head>

<body id="quix-amp">
	<!-- Start Navbar -->
	<header class="ampstart-headerbar">
		<div role="button" aria-label="open sidebar" on="tap:header-sidebar.toggle" tabindex="0"
			class="ampstart-navbar-trigger pr2">
			☰
		</div>
		<a class="logo" href="<?php echo JUri::root(); ?>">
			<amp-img
				src="<?php echo JUri::root() . $this->config->get('header_logo', 'libraries/quixnxt/assets/images/quix-logo.png', 'string') ?>"
				width="<?php echo $this->config->get('logo_width', '100', 'string') ?>"
				height="<?php echo $this->config->get('logo_height', '63', 'string') ?>"
				layout="fixed" alt="<?php echo $siteName; ?>">
			</amp-img>
		</a>

	</header>

	<?php echo $this->loadTemplate('sidebar'); ?>

	<main id="content" role="main" class="">
		<article class="recipe-article">
			<header>
				<?php if (isset($this->image_intro) && $this->image_intro) { ?>
				<amp-img class="banner-img"
					src="<?php echo $this->image_intro ?>"
					width="100" height="80" layout="responsive"
					alt="<?php echo $siteName; ?>"></amp-img>
				<?php } ?>
				<h1>
					<?php echo $this->title; ?>
				</h1>
			</header>

			<section class="qx-container">
				<?php print($this->amp_html); ?>
			</section>

		</article>
	</main>
	<!-- Start Footer -->
	<footer class="ampstart-footer flex flex-column items-center px3 ">
		<small>
			© <?php echo $siteName; ?>
		</small>
	</footer>
	<!-- End Footer -->
</body>

</html>
