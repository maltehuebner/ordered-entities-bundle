<?php declare(strict_types=1);

namespace MalteHuebner\OrderedEntitiesBundle\Tests\CriteriaBuilder;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\Common\Collections\Expr\CompositeExpression;
use MalteHuebner\OrderedEntitiesBundle\CriteriaBuilder\CriteriaBuilder;
use MalteHuebner\OrderedEntitiesBundle\SortOrder;
use MalteHuebner\OrderedEntitiesBundle\Tests\Fixtures\CategorizedEntity;
use MalteHuebner\OrderedEntitiesBundle\Tests\Fixtures\SimpleOrderEntity;
use MalteHuebner\OrderedEntitiesBundle\Tests\Fixtures\UnattributedEntity;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Applies the generated Criteria to in-memory collections so the tests check
 * what the expressions actually select, not only that "some" expression exists.
 */
class CriteriaBuilderBehaviourTest extends TestCase
{
    private CriteriaBuilder $criteriaBuilder;

    protected function setUp(): void
    {
        $this->criteriaBuilder = new CriteriaBuilder();
    }

    public function testEntityWithoutAttributesProducesEmptyCriteria(): void
    {
        $criteria = $this->criteriaBuilder->build(new UnattributedEntity(3), SortOrder::ASC);

        $this->assertNull($criteria->getWhereExpression());
        $this->assertSame([], $criteria->orderings());
        $this->assertNull($criteria->getMaxResults());
    }

    public function testAscSelectsOnlyStrictlySmallerPositions(): void
    {
        $collection = $this->positions(1, 3, 5, 5, 7);

        $matched = $collection->matching($this->criteriaBuilder->build(new SimpleOrderEntity(5), SortOrder::ASC));

        $this->assertSame([1, 3], $this->positionsOf($matched));
    }

    public function testDescSelectsOnlyStrictlyGreaterPositions(): void
    {
        $collection = $this->positions(1, 3, 5, 5, 7, 9);

        $matched = $collection->matching($this->criteriaBuilder->build(new SimpleOrderEntity(5), SortOrder::DESC));

        $this->assertSame([9, 7], $this->positionsOf($matched), 'descending order: largest first');
    }

    public function testAscOrdersCandidatesAscendingSoTheLastOneIsTheClosest(): void
    {
        $collection = $this->positions(7, 1, 4, 2);

        $matched = $collection->matching($this->criteriaBuilder->build(new SimpleOrderEntity(5), SortOrder::ASC));

        $this->assertSame([1, 2, 4], $this->positionsOf($matched));
    }

    public function testNothingMatchesForTheSmallestEntityInAscDirection(): void
    {
        $collection = $this->positions(1, 2, 3);

        $matched = $collection->matching($this->criteriaBuilder->build(new SimpleOrderEntity(1), SortOrder::ASC));

        $this->assertCount(0, $matched);
    }

    public function testNothingMatchesForTheLargestEntityInDescDirection(): void
    {
        $collection = $this->positions(1, 2, 3);

        $matched = $collection->matching($this->criteriaBuilder->build(new SimpleOrderEntity(3), SortOrder::DESC));

        $this->assertCount(0, $matched);
    }

    public function testOrderAttributeDirectionIsIgnoredInFavourOfTheRequestedDirection(): void
    {
        // SimpleOrderEntity declares #[Order(direction: 'desc')], but the builder
        // only uses the direction passed to build().
        $criteria = $this->criteriaBuilder->build(new SimpleOrderEntity(5), SortOrder::ASC);

        $this->assertSame(\Doctrine\Common\Collections\Order::Ascending, $criteria->orderings()['position']);
    }

    public function testIdenticalRestrictsToTheSameCategory(): void
    {
        $collection = new ArrayCollection([
            new CategorizedEntity(1, 'a'),
            new CategorizedEntity(2, 'b'),
            new CategorizedEntity(3, 'a'),
            new CategorizedEntity(4, 'b'),
        ]);

        $matched = $collection->matching($this->criteriaBuilder->build(new CategorizedEntity(5, 'a'), SortOrder::ASC));

        $this->assertSame([1, 3], $this->positionsOf($matched));
    }

    public function testIdenticalDoesNotMatchAcrossCategories(): void
    {
        $collection = new ArrayCollection([
            new CategorizedEntity(1, 'a'),
            new CategorizedEntity(2, 'a'),
        ]);

        $matched = $collection->matching($this->criteriaBuilder->build(new CategorizedEntity(5, 'zzz'), SortOrder::ASC));

        $this->assertCount(0, $matched);
    }

