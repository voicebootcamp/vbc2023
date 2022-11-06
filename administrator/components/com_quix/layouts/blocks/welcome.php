<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_messages
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
$session = JFactory::getSession();
$status  = $session->get('welcome-toolbar', 'open');
?>
<div id="qx-welcome-v3-wrapper" class="qx-position-relative">
  <div
          id="qx-welcome-v3"
          class="<?php echo $status === 'open' ? '' : 'qx-padding-small '; ?>qx-card qx-card-default qx-card-body qx-overflow-hidden qx-box-shadow-small qx-box-shadow-hover-medium"
          style="background-size: cover;background-image: url(<?php echo QuixAppHelper::getQuixMediaUrl(); ?>/images/banners/bg_gradient_1.png);">

    <div style="<?php echo $status === 'open' ? 'display: none' : ''; ?>" id="welcome-collapse">
      <div class="qx-grid" qx-grid>
        <div class="qx-width-1-1 qx-width-5-6@s qx-first-column qx-text-left@s qx-text-center">
          <h3>Welcome to Quix 4</h3>
        </div>
        <div class="qx-width-expand qx-flex qx-flex-center qx-flex-right@s">
          <a class="qx-button qx-button-default" href="#" onclick="javascript:window.toggleWelcome();">
            <i class="qxuicon-arrow-down qx-margin-small-right"></i>
            Expand
          </a>
        </div>
      </div>
    </div>

    <div style="<?php echo $status === 'collapse' ? 'display: none' : ''; ?>" id="welcome-content">
      <a class="qx-position-top-right qx-button" href="#" onclick="javascript:window.toggleWelcome(true);">
        <span class="qxuicon-arrow-up"></span>
      </a>
      <div class="qx-grid" qx-grid>
        <div class="qx-width-3-4 qx-first-column">
          <div class="qx-grid" qx-grid>
            <img
                    src="<?php echo QuixAppHelper::getQuixMediaUrl(); ?>/images/banners/avatar_1.svg" alt="Welcome to Quix 4"
                    style="margin: 0 0 -40px -50px; width: 280px;height: 212px;"
                    class="qx-width-expand"
            >
            <div class="qx-flex qx-flex-column qx-width-3-4">
              <div class="qx-margin-auto-vertical" qx-margin>
                <h1 class="qx-h1">Welcome to Quix 4</h1>
                <div>Experience the next generation of Joomla page builder.</div>

                <div>
                  <ul class="qx-nav qx-nav-default qx-subnav">
                    <li>
                      <a href="https://www.themexpert.com/docs" target="_blank">
                        <span class="qxuicon-book qx-margin-small-right"></span>Documentation
                      </a>
                    </li>
                    <li>
                      <a href="https://www.themexpert.com/video-tutorials" target="_blank">
                        <span class="qxuicon-video qx-margin-small-right"></span>Video Tutorials
                      </a>
                    </li>
                    <li>
                      <a href="https://www.themexpert.com/quix/layouts" target="_blank">
                        <span class="qxuicon-cloud qx-margin-small-right"></span>Template Cloud
                      </a>
                    </li>
                    <li>
                      <a href="https://www.facebook.com/groups/QuixUserGroup" target="_blank">
                        <span class="qxuicon-users qx-margin-small-right"></span>Facebook Group
                      </a>
                    </li>
                  </ul>
                </div>
                <div class="qx-margin-medium-top qx-flex">
                  <a class="qx-button qx-button-default" href="https://www.themexpert.com/quix/whats-new" target="_blank">What's New</a>
                  <a class="qx-button qx-margin-small-left" href="https://extensions.joomla.org/extension/quix/" target="_blank">
                    <span class="qxuicon-thumbs-up qx-margin-small-right"></span>Review Quix
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="qx-width-expand">
          <div class="qx-child-width-expand@s qx-grid qx-grid-small" qx-grid>
            <div class="qx-first-column qx-width-medium">
              <img
                      class="qx-hidden qx-margin-small-top qx-box-shadow-medium qx-box-shadow-hover-large"
                      src="<?php echo QuixAppHelper::getQuixMediaUrl(); ?>/images/banners/thumb_video.jpg" alt="Getting Started Video"
                      width="220" height="151"
              >
            </div>
            <div id="essentials-links">
              <ul class="qx-nav qx-nav-default">
                <li class="qx-nav-header">ThriveDesk</li>
                <li class="qx-margin-small-bottom">Customer support tools for your website.</li>
                <li>
                  <a href="https://www.youtube.com/watch?v=qKjHXH1nJH8" target="_blank">
                    <img
                            class="qx-margin-small-top qx-box-shadow-medium qx-box-shadow-hover-large"
                            src=" https://i.ytimg.com/vi/qKjHXH1nJH8/maxresdefault.jpg" alt="Getting Started Video"
                            width="220" height="151"
                    >
                  </a>
                </li>
                <li>
                  <a href="https://www.thrivedesk.com/?ref=thmx" target="_blank">Click here for more details</a>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<!--
TD advertisement
-->
<?php
return;
$session = JFactory::getSession();
$input  = JFactory::getApplication()->input;
$status  = $session->get('guide-quix', 'show');
// show_tour_guide
$config = JComponentHelper::getParams('com_quix');
$tourComplete = $config->get('guide-quix', 'show');
$tdModal = $config->get('td-modal', 'show');

if ($tourComplete !== 'hide' && $status !== 'hide') {
    return;
}
if($tdModal === 'hide'){
  return;
}
QuixHelper::updateComponentParams('td-modal', 'hide');
?>
<script>
    setTimeout(()=>qxUIkit.modal('#quix-td-modal-popup').show(), 1000);
</script>

<div id="quix-td-modal-popup" qx-modal>
  <div class="qx-modal-dialog" style="width:1024px;">

    <button class="qx-modal-close-default" type="button" qx-close></button>

    <div class="qx-modal-header">
      <h2>Hi, Parvez from ThemeXpert</h2>
    </div>

    <div class="qx-modal-body" qx-overflow-auto>

      <p>If customer support is pulling your business back, you may want to look at this,</p>

      <div class="qx-child-width-expand@s qx-text-center" qx-grid>
        <div>
          <div class="qx-card qx-card-default qx-card-body qx-padding-small">
            <img src="https://www.thrivedesk.com/wp-content/uploads/2022/08/benefits-of-livechat-software.jpg" alt="">
            <h3>9 Benefits of Having Live Chat on Your Website in 2022</h3>
            <p>In today’s fast-paced environment, users generally expect a business to act fast with instant customer support. According to Ifbyphone, 59% of customers are more likely</p>
            <a href="https://www.thrivedesk.com/benefits-of-livechat-software/">Read More »</a>
          </div>
        </div>
        <div>
          <div class="qx-card qx-card-default qx-card-body qx-padding-small">
            <img src="https://www.thrivedesk.com/wp-content/uploads/2022/08/why-live-chat-software.jpg" alt="">
            <h3>9 Benefits of Having Live Chat on Your Website in 2022</h3>
            <p>If you are the owner of an online business, you may already know that customers want answers to their questions right away, and any doubts</p>
            <a href="https://www.thrivedesk.com/why-live-chat-software-is-important/">Read More »</a>
          </div>
        </div>
        <div>
          <div class="qx-card qx-card-default qx-card-body qx-padding-small">
            <img src="https://www.thrivedesk.com/wp-content/uploads/2022/07/chatbot-vs-livechat.jpg" alt="">
            <h3>Chatbots vs Live Chat: Which One to Choose for Your Business</h3>
            <p>In  51% of cases, customers never go back to a business if they have had a bad experience at the initial go. This means, every</p>
            <a href="https://www.thrivedesk.com/chatbots-vs-livechat/">Read More »</a>
          </div>
        </div>
      </div>

      <p>Your online business must not suffer because of poor customers support. If you are planning to increase your business revenue, you may want to look at how your customer support is doing.</p>
      <iframe src="https://www.youtube-nocookie.com/embed/c2pz2mlSfXA?autoplay=0&amp;showinfo=0&amp;rel=0&amp;modestbranding=1&amp;playsinline=1" width="1920" height="1080" allowfullscreen uk-responsive uk-video="automute: true"></iframe>

      <p>At ThemeXpert, we have recently done extensive research on thriving customer support to increase business revenue. Feel free to read these resources::</p>

      <p>And if you want to do more with less money with your customer support, don’t forget to check out ThriveDesk.</p>

      <p>ThriveDesk is developed by the same group of people who made your favorite page builder, Quix</p>
    </div>

    <div class="qx-modal-footer qx-text-right">
      <button class="qx-button qx-button-default qx-modal-close" type="button">Cancel</button>
      <button class="qx-button qx-button-primary" type="button">Save</button>
    </div>

  </div>
</div>
