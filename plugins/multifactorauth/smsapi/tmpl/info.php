<?php
/**
 * @package   AkeebaLoginGuard
 * @copyright Copyright (c)2016-2022 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\Plugin\Multifactorauth\Smsapi\Helper\SmsApi as ApiHelper;

$info = ApiHelper::splitPhoneNumber($phone ?? '');
$info['prefix'] = $info['prefix'] ? ('+' . $info['prefix']) : $info['prefix'];
$info['number'] = ApiHelper::redactNumber($info['number']);

?>
<div class="row mb-3">
	<label for="loginGuardSMSAPIPhone" class="col-sm-3 col-form-label">
		<?= Text::_('PLG_MULTIFACTORAUTH_SMSAPI_LBL_PHONE') ?>
	</label>
	<div class="col-sm-9">
		<div class="input-group">
			<?php if (!empty($info['prefix'])): ?>
			<span class="input-group-text bg-primary text-white"><?= $info['prefix'] ?></span>
			<?php endif; ?>
			<input type="text" name="redacted-phone" id="loginGuardSMSAPIPhone" value="<?= $info['number'] ?>" class="form-control" readonly />
		</div>
		<div class="form-text">
			<?= Text::_('PLG_MULTIFACTORAUTH_SMSAPI_LBL_PHONE_AFTERSETUP') ?>
		</div>
	</div>
</div>