    public function testBooleanFiltersByTheAttributeValueNotByTheEntityValue(): void
    {
        $collection = new ArrayCollection([
            new CategorizedEntity(1, 'a', enabled: true),
            new CategorizedEntity(2, 'a', enabled: false),
            new CategorizedEntity(3, 'a', enabled: true),
        ]);

        // The reference entity itself is disabled; the #[Boolean(value: true)]
        // attribute still selects only enabled siblings.
        $matched = $collection->matching($this->criteriaBuilder->build(new CategorizedEntity(9, 'a', enabled: false), SortOrder::ASC));

        $this->assertSame([1, 3], $this->positionsOf($matched));
    }

    public function testAllConditionsAreCombinedWithAnd(): void
    {
        $criteria = $this->criteriaBuilder->build(new CategorizedEntity(5, 'a'), SortOrder::ASC);
        $where = $criteria->getWhereExpression();

        $this->assertInstanceOf(CompositeExpression::class, $where);
        $this->assertSame(CompositeExpression::TYPE_AND, $where->getType());
        $this->assertSame(['position', 'category', 'enabled'], $this->comparedFields($where));
    }

    /**
     * andWhere() nests composites ((a AND b) AND c); collect the leaf comparisons
     * and make sure every composite on the way is an AND.
     *
     * @return list<string>
     */
    private function comparedFields(CompositeExpression|Comparison $expression): array
    {
        if ($expression instanceof Comparison) {
            return [$expression->getField()];
        }

        $this->assertSame(CompositeExpression::TYPE_AND, $expression->getType());
        $fields = [];
        foreach ($expression->getExpressionList() as $child) {
            $fields = [...$fields, ...$this->comparedFields($child)];
        }

        return $fields;
    }

    public function testComparisonOperatorsFollowTheDirection(): void
    {
        $asc = $this->criteriaBuilder->build(new SimpleOrderEntity(5), SortOrder::ASC)->getWhereExpression();
        $desc = $this->criteriaBuilder->build(new SimpleOrderEntity(5), SortOrder::DESC)->getWhereExpression();

        $this->assertInstanceOf(Comparison::class, $asc);
        $this->assertInstanceOf(Comparison::class, $desc);
        $this->assertSame(Comparison::LT, $asc->getOperator());
        $this->assertSame(Comparison::GT, $desc->getOperator());
        $this->assertSame('position', $asc->getField());
        $this->assertSame(5, $asc->getValue()->getValue());
    }

    #[DataProvider('directions')]
    public function testEachCallBuildsAFreshCriteriaInstance(string $direction): void
    {
        $entity = new SimpleOrderEntity(5);

        $first = $this->criteriaBuilder->build($entity, $direction);
        $second = $this->criteriaBuilder->build($entity, $direction);

        $this->assertNotSame($first, $second);
        $this->assertEquals($first->orderings(), $second->orderings());
    }

    /** @return iterable<string, array{string}> */
    public static function directions(): iterable
    {
        yield 'asc' => [SortOrder::ASC];
        yield 'desc' => [SortOrder::DESC];
    }

    public function testDateTimeOrderingComparesChronologically(): void
    {
        $older = new \MalteHuebner\OrderedEntitiesBundle\Tests\Fixtures\TestEntity(new \DateTime('2024-01-01'), 'c', true, 'older');
        $newer = new \MalteHuebner\OrderedEntitiesBundle\Tests\Fixtures\TestEntity(new \DateTime('2024-03-01'), 'c', true, 'newer');
        $reference = new \MalteHuebner\OrderedEntitiesBundle\Tests\Fixtures\TestEntity(new \DateTime('2024-02-01'), 'c', true, 'reference');

        $collection = new ArrayCollection([$newer, $older]);

        $previous = $collection->matching($this->criteriaBuilder->build($reference, SortOrder::ASC));
        $next = $collection->matching($this->criteriaBuilder->build($reference, SortOrder::DESC));

        $this->assertSame([$older], $previous->getValues());
        $this->assertSame([$newer], $next->getValues());
    }

    /** @return ArrayCollection<int, SimpleOrderEntity> */
    private function positions(int ...$positions): ArrayCollection
    {
        return new ArrayCollection(array_map(static fn (int $p) => new SimpleOrderEntity($p), $positions));
    }

    /** @return list<int> */
    private function positionsOf(iterable $entities): array
    {
        $result = [];
        foreach ($entities as $entity) {
            $result[] = $entity->getPosition();
        }

        return $result;
    }
}
