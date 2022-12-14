<?php 
/** 
 * @package JMAP::SOURCES::administrator::components::com_jmap
 * @subpackage views
 * @subpackage sources
 * @subpackage tmpl
 * @author Joomla! Extensions Store
 * @copyright (C) 2021 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html  
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );
use Joomla\CMS\Language\Text;
?>
<div id="accordion_datasource_pluginimport" class="sqlquerier card card-info adminform">
	<div class="card-header accordion-toggle accordion_lightblue" data-bs-toggle="collapse" data-bs-target="#datasource_pluginimport"><h4><?php echo Text::_('COM_JMAP_IMPORT_PLUGIN_FILE' ); ?></h4></div>
	<div class="card-body card-block collapse" id="datasource_pluginimport">
		<table class="admintable">
		<tbody>
			<tr>
				<td class="key left_title">
					<label for="title">
						<?php echo Text::_('COM_JMAP_PICK_PLUGIN_FILE' ); ?>:
					</label>
				</td>
				<td class="right_details">
					<div id="uploadrow">
						<span class="input-group">
							<input type="file" id="datasourceinstallplugin" name="datasourceinstallplugin" value="">
						</span>
						<button onclick="Joomla.submitbutton('sources.importPlugins')" class="btn btn-sm btn-primary">
							<span class="icon-apply icon-white"></span> <?php echo Text::_('COM_JMAP_IMPORT_PLUGIN' ); ?>
						</button>
					</div>
				</td>
			</tr>
		</tbody>
		</table>
	</div>
</div>