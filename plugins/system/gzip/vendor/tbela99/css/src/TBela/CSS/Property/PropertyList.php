<?php

namespace TBela\CSS\Property;

use ArrayIterator;
use IteratorAggregate;
use TBela\CSS\Value;
use TBela\CSS\Element\Rule;
use TBela\CSS\Element\RuleList;

/**
 * Property list
 * @package CSS
 */
class PropertyList implements IteratorAggregate
{

    /**
     * @var Property[]
     * @ignore
     */
    protected $properties = [];

    /**
     * @var array
     * @ignore
     */
    protected $options = [

        'compute_shorthand' => true,
        'allow_duplicate_declarations' => false
    ];

    /***
     * PropertyList constructor.
     * @param RuleList|null $list
     * @param array $options
     */
    public function __construct(RuleList $list = null, array $options = [])
    {

        $this->options = array_merge($this->options, $options);

        if ((is_callable([$list, 'hasDeclarations']) && $list->hasDeclarations()) || $list instanceof Rule) {

            foreach ($list as $element) {

                $this->set($element['name'], $element['value'], $element['type'], $element['leadingcomments'], $element['trailingcomments']);
            }
        }
    }

    /**
     * set property
     * @param string|null $name
     * @param Value|string $value
     * @param string|null $propertyType
     * @param array|null $leadingcomments
     * @param array|null $trailingcomments
     * @param string|null $src
     * @param string|null $vendor
     * @return $this
     */

    public function set($name, $value, $propertyType = null, array $leadingcomments = null, array $trailingcomments = null, $src = '', $vendor = null)
    {

        if ($propertyType == 'Comment') {

            $this->properties[] = new Comment($value);
            return $this;
        }

        $name = (string) $name;

        if(!empty($this->options['allow_duplicate_declarations'])) {

            if ($this->options['allow_duplicate_declarations'] === true ||
                (is_array($this->options['allow_duplicate_declarations']) && in_array($name, $this->options['allow_duplicate_declarations']))) {

                $property = (new Property($name))->setValue($value);

                if (!is_null($src)) {

                    $property->setSrc($src);
                }

                if (!is_null($vendor)) {

                    $property->setVendor($vendor);
                }

                if (!empty($leadingcomments)) {

                    $property->setLeadingComments($leadingcomments);
                }

                if (!empty($trailingcomments)) {

                    $property->setTrailingComments($trailingcomments);
                }

                $this->properties[] = $property;
                return $this;
            }
        }

        $propertyName = $name;

        if (substr($name, 0, 1) == '-' && preg_match('/^(-([a-zA-Z]+)-(\S+))/', trim($name), $match)) {

            $name = $match[3];

            if (is_null($vendor)) {

                $vendor = $match[2];
            }
        }


        if (!is_null($vendor)) {

            $propertyName = '-'.$vendor.'-'.$name;
        }

        if (empty($this->options['compute_shorthand'])) {

            $property = (new Property($name))->setValue($value);

            if (!is_null($src)) {

                $property->setSrc($src);
            }

            if (!is_null($vendor)) {

                $property->setVendor($vendor);
            }

            if (!empty($leadingcomments)) {

                $property->setLeadingComments($leadingcomments);
            }

            if (!empty($trailingcomments)) {

                $property->setTrailingComments($trailingcomments);
            }

            $this->properties[$property->getName(true)] = $property;
            return $this;
        }

        $shorthand = Config::getProperty($propertyName.'.shorthand');

        // is is an shorthand property?
        if (!is_null($shorthand) && !is_null(Config::getProperty($shorthand))) {

           $config = Config::getProperty($shorthand);

            if (!isset($this->properties[$shorthand]) || (!($this->properties[$shorthand] instanceof PropertySet))) {

                $this->properties[$shorthand] = new PropertySet($shorthand, $config);

                if (!is_null($src)) {

                    $this->properties[$shorthand]->setSrc($src);
                }
            }

            $this->properties[$shorthand]->set($name, $value, $leadingcomments, $trailingcomments, $vendor);

//            else {

//                throw new \Exception('Invalid shorthand '.$shorthand);
//            }

        }

        else {

            $shorthand = Config::getPath('map.'.$name.'.shorthand');

            // is is a shorthand property?
            if (!is_null($shorthand)) {

                $config = Config::getPath('map.'.$shorthand);

                if (!isset($this->properties[$shorthand])) {

                    $this->properties[$shorthand] = new PropertyMap($shorthand, $config);

                    if (!is_null($src)) {

                        $this->properties[$shorthand]->setSrc($src);
                    }
                }

                $this->properties[$shorthand]->set($name, $value, $leadingcomments, $trailingcomments);
            }

            else {

                // regular property
                if (!isset($this->properties[$propertyName])) {

                    $this->properties[$propertyName] = new Property($name);
                }

                $property = $this->properties[$propertyName]->setValue($value);

                if (!is_null($vendor)) {

                    $property->setVendor($vendor);
                }

                if (!empty($leadingcomments)) {

                    $property->setLeadingComments($leadingcomments);
                }

                if (!empty($trailingcomments)) {

                    $property->setTrailingComments($trailingcomments);
                }
            }
        }

        return $this;
    }

    /**
     * convert properties to string
     * @param string $glue
     * @param string $join
     * @return string
     */
    public function render ($glue = ';', $join = "\n")
    {

        $result = [];

        foreach ($this->getProperties() as $property) {

            $output = $property->render($this->options);

            if (!($property instanceof Comment)) {

                $output .= $glue;
            }

            $output .= $join;
            $result[] = $output;
        }

        return rtrim(rtrim(implode('', $result)), $glue);
    }

    /**
     * convert this object to string
     * @return string
     */
    public function __toString() {

        return $this->render();
    }

    /**
     * return properties iterator
     * @return ArrayIterator<Property>
     */
    public function getProperties () {

        /**
         * @var Property[] $result
         */
        $result = [];

        foreach ($this->properties as $property) {

            if (is_callable([$property, 'getProperties'])) {

                array_splice($result, count($result), 0, $property->getProperties());
            }

            else {

                $result[] = $property;
            }
        }

        return new ArrayIterator($result);
    }

    /**
     * @return bool
     */
    public function isEmpty() {

        return empty($this->properties);
    }

    /**
     * @inheritDoc
     */
    public function getIterator()
    {
        return $this->getProperties();
    }

    /**
     * @return array
     * @ignore
     */
    public function toObject() {

        $data = [];

        foreach ($this->getProperties() as $property) {

            $data[] = $property->toObject();
        }

        return $data;
    }
}
