<?php declare(strict_types=1);

namespace MalteHuebner\OrderedEntitiesBundle\Tests\Fixtures;

use MalteHuebner\OrderedEntitiesBundle\Annotation\Boolean;
use MalteHuebner\OrderedEntitiesBundle\Annotation\Identical;
use MalteHuebner\OrderedEntitiesBundle\Annotation\Order;
use MalteHuebner\OrderedEntitiesBundle\Annotation\OrderedEntity;
use MalteHuebner\OrderedEntitiesBundle\OrderedEntityInterface;

/**
 * @OrderedEntity
 */
class TestEntity implements OrderedEntityInterface
{
    /**
     * @Order(direction="asc")
     */
    private \DateTimeInterface $dateTime;

    /**
     * @Identical
     */
    private string $category;

    /**
     * @Boolean(value=true)
     */
    private bool $enabled;

    private string $title;

    public function __construct(
        \DateTimeInterface $dateTime,
        string $category,
        bool $enabled,
        string $title
    ) {
        $this->dateTime = $dateTime;
        $this->category = $category;
        $this->enabled = $enabled;
        $this->title = $title;
    }

    public function getDateTime(): \DateTimeInterface
    {
        return $this->dateTime;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function getEnabled(): bool
    {
        return $this->enabled;
    }

    public function getTitle(): string
    {
        return $this->title;
    }
}
