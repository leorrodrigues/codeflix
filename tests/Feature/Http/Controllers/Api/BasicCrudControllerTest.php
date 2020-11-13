<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Http\Controllers\Api\BasicCrudController;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\Stubs\Controllers\CategoryControllerStub;
use Tests\Stubs\Models\CategoryStub;
use Tests\TestCase;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use ReflectionClass;

class BasicCrudControllerTest extends TestCase
{
    private $controller;

    protected function setUp(): void
    {
        parent::setUp();
        CategoryStub::dropTable();
        CategoryStub::createTable();
        $this->controller = new CategoryControllerStub();
    }

    protected function tearDown(): void
    {
        CategoryStub::dropTable();
        $this->controller = null;
        parent::tearDown();
    }

    public function testIndex()
    {
        /** @var CategoryStub $category */
        $category = CategoryStub::create(['name' => 'test_name', 'description' => 'test_description']);
        $result =  $this->controller->index()->toArray();
        $this->assertEquals([$category->toArray()], $result);
    }

    public function testInvalidationDataInStore()
    {
        $this->expectException(ValidationException::class);
        /** @var \Mockery\MockInterface */
        $request = \Mockery::mock(Request::class);
        $request->shouldReceive('all')
            ->once()
            ->andReturn(['name' => '']);
        $this->controller->store($request);
    }

    public function testStore()
    {
        /** @var \Mockery\MockInterface */
        $request = \Mockery::mock(Request::class);
        $request
            ->shouldReceive('all')
            ->once()
            ->andReturn(['name' => 'test_name', 'description' => 'test_description']);

        $obj = $this->controller->store($request);
        $this->assertEquals(
            CategoryStub::find(1)->toArray(),
            $obj->toArray()
        );
    }

    public function testIfFindOrFailFetchModel()
    {
        /** @var CategoryStub $category */
        $category = CategoryStub::create(['name' => 'test_name', 'description' => 'test_description']);

        $reflectionClass = new \ReflectionClass(BasicCrudController::class);
        $reflectionMethod = $reflectionClass->getMethod('findOrFail');
        $reflectionMethod->setAccessible(true);

        $result = $reflectionMethod->invokeArgs($this->controller, [$category->id]);
        $this->assertInstanceOf(CategoryStub::class, $result);
    }


    public function testIfFindOrFailThrowExceptionWhenIdInvalid()
    {
        $this->expectException(ModelNotFoundException::class);

        $reflectionClass = new \ReflectionClass(BasicCrudController::class);
        $reflectionMethod = $reflectionClass->getMethod('findOrFail');
        $reflectionMethod->setAccessible(true);

        $result = $reflectionMethod->invokeArgs($this->controller, [0]);
    }

    public function testShow()
    {
        $category = CategoryStub::create(['name'=>'test_name','description'=>'test_description']);
        $result = $this->controller->show($category->id);
        $this->assertEquals($result->toArray(), CategoryStub::find(1)->toArray());
    }

    public function testUpdate()
    {
        $category = CategoryStub::create(['name'=>'test_name','description'=>'test_description']);
        /** @var \Mockery\MockInterface */
        $result = \Mockery::mock(Request::class);
        $result
            ->shouldReceive('all')
            ->once()
            ->andReturn(['name'=>'test_changed','description'=>'description_changed']);
        $result = $this->controller->update($result, $category->id);
        $this->assertEquals($result->toArray(), CategoryStub::find(1)->toArray());
    }

    public function testDestroy()
    {
        $category = CategoryStub::create(['name'=>'test_name','description'=>'test_description']);
        /** @var \Mockery\MockInterface */
        $response = $this->controller->destroy($category->id);
        $this
            ->createTestResponse($response)
            ->assertStatus(204);
        $this->assertCount(0, CategoryStub::all());
    }
}
