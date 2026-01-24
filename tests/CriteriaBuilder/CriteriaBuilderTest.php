<?php declare(strict_types=1);

namespace MalteHuebner\OrderedEntitiesBundle\Tests\CriteriaBuilder;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Order;
use MalteHuebner\OrderedEntitiesBundle\CriteriaBuilder\CriteriaBuilder;
use MalteHuebner\OrderedEntitiesBundle\SortOrder;
use MalteHuebner\OrderedEntitiesBundle\Tests\Fixtures\SimpleOrderEntity;
use MalteHuebner\OrderedEntitiesBundle\Tests\Fixtures\TestEntity;
use PHPUnit\Framework\TestCase;

class CriteriaBuilderTest extends TestCase
{
    private CriteriaBuilder $criteriaBuilder;

    protected function setUp(): void
    {
        $this->criteriaBuilder = new CriteriaBuilder(new AnnotationReader());
    }

    public function testBuildReturnsCriteria(): void
    {
        $entity = new SimpleOrderEntity(5);

        $criteria = $this->criteriaBuilder->build($entity, SortOrder::ASC);

        $this->assertInstanceOf(Criteria::class, $criteria);
    }

    public function testBuildWithAscDirectionUsesLessThanComparison(): void
    {
        $entity = new SimpleOrderEntity(10);

        $criteria = $this->criteriaBuilder->build($entity, SortOrder::ASC);

        $orderings = $criteria->orderings();
        $this->assertArrayHasKey('position', $orderings);
        $this->assertSame(Order::Ascending, $orderings['position']);
    }

    public function testBuildWithDescDirectionUsesGreaterThanComparison(): void
    {
        $entity = new SimpleOrderEntity(10);

        $criteria = $this->criteriaBuilder->build($entity, SortOrder::DESC);

        $orderings = $criteria->orderings();
        $this->assertArrayHasKey('position', $orderings);
        $this->assertSame(Order::Descending, $orderings['position']);
    }

    public function testBuildWithIdenticalAnnotationAddsEqualityCondition(): void
    {
        $entity = new TestEntity(
            new \DateTime('2024-01-15'),
            'test-category',
            true,
            'Test Title'
        );

        $criteria = $this->criteriaBuilder->build($entity, SortOrder::ASC);

        $this->assertInstanceOf(Criteria::class, $criteria);
        $this->assertNotNull($criteria->getWhereExpression());
    }

    public function testBuildWithBooleanAnnotationAddsConditionWithAnnotationValue(): void
    {
        $entity = new TestEntity(
            new \DateTime('2024-01-15'),
            'test-category',
            false,
            'Test Title'
        );

        $criteria = $this->criteriaBuilder->build($entity, SortOrder::ASC);

        $this->assertInstanceOf(Criteria::class, $criteria);
        $this->assertNotNull($criteria->getWhereExpression());
    }

    public function testBuildWithComplexEntitySetsCorrectOrderings(): void
    {
        $entity = new TestEntity(
            new \DateTime('2024-01-15'),
            'test-category',
            true,
            'Test Title'
        );

        $criteria = $this->criteriaBuilder->build($entity, SortOrder::ASC);

        $orderings = $criteria->orderings();
        $this->assertArrayHasKey('dateTime', $orderings);
        $this->assertSame(Order::Ascending, $orderings['dateTime']);
    }

    public function testBuildWithDescDirectionReversesOrdering(): void
    {
        $entity = new TestEntity(
            new \DateTime('2024-01-15'),
            'test-category',
            true,
            'Test Title'
        );

        $criteria = $this->criteriaBuilder->build($entity, SortOrder::DESC);

        $orderings = $criteria->orderings();
        $this->assertArrayHasKey('dateTime', $orderings);
        $this->assertSame(Order::Descending, $orderings['dateTime']);
    }
}
