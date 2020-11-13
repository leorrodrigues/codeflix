<?php

namespace Tests\Feature\Models;

use App\Models\Video;
use Illuminate\Database\Events\TransactionCommitted;
use Illuminate\Http\UploadedFile;
use Tests\Exceptions\TestException;
use Tests\Feature\Models\Video\BaseVideoTestCase;

class VideoUploadTest extends BaseVideoTestCase
{
    protected $thumbFile;
    protected $bannerFile;
    protected $trailerFile;
    protected $videoFile;

    protected function setUp(): void
    {
        parent::setUp();

        $this->thumbFile = UploadedFile::fake()->image('thumb.jpg')->size(Video::THUMB_FILE_MAX_SIZE);
        $this->bannerFile = UploadedFile::fake()->image('banner.jpg')->size(Video::BANNER_FILE_MAX_SIZE);
        $this->trailerFile = UploadedFile::fake()->create('trailer.mp4')->size(Video::TRAILER_FILE_MAX_SIZE);
        $this->videoFile = UploadedFile::fake()->create('video.mp4')->size(Video::VIDEO_FILE_MAX_SIZE);
    }

    protected function tearDown(): void
    {
        $thumbFile = null;
        $bannerFile = null;
        $trailerFile = null;
        $videoFile = null;

        parent::tearDown();
    }

    public function testCreateWithFiles()
    {
        \Storage::fake();
        $video = Video::create(
            $this->data + [
                'thumb_file' => $this->thumbFile,
                'banner_file' => $this->bannerFile,
                'trailer_file' => $this->trailerFile,
                'video_file' => $this->videoFile,

            ]
        );
        \Storage::assertExists("{$video->id}/{$video->thumb_file}");
        \Storage::assertExists("{$video->id}/{$video->banner_file}");
        \Storage::assertExists("{$video->id}/{$video->trailer_file}");
        \Storage::assertExists("{$video->id}/{$video->video_file}");
    }

    public function testCreateIfRollbackFiles()
    {
        \Storage::fake();
        \Event::listen(TransactionCommitted::class, function() {
            throw new TestException();
        });

        try {
            Video::create(
                $this->data + [
                    'thumb_file' => $this->thumbFile,
                    'banner_file' => $this->bannerFile,
                    'trailer_file' => $this->trailerFile,
                    'video_file' => $this->videoFile,
                ]
            );
        } catch(TestException $e) {
            $this->assertCount(0, \Storage::allFiles());
            $hasError = true;
        }
        $this->assertTrue($hasError);
    }

    public function testUpdateWithFiles()
    {
        \Storage::fake();
        $video = factory(Video::class)->create();

        $video->update($this->data + [
            'thumb_file' => $this->thumbFile,
            'banner_file' => $this->bannerFile,
            'trailer_file' => $this->trailerFile,
            'video_file' => $this->videoFile,
        ]);

        \Storage::assertExists("{$video->id}/{$video->thumb_file}");
        \Storage::assertExists("{$video->id}/{$video->banner_file}");
        \Storage::assertExists("{$video->id}/{$video->trailer_file}");
        \Storage::assertExists("{$video->id}/{$video->video_file}");

        $newBannerFile = UploadedFile::fake()->image('banner.jpg');
        $newVideoFile = UploadedFile::fake()->create('video.mp4');
        $video->update($this->data + [
            'video_file' => $newVideoFile,
            'banner_file' => $newBannerFile,
        ]);

        \Storage::assertExists("{$video->id}/{$this->thumbFile->hashName()}");
        \Storage::assertExists("{$video->id}/{$newBannerFile->hashName()}");
        \Storage::assertExists("{$video->id}/{$this->trailerFile->hashName()}");
        \Storage::assertExists("{$video->id}/{$newVideoFile->hashName()}");

        \Storage::assertMissing("{$video->id}/{$this->videoFile->hashName()}");
        \Storage::assertMissing("{$video->id}/{$this->bannerFile->hashName()}");
    }

    public function testUpdateIfRollbackFiles()
    {
        \Storage::fake();
        $video = factory(Video::class)->create();
        \Event::listen(TransactionCommitted::class, function() {
            throw new TestException();
        });

        $hasError = false;
        try {
            $video->update([
                'thumb_file' => UploadedFile::fake()->image('thumb.jpg'),
                'banner_file' => UploadedFile::fake()->image('banner.jpg'),
                'trailer_file' => UploadedFile::fake()->create('trailer.mp4'),
                'video_file' => UploadedFile::fake()->create('video.mp4'),
            ]);
        } catch(TestException $e) {
            $this->assertCount(0, \Storage::allFiles());
            $hasError = true;
        }
        $this->assertTrue($hasError);
    }

    public function testFileUrlWithLocalDriver()
    {
        $fileFields = [];
        foreach (Video::$fileFields as $field) {
            $fileFields[$field] = "$field.test";
        }
        $video = factory(Video::class)->create($fileFields);
        $localDriver = config('filesystems.default');
        $baseUrl = config('filesystems.disks.' . $localDriver)['url'];
        foreach ($fileFields as $field => $value) {
            $fileUrl = $video->{"{$field}_url"};
            $this->assertEquals("{$baseUrl}/$video->id/$value", $fileUrl);
        }
    }

    public function testFileUrlWithGCSDriver()
    {
        $fileFields = [];
        foreach (Video::$fileFields as $field) {
            $fileFields[$field] = "$field.test";
        }
        $video = factory(Video::class)->create($fileFields);
        $baseUrl = config('filesystems.disks.gcs.storage_api_uri');
        \Config::set('filesystems.default', 'gcs');
        foreach ($fileFields as $field => $value) {
            $fileUrl = $video->{"{$field}_url"};
            $this->assertEquals("{$baseUrl}/$video->id/$value", $fileUrl);
        }
    }

    public function testFileUrlsIfNullWhenFiledsAreNull()
    {
        $video = factory(Video::class)->create();
        foreach (Video::$fileFields as $field) {
            $fileUrl = $video->{"{$field}_url"};
            $this->assertNull($fileUrl);
        }
    }
}
