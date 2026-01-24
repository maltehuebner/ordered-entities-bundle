<?php declare(strict_types=1);

namespace MalteHuebner\OrderedEntitiesBundle\Annotation;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Identical extends AbstractAnnotation
{

}
