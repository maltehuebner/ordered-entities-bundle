<?php declare(strict_types=1);

namespace MalteHuebner\OrderedEntitiesBundle\Tests\Attribute;

use MalteHuebner\OrderedEntitiesBundle\Attribute\OrderedEntity;
use PHPUnit\Framework\TestCase;

class OrderedEntityTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $orderedEntity = new OrderedEntity();

        $this->assertInstanceOf(OrderedEntity::class, $orderedEntity);
    }
}
