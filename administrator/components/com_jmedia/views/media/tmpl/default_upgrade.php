<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_jmedia
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
$params = JComponentHelper::getParams('com_jmedia');

$username = $params->get('username');
$license  = $params->get('license');
$notice   = ! $username || ! $license ? true : false;
?>
<style type="text/css">
    #upgradeModal {
        color: #606060;
        font-family: "Open Sans", sans-serif;
        font-weight: 400;
        line-height: 1.8;
        text-align: left;
        font-size: 1rem;
    }

    #upgradeModal .modal-body {
        overflow: auto;
    }

    .feature-list .flex-row > div:first-child {
        width: 500px;
        text-align: left;
    }

    .feature-list .flex-row > div {
        line-height: 40px;
        width: auto;
        padding: 0px 20px;
        min-width: 100px;
        text-align: center;
    }

    .feature-list .flex-row {
        border-bottom: 1px solid #ddd;
    }

    .flex-row {
        display: flex;
        align-items: center;
        justify-content: space-around;
    }

    .auth-form .well > div {
        min-width: 20%;
    }

    .auth-form .well > div input {
        width: 100%;
        height: 38px;
    }

    .auth-form .well > div button {
        height: 38px;
    }

    .padding {
        padding-right: 20px;
    }
</style>
<div id="upgradeToPro">

  <h1 style="margin: 20px auto 0px;text-align: center;">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 180 50" width="150">
      <g fill="#454c55">
        <path d="M65.69 33.87a6.87 6.87 0 01-4.83 1.47q-3.26 0-6.91-.32v-4.3q2.66.16 5.46.16a1.75 1.75 0 001.25-.43 1.51 1.51 0 00.46-1.16V17.13h-4.44v-4.42h10.58v16.57a6 6 0 01-1.57 4.59zM91.83 22.59l-4.78 8h-4.77l-4.78-8v12.43h-6.14V12.71h6.48l6.83 11.79 6.82-11.79h6.48v22.31h-6.14zM118.45 34.7q-6.59.64-11.94.64a5.23 5.23 0 01-3.74-1.31 4.53 4.53 0 01-1.4-3.47v-7.65a4.87 4.87 0 011.47-3.78 5.64 5.64 0 014-1.35h6.83a5.64 5.64 0 014 1.35 4.87 4.87 0 011.45 3.75v5.9h-11.76v1.27a1 1 0 00.34.8 1.2 1.2 0 00.85.32q3.38 0 9.9-.48zm-9.68-12.91q-1.37 0-1.37 1.27v1.75h5.8v-1.75q0-1.27-1.37-1.27zM135 35.02l-.34-1.59a12.6 12.6 0 01-3.29 1.45 11.15 11.15 0 01-2.85.46h-1.54a5.23 5.23 0 01-3.72-1.31 4.53 4.53 0 01-1.4-3.47v-7.34a4.87 4.87 0 011.45-3.75 5.64 5.64 0 014-1.35h7v-5.41h6v22.31zm-4.78-4.3a15.41 15.41 0 004.09-.64v-7.65h-5.12q-1.37 0-1.37 1.27v5.9a1 1 0 00.34.8 1.2 1.2 0 00.85.32zM151.2 18.76v16.26h-6v-12h-2.53v-4.3zm-6-6.37h6v4.46h-6zM155.47 18.44a111.94 111.94 0 0111.3-.63 5.23 5.23 0 013.68 1.32 4.53 4.53 0 011.4 3.47v12.42h-5.29l-.34-1.59a12.6 12.6 0 01-3.29 1.45 11.14 11.14 0 01-2.85.46h-1.19a5.23 5.23 0 01-3.72-1.31 4.53 4.53 0 01-1.4-3.47v-1.43a4.53 4.53 0 011.4-3.47 5.23 5.23 0 013.72-1.31h7v-1.29a1 1 0 00-.34-.8 1.2 1.2 0 00-.85-.32q-2.22 0-4.9.19l-4.32.29zm6.3 12.43a15.42 15.42 0 004.09-.64v-1.91h-4.95a1.2 1.2 0 00-.85.32 1 1 0 00-.34.8v.32a1 1 0 00.34.8 1.2 1.2 0 00.85.32z" />
      </g>
      <path d="M8.5 38.83V4.9a3.78 3.78 0 013.77-3.77h26.39a7.56 7.56 0 017.54 7.54v30.16a1.87 1.87 0 01-1.88 1.88H10.39a1.87 1.87 0 01-1.89-1.88z"
            fill="#fff" />
      <path d="M46.2 8.67h-3.77a3.78 3.78 0 01-3.77-3.77V1.13a7.56 7.56 0 017.54 7.54z" fill="#fff" />
      <path d="M47.33 8.67v-.15A8.62 8.62 0 0038.66 0H12.27a4.89 4.89 0 00-4.9 4.9v2.64a1.13 1.13 0 002.26 0V4.9a2.62 2.62 0 012.64-2.64h25.26V4.9a4.89 4.89 0 004.9 4.9h3.77a1.23 1.23 0 001.06-.67 1.89 1.89 0 00.07-.46zM39.77 4.9V2.37a6.39 6.39 0 015.16 5.16h-2.5a2.62 2.62 0 01-2.66-2.63z"
            fill="#454c55" />
      <path d="M39.3 17.34H26.67a6.29 6.29 0 01-5-2.45 6.29 6.29 0 00-5-2.45H6.62a1.87 1.87 0 00-1.85 1.88v24.51a1.87 1.87 0 001.88 1.88h34.54V19.22a1.89 1.89 0 00-1.89-1.88z"
            fill="#fff" />
      <path d="M11.4 30.13a9.42 9.42 0 1010.58-8.11 9.42 9.42 0 00-10.58 8.11zM1.6 45.73a2.27 2.27 0 01.41-3.17l4.76-3.66a2.27 2.27 0 013.17.41 2.27 2.27 0 01-.41 3.17l-4.76 3.65a2.28 2.28 0 01-3.17-.4z"
            fill="#fff" />
      <path d="M20.77 41.88c-.45 0-.94 0-1.39-.08a10.55 10.55 0 119.76-16.89 10.35 10.35 0 012.07 7.8 10.47 10.47 0 01-10.44 9.17zm0-18.85a8.29 8.29 0 00-1.09 16.51 8.22 8.22 0 006.09-1.65 8.13 8.13 0 003.17-5.5 8.22 8.22 0 00-1.66-6.14 8.13 8.13 0 00-5.51-3.18 9.53 9.53 0 00-1.05-.04z"
            fill="#7fbee8" />
      <path d="M10.01 40.45a1.12 1.12 0 01-.9-.45 1.11 1.11 0 01.23-1.58l3-2.3a1.1320004 1.1320004 0 111.36 1.81l-3 2.3a1.22 1.22 0 01-.69.22z"
            fill="#7fbee8" />
      <path d="M3.41 47.72a1.84 1.84 0 01-.45 0A3.39 3.39 0 01.7 46.44a3.38 3.38 0 01.64-4.75l4.79-3.66a3.4104435 3.4104435 0 014.18 5.39l-4.83 3.66a3.42 3.42 0 01-2.07.64zm-.9-2.68a1.16 1.16 0 00.75.41 1.08 1.08 0 00.83-.23l4.79-3.66a1.16 1.16 0 00.41-.75 1.08 1.08 0 00-.23-.83 1.16 1.16 0 00-.75-.41 1.08 1.08 0 00-.83.23l-4.79 3.66a1.17 1.17 0 00-.19 1.58z"
            fill="#7fbee8" />
      <path d="M47.33 16.13v-2.56a1.11 1.11 0 00-1.13-1.13 1.13 1.13 0 00-1.13 1.13v24.62a1.395 1.395 0 11-2.79 0v-19a3 3 0 00-3-3H26.67a5.16 5.16 0 01-4.07-2 7.47 7.47 0 00-5.83-2.88H6.62a3 3 0 00-3 3v19.24a1.13 1.13 0 002.26 0V14.32a.76.76 0 01.75-.75h10.14a5.16 5.16 0 014.07 2 7.47 7.47 0 005.88 2.9H39.3a.76.76 0 01.75.75v19a4.36 4.36 0 00.26 1.39h-8.06a1.13 1.13 0 000 2.26h11.42a3.66 3.66 0 003.66-3.66V16.13z"
            fill="#454c55" />
    </svg>
  </h1>

  <div class="container-fluid auth-form">
    <div class="well">
      <div>
        <p>Enter your license key to upgrade.</p>
        <p><small>Get your username and license key from</small> <a href="https://www.themexpert.com/dashboard" target="_blank">ThemeXpert Dashboard</a></p>
      </div>
      <hr>
      <div style="display: flex;align-items: center;justify-content: space-around;">
        <div style="width: 40%">
          <div class="padding">
            <!--<label form="username">Username</label>-->
            <input type="text" id="username" placeholder="Username" class="form-control" name="username" value="<?php echo $username; ?>" />
          </div>
        </div>
        <div style="width: 40%">
          <div class="padding">
            <!--<label for="license">License Key</label>-->
            <input type="text" id="license" placeholder="License Key" class="form-control" name="license" value="<?php echo $license; ?>" />
          </div>
        </div>
        <div style="width: 20%;text-align: center;">
          <div class="padding">
            <button id="validateAction" class="btn btn-primary" onClick="getValidationData();">
              Validate
            </button>
          </div>
        </div>
      </div>
      <hr>
    </div>
  </div>

  <div id="freeLicense" class="alert alert-danger" style="display: none;align-items: center;justify-content: space-around;">
    <p>It seems either you have a free license or Dont have a Pro License.</p>
    <a class="btn btn-primary btn-large" target="_blank"
       href="https://www.themexpert.com/jmdeia"
    >Get JMedia Pro</a>
  </div>

  <div id="updateToPro" class="alert alert-info" style="display: none;align-items: center;justify-content: space-around;">
    <p>Thank you for validating your license. You are just a click away to PRO version.</p>
    <button id="upgradeNow" class="btn btn-primary btn-large">Update to Pro</button>
  </div>

  <div id="updateComplete" class="alert alert-success" style="display:none;text-align: center;padding: 30px 0;">
    <i class="icon-checkmark-circle"></i>
    <p>Congratulations! You are now using PRO version of JMedia.</p>
    <button class="btn btn-success btn-large" onclick="location.reload();">RELOAD NOW</button>
  </div>

  <div class="feature-list" style="margin: 50px auto">
    <h2 style="text-align: center;margin-bottom: 20px;">JMedia - PRO vs FREE</h2>

    <div class="flex" style="width: 90%;margin: 0 auto;border: 1px solid #ccc;">
      <div class="flex-row">
        <div>FEATURES</div>
        <div>FREE</div>
        <div>PRO</div>
      </div>
      <div class="flex-row">
        <div>Directory Tree</div>
        <div><i class="icon-checkmark-circle"></i></div>
        <div><i class="icon-checkmark-circle"></i></div>
      </div>
      <div class="flex-row">
        <div>File Search</div>
        <div><i class="icon-checkmark-circle"></i></div>
        <div><i class="icon-checkmark-circle"></i></div>
      </div>
      <div class="flex-row">
        <div>File Filter by Type</div>
        <div><i class="icon-checkmark-circle"></i></div>
        <div><i class="icon-checkmark-circle"></i></div>
      </div>
      <div class="flex-row">
        <div>Upload Files</div>
        <div><i class="icon-checkmark-circle"></i></div>
        <div><i class="icon-checkmark-circle"></i></div>
      </div>
      <div class="flex-row">
        <div>Upload Files from URL</div>
        <div><i class="icon-minus"></i></div>
        <div><i class="icon-checkmark-circle"></i></div>
      </div>
      <div class="flex-row">
        <div>Create Folders</div>
        <div><i class="icon-checkmark-circle"></i></div>
        <div><i class="icon-checkmark-circle"></i></div>
      </div>
      <div class="flex-row">
        <div>Remove Files</div>
        <div><i class="icon-checkmark-circle"></i></div>
        <div><i class="icon-checkmark-circle"></i></div>
      </div>
      <div class="flex-row">
        <div>Remove Folders</div>
        <div><i class="icon-checkmark-circle"></i></div>
        <div><i class="icon-checkmark-circle"></i></div>
      </div>
      <div class="flex-row">
        <div>Copy Files</div>
        <div><i class="icon-checkmark-circle"></i></div>
        <div><i class="icon-checkmark-circle"></i></div>
      </div>
      <div class="flex-row">
        <div>Copy Folders</div>
        <div><i class="icon-checkmark-circle"></i></div>
        <div><i class="icon-checkmark-circle"></i></div>
      </div>
      <div class="flex-row">
        <div>Change File Permissions</div>
        <div><i class="icon-minus"></i></div>
        <div><i class="icon-checkmark-circle"></i></div>
      </div>
      <div class="flex-row">
        <div>File Preview</div>
        <div><i class="icon-checkmark-circle"></i></div>
        <div><i class="icon-checkmark-circle"></i></div>
      </div>
      <div class="flex-row">
        <div>File Rename</div>
        <div><i class="icon-checkmark-circle"></i></div>
        <div><i class="icon-checkmark-circle"></i></div>
      </div>
      <div class="flex-row">
        <div>Copy Url</div>
        <div><i class="icon-checkmark-circle"></i></div>
        <div><i class="icon-checkmark-circle"></i></div>
      </div>
      <div class="flex-row">
        <div>File Download</div>
        <div><i class="icon-checkmark-circle"></i></div>
        <div><i class="icon-checkmark-circle"></i></div>
      </div>
      <div class="flex-row">
        <div>File Ordering Filter</div>
        <div><i class="icon-checkmark-circle"></i></div>
        <div><i class="icon-checkmark-circle"></i></div>
      </div>
      <div class="flex-row">
        <div>Grid View</div>
        <div><i class="icon-checkmark-circle"></i></div>
        <div><i class="icon-checkmark-circle"></i></div>
      </div>
      <div class="flex-row">
        <div>List View</div>
        <div><i class="icon-checkmark-circle"></i></div>
        <div><i class="icon-checkmark-circle"></i></div>
      </div>
      <div class="flex-row">
        <div>Joomla! ACL Support</div>
        <div><i class="icon-checkmark-circle"></i></div>
        <div><i class="icon-checkmark-circle"></i></div>
      </div>
      <div class="flex-row">
        <div>File Type and Upload Restriction.</div>
        <div><i class="icon-minus"></i></div>
        <div><i class="icon-checkmark-circle"></i></div>
      </div>
    </div>
  </div>

  <div class="well" style="margin-bottom: 0px;">
    <div class="flex-row" style="width: 90%;margin: 0 auto;">
      <div>
        <h3>Ready to grow your fundraising?</h3>
        <p>Join 10,000+ non-profits already getting more donations with Charitable.</p>
      </div>
      <div>
        <a class="btn btn-primary btn-large" target="_blank"
           href="https://www.themexpert.com/jmdeia">JMedia Pro</a>
      </div>
    </div>
  </div>

