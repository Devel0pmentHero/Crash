<?php
declare(strict_types=1);

namespace Crash\Test\Method;

use Crash\Attribute;
use Crash\Random;

/**
 * Attribute that represents a Test case crasher.
 *
 * @package Crash
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
class BruteForce extends Attribute implements \IteratorAggregate {

    /**
     * Initializes a new instance of the Crash Attribute class.
     *
     * @param string   $Password
     * @param null|int $Limit
     * @param null|int $Interval
     */
    public function __construct(
        public string $Password = "",
        public ?int $Limit = null,
        public ?int $Interval = null
    ) {
        $this->Amount ??= \random_int(1, 100);
    }

    /**
     * @inheritDoc
     */
    public function getIterator(): \Generator {
        for($Index = 0; $Index < $this->Amount; $Index++) {
            yield $Index => Random::Values();
        }
    }
}