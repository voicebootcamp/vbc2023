<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2022 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Updater\Updater;

class OSMembershipViewDashboardHtml extends MPFViewHtml
{
	public $hasModel = false;

	/**
     * Store update result
     *
	 * @var array
	 */
	protected $updateResult = [];

	/**
	 * Display the view
     *
     * @return void
	 */
	public function display()
	{
		$this->subscriptions = MPFModel::getTempInstance('Subscriptions', 'OSMembershipModel')
			->limitstart(0)
			->limit(10)
			->filter_order('tbl.created_date')
			->filter_order_Dir('DESC')
			->getData();
		$this->config        = OSMembershipHelper::getConfig();
		$this->data          = OSMembershipModelSubscriptions::getStatisticsData();
		$this->sales         = OSMembershipModelSubscriptions::getLast12MonthSales();

		// Get list of plans
		$db    = Factory::getDbo();
		$query = $db->getQuery(true)
			->select('id, title')
			->from('#__osmembership_plans')
			->where('published = 1')
			->order('ordering');
		$db->setQuery($query);

		$planId = $this->input->getInt('plan_id', 0);

		$options                = [];
		$options[]              = HTMLHelper::_('select.option', 0, Text::_('OSM_ALL_PLANS'), 'id', 'title');
		$options                = array_merge($options, $db->loadObjectList());
		$this->lists['plan_id'] = HTMLHelper::_('select.genericlist', $options, 'plan_id', 'onchange="reloadSalesChart();" class="form-select"', 'id', 'title', $planId);

		// Render sub-menu in dashboard
		OSMembershipHelperHtml::renderSubmenu('dashboard');

		// Check update information
        $this->checkUpdate();

        if ($this->updateResult['status'] == 2)
        {
            Factory::getApplication()->enqueueMessage(Text::sprintf('OSM_UPDATE_AVAILABLE', 'index.php?option=com_installer&view=update', $this->updateResult['version']));
        }

		parent::display();
	}

	/**
	 * Function to create the buttons view.
	 *
	 * @param   string  $link   targeturl
	 * @param   string  $image  path to image
	 * @param   string  $text   image description
	 */
	protected function quickiconButton($link, $image, $text, $id = null)
	{
		$language = Factory::getLanguage();
		?>
		<div
				style="float:<?php echo ($language->isRTL()) ? 'right' : 'left'; ?>;" <?php if ($id) echo 'id="' . $id . '"'; ?>>
			<div class="icon">
				<a href="<?php echo $link; ?>" title="<?php echo $text; ?>">
					<?php echo HTMLHelper::_('image', 'administrator/components/com_osmembership/assets/icons/' . $image, $text); ?>
					<span><?php echo $text; ?></span>
				</a>
			</div>
		</div>
		<?php
	}

	protected function checkUpdate()
    {
	    // Get the caching duration.
	    $component     = ComponentHelper::getComponent('com_installer');
	    $params        = $component->params;
	    $cache_timeout = $params->get('cachetimeout', 6);
	    $cache_timeout = 3600 * $cache_timeout;

	    // Get the minimum stability.
	    $minimum_stability = $params->get('minimum_stability', Updater::STABILITY_STABLE);

	    if (OSMembershipHelper::isJoomla4())
	    {
		    /* @var \Joomla\Component\Installer\Administrator\Model\UpdateModel $model */
		    $model = Factory::getApplication()->bootComponent('com_installer')->getMVCFactory()
			    ->createModel('Update', 'Administrator', ['ignore_request' => true]);
	    }
	    else
	    {
		    BaseDatabaseModel::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_installer/models');

		    /** @var InstallerModelUpdate $model */
		    $model = BaseDatabaseModel::getInstance('Update', 'InstallerModel');
	    }

	    $model->purge();

	    $db    = Factory::getDbo();
	    $query = $db->getQuery(true)
		    ->select('extension_id')
		    ->from('#__extensions')
		    ->where('`type` = "package"')
		    ->where('`element` = "pkg_osmembership"');
	    $db->setQuery($query);
	    $eid = (int) $db->loadResult();

	    $result['status'] = 0;

	    if ($eid)
	    {
		    $ret = Updater::getInstance()->findUpdates($eid, $cache_timeout, $minimum_stability);

		    if ($ret)
		    {
			    $model->setState('list.start', 0);
			    $model->setState('list.limit', 0);
			    $model->setState('filter.extension_id', $eid);
			    $updates           = $model->getItems();
			    $result['status']  = 2;
			    $result['version'] = '';

			    if (count($updates))
			    {
				    $result['message'] = Text::sprintf('OSM_UPDATE_CHECKING_UPDATEFOUND', $updates[0]->version);
				    $result['version'] = $updates[0]->version;
			    }
			    else
			    {
				    $result['message'] = Text::sprintf('OSM_UPDATE_CHECKING_UPDATEFOUND', null);
			    }
		    }
		    else
		    {
			    $result['status']  = 1;
			    $result['message'] = Text::_('OSM_UPDATE_CHECKING_UPTODATE');
		    }
	    }

	    $this->updateResult = $result;
    }
}
