<?php
declare(strict_types=1);

namespace Crash\Test\Method;

/**
 * Attribute that represents a repeatable Test or case.
 *
 * @package Crash
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
class Repeat extends \Crash\Test\Repeat {}