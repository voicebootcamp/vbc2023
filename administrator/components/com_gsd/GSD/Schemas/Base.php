<?php

/**
 * @package         Google Structured Data
 * @version         5.1.6 Pro
 *
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2022 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

namespace GSD\Schemas;

// No direct access
defined('_JEXEC') or die;

use GSD\Helper;
use Joomla\Registry\Registry;
use NRFramework\Functions;

class Base
{
    /**
     * The schema properties
     *
     * @var object
     */
    protected $data;

    /**
     * The HTML tags allowed to be used in certain schema properties, such as the headline and the description.
     *
     * @var mixed
     */
    protected $allowed_HTML_tags = null;

    /**
     * A key => value array with schema properties that needs to be renamed.
     * 
     * The left value represents the name of the property as defined in the schema's XML file.
     * The right value represents the name of the property as it's expected in JSON class.
     *  
     * @Todo - We should rename all properties directly in each schema XML file and then get rid of this property.
     * 
     * @var array
     */
    protected $rename_properties;

    /**
     * Class constructor
     *
     * @param JRegistry $data The schema properties
     */
    public function __construct($data = null)
    {
        $this->data = $data;
    }

    /**
     * Return all schema properties
     *
     * @return JRegistry
     */
    public function get()
    {
        $this->initProps();
        $this->cleanProps();

        return $this->data;
    }

    /**
     * Run a housekeeping on each property. Remove unwanted HTML tags and whitespace and encode remaining HTML.
     *
     * @return void
     */
    protected function cleanProps()
    {   
        $props = $this->data->toArray();

        array_walk_recursive($props, function(&$prop)
        {
            if (!is_null($prop)) // Make PHP 8.1 happy.
            {
		        // Remove all <script> tags and their content
		        $prop = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $prop);

                // Remove invalid HTML tags
		        $prop = strip_tags($prop, $this->allowed_HTML_tags);

                // Convert remaining HTML tags into HTML entities to prevent structured data errors.
                $prop = htmlspecialchars($prop, ENT_QUOTES, 'UTF-8');

                // Remove whitespace
                $prop = preg_replace('/(\s)+/s', ' ', $prop);

                // Remove whitespace from the beginning and end of the prop
                $prop = trim($prop);
            }
        });

        $this->data = new Registry($props);
    }
    
    /**
     * Prepare common schema properties.
     * 
     * - Rename properties
     * - Add timezone offset and format dates to ISO8601
     * - Strip HTML tags from certain properties
     * - Convert relative paths to absolute URLs
     *
     * @return void
     */
    protected function initProps()
    {
        $this->renameProperties();

        // Fix dates in the Reviews property. Used in schemas: Product, Movie, Local Business
        if ($reviews = $this->data->get('reviews'))
        {
            foreach ($reviews as &$review)
            {
                if (!isset($review['datePublished']))
                {
                    continue;
                }
    
                // Convert date to ISO8601
                $review['datePublished'] = Helper::date($review['datePublished'], true);
            }
    
            $this->data->set('reviews', $reviews);
        }

        // Common properties
        $props = [
            'contentType'   => $this->getName(),
            // Make sure the @id property is unique, to prevent structured data awkwardly merged by the Google Structured Data Testing Tool
            'id'            => \JURI::current() . '#' .  $this->getName() . $this->data['snippet_id'],
            'title'         => $this->data['headline'],
            'description'   => $this->data['description'],
            'image'         => Helper::cleanImage(Helper::absURL($this->data->get('image'))), 

            // Author / Publisher
            'authorName'    => $this->data['author'],

            // Rating
            'ratingValue'   => $this->data['rating_value'],
            'reviewCount'   => $this->data['review_count'],
            'bestRating'    => $this->data['bestRating'],
            'worstRating'   => $this->data['worstRating'],

            // Dates
            'datePublished' => Helper::date($this->data['publish_up'], true),
            'dateCreated'   => Helper::date($this->data['created'], true),
            'dateModified'  => Helper::date($this->data['modified'], true),

            // Site based
            'url'           => \JURI::current(),
            'siteurl'       => Helper::getSiteURL(),
            'sitename'      => Helper::getSiteName(),
        ];

        $this->data->merge(new Registry($props));
    }

    /**
     * Some schema properties are declared with the wrong name in Schema XML files. With this method, we attemp to rename those properties with the proper name expected by the JSON class.
     * 
     * @todo Rename all properties in XML files and create a migration script that will update users database. Then, we can get get rid of this method.
     *
     * @return void
     */
    private function renameProperties()
    {
        if (!$this->rename_properties)
        {
            return;
        }

        foreach ($this->rename_properties as $old_property_name => $new_property_name)
        {
            if (!isset($this->data[$old_property_name]))
            {
                continue;
            }

            $this->data[$new_property_name] = $this->data[$old_property_name];

            // Remove old property as we no longer need it.
            unset($this->data[$old_property_name]);
        }
    }

    /**
     * Return the name of this schema type
     *
     * @return string
     */
    private function getName()
    {
        $reflect = new \ReflectionClass($this);
        return strtolower($reflect->getShortName());
    }

    /**
     * This method runs everytime a structured data item is saved in the backend. 
     *
     * @param  array    $data   The data to be stored in the database
     * 
     * @return void
     */
    public function onSave(&$data)
    {
        if (!$data)
        {
            return;
        }

        foreach ($data as $optionKey => &$optionValue)
        {
            $commonDateFieldNames = [
                'publish_up',
                'modified',
                'created',
                'valid_through',
                'validFrom',
                'priceValidUntil'
            ];

            // Find date fields by their name.
            if (strpos(strtolower($optionKey), 'date') === false && !in_array($optionKey, $commonDateFieldNames))
            {
                continue;
            }

            // Only when the mapping option is using the "Fixed Dates" option
            // The "Custom Option" is ignored as it may include a shortcode or some other formatted value.
            if ($optionValue['option'] !== 'fixed' || empty($optionValue['fixed']))
            {
                continue;
            }

            $optionValue['fixed'] = Functions::dateToUTC($optionValue['fixed']);
        }
    }
}