<?php declare(strict_types=1);

namespace MalteHuebner\OrderedEntitiesBundle\Annotation;

/**
 * @Annotation
 */
class Order extends AbstractAnnotation
{
    /** @var string $direction */
    protected $direction;

    public function getDirection(): ?string
    {
        return $this->direction;
    }
}
