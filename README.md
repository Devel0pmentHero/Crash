# Crash

A small unit- and pentest library for PHP.

## Installation

``composer require devel0pmenthero/crash``

## Usage

Note: Any parameters can be specified with a leading capital letter.

### Generating tests

#### Single classes

```php
Crash::Create(Subject::class, true, false);
```

```bash
php Crash.php -c[--create] --class="Subject"
```

#### Directories

```php
Crash::CreateFromPath("./targetdir", true, false);
```

```bash
php Crash.php -c[--create] -p[--path]="./targetdir"
```

Inherited methods can be included via passing an optional "inherit"-parameter.

```bash
php Crash.php -c[--create] -p[--path]="" -i[--inherit]
```

Existing test classes can be overwritten by passing an optional "overwrite"-parameter.

```bash
php Crash.php -c[--create] -p[--path]="" -o[--overwrite]
```

### Running tests

```php
Crash::Test("./targetdir");
```

```bash
php Crash.php -t[--test] -p[--path]="./targetdir"
```

Omitting the path will default to the path stored in the public ``Crash::Tests`` constant.

## Attributes

Crash allows the user to control the execution of specific tests via defining custom attributes. While generating
Crash\Tests from existing source, the library will copy over any existing (Crash-)Attributes to the final test classes.

### Repeat

``#[Repeat(Amount: int, Interval: int)]``

The "Repeat"-Attribute allows to execute a test case multiple times in an optional specified interval of microseconds.

### Crash

``#[Crash(Amount: int, Interval: int)]``

The "Crash"-Attribute acts like the former one but passes a random amount of random values to the desired test case
instead.

### Values

``#[Values(Random: int, Interval: int, ...$Values)]``

The "Values"-Attribute allows specifying custom values being passed as parameters to the desired test case.
Alternatively, the Attribute can generate a specified random amount of values.

### Skip

``#[Skip]``

The "Skip"-Attribute simply instructs the library to skip the test- or case.

### Writing Tests

Test are simple php classes and must be located in the "Crash\Tests"-namespace to be recognized as a test.

```php
<?php
declare(strict_types=1);

namespace Crash\Tests;

use Crash\Test\Skip;
use Crash\Test\Method;
use Crash\Test\Method\Repeat;
use Crash\Test\Method\Values;
use Crash\Test\Method\Crash;

#[Skip]
class MyTest extends \Crash\Test {

    private int $Repetitions = 0;

    #[Repeat(5)]
    public function Repeat() {
        $this->Repetitions++;
    }

    public function RepeatedEnough(): void{
        \assert($this->Repetitions === 5);
    }
    
    #[Method\Skip]
    public function FailsAnyway(): void {
        \assert(false);
    }
    
    #[Values("a", 12, true)]
    public function Values($A, $B, $C): void {
        \assert($A === "a");
        \assert($B === 12);
        \assert($C === true);
    }    
    
    #[Values(Random: 8)]
    public function RandomValues(...$Values): void {
        \assert(\count($Values) === 8);
    }    
    
    #[Crash(Amount: 1000000, Interval: 20)]
    public function Crash(...$Values): void {
        ClassToTest::Method(...$Values);
    }

}
```