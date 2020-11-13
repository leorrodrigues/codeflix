<?php

namespace Tests\Feature\Http\Controllers\Api\VideoController;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

use App\Models\Video;
use App\Models\Genre;
use App\Models\Category;

abstract class BaseVideoControllerTestCase extends TestCase
{
    use DatabaseMigrations;

    protected $video;
    protected $sendData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->video = factory(Video::class)->create([
            'opened' => false,
            'thumb_file' => 'thumb.jpg',
            'banner_file' => 'banner.jpg',
            'trailer_file' => 'trailer.mp4',
            'video_file' => 'video.mp4',
        ]);

        $category = factory(Category::class)->create();
        $genre = factory(Genre::class)->create();
        $genre->categories()->sync($category->id);

        $this->sendData = [
            'title' => 'title',
            'description' => 'description',
            'year_launched' => 2010,
            'rating' => Video::RATING_LIST[0],
            'duration' => 90,
            'opened' => false,
            'categories_id' => [$category->id],
            'genres_id' => [$genre->id],
        ];
    }

    protected function tearDown(): void
    {
        $this->video = null;
        $this->sendData = null;
        parent::tearDown();
    }
}
