<?php declare(strict_types=1);

namespace MalteHuebner\OrderedEntitiesBundle\Tests\Fixtures;

use MalteHuebner\OrderedEntitiesBundle\Attribute\Order;
use MalteHuebner\OrderedEntitiesBundle\OrderedEntityInterface;

class SimpleOrderEntity implements OrderedEntityInterface
{
    #[Order(direction: 'desc')]
    private int $position;

    public function __construct(int $position)
    {
        $this->position = $position;
    }

    public function getPosition(): int
    {
        return $this->position;
    }
}
