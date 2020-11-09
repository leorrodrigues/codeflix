<?php

namespace Tests\Feature\Http\Controllers\Api\VideoController;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

use App\Models\Video;

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
            'video_file' => 'video.mp4',
        ]);

        $this->sendData = [
            'title' => 'title',
            'description' => 'description',
            'year_launched' => 2010,
            'rating' => Video::RATING_LIST[0],
            'duration' => 90,
            'opened' => false,
        ];
    }

    protected function tearDown(): void
    {
        // $this->video = null;
        // $this->sendData = null;
        parent::tearDown();
    }
}
