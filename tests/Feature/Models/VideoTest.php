<?php

namespace Tests\Feature\Models;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Video;

class VideoTest extends TestCase
{

    use DatabaseMigrations;

    public function testList()
    {
        factory(Video::class,1)->create();
        $videos = Video::all();
        $this->assertCount(1,$videos);
        $videoKey = array_keys($videos->first()->getAttributes());
        $this->assertEqualsCanonicalizing(
            [
                'id',
                'title',
                'description',
                'year_launched',
                'opened',
                'rating',
                'duration',
                'created_at',
                'updated_at',
                'deleted_at'
            ],
            $videoKey
        );
    }

    public function testCreate()
    {

        $Video = Video::create(
            [
                'title' => 'test1',
                'description' => 'description',
                'year_launched' => 2010,
                'rating' => Video::RATING_LIST[0],
                'duration' => 90
            ]
         );
         $Video->refresh();
         $regex = '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/';
         $this->assertTrue((bool) preg_match($regex, $Video->id));
         $this->assertEquals(36, strlen($Video->id));
         $this->assertNotNull(Video::find($Video->id));

        $Video = Video::create(
            [
                'title' => 'test1',
                'description' => 'description',
                'year_launched' => 2010,
                'rating' => Video::RATING_LIST[0],
                'duration' => 90
            ]
        );
        $Video->refresh();
        $this->assertEquals('test1',$Video->title);
        $this->assertNull($Video->deleted_at);

        $Video = Video::create(
            [
                'title' => 'test1',
                'description' => 'test_description',
                'opened' => true,
                'year_launched' => 2010,
                'rating' => Video::RATING_LIST[0],
                'duration' => 90
            ]
        );
        $Video->refresh();
        $this->assertTrue($Video->opened);

        $Video = Video::create(
            [
                'title' => 'test1',
                'description' => 'test_description',
                'year_launched' => 2010,
                'rating' => Video::RATING_LIST[0],
                'duration' => 90
            ]
        );
        $Video->refresh();
        $this->assertEquals('test_description',$Video->description);

        $Video = Video::create(
            [
                'title' => 'test1',
                'description' => 'test_description',
                'year_launched' => 2010,
                'rating' => Video::RATING_LIST[0],
                'duration' => 90
            ]
        );
        $Video->refresh();

        $Video = Video::create(
            [
                'title' => 'test1',
                'description' => 'test_description',
                'year_launched' => 2010,
                'rating' => Video::RATING_LIST[0],
                'duration' => 90
            ]
        );
        $Video->refresh();

    }

    public function testUpdate()
    {

        $Video = factory(Video::class)->create(
            [
                'title' => 'test_title',
                'description' => 'test_description',
                'year_launched' => 2010,
                'rating' => Video::RATING_LIST[0],
                'duration' => 90,
            ]
        );

        $Video->refresh();

        $data = [
            'title' => 'test_title_updated',
            'description' => 'test_description_updated',
            'year_launched' => 2010,
            'rating' => Video::RATING_LIST[0],
            'duration' => 90,
        ];

        $Video->update($data);

        foreach ($data as $key => $value){
            $this->assertEquals($value, $Video->{$key});
        }

    }

    public function testDelete()
    {

        $Video = factory(Video::class)->create();
        $videos = Video::all();
        $this->assertCount(1, $videos);

        $Video->delete();
        $this->assertNull(Video::find($Video->id));

        $videos = Video::all();
        $this->assertCount(0, $videos);

        $this->assertNotNull($Video->deleted_at);
        $this->assertNotNull(Video::onlyTrashed()->first());

        $Video->restore();
        $this->assertNotNull(Video::find($Video->id));

    }

}
