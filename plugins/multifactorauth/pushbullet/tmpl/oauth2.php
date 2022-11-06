<?php
/**
 * @package   AkeebaLoginGuard
 * @copyright Copyright (c)2016-2022 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

// Prevent direct access
defined('_JEXEC') || die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

$baseURL = Uri::root();
$backend = Factory::getApplication()->isClient('administrator') ? 1 : 0;

$redirectURL = urlencode($baseURL . 'index.php?option=com_users&view=callback&task=callback&method=pushbullet');
$oauth2URL   = "https://www.pushbullet.com/authorize?client_id={$this->clientId}&redirect_uri=$redirectURL&response_type=code&state=$backend"

?>
<div id="loginguard-pushbullet-controls" style="margin: 0.5em 0">
	<a class="btn btn-primary btn-lg btn-big loginguard-button-primary-large" href="<?= $oauth2URL ?>">
		<span class="icon icon-lock"></span>
		<?= Text::_('PLG_MULTIFACTORAUTH_PUSHBULLET_LBL_OAUTH2BUTTON'); ?>
	</a>
</div>