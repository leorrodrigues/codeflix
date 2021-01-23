<?php

namespace App\Rules;

use \App\Rules\GenresHasCategoriesRule;
use Mockery\MockInterface;
use Tests\TestCase;

class GenresHasCategoriesRuleUnitTest extends TestCase
{
    public function testCategoriesIdField()
    {
        $rule = new GenresHasCategoriesRule(
            [1, 1, 2, 2]
        );
        $reflectionClass = new \ReflectionClass(GenresHasCategoriesRule::class);
        $reflectionProperty = $reflectionClass->getProperty('categoriesId');
        $reflectionProperty->setAccessible(true);

        $categoriesId = $reflectionProperty->getValue($rule);
        $this->assertEqualsCanonicalizing([1,2], $categoriesId);
    }

    public function testGenresIdValue()
    {
        /** @var \Mock\MockInterface */
        $rule = $this->createRuleMock([]);

        $rule->shouldReceive('getRows')
            ->withAnyArgs()
            ->andReturnNull();

        $rule->passes('', [1, 1, 2, 2]);

        $reflectionClass = new \ReflectionClass(GenresHasCategoriesRule::class);
        $reflectionProperty = $reflectionClass->getProperty('genresId');
        $reflectionProperty->setAccessible(true);

        $genresId = $reflectionProperty->getValue($rule);
        $this->assertEqualsCanonicalizing([1,2], $genresId);
    }

    public function testPassesReturnFalseWhenCategoriesOrGenresIsArrayEmpty()
    {
        /** @var \Mock\MockInterface */
        $rule = $this->createRuleMock([1]);
        $this->assertFalse($rule->passes('',[]));

        /** @var \Mock\MockInterface */
        $rule = $this->createRuleMock([]);
        $this->assertFalse($rule->passes('',[1]));
    }

    public function testPassesReturnsFalseWhenGetRowsIsEmpty()
    {
        /** @var \Mock\MockInterface */
        $rule = $this->createRuleMock([]);
        $rule->shouldReceive('getRows')
            ->withAnyArgs()
            ->andReturn(collect());
        $this->assertFalse($rule->passes('', [1]));
    }

    public function testPassesReturnsFalseWhenHasCategoriesWithoutGenres()
    {
        /** @var \Mock\MockInterface */
        $rule = $this->createRuleMock([1,2]);
        $rule->shouldReceive('getRows')
            ->withAnyArgs()
            ->andReturn(collect(['category_id' => 1]));
        $this->assertFalse($rule->passes('', [1]));
    }

    public function testPassesIsValid()
    {
        /** @var \Mock\MockInterface */
        $rule = $this->createRuleMock([1,2]);
        $rule->shouldReceive('getRows')
            ->withAnyArgs()
            ->andReturn(collect([
                ['category_id' => 1],
                ['category_id' => 2]
            ]));
        $this->assertTrue($rule->passes('', [1]));

        /** @var \Mock\MockInterface */
        $rule = $this->createRuleMock([1,2]);
        $rule->shouldReceive('getRows')
            ->withAnyArgs()
            ->andReturn(collect([
                ['category_id' => 1],
                ['category_id' => 2],
                ['category_id' => 1],
                ['category_id' => 2],
            ]));
        $this->assertTrue($rule->passes('', [1]));
    }

    protected function createRuleMock(array $categoriesId): MockInterface
    {
        return \Mockery::mock(GenresHasCategoriesRule::class, [$categoriesId])
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
    }
}
