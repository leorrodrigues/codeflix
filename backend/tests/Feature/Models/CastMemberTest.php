<?php

namespace Tests\Feature\Models;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\CastMember;

class CastMemberTest extends TestCase
{

    use DatabaseMigrations;

    public function testList()
    {
        factory(CastMember::class,1)->create();
        $castMembers = CastMember::all();
        $this->assertCount(1,$castMembers);
        $castMemberKey = array_keys($castMembers->first()->getAttributes());
        $this->assertEqualsCanonicalizing(
            [
                'id',
                'name',
                'type',
                'created_at',
                'updated_at',
                'deleted_at',
                'is_active'
            ],
            $castMemberKey
        );
    }

    public function testCreate()
    {

        $castMember = CastMember::create(
            [
                'name' => 'name_test',
                'type' => '1'
            ]
         );
         $castMember->refresh();
         $regex = '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/';
         $this->assertTrue((bool) preg_match($regex, $castMember->id));
         $this->assertEquals(36, strlen($castMember->id));
         $this->assertNotNull(CastMember::find($castMember->id));

        $castMember = CastMember::create(
            [
                'name' => 'name_test',
                'type' => '1'
            ]
        );
        $castMember->refresh();
        $this->assertEquals('name_test',$castMember->name);
        $this->assertEquals('1',$castMember->type);
        $this->assertTrue($castMember->is_active);

        $castMember = CastMember::create(
            [
                'name' => 'name_test',
                'type' => '1',
                'is_active' => false
            ]
        );
        $castMember->refresh();
        $this->assertFalse($castMember->is_active);

        $castMember = CastMember::create(
            [
                'name' => 'name_test',
                'type' => '1',
                'is_active' => true
            ]
        );
        $castMember->refresh();
        $this->assertTrue($castMember->is_active);

    }

    public function testUpdate()
    {

        $castMember = factory(CastMember::class)->create(
            [
                'name' => 'test_name',
                'type' => '1',
                'is_active' => false
            ]
        );

        $castMember->refresh();

        $data = [
            'name' => 'test_name_updated',
            'type' => '2',
            'is_active' => true
        ];

        $castMember->update($data);

        foreach ($data as $key => $value){
            $this->assertEquals($value, $castMember->{$key});
        }

    }

    public function testDelete()
    {

        $castMember = factory(CastMember::class)->create();
        $castMembers = CastMember::all();
        $this->assertCount(1, $castMembers);

        $castMember->delete();
        $this->assertNull(CastMember::find($castMember->id));

        $castMembers = CastMember::all();
        $this->assertCount(0, $castMembers);

        $this->assertNotNull($castMember->deleted_at);
        $this->assertNotNull(CastMember::onlyTrashed()->first());

        $castMember->restore();
        $this->assertNotNull(CastMember::find($castMember->id));

    }

}
