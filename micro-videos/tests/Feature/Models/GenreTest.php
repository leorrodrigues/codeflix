<?php

namespace Tests\Feature\Models;

use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GenreTest extends TestCase
{
    use DatabaseMigrations;

    public function testList()
    {
        factory(Genre::class, 1)->create();
        $genres = Genre::all();
        $this->assertCount(1, $genres);
        $genreKeys = array_keys($genres->first()->getAttributes());
        $expectedKeys = [
            'id', 'name', 'is_active','created_at', 'updated_at', 'deleted_at'
        ];
        $this->assertEqualsCanonicalizing($expectedKeys, $genreKeys);
    }

    public function testCreate()
    {
        $genre = Genre::create([
            'name' => 'test_name'
        ]);
        $genre->refresh();

        $this->assertEquals('test_name', $genre->name);
        $this->assertTrue($genre->is_active);

        $genre = Genre::create([
            'name' => 'test_name',
            'is_active' => false
        ]);

        $this->assertFalse($genre->is_active);


        $genre = Genre::create([
            'name' => 'test_name',
            'is_active' => true
        ]);

        $this->assertTrue($genre->is_active);

        $UUIDv4 = '/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i';
        $this->assertTrue((bool)preg_match($UUIDv4, $genre->id));
    }

    public function testUpdate()
    {
        $genre = factory(Genre::class)->create([
            'is_active' => false
        ]);

        $data = [
            'name' => 'test_name_updated',
            'is_active' => true
        ];

        $genre->update($data);

        foreach($data as $key => $value) {
            $this->assertEquals($value, $genre->{$key});
        }
    }

    public function testDelete()
    {
        /** @var Genre $genre */
        $genre = factory(Genre::class)->create();
        $genre->delete();
        $this->assertNull(Genre::find($genre->id));

        $genres = Genre::all();
        $this->assertCount(0, $genres);

        $genre->restore();
        $this->assertNotNull(Genre::find($genre->id));
    }
}
