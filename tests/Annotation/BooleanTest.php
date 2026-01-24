<?php declare(strict_types=1);

namespace MalteHuebner\OrderedEntitiesBundle\Tests\Annotation;

use MalteHuebner\OrderedEntitiesBundle\Annotation\Boolean;
use PHPUnit\Framework\TestCase;

class BooleanTest extends TestCase
{
    public function testGetValueReturnsTrue(): void
    {
        $boolean = new Boolean(['value' => true]);

        $this->assertTrue($boolean->getValue());
    }

    public function testGetValueReturnsFalse(): void
    {
        $boolean = new Boolean(['value' => false]);

        $this->assertFalse($boolean->getValue());
    }

    public function testThrowsExceptionForUnknownProperty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Property "unknown" does not exist');

        new Boolean(['unknown' => true]);
    }
}
