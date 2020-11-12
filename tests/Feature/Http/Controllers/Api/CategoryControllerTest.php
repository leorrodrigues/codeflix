<?php

namespace Tests\Feature\Http\Controllers\Api;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\Category;
use Tests\TestCase;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;
use Illuminate\Foundation\Testing\TestResponse;

class CategoryControllerTest extends TestCase
{
    use DatabaseMigrations, TestValidations, TestSaves;

    private $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->category = factory(Category::class)->create();
    }

    protected function tearDown(): void
    {
        $this->category = null;
        parent::tearDown();
    }

    public function testIndex()
    {
        $response = $this->get(route('categories.index'));

        $response
            ->assertStatus(200)
            ->assertJson([$this->category->toArray()]);
    }

    public function testShow()
    {
        $response = $this->get(route('categories.show', ['category'=> $this->category->id]));

        $response
            ->assertStatus(200)
            ->assertJson($this->category->toArray());
    }

    public function testInvalidationData()
    {
        $data = [
            'name' => ''
        ];
        $this->assertInvalidationInStoreAction($data, 'required');
        $this->assertInvalidationInUpdateAction($data, 'required');

        $data = [
            'name' => str_repeat('a', 256),
        ];
        $this->assertInvalidationInStoreAction($data, 'max.string', ['max' => 255]);
        $this->assertInvalidationInUpdateAction($data, 'max.string', ['max' => 255]);

        $data = [
            'is_active' => 'a'
        ];
        $this->assertInvalidationInStoreAction($data, 'boolean');
        $this->assertInvalidationInUpdateAction($data, 'boolean');
    }

    protected function assertInvalidationRequired(TestResponse $response){
        $this->assertInvalidationFields(
            $response,
            ['name'],
            'required',
            []
        );
        $response
            ->assertJsonMissingValidationErrors(['is_active']);
    }

    protected function assertInvalidationMax(TestResponse $response){
        $this->assertInvalidationFields(
            $response,
            ['name'],
            'max.string',
            ['max'=>'255']
        );
    }

    protected function assertInvalidationBoolean(TestResponse $response){
        $this->assertInvalidationFields(
            $response,
            ['is_active'],
            'boolean'
        );
    }

    public function testStore(){

        $data = [
            'name'=>'test'
        ];
        $response = $this->assertStore($data,$data + ['description' => null,'is_active' => true,'deleted_at' => null]);
        $response->assertJsonStructure([
            'created_at',
            'updated_at'
        ]);

        $data = [
            'name'=>'test',
            'description'=>'description',
            'is_active'=>false
        ];
        $response = $this->assertStore($data,$data + ['description' => 'description','is_active' => false]);

    }

    public function testUpdate()
    {
        $data = [
            'name'=>'test',
            'description'=>'test',
            'is_active'=>true
        ];
        $response = $this->assertUpdate($data,$data + ['deleted_at' => null]);
        $response->assertJsonStructure([
            'created_at',
            'updated_at'
        ]);

        $data = [
            'name'=>'test',
            'description'=>'',
            'is_active'=>true
        ];
        $response = $this->assertUpdate($data,array_merge($data, ['description' => null]));

        $data['description']='test';
        $response = $this->assertUpdate($data,array_merge($data, ['description' => 'test']));

        $data['description']=null;
        $response = $this->assertUpdate($data,array_merge($data, ['description' => null]));
    }

    public function testDelete()
    {

        $response = $this->json('DELETE', route('categories.destroy',['category' => $this->category->id]));

        $response->assertStatus(204);

        $this->category->refresh();

        $this->assertNull(Category::find($this->category->id));

        $categories = Category::all();
        $this->assertCount(0, $categories);

        $this->assertNotNull($this->category->deleted_at);
        $this->assertNotNull(Category::onlyTrashed()->first());

    }

    protected function routeStore()
    {
        return route('categories.store');
    }

    protected function routeUpdate()
    {
        return route('categories.update', ['category' => $this->category->id]);
    }

    protected function model()
    {
        return Category::class;
    }
}
