<?php declare(strict_types=1);

namespace MalteHuebner\OrderedEntitiesBundle\Annotation;

/**
 * @Annotation
 */
class Order extends AbstractAnnotation
{
    protected string $direction;

    public function getDirection(): ?string
    {
        return $this->direction;
    }
}
