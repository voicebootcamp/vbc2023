<?php
defined('_JEXEC') or die('Restricted access');

JFormHelper::loadFieldClass('list');

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Http\HttpFactory;
use Joomla\CMS\Language\Text;
use Joomla\Registry\Registry;

class JFormFieldAcfields extends JFormFieldList
{
	protected $type = 'Acfields';

	protected function getOptions()
	{
		$options   = [];
		$options[] = HTMLHelper::_('select.option', '', Text::_('Select Field'));

		$db    = Factory::getDbo();
		$query = $db->getQuery(true)
			->select('params')
			->from('#__extensions')
			->where('folder = ' . $db->quote('osmembership'))
			->where('element = ' . $db->quote('activecampaign'));
		$db->setQuery($query);
		$params = $db->loadResult();

		if (!$params)
		{
			return $options;
		}

		$params   = new Registry($params);
		$apiUrl   = $params->get('api_url', 'https://thequiltshow78384.api-us1.com/api/3');
		$apiToken = $params->get('api_token',
			'd3a2a755875a992b5f106dcbfb875f8d9b46ba152edc596cdfd91757f059516b4c156ca3');

		if ($apiUrl && $apiToken)
		{
			$http    = HttpFactory::getHttp();
			$headers = [
				'User-Agent' => 'Membership Pro',
				'Api-Token'  => $apiToken,
				'Accept'     => 'application/json',
			];


			$response = $http->get($apiUrl . '/fields?limit=500', $headers);

			if ($response->code == 200)
			{
				$fields = json_decode($response->body, true)['fields'];

				foreach ($fields as $field)
				{
					$options[] = HTMLHelper::_('select.option', $field['id'], $field['title']);
				}
			}
		}

		return $options;
	}
}
