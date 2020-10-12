<?php declare(strict_types=1);

namespace MalteHuebner\OrderedEntitiesBundle\Annotation;

/**
 * @Annotation
 */
class Boolean extends AbstractAnnotation
{
    /** @var bool $value */
    protected $value;

    public function getValue(): ?bool
    {
        return $this->value;
    }
}
