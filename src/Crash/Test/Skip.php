<?php
declare(strict_types=1);

namespace Crash\Test;

use Crash\Attribute;

/**
 * Attribute that represents a skipped Test.
 *
 * @package Crash
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class Skip extends Attribute {}