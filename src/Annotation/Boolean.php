<?php declare(strict_types=1);

namespace MalteHuebner\OrderedEntitiesBundle\Annotation;

/**
 * @Annotation
 */
class Boolean extends AbstractAnnotation
{
    protected bool $value;

    public function getValue(): ?bool
    {
        return $this->value;
    }
}
