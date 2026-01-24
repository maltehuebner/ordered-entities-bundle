<?php declare(strict_types=1);

namespace MalteHuebner\OrderedEntitiesBundle\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Boolean extends AbstractAttribute
{
    public function __construct(
        protected bool $value
    ) {
    }

    public function getValue(): bool
    {
        return $this->value;
    }
}
