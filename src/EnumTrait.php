<?php

namespace rikudou\PHPEnum;

use InvalidArgumentException;
use LogicException;

trait EnumTrait
{
    /**
     * @var static[]
     */
    private static $cache = [];

    /**
     * @var mixed
     */
    private $value;

    /**
     * EnumTrait constructor.
     *
     * @param mixed $value
     */
    private function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @param mixed $value
     *
     * @return static
     */
    private static function _get($value)
    {
        if (!isset(static::$cache[$value])) {
            static::$cache[$value] = new static($value);
        }
        return static::$cache[$value];
    }

    private static function allowedValues()
    {
        return [];
    }

    /**
     * @param string $name
     * @param array $arguments
     *
     * @return static
     */
    public static function __callStatic($name, $arguments)
    {
        if (static::allowedValues() && !in_array($name, static::allowedValues())) {
            throw new InvalidArgumentException("'$name' is not a valid enum value");
        }
        return static::_get($name);
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    public function __sleep()
    {
        throw new LogicException("The enum cannot be serialized");
    }

    public function __wakeup()
    {
        throw new LogicException("The enum cannot be unserialized");
    }
}
