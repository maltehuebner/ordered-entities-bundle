<?php declare(strict_types=1);

namespace MalteHuebner\OrderedEntitiesBundle\Tests\Fixtures;

use MalteHuebner\OrderedEntitiesBundle\Attribute\Boolean;
use MalteHuebner\OrderedEntitiesBundle\Attribute\Identical;
use MalteHuebner\OrderedEntitiesBundle\Attribute\Order;
use MalteHuebner\OrderedEntitiesBundle\OrderedEntityInterface;

/**
 * Entity with all three property attributes: siblings are only entities of
 * the same category that are enabled, ordered by position.
 */
class CategorizedEntity implements OrderedEntityInterface
{
    public function __construct(
        #[Order(direction: 'asc')]
        private readonly int $position,
        #[Identical]
        private readonly string $category,
        #[Boolean(value: true)]
        private readonly bool $enabled = true,
        private readonly string $name = '',
    ) {
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function getEnabled(): bool
    {
        return $this->enabled;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
