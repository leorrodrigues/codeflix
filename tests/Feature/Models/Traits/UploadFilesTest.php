<?php

namespace Tests\Feature\Http\Models\Traits;

use Illuminate\Http\UploadedFile;
use Tests\Stubs\Models\UploadFilesStub;
use Tests\TestCase;

class UploadFilesTest extends TestCase {
    private $obj;

    protected function setUp(): void
    {
        parent::setUp();

        UploadFilesStub::dropTable();
        UploadFilesStub::makeTable();

        $this->obj = new UploadFilesStub();
    }

    public function testMakeOldFilesOnSaving()
    {
        $this->obj->fill([
            'name' => 'test',
            'file1' => 'test1.mp4',
            'file2' => 'test2.mp4',
        ]);
        $this->obj->save();

        $this->assertCount(0, $this->obj->oldFiles);

        $this->obj->update([
            'name' => 'test_name',
            'file2' => 'test3.mp4'
        ]);

        $this->assertEqualsCanonicalizing(['test2.mp4'], $this->obj->oldFiles);
    }

    public function testMakeOldFilesNullOnSaving()
    {
        $this->obj->fill([
            'name' => 'test',
        ]);
        $this->obj->save();

        $this->assertCount(0, $this->obj->oldFiles);

        $this->obj->update([
            'name' => 'test_name',
            'file2' => 'test3.mp4'
        ]);

        $this->assertEqualsCanonicalizing([], $this->obj->oldFiles);
    }
}
