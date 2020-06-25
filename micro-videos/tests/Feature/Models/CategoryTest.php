<?php

namespace Tests\Feature\Models;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use DatabaseMigrations;

    public function testList()
    {
        factory(Category::class, 1)->create();
        $categories = Category::all();
        $this->assertCount(1, $categories);
        $categoryKeys = array_keys($categories->first()->getAttributes());
        $expectedKeys = [
            'id', 'name', 'description', 'is_active','created_at', 'updated_at', 'deleted_at'
        ];
        $this->assertEqualsCanonicalizing($expectedKeys, $categoryKeys);
    }

    public function testCreate()
    {
        $category = Category::create([
            'name' => 'test_name'
        ]);
        $category->refresh();

        $this->assertEquals('test_name', $category->name);
        $this->assertNull($category->description);
        $this->assertTrue($category->is_active);

        $category = Category::create([
            'name' => 'test_name',
            'description' => null
        ]);

        $this->assertNull($category->description);


        $category = Category::create([
            'name' => 'test_name',
            'description' => 'test_description'
        ]);

        $this->assertEquals('test_description', $category->description);

        $category = Category::create([
            'name' => 'test_name',
            'is_active' => false
        ]);

        $this->assertFalse($category->is_active);


        $category = Category::create([
            'name' => 'test_name',
            'is_active' => true
        ]);

        $this->assertTrue($category->is_active);
    }


}
