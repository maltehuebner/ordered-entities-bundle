<?php declare(strict_types=1);

namespace MalteHuebner\OrderedEntitiesBundle\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Order extends AbstractAttribute
{
    public function __construct(
        protected string $direction
    ) {
    }

    public function getDirection(): string
    {
        return $this->direction;
    }
}
