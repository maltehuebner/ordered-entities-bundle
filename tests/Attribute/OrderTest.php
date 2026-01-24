<?php declare(strict_types=1);

namespace MalteHuebner\OrderedEntitiesBundle\Tests\Attribute;

use MalteHuebner\OrderedEntitiesBundle\Attribute\Order;
use PHPUnit\Framework\TestCase;

class OrderTest extends TestCase
{
    public function testGetDirectionReturnsAsc(): void
    {
        $order = new Order(direction: 'asc');

        $this->assertSame('asc', $order->getDirection());
    }

    public function testGetDirectionReturnsDesc(): void
    {
        $order = new Order(direction: 'desc');

        $this->assertSame('desc', $order->getDirection());
    }
}
