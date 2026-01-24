<?php declare(strict_types=1);

namespace MalteHuebner\OrderedEntitiesBundle\Annotation;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Boolean extends AbstractAnnotation
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
