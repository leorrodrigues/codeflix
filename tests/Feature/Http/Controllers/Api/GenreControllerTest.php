<?php

namespace Tests\Feature\Http\Controllers\Api;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Http\Request;
use Tests\TestCase;
use Lang;
use Tests\Traits\TestValidations;
use Tests\Traits\TestSaves;
use Tests\Exceptions\TestException;
use App\Http\Controllers\Api\GenreController;
use App\Models\Genre;
use App\Models\Category;


class GenreControllerTest extends TestCase
{

    use DatabaseMigrations, TestValidations, TestSaves;

    private $genre;
    private $sendData;

    protected function setUp():void
    {
        parent::setUp();

        $this->genre = factory(Genre::class)->create();

        $this->sendData = [
            'name' => 'name',
            'is_active' => true,
        ];

    }

    public function testIndex()
    {
        $response = $this->get(route('genres.index'));
        $response
            ->assertStatus(200)
            ->assertJson([$this->genre->toArray()]);
    }

    public function testShow()
    {
        $response = $this->get(route('genres.show',['genre'=>$this->genre->id]));
        $response
            ->assertStatus(200)
            ->assertJson($this->genre->toArray());
    }

    public function testInvalidationData()
    {

        $data = [
            'name'=>'',
            'categories_id' => ''
        ];
        $this->assertInvalidationInStoreAction($data,'required');
        $this->assertInvalidationInUpdateAction($data,'required');

        $data = [
            'name' => \str_repeat('a',256)
        ];
        $this->assertInvalidationInStoreAction($data,'max.string',['max'=>'255']);
        $this->assertInvalidationInUpdateAction($data,'max.string',['max'=>'255']);

        $data = [
            'is_active' => 'a'
        ];
        $this->assertInvalidationInStoreAction($data,'boolean');
        $this->assertInvalidationInUpdateAction($data,'boolean');


        $data = [
            'categories_id' => 'a'
        ];
        $this->assertInvalidationInStoreAction($data,'array');
        $this->assertInvalidationInUpdateAction($data,'array');


        $data = [
            'categories_id' => [100]
        ];
        $this->assertInvalidationInStoreAction($data,'exists');
        $this->assertInvalidationInUpdateAction($data,'exists');

        $category = factory(Category::class)->create();
        $category->delete();
        $data = [
            'categories_id' => [$category->id]
        ];
        $this->assertInvalidationInStoreAction($data, 'exists');
        $this->assertInvalidationInUpdateAction($data,'exists');
    }

    protected function assertInvalidationRequired(TestResponse $response){
        $this->assertInvalidationFields(
            $response,
            ['name','categories_id'],
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

    public function testSave()
    {
        $category = factory(Category::class)->create();

        $data = [
            [
                'send_data' => $this->sendData + [
                    'categories_id' => [$category->id]
                ],
                'test_data' => $this->sendData,
            ]
        ];

        foreach ($data as $key => $value){
            $response = $this->assertStore(
                $value['send_data'],
                $value['test_data'] +
                ['deleted_at' => null]
            );

            $response->assertJsonStructure([
                    'created_at',
                    'updated_at'
            ]);

            $response = $this->assertUpdate(
                $value['send_data'],
                $value['test_data'] +
                ['deleted_at' => null]
            );

            $response->assertJsonStructure([
                    'created_at',
                    'updated_at'
            ]);

            $this->assertHasCategory($response->json('id'), $category->id);
        }
    }

    protected function assertHasCategory($genreId, $categoryId)
    {
        $this->assertDatabaseHas('category_genre', [
            'genre_id' => $genreId,
            'category_id' => $categoryId,
        ]);
    }

    public function testRollbackStore()
    {
        $data = [
            'name'=>'test'
        ];
        $controller = \Mockery::mock(GenreController::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
        $controller->shouldReceive('handleRelations')
            ->once()
            ->andThrow(new TestException());
        $controller->shouldReceive('validate')
            ->withAnyArgs()
            ->andReturn($data);
        $controller->shouldReceive('rulesStore')
            ->withAnyArgs()
            ->andReturn([]);

        /** @var \Mockery\Interface $request */
        $request = \Mockery::mock(Request::class);
        $request->shouldReceive('get')->withAnyArgs()->andReturnNull();

        $hasError = false;
        try {
            $controller->store($request);
        }catch (TestException $e){
            $this->assertCount(1, Genre::all());
            $hasError = true;
        }
        $this->assertTrue($hasError);
    }

    public function testRollbackUpdate()
    {
        $controller = \Mockery::mock(GenreController::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $controller->shouldReceive('findOrFail')
            ->withAnyArgs()
            ->andReturn($this->genre);

        $controller->shouldReceive('validate')
            ->withAnyArgs()
            ->andReturn($this->sendData);

        $controller->shouldReceive('rulesUpdate')
            ->withAnyArgs()
            ->andReturn([]);

        $controller->shouldReceive('handleRelations')
            ->once()
            ->andThrow(new TestException());

        $request = \Mockery::mock(Request::class);

        $hasError = false;
        $this->genre->refresh();
        try {
            $controller->update($request, $this->genre->id);
        }catch (TestException $e){
            $this->assertCount(1, Genre::all());
            $hasError = true;
        }
        $this->assertTrue($hasError);
    }

    public function testDelete()
    {

        $response = $this->json('DELETE', route('genres.destroy',['genre' => $this->genre->id]));

        $response->assertStatus(204);

        $this->genre->refresh();

        $this->assertNull(Genre::find($this->genre->id));

        $genres = Genre::all();
        $this->assertCount(0, $genres);

        $this->assertNotNull($this->genre->deleted_at);
        $this->assertNotNull(Genre::onlyTrashed()->first());

    }

    public function testSyncCategories()
    {
        $categoriesId = factory(Category::class, 3)
            ->create()
            ->pluck('id')
            ->toArray();

        $sendData = [
            'name' => 'test',
            'categories_id' => [$categoriesId[0]]
        ];

        $response = $this->json('POST', $this->routeStore(), $sendData);
        $this->assertDatabaseHas('category_genre', [
            'category_id' => $categoriesId[0],
            'genre_id' => $response->json('id')
        ]);

        $sendData = [
            'name' => 'test',
            'categories_id' => [$categoriesId[1], $categoriesId[2]]
        ];
        $response = $this->json(
            'PUT',
            route('genres.update', ['genre' => $response->json('id')]),
            $sendData
        );
        $this->assertDatabaseMissing('category_genre', [
            'category_id' => $categoriesId[0],
            'genre_id' => $response->json('id')
        ]);
        $this->assertDatabaseHas('category_genre', [
            'category_id' => $categoriesId[1],
            'genre_id' => $response->json('id')
        ]);
        $this->assertDatabaseHas('category_genre', [
            'category_id' => $categoriesId[2],
            'genre_id' => $response->json('id')
        ]);
    }

    protected function routeStore(){
        return route('genres.store');
    }

    protected function routeUpdate(){
        return route('genres.update',['genre'=>$this->genre]);
    }

    protected function model(){
        return Genre::class;
    }

    protected function rulesStore()
    {
        return $this->rules;
    }

    protected function rulesUpdate()
    {
        return $this->rules;
    }
}