</div>
<script type="text/javascript">
    <?php
    $url = 'https://www.themexpert.com/index.php?option=com_digicom&task=responses&source=authapi&catid=87&';
    ?>

    window.getValidationData = function() {

        var username = jQuery('#username');
        var license = jQuery('#license');
        username.attr('disabled', 'disabled');
        license.attr('disabled', 'disabled');

        jQuery('#validateAction').attr('disabled', 'disabled');
        jQuery('#validateAction').text('Validating...');

        jQuery.getJSON('<?php echo $url; ?>username=' + username.val() + '&key=' + license.val(), secondStep);

        storeConfig();
    };

    window.storeConfig = function() {

        jQuery('#freeLicense').css('display', 'none');
        jQuery('#updateToPro').css('display', 'none');

        /* store config */
        var username = jQuery('#username');
        var license = jQuery('#license');

        var form = {
            'jform': {
                'username': username.val(),
                'license': license.val(),
            },
        };

        form[Joomla.getOptions('csrf.token')] = 1;

        // jQuery.post( 'index.php?option=com_config', form );
        jQuery.post('index.php?option=com_jmedia&task=updateconfig', form);
    };

    window.secondStep = function(data) {

        var usernameTag = jQuery('#username');
        var licenseTag = jQuery('#license');
        usernameTag.removeAttr('disabled');
        licenseTag.removeAttr('disabled');

        var items = data.data;
        var license = items.some(function(item) {
            if (item.has_access && item.id == '225') {
                window.licenseinfo = item;
                return true;
            }
            return false;
        });

        jQuery('#validateAction').text('Validate');
        jQuery('#validateAction').removeAttr('disabled');

        if (license == false) {
            jQuery('#freeLicense').css('display', 'flex');
            jQuery('#updateToPro').css('display', 'done');
            jQuery('#validateAction').text('Validate');
        }
        else {
            jQuery('#freeLicense').css('display', 'none');
            jQuery('#updateToPro').css('display', 'flex');
        }

        jQuery('#upgradeNow').on('click', function() {
            jQuery(this).attr('disabled', 'disabled');
            jQuery(this).text('Updating...');

            // Send the data using post
            let form = {
                'install_url': licenseinfo.download_url,
                'installtype': 'url',
                'task': 'install.install',
            };
            form[Joomla.getOptions('csrf.token')] = 1;

            var posting = jQuery.post('index.php?option=com_installer&view=install', form);

            // Put the results in a div
            posting.done(function(data) {
                var success = jQuery(data).find('#system-message-container .alert.alert-success');
                if (success.length) {
                    // location.reload();
                    jQuery('#updateToPro').hide();
                    jQuery('#upgradeNow').removeAttr('disabled');
                    jQuery('#upgradeNow').text('Update to PRO');
                    jQuery('#updateComplete').show();

                }
                else {

                    jQuery('#updateToPro').css('display', 'flex');
                    alert('Something went wrong!, please try again.');

                    jQuery('#upgradeNow').removeAttr('disabled');
                    jQuery('#upgradeNow').text('Update to PRO');
                }

            });

        });

    };
</script>
