<?php declare(strict_types=1);

namespace MalteHuebner\OrderedEntitiesBundle\Tests\Annotation;

use MalteHuebner\OrderedEntitiesBundle\Annotation\Identical;
use PHPUnit\Framework\TestCase;

class IdenticalTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $identical = new Identical();

        $this->assertInstanceOf(Identical::class, $identical);
    }

    public function testCanBeInstantiatedWithEmptyOptions(): void
    {
        $identical = new Identical([]);

        $this->assertInstanceOf(Identical::class, $identical);
    }
}
