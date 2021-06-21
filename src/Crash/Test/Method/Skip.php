<?php
declare(strict_types=1);

namespace Crash\Test\Method;

/**
 * Attribute that represents a skipped Test case.
 *
 * @package Crash
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
class Skip extends \Crash\Test\Skip {}