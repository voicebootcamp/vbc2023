<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_messages
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
$text = JText::_('COM_QUIX_CLEAR_CACHE_TITLE');
$text2 = JText::_('COM_QUIX_CLEAR_CACHE_PAGE_TITLE');
?>
<div class="clearcache-toolbar">
  <a onClick="quixClearCache();" id="clearQuixCache" href="javascript:void(0);"
     title="<?php echo $text; ?>" class="btn hasTooltip"
     >
    <i id="quixcacheicon" class="icon-loop" style="margin: 0px;"></i>
  </a>
    <div id="cacheCleanMessage" data-success="Cache has successfully cleaned."
      data-error="Something went wrong! Please reload the page and try again." data-colorFalse="#f57f17"
      data-colorTrue="#30c939b8" style="display: none;">
    </div>
  <style type="text/css">
    .clearcache-toolbar{
      position: relative;
    }
    #cacheCleanMessage {
      display: block;
      background: #30c939b8;
      color: #fff;
      font-size: 12px;
      border-radius: 3px;
      line-height: 1;
      padding: 9px 5px;
      margin: 0px;
      position: absolute;
      left: -200px;
      top: 10px;
    }

    .icon-spin {
      -webkit-animation: spin .5s infinite linear;
      animation: spin .5s infinite linear;
    }

    @-webkit-keyframes spin {
      0% {
        -webkit-transform: rotate(0deg);
      }

      100% {
        -webkit-transform: rotate(359deg);
      }
    }

    @-moz-keyframes spin {
      0% {
        -moz-transform: rotate(0deg);
      }

      100% {
        -moz-transform: rotate(359deg);
      }
    }

    @-ms-keyframes spin {
      0% {
        -ms-transform: rotate(0deg);
      }

      100% {
        -ms-transform: rotate(359deg);
      }
    }

    @-o-keyframes spin {
      0% {
        -o-transform: rotate(0deg);
      }

      100% {
        -o-transform: rotate(359deg);
      }
    }

    @keyframes spin {
      0% {
        transform: rotate(0deg);
      }

      100% {
        transform: rotate(359deg);
      }
    }
  </style>
  <script>
  function quixClearCache() {
    let step = 1;
    jQuery('#cacheCleanMessage').fadeOut(0);
    jQuery('#clearQuixCache').attr('disabled', 'disabled');
    jQuery('#quixcacheicon').addClass('icon-spin');

    setTimeout(function(){

      jQuery.get("index.php?option=com_quix&task=clear_cache&step=" + step, function(data) {
        if (typeof (data) === 'object') {
          jQuery('#cacheCleanMessage').html(jQuery('#cacheCleanMessage').data('success'));
          jQuery('#cacheCleanMessage').css('background-color', jQuery('#cacheCleanMessage').data('colortrue'));
        } else {
          jQuery('#cacheCleanMessage').html(jQuery('#cacheCleanMessage').data('error'));
          jQuery('#cacheCleanMessage').css('background-color', jQuery('#cacheCleanMessage').data('colorfalse'));
        }

        jQuery('#cacheCleanMessage').fadeIn();

        setTimeout(function (){
          jQuery('#clearQuixCache').removeAttr('disabled');
          jQuery('#quixcacheicon').removeClass('icon-spin');
          jQuery('#cacheCleanMessage').fadeOut(500);
        }, 500);
      });
    }, 1000);
  }
</script>
</div>
