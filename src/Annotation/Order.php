<?php declare(strict_types=1);

namespace MalteHuebner\OrderedEntitiesBundle\Annotation;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Order extends AbstractAnnotation
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
