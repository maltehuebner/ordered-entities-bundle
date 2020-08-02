<?php declare(strict_types=1);

namespace MalteHuebner\OrderedEntitiesBundle\OrderedEntities\Annotation;

abstract class AbstractAnnotation
{
    public function __construct(array $options = [])
    {
        foreach ($options as $key => $value) {
            if (!property_exists($this, $key)) {
                throw new \InvalidArgumentException(sprintf('Property "%s" does not exist', $key));
            }

            $this->$key = $value;
        }
    }
}
