<?php declare(strict_types=1);

namespace MalteHuebner\OrderedEntitiesBundle\Tests\Attribute;

use MalteHuebner\OrderedEntitiesBundle\Attribute\Identical;
use PHPUnit\Framework\TestCase;

class IdenticalTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $identical = new Identical();

        $this->assertInstanceOf(Identical::class, $identical);
    }
}
