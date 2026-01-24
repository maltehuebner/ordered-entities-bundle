<?php declare(strict_types=1);

namespace MalteHuebner\OrderedEntitiesBundle\Tests\Annotation;

use MalteHuebner\OrderedEntitiesBundle\Annotation\OrderedEntity;
use PHPUnit\Framework\TestCase;

class OrderedEntityTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $orderedEntity = new OrderedEntity();

        $this->assertInstanceOf(OrderedEntity::class, $orderedEntity);
    }

    public function testCanBeInstantiatedWithEmptyOptions(): void
    {
        $orderedEntity = new OrderedEntity([]);

        $this->assertInstanceOf(OrderedEntity::class, $orderedEntity);
    }
}
