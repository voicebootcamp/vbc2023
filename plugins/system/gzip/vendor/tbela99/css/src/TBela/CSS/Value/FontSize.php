<?php

namespace TBela\CSS\Value;

use \TBela\CSS\Value;

/**
 * Css string value
 * @package TBela\CSS\Value
 */
class FontSize extends Unit
{

    use UnitTrait, ValueTrait;

    protected static $keywords = [
        'xx-small',
        'x-small',
        'small',
        'medium',
        'large',
        'x-large',
        'xx-large',
        'xxx-large',
        'larger',
        'smaller'
    ];

    protected static $defaults = ['medium'];

    /**
     * @inheritDoc
     */
    public static function matchToken($token, $previousToken = null, $previousValue = null, $nextToken = null, $nextValue = null, $index = null, array $tokens = [])
    {
        if (($token->type == 'number' && $token->value == 0) || ($token->type == 'unit' && !in_array($token->unit, ['turn', 'rad', 'grad', 'deg']))) {

            return true;
        }

        if ($token->type == 'css-string' && in_array(strtolower($token->value), static::$keywords)) {

            return true;
        }

        return $token->type == static::type();
    }

    /**
     * @inheritDoc
     */
    public function render(array $options = [])
    {

        return static::doRender($this->data, $options);
    }
}
