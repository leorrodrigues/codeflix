<?php

namespace Tests\Feature\Http\Controllers\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\TestValidations;
use Tests\Traits\TestResources;
use Tests\Traits\TestSaves;
use Tests\TestCase;

use App\Http\Resources\CastMemberResource;
use App\Models\CastMember;

class CastMemberControllerTest extends TestCase
{
    use RefreshDatabase, TestValidations, TestSaves, TestResources;

    private $castMember;
    private $fieldsSerialized = [
        'id',
        'name',
        'type',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->castMember = factory(CastMember::class)->create([
            'type' => CastMember::TYPE_DIRECTOR
        ]);
    }

    protected function tearDown(): void
    {
        $this->castMember = null;
        parent::tearDown();
    }

    public function testIndex()
    {
        $response = $this->get(route('cast_members.index'));

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => $this->fieldsSerialized
                ],
                'meta' => [],
                'links' => [],
            ])
            ->assertJsonFragment($this->castMember->toArray());
    }

    public function testShow()
    {
        $response = $this->json('GET', route('cast_members.show', ['cast_member' => $this->castMember->id]));

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => $this->fieldsSerialized
            ]);

        $id = $response->json('data.id');
        $resource = new CastMemberResource(CastMember::find($id));
        $this->assertResource($response, $resource);
    }

    public function testInvalidationData()
    {
        $data = [
            'name' => '',
            'type' => ''
        ];
        $this->assertInvalidationInStoreAction($data, 'required');
        $this->assertInvalidationInUpdateAction($data, 'required');

        $data = [
            'type' => 's',
        ];
        $this->assertInvalidationInStoreAction($data, 'in');
        $this->assertInvalidationInUpdateAction($data, 'in');
    }

    public function testStore(){

        $data = [
            [
                'name'=>'test',
                'type' => CastMember::TYPE_DIRECTOR
            ],
            [
                'name' => 'test',
                'type' => CastMember::TYPE_ACTOR
            ]
        ];

        foreach ($data as $key => $value) {
            $response = $this->assertStore($value,$value + ['deleted_at' => null]);
            $response->assertJsonStructure([
                'data' => $this->fieldsSerialized
            ]);
            $this->assertResource($response, new CastMemberResource(
                CastMember::find($response->json('data.id'))
            ));
        }
    }

    public function testUpdate(){
        $data = [
            'name' => 'test',
            'type' => CastMember::TYPE_ACTOR
        ];
        $response = $this->assertUpdate($data, $data + ['deleted_at' => null]);
        $response->assertJsonStructure([
            'data' => $this->fieldsSerialized
        ]);
        $this->assertResource($response, new CastMemberResource(
            CastMember::find($response->json('data.id'))
        ));
    }

    public function testDelete()
    {

        $response = $this->json('DELETE', route('cast_members.destroy',['cast_member' => $this->castMember->id]));

        $response->assertStatus(204);

        $this->castMember->refresh();

        $this->assertNull(CastMember::find($this->castMember->id));

        $this->assertNotNull($this->castMember->deleted_at);
        $this->assertNotNull(CastMember::withTrashed()->find($this->castMember->id));

    }

    protected function routeStore()
    {
        return route('cast_members.store');
    }

    protected function routeUpdate()
    {
        return route('cast_members.update', ['cast_member' => $this->castMember]);
    }

    protected function model()
    {
        return CastMember::class;
    }
}
