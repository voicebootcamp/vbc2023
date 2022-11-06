<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2022 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

$controlGroup = $bootstrapHelper ? $bootstrapHelper->getClassMapping('control-group') : 'control-group';
?>
<div class="<?php echo $controlGroup; ?> osm-message" <?php echo $controlGroupAttributes; ?>><?php echo $description; ?></div>
