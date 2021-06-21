<?php
declare(strict_types=1);

namespace Crash;

/**
 * Utility class that provides functionality for generating random values.
 *
 * @package Crash
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Random {

    /**
     * Text values for random value generation.
     */
    public const Text = ["Lorem", "Ipsum", "Dolor", "Sit", "Amet"];

    /**
     * Generates a random value.
     *
     * @return mixed A random value of any type.
     */
    public static function Value() {
        switch(\random_int(0, 7)) {
            case 0:
                return static::Int(true);
            case 1:
                return static::Float(true);
            case 2:
                return static::Char();
            case 3:
                return static::String();
            case 4:
                return static::Bool();
            case 5:
                return static::Array();
            case 6:
                return (object)static::Array();
            case 7:
                return null;
        }
    }

    /**
     * Generates a numeric array of random values.
     *
     * @param null|int $Amount Optional amount of random values.
     *
     * @return array A numeric array of random values.
     */
    public static function Values(int $Amount = null): array {
        return \array_map("static::Value", \range(0, $Amount ?? \random_int(1, 10)));
    }

    /**
     * Generates a random integer.
     *
     * @param bool $Negative $Flag indicating whether to allow negative values.
     *
     * @return int A random integer.
     */
    public static function Int(bool $Negative = false): int {
        if($Negative) {
            return \random_int(\PHP_INT_MAX * -1, \PHP_INT_MAX);
        }
        return \random_int(0, \PHP_INT_MAX);
    }


    /**
     * Generates a random float.
     *
     * @param bool $Negative $Flag indicating whether to allow negative values.
     *
     * @return float A random float.
     */
    public static function Float(bool $Negative = false): float {
        return static::Int($Negative) / \time();
    }

    /**
     * Generates a random boolean value.
     *
     * @return bool A random bool.
     */
    public static function Bool(): bool {
        return (bool)\random_int(0, 1);
    }

    /**
     * Generates a random string.
     *
     * @return string A random string.
     */
    public static function String(): string {

        switch(\random_int(0, 4)) {
            case 0:
                return static::Text[\random_int(0, \count(static::Text) - 1)];
            case 1:
                return \implode(
                    "",
                    \array_map(
                        static function($Index) {
                            return static::Text[$Index];
                        },
                        \array_rand(
                            static::Text,
                            \random_int(2, \count(static::Text) - 1)
                        )
                    )
                );
            case 2:
                return \implode("", \array_map("static::Char", \range(0, \random_int(2, 60))));
            case 3:
                return \implode(static::Char(), static::Text);
            case 4:
                return \str_shuffle(static::String());

        }
    }

    /**
     * Generates a random upper- or lowercase alphabetical character.
     *
     * @return string A random char.
     */
    public static function Char(): string {
        switch(\random_int(0, 1)) {
            case 0:
                return \chr(\random_int(65, 90));
            case 1:
                return \chr(\random_int(97, 122));
        }
    }

    /**
     * Generates a random array.
     *
     * @return array A random array.
     */
    public static function Array(): array {
        switch(\random_int(0, 5)) {
            case 0:
                return \range(0, \random_int(1, 15));
            case 1:
                switch(\random_int(0, 1)) {
                    case 0:
                        return static::Text;
                    case 1:
                        return \array_reverse(static::Text);
                }
            case 2:
                return \array_map("static::Value", \range(0, \random_int(3, 10)));
            case 3:
                return \array_map("static::String", \range(0, \random_int(1, 15)));
            case 4:
                return \array_map("static::Float", \range(0, \random_int(1, 15)));
            case 5:
                return \array_map("static::Int", \range(0, \random_int(1, 15)));
        }
    }

}