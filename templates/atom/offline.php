<?php
/**
 * @package     Quix.Joomla Page Builder
 * @subpackage  Templates.Atom
 *
 * @copyright   Copyright (C) 2005 - 2020 ThemeXpert, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/** @var JDocumentHtml $this */

$twoFactorMethods = JAuthenticationHelper::getTwoFactorMethods();
$app              = JFactory::getApplication();

// Output as HTML5
$this->setHtml5(true);

// Add html5 shiv
JHtml::_('script', 'jui/html5.js', array('version' => 'auto', 'relative' => true, 'conditional' => 'lt IE 9'));

// Logo file or site title param
$siteName = htmlspecialchars($app->get('sitename'), ENT_QUOTES, 'UTF-8');

if ($this->params->get('logoFile')) {
    $logo = '<img src="'.JUri::root().$this->params->get('logoFile').'" alt="'.$siteName.'" />';
} elseif ($this->params->get('sitetitle')) {
    $logo = '<span class="site-title" title="'.$siteName.'">'.htmlspecialchars($this->params->get('sitetitle')).'</span>';
} else {
    $logo = '<span class="site-title" title="'.$siteName.'">'.$siteName.'</span>';
}
?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="<?php echo JUri::root().'?quix-asset=/css/quix-core.css&ver='.QUIXNXT_VERSION; ?>" rel="stylesheet" />
  <jdoc:include type="head" />
</head>
<body class="site">
<section class="qx-section qx-margin-left qx-margin-right">
  <div class="qx-margin-auto qx-container-small">
    <div class=" qx-card qx-card-body">
      <div class="header">
        <div class="qx-margin-large qx-text-center">
            <?php if ( ! empty($logo)) : ?>
              <h1><?php echo $logo; ?></h1>
            <?php else : ?>
              <h1><?php echo $siteName; ?></h1>
            <?php endif; ?>

            <?php if ($app->get('offline_image') && file_exists($app->get('offline_image'))) : ?>
              <img src="<?php echo $app->get('offline_image'); ?>" alt="<?php echo $siteName; ?>" />
            <?php endif; ?>
        </div>
        <div class="qx-margin">
            <?php if ($app->get('display_offline_message', 1) == 1 && str_replace(' ', '', $app->get('offline_message')) !== '') : ?>
              <p class="qx-alert"><?php echo $app->get('offline_message'); ?></p>
            <?php elseif ($app->get('display_offline_message', 1) == 2) : ?>
              <p class="qx-alert"><?php echo JText::_('JOFFLINE_MESSAGE'); ?></p>
            <?php endif; ?>
        </div>
      </div>

      <jdoc:include type="message" />

      <form class="qx-form" action="<?php echo JRoute::_('index.php', true); ?>" method="post" id="form-login">
        <div class="qx-card qx-card-default qx-card-body">
          <div class="qx-margin">
            <label class="qx-form-label" for="username"><?php echo JText::_('JGLOBAL_USERNAME'); ?></label>
            <input class="qx-input" name="username" id="username" type="text" title="<?php echo JText::_('JGLOBAL_USERNAME'); ?>" />

          </div>

          <div class="qx-margin">
            <label class="qx-form-label" for="password"><?php echo JText::_('JGLOBAL_PASSWORD'); ?></label>
            <input class="qx-input" type="password" name="password" id="password" title="<?php echo JText::_('JGLOBAL_PASSWORD'); ?>" />

          </div>

            <?php if (count($twoFactorMethods) > 1) : ?>
              <div class="qx-margin">
                <label class="qx-form-label" for="secretkey"><?php echo JText::_('JGLOBAL_SECRETKEY'); ?></label>
                <input class="qx-input" type="text" name="secretkey" autocomplete="one-time-code" id="secretkey"
                       title="<?php echo JText::_('JGLOBAL_SECRETKEY'); ?>" />
              </div>
            <?php endif; ?>

          <input type="submit" name="Submit" class="qx-button qx-button-primary" value="<?php echo JText::_('JLOGIN'); ?>" />

          <input type="hidden" name="option" value="com_users" />
          <input type="hidden" name="task" value="user.login" />
          <input type="hidden" name="return" value="<?php echo base64_encode(JUri::base()); ?>" />
            <?php echo JHtml::_('form.token'); ?>
        </div>
      </form>
    </div>
  </div>
</section>
</body>
</html>
