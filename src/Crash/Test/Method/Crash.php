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
class Crash extends Attribute implements \IteratorAggregate {

    /**
     * Initializes a new instance of the Crash Attribute class.
     *
     * @param null|int $Amount   Initializes the Crash Attribute with the specified repetition amount.
     * @param null|int $Interval Initializes the Crash Attribute with the specified interval in microseconds.
     */
    public function __construct(public ?int $Amount = null, public ?int $Interval = null) {
        $this->Amount ??= \random_int(1, 100);
    }

    /**
     * @inheritDoc
     */
    public function getIterator(): \Generator {
        if($this->Interval !== null) {
            for($Index = 0; $Index < $this->Amount; $Index++) {
                yield $Index => Random::Values();
                \usleep($this->Interval);
            }
        } else {
            for($Index = 0; $Index < $this->Amount; $Index++) {
                yield $Index => Random::Values();
            }
        }
    }

}