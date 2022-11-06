<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2018 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

$size          = (int) $row->size ?: 1;
$span          = intval(12 / $size);
$i             = 0;
$numberOptions = count($options);

$rowFluid        = $bootstrapHelper ? $bootstrapHelper->getClassMapping('row-fluid') : 'row-fluid';
$spanClass       = $bootstrapHelper ? $bootstrapHelper->getClassMapping('span' . $span) : 'span' . $span;
$clearFixClass   = $bootstrapHelper ? $bootstrapHelper->getClassMapping('clearfix') : 'clearfix';
$ukFieldsetClass = $bootstrapHelper ? $bootstrapHelper->getFrameworkClass('uk-fieldset', 2) : '';
?>
<fieldset id="<?php echo $name; ?>" class="<?php echo $ukFieldsetClass . $clearFixClass; ?> osm-checkboxes-contaier">
    <div class="<?php echo $rowFluid . ' ' . $clearFixClass; ?>">
		<?php
		foreach ($options as $option)
		{
		$i++;
		$checked = in_array($option, $selectedOptions) ? 'checked' : '';
		?>
            <div class="<?php echo $spanClass ?>">
                <label class="checkbox">
                    <input type="checkbox" id="<?php echo $name . $i; ?>"
                           name="<?php echo $name; ?>[]"
                           value="<?php echo htmlspecialchars((string) $option, ENT_COMPAT, 'UTF-8') ?>"
                        <?php echo $checked . $attributes; ?>
                    /><?php echo $option; ?>
                </label>
            </div>
		<?php
            if ($i % $size == 0 && $i < $numberOptions)
            {
            ?>
                </div>
                <div class="<?php echo $rowFluid . ' ' . $clearFixClass; ?>">
            <?php
            }
		}
		?>
    </div>
</fieldset>
