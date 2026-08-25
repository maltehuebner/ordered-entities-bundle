<?php declare(strict_types=1);

namespace MalteHuebner\OrderedEntitiesBundle\Tests\Fixtures;

use MalteHuebner\OrderedEntitiesBundle\OrderedEntityInterface;

class UnattributedEntity implements OrderedEntityInterface
{
    public function __construct(private readonly int $position)
    {
    }

    public function getPosition(): int
    {
        return $this->position;
    }
}
