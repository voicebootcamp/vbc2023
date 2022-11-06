<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_messages
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$systemInfo = $this->getSystemInfo();
$meetRequirements = true;
$trueM = '<i class="qxuicon-check-circle qx-text-success"></i>';
$falseM = '<i class="qxuicon-times-circle qx-text-danger"></i>';
?>
<h3>System Requirement</h3>

<div class="body">

  <table class="qx-table qx-table-striped qx-table-hover qx-table-small">
    <thead>
      <tr>
        <th>Required</th>
        <th></th>
        <th>value</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><strong>PHP Version</strong> (min:7.2.x)</td>
        <td><?php echo version_compare($systemInfo['php_version'], '7.2.0') == -1 ? $falseM : $trueM ?></td>
        <td><?php echo $systemInfo['php_version'] ?></td>
      </tr>
      <tr>
        <td><strong>Memory Limit</strong> (min: 64M)</td>
        <td><?php echo intval($systemInfo['memory_limit']) > 64 ? $trueM : $falseM ?></td>
        <td><?php echo $systemInfo['memory_limit'] ?></td>
      </tr>
      <tr>
        <td><strong>post_size</strong> (min:5M)</td>
        <td><?php echo intval($systemInfo['postSize']) < '5' ? $falseM : $trueM ?></td>
        <td><?php echo $systemInfo['postSize'] ?></td>
      </tr>
      <tr>
        <td><strong>max_execution</strong> (min:60)</td>
        <td><?php echo $systemInfo['max_execution'] < '60' ? $falseM : $trueM ?></td>
        <td><?php echo $systemInfo['max_execution'] ?></td>
      </tr>
      <tr>
        <td><strong>Cache Folder</strong></td>
        <td><?php echo $systemInfo['cache_writable'] ? $trueM : $falseM ?></td>
        <td><?php echo ($systemInfo['cache_writable'] ? 'Writable' : 'is not writable') ?></td>
      </tr>
      <tr>
        <td><strong>cURL</strong></td>
        <td><?php echo $systemInfo['curl_support'] ? $trueM : $falseM ?></td>
        <td><?php echo $systemInfo['curl_support'] ? 'Yes' : 'No' ?></td>
      </tr>
      <tr>
        <td><strong>GD Library</strong> Support</td>
        <td><?php echo $systemInfo['gd_info'] ? $trueM : $falseM ?></td>
        <td><?php echo $systemInfo['curl_support'] ? 'Yes' : 'No' ?></td>
      </tr>
      <tr>
        <td><strong>cType</strong> Support</td>
        <td><?php echo $systemInfo['ctype_support'] ? $trueM : $falseM ?></td>
        <td><?php echo $systemInfo['ctype_support'] ? 'Yes' : 'No' ?></td>
      </tr>
      <tr>
        <td><strong>Fileinfo</strong> Support</td>
        <td><?php echo $systemInfo['fileinfo'] ? $trueM : $falseM ?></td>
        <td><?php echo $systemInfo['fileinfo'] ? 'Yes' : "No" ?></td>
      </tr>
      <tr>
        <td><strong>allow_url_fopen</strong> Support</td>
        <td><?php echo $systemInfo['allow_url_fopen'] ? $trueM : $falseM ?></td>
        <td><?php echo $systemInfo['allow_url_fopen'] ? 'Yes' : "No" ?></td>
      </tr>
    </tbody>
  </table>
</div> <!--body end-->

