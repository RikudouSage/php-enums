# PHP Enums

This is yet another PHP enum implementation.

What is so different about this one? It uses trait which solves one problem all the
other implementations I've seen have - extending some base enum class.

All the enums return the same instance for the same enum, so you can check for equality
(`===`) and the result is true.

The constructor is made private, so you cannot directly construct an instance via
`new` keyword (but you can create an constructor in your class and make it public
if you need to, but I advise against it).

Every internal method is made private so you cannot extend the enum (well, you can
but it would be useless).

```php
<?php

MyCoolEnum::Test() == MyCoolEnum::Test(); // true
MyCoolEnum::Test() === MyCoolEnum::Test(); // true
MyCoolEnum::Test() == MyCoolEnum::Test2(); // false
MyCoolEnum::Test() === MyCoolEnum::Test2(); // false
```

Usage:

## Creating enum

```php
<?php

use rikudou\PHPEnum\EnumTrait;

class MyCoolEnum
{
  use EnumTrait;
}
```

That's it, you just created an enum. It's a pretty basic one, that supports pretty much
any value via magic method `__callStatic`. For example:

```php
<?php


$testValue = MyCoolEnum::TestValue();
$testValue2 = MyCoolEnum::TestValue2();
$testValue3 = MyCoolEnum::PrettyMuchAnythingCanGoHere();
```

If you want IDE completion you have two options:

1. create the static methods yourself
2. add phpdoc comments to the class (for example PHPStorm supports it)

### Create the methods yourself

```php
<?php

use rikudou\PHPEnum\EnumTrait;

class MyCoolEnum
{
  use EnumTrait;
  
  public static function EnumValue1() {
    return static::_get("EnumValue1");
  }
  
  public static function EnumValue2() {
    return static::_get("EnumValue2");
  }
}
```

Your IDE will now autocomplete for `EnumValue1` and `EnumValue2` static methods.

### Add phpdoc comments

```php
<?php

use rikudou\PHPEnum\EnumTrait;

/**
 * @method static static EnumValue1()
 * @method static static EnumValue2()
 */
class MyCoolEnum
{ 
  use EnumTrait;
}
```

The result for IDE is the same as creating the methods yourself.

## Limiting the allowed enums

If you don't like the idea that you can create pretty much any enum via the magic
method, you can limit them like this:

```php
<?php

use rikudou\PHPEnum\EnumTrait;

class MyCoolEnum
{
  use EnumTrait;
  
  private static function allowedValues()
  {
    return [
      "Value1",
      "Value2",
    ];
  }
  
  public static function Value3()
  {
    return static::_get("Value3");
  } 
}
```

Now your enum contains **only** `Value1`, `Value2` and `Value3`.
Anything else will throw `InvalidArgumentException`.

As you can see, the `allowedValues()` array doesn't care about the methods
you create manually.

## Using the enums

```php
<?php

function doSomething(MyCoolEnum $myCoolEnum)
{
  switch ($myCoolEnum) {
    case MyCoolEnum::Value1():
      return "Value1";
    case MyCoolEnum::Value2():
      return "Value2";
    // etc
  }
}
```

As you can see, you can typehint the enum and php itself will check that it's really
an instance of the enum.

Then you can just check them in switch or if/else or whatever.

## Getting enum value

If you need the value that the enum holds, you can use the `getValue()` method.

The methods constructed `__callStatic` magic method hold the value of the method name:

```php
<?php

echo MyCoolEnum::SomeCoolValue()->getValue(); // echoes "SomeCoolValue"
```

The methods you create have a value that you give them:

```php
<?php

use rikudou\PHPEnum\EnumTrait;

class MyCoolEnum {
  
  use EnumTrait;
  
  public static function MyCoolValue() {
    return static::_get(1);
  }
  
}

echo MyCoolEnum::MyCoolValue()->getValue(); // echoes 1
```

## Some caveats

- the trait caches the objects based on value, meaning that if you create
two enum values (static methods) with same value, they will return the same object,
e.g. they will return true for equality test

```php
<?php

use rikudou\PHPEnum\EnumTrait;

class MyCoolEnum {
  
  use EnumTrait;
  
  public static function Value1() {
    return static::_get("Value");
  }
  
  public static function Value2() {
    return static::_get("Value"); // same as in Value1()
  }
  
}

var_dump(MyCoolEnum::Value1() === MyCoolEnum::Value2()); // dumps true

```

- if you for any reason wanted to serialize the enum and store it (session, db, etc.),
after unserializing it wouldn't pass the equality test, therefore the enum cannot be
serialized and will throw a `LogicException` if you try to (using magic methods 
`__sleep` and `__wakeup`)


And that's all, folks. Now all that remains is waiting for a real enum implementation
on the php side.
