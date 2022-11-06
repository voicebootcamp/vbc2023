<?php

namespace Spatie\IcalendarGenerator\PropertyTypes;

final class TextPropertyType extends PropertyType
{
    /** @var string */
    private $text;

    /** @var bool */
    private $disableEscaping;

    public static function create($names, string $text, $disableEscaping = false): TextPropertyType
    {
        return new self($names, $text, $disableEscaping);
    }

    /**
     * TextPropertyType constructor.
     *
     * @param array|string $names
     * @param string $text
     * @param bool $disableEscaping
     */
    public function __construct($names, string $text, $disableEscaping = false)
    {
        parent::__construct($names);

        $this->text = $text;
        $this->disableEscaping = $disableEscaping;
    }

	public function getValue(): string
	{
		if ($this->disableEscaping)
		{
			return $this->text;
		}

		$text = str_replace("\r\n", '@@@@@', $this->text);

		$replacements = [
			'\\' => '\\\\',
			'"'  => '\\"',
			','  => '\\,',
			';'  => '\\;',
			"\n" => '\\n',
		];

		$text = str_replace(array_keys($replacements), $replacements, $text);

		return $text = str_replace('@@@@@', "\r\n", $text);
	}

    public function getOriginalValue(): string
    {
        return $this->text;
    }
}
