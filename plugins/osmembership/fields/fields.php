<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2022 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;

class plgOSMembershipFields extends CMSPlugin
{
	/**
	 * Application object.
	 *
	 * @var    JApplicationCms
	 */
	protected $app;

	/**
	 * Database object.
	 *
	 * @var    JDatabaseDriver
	 */
	protected $db;

	/**
	 * Make language files will be loaded automatically.
	 *
	 * @var bool
	 */
	protected $autoloadLanguage = true;

	/**
	 * Render setting form
	 *
	 * @param   OSMembershipTablePlan  $row
	 *
	 * @return array
	 */
	public function onEditSubscriptionPlan($row)
	{
		if (!$this->isExecutable())
		{
			return [];
		}

		ob_start();
		$this->drawSettingForm($row);

		return ['title' => Text::_('OSM_FIELDS_ASSIGNMENT'),
		        'form'  => ob_get_clean(),
		];
	}

	/**
	 * Store setting into database, in this case, use params field of plans table
	 *
	 * @param   string                 $context
	 * @param   OSMembershipTablePlan  $row
	 * @param   array                  $data
	 * @param   bool                   $isNew  true if create new plan, false if edit
	 *
	 * @return bool
	 */
	public function onAfterSaveSubscriptionPlan($context, $row, $data, $isNew)
	{
		if (!$this->isExecutable())
		{
			return;
		}

		$db         = $this->db;
		$query      = $db->getQuery(true);
		$formFields = $data['subscription_form_fields'] ?? [];
		$formFields = array_filter($formFields);

		if (!$isNew)
		{
			$query->delete('#__osmembership_field_plan')
				->where('plan_id = ' . $row->id);
			$db->setQuery($query)
				->execute();
		}

		if (!count($formFields))
		{
			return true;
		}

		$query->clear()
			->insert('#__osmembership_field_plan')
			->columns($this->db->quoteName(['field_id', 'plan_id']));

		foreach ($formFields as $field)
		{
			$query->values(implode(',', $db->quote([$field, $row->id])));
		}

		$db->setQuery($query)
			->execute();

		return true;
	}

	/**
	 * Method to check if the plugin is executable
	 *
	 * @return bool
	 */
	private function isExecutable()
	{
	    if ($this->app->isClient('site') && !$this->params->get('show_on_frontend'))
		{
			return false;
		}

		return true;
	}

	/**
	 * Display form allows users to change settings on subscription plan add/edit screen
	 *
	 * @param   OSMembershipTablePlan  $row
	 */
	private function drawSettingForm($row)
	{
		$db    = $this->db;
		$query = $db->getQuery(true)
			->select('id, plan_id, name, title')
			->from('#__osmembership_fields')
			->where('published = 1')
			->order('plan_id, ordering');
		$db->setQuery($query);
		$rowFields = $db->loadObjectList();

		$selectedFieldIds = [];

		// Load assigned fields for this event
		if ($row->id)
		{
			$query->clear()
				->select('field_id')
				->from('#__osmembership_field_plan')
				->where('plan_id = ' . $row->id);
			$db->setQuery($query);
			$selectedFieldIds = $db->loadColumn();
		}

		$count           = 0;
		$bootstrapHelper = OSMembershipHelperBootstrap::getInstance();
		$rowFluid        = $bootstrapHelper->getClassMapping('row-fluid');
		$spanClass       = $bootstrapHelper->getClassMapping('span3');
		?>
        <div class="<?php echo $rowFluid; ?>">
		<?php
		foreach ($rowFields as $rowField)
		{
			$count++;
			$attributes = [];

			if ($rowField->plan_id == 0 || $rowField->name == 'email')
			{
				$attributes[] = 'disabled';
				$attributes[] = 'checked';
			}
            elseif (in_array($rowField->id, $selectedFieldIds))
			{
				$attributes[] = 'checked';
			}
			?>
                <div class="<?php echo $spanClass; ?>">
                    <label class="checkbox">
                        <input type="checkbox" value="<?php echo $rowField->id ?>"
                               name="subscription_form_fields[]"<?php if (count($attributes)) echo ' ' . implode(' ', $attributes); ?>><?php echo '[' . $rowField->id . '] - ' . $rowField->title; ?>
                    </label>
                </div>
		    <?php
		}
		?>
        </div>
		<?php
	}
}
