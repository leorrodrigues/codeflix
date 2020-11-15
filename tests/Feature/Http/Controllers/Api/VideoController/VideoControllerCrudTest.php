<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Http\Resources\VideoResource;
use Tests\Feature\Http\Controllers\Api\VideoController\BaseVideoControllerTestCase;
use Tests\Traits\TestValidations;
use Tests\Traits\TestSaves;
use App\Models\Category;
use App\Models\Video;
use App\Models\Genre;

use Illuminate\Support\Arr;
use Tests\Traits\TestResources;

class VideoControllerCrudTest extends BaseVideoControllerTestCase
{
    use TestValidations, TestSaves, TestResources;

    private $fieldSerialized = [
        'id',
        'title',
        'description',
        'rating',
        'duration',
        'opened',
        'thumb_file_url',
        'banner_file_url',
        'trailer_file_url',
        'video_file_url',
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
                'deleted_at',
            ],
        ],
        'genres' => [
            '*' => [
                'id',
                'name',
                'is_active',
                'created_at',
                'updated_at',
                'deleted_at',
            ],
        ],
    ];

    public function testIndex()
    {
        $response = $this->get(route('videos.index'));

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => $this->fieldSerialized
                ],
                'meta' => [],
                'links' => []
            ]);
        $this->assertResource($response, VideoResource::collection(collect([$this->video])));
        $this->assertIfFilesUrlExists($this->video, $response);
    }

    public function testShow()
    {
        $response = $this->get(route('videos.show', ['video'=> $this->video->id]));

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => $this->fieldSerialized
            ]);
        $this->assertResource(
            $response,
            new VideoResource(Video::find($response->json('data.id')))
        );
        $this->assertIfFilesUrlExists($this->video, $response);
    }

    public function testInvalidationRequired()
    {
        $data = [
            'title' => '',
            'description' => '',
            'year_launched' => '',
            'rating' => '',
            'duration' => '',
            'categories_id' => '',
            'genres_id' => '',
        ];
        $this->assertInvalidationInStoreAction($data, 'required');
        $this->assertInvalidationInUpdateAction($data, 'required');
    }

    public function testInvalidationMax(){
        $data = [
            'title' => str_repeat('a', 256),
        ];

        $this->assertInvalidationInStoreAction($data, 'max.string', ['max' => 255]);
        $this->assertInvalidationInUpdateAction($data, 'max.string', ['max' => 255]);
    }

    public function testInvalidationInteger(){
        $data = [
            'duration' => 's',
        ];

        $this->assertInvalidationInStoreAction($data, 'integer');
        $this->assertInvalidationInUpdateAction($data, 'integer');
    }

    public function testInvalidationYearLaunchedField(){
        $data = [
            'year_launched' => 'a',
        ];

        $this->assertInvalidationInStoreAction($data, 'date_format', ['format' => 'Y']);
        $this->assertInvalidationInUpdateAction($data, 'date_format', ['format' => 'Y']);
    }

    public function testInvalidationOpenedField(){
        $data = [
            'opened' => 's',
        ];

        $this->assertInvalidationInStoreAction($data, 'boolean');
        $this->assertInvalidationInUpdateAction($data, 'boolean');
    }

    public function testInvalidationRatingField(){
        $data = [
            'rating' => 0,
        ];

        $this->assertInvalidationInStoreAction($data, 'in');
        $this->assertInvalidationInUpdateAction($data, 'in');
    }

    public function testSaveWithoudFiles(){
        $testData = Arr::except($this->sendData, ['categories_id', 'genres_id']);
        $data = [
            [
                'send_data' => $this->sendData,
                'test_data' => $testData + ['opened' => false]
            ],
            [
                'send_data' => $this->sendData + [
                    'opened' => true,
                ],
                'test_data' => $testData + ['opened' => true]
            ],
            [
                'send_data' => $this->sendData + [
                    'rating' => Video::RATING_LIST[1],
                ],
                'test_data' => $testData + ['rating' => Video::RATING_LIST[1]]
            ],
        ];

        foreach ($data as $key => $value) {
            $response = $this->assertStore(
                $value['send_data'],
                $value['test_data'] + ['deleted_at' => null]
            );

            $response -> assertJsonStructure([
                'data' => $this->fieldSerialized
            ]);
            $this->assertHasCategory($response->json('data.id'), $value['send_data']['categories_id'][0]);
            $this->assertHasGenre($response->json('data.id'), $value['send_data']['genres_id'][0]);
            $this->assertResource(
                $response,
                new VideoResource(Video::find($response->json('data.id')))
            );

            $response = $this->assertUpdate(
                $value['send_data'],
                $value['test_data'] + ['deleted_at' => null]
            );

            $response -> assertJsonStructure([
                'data' => $this->fieldSerialized
            ]);
            $this->assertHasCategory($response->json('data.id'), $value['send_data']['categories_id'][0]);
            $this->assertHasGenre($response->json('data.id'), $value['send_data']['genres_id'][0]);
            $this->assertResource(
                $response,
                new VideoResource(Video::find($response->json('data.id')))
            );
            $this->assertIfFilesUrlExists($this->video, $response);
        }
    }

    protected function assertHasCategory($videoId, $categoryId)
    {
        $this->assertDatabaseHas('category_video',[
            'video_id' => $videoId,
            'category_id' => $categoryId,
        ]);
    }

    protected function assertHasGenre($videoId, $genreId)
    {
        $this->assertDatabaseHas('genre_video',[
            'video_id' => $videoId,
            'genre_id' => $genreId,
        ]);
    }

    public function testInvalidationCategoriesIdField()
    {
        $data = [
            'categories_id' => 'a'
        ];
        $this->assertInvalidationInStoreAction($data, 'array');
        $this->assertInvalidationInUpdateAction($data, 'array');


        $data = [
            'categories_id' => [100]
        ];
        $this->assertInvalidationInStoreAction($data, 'exists');
        $this->assertInvalidationInUpdateAction($data, 'exists');

        $category = factory(Category::class)->create();
        $category->delete();
        $data = [
            'categories_id' => [$category->id]
        ];
        $this->assertInvalidationInStoreAction($data, 'exists');
        $this->assertInvalidationInUpdateAction($data, 'exists');
    }

    public function testInvalidationGenresIdField()
    {
        $data = [
            'genres_id' => 'a'
        ];
        $this->assertInvalidationInStoreAction($data, 'array');
        $this->assertInvalidationInUpdateAction($data, 'array');

        $data = [
            'genres_id' => [100]
        ];
        $this->assertInvalidationInStoreAction($data, 'exists');
        $this->assertInvalidationInUpdateAction($data, 'exists');

        $genre = factory(Genre::class)->create();
        $genre->delete();
        $data = [
            'categories_id' => [$genre->id]
        ];
        $this->assertInvalidationInStoreAction($data, 'exists');
        $this->assertInvalidationInUpdateAction($data, 'exists');
    }

    public function testDelete()
    {

        $response = $this->json('DELETE', route('videos.destroy',['video' => $this->video->id]));

        $response->assertStatus(204);

        $this->video->refresh();

        $this->assertNull(Video::find($this->video->id));

        $videos = Video::all();
        $this->assertCount(0, $videos);

        $this->assertNotNull($this->video->deleted_at);
        $this->assertNotNull(Video::onlyTrashed()->first());

    }

    protected function routeStore()
    {
        return route('videos.store');
    }

    protected function routeUpdate()
    {
        return route('videos.update', ['video' => $this->video->id]);
    }

    protected function model()
    {
        return Video::class;
    }
}
