<?php

namespace Tests\Unit\Models;

use App\Models\Category;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
use PHPUnit\Framework\TestCase;

class CategoryUnitTest extends TestCase
{
    protected function setUp(): void {
        parent::setUp();
        $this->category = new Category();
    }

    protected function tearDown(): void
    {
        $this->category = null;
        parent::tearDown();
    }

    public function testIfUseTraits(){
        $traits = [
            Uuid::class,
            SoftDeletes::class
        ];
        $categoryTraits = array_keys(class_uses(Category::class));
        $this->assertEqualsCanonicalizing($traits, $categoryTraits);
    }

    public function testFillableAttribute()
    {
        $fillable = ['name', 'description', 'is_active'];
        $this->assertEquals($fillable, $this->category->getFillable());
    }

    public function testCastsAttribute()
    {
        $casts = ['id' => 'string', 'is_active' => 'boolean'];
        $this->assertEquals($casts, $this->category->getCasts());
    }

    public function testIncrementingAttribute()
    {
        $this->assertFalse($this->category->incrementing);
    }

    public function testDatesAttribute()
    {
        $dates = ['deleted_at', 'created_at', 'updated_at'];

        $this->assertEqualsCanonicalizing($dates, $this->category->getDates());
    }
}
