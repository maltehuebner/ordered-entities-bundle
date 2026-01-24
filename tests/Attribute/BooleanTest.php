<?php declare(strict_types=1);

namespace MalteHuebner\OrderedEntitiesBundle\Tests\Attribute;

use MalteHuebner\OrderedEntitiesBundle\Attribute\Boolean;
use PHPUnit\Framework\TestCase;

class BooleanTest extends TestCase
{
    public function testGetValueReturnsTrue(): void
    {
        $boolean = new Boolean(value: true);

        $this->assertTrue($boolean->getValue());
    }

    public function testGetValueReturnsFalse(): void
    {
        $boolean = new Boolean(value: false);

        $this->assertFalse($boolean->getValue());
    }
}
