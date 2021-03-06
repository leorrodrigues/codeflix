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
use App\Http\Resources\GenreResource;
use App\Models\Genre;
use App\Models\Category;
use Tests\Traits\TestResources;

class GenreControllerTest extends TestCase
{

    use DatabaseMigrations, TestValidations, TestSaves, TestResources;

    private $genre;
    private $sendData;
    private $fieldsSerialized = [
        'id',
        'name',
        'is_active',
        'created_at',
        'updated_at',
        'deleted_at',
        'categories' => [
            '*' => [
                'id',
                'name',
                'description',
                'is_active',
                'created_at',
                'updated_at',
                'deleted_at'
            ]
        ]
    ];

    protected function setUp():void
    {
        parent::setUp();

        $this->genre = factory(Genre::class)->create();

        $this->sendData = [
            'name' => 'name',
            'is_active' => true,
        ];

    }

    protected function tearDown(): void
    {
        $this->genre = null;
        $this->sendData = null;
        parent::tearDown();
    }

    public function testIndex()
    {
        $response = $this->get(route('genres.index'));
        $response
            ->assertStatus(200)
            ->assertJsonStructure(
                [
                    'data' => [
                        '*' => $this->fieldsSerialized
                    ],
                    'meta' => [],
                    'links' => [],
                ]
            );
        $this->assertResource($response, GenreResource::collection(collect([$this->genre])));
    }

    public function testShow()
    {
        $response = $this->get(route('genres.show',['genre'=>$this->genre->id]));
        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => $this->fieldsSerialized
            ])
            ->assertJsonFragment($this->genre->toArray());


        $this->assertResource($response, new GenreResource($this->genre));
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
            ],
            [
                'send_data' => $this->sendData + [
                    'categories_id' => [$category->id]
                ],
                'test_data' => $this->sendData + [
                    'is_active' => false
                ],
            ]
        ];

        foreach ($data as $key => $value){
            $response = $this->assertStore(
                $value['send_data'],
                $value['test_data'] +
                ['deleted_at' => null]
            );

            $this->assertResource($response, new GenreResource(Genre::find($response->json('data.id'))));
            $response->assertJsonStructure([
                'data' => $this->fieldsSerialized
            ]);

            $response = $this->assertUpdate(
                $value['send_data'],
                $value['test_data'] +
                ['deleted_at' => null]
            );

            $response->assertJsonStructure([
                    'data' => $this->fieldsSerialized
            ]);

            $this->assertResource($response, new GenreResource(Genre::find($response->json('data.id'))));
            $this->assertHasCategory($response->json('data.id'), $category->id);
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
            'genre_id' => $response->json('data.id')
        ]);

        $sendData = [
            'name' => 'test',
            'categories_id' => [$categoriesId[1], $categoriesId[2]]
        ];
        $response = $this->json(
            'PUT',
            route('genres.update', ['genre' => $response->json('data.id')]),
            $sendData
        );
        $this->assertDatabaseMissing('category_genre', [
            'category_id' => $categoriesId[0],
            'genre_id' => $response->json('data.id')
        ]);
        $this->assertDatabaseHas('category_genre', [
            'category_id' => $categoriesId[1],
            'genre_id' => $response->json('data.id')
        ]);
        $this->assertDatabaseHas('category_genre', [
            'category_id' => $categoriesId[2],
            'genre_id' => $response->json('data.id')
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
