# Crash
A small unit- and pentest library for PHP.

## Installation
``composer require devel0pmenthero/crash``

## Usage
Generating tests:
``php Crash.php -c[--Create] -p[--path]="./targetdir"``
Inherited methods can be included via passing an optional "inherit"-parameter.
``php Crash.php -c[--Create] -p[--path]="" -i[--inherit]``
Existing test classes can be overwritten by passing an optional "overwrite"-parameter.
``php Crash.php -c[--Create] -p[--path]="" -o[--overwrite]``

Note: Any parameters can be specified with a leading capital letter.

## Attributes
Crash allows the user to control the execution of specific tests via defining custom attributes.
While generating Crash\Tests from existing source, the library will copy over any existing (Crash-)Attributes to the final test classes.

### Repeat
``#[Repeat(Amount: int, Interval: int)]``

The "Repeat"-Attribute allows to execute a test case multiple times in an optional specified interval of microseconds.

### Crash
``#[Crash(Amount: int, Interval: int)]``

The "Crash"-Attribute acts like the former one but passes a random amount of random values to the desired test case instead.

### Values
``#[Values(Random: int, Interval: int, ...$Values)]``

The "Values"-Attribute allows specifying custom values being passed as parameters to the desired test case.
Alternatively, the Attribute can generate a specified random amount of values.

### Skip
``#[Skip]``

The "Skip"-Attribute simply instructs the library to skip the test- or case. 