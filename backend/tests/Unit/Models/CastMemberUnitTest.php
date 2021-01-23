<?php

namespace Tests\Unit\Models;

use App\Models\CastMember;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
use PHPUnit\Framework\TestCase;

class CastMemberTest extends TestCase
{
    private $castMember;

    protected function setUp(): void
    {
        parent::setUp();
        $this->castMember = new CastMember();
    }

    protected function tearDown(): void
    {
        $this->castMember = null;
        parent::tearDown();
    }

    public function testFillableAttribute()
    {
        $fillable = ['name','type','is_active'];
        $this->assertEquals($fillable,$this->castMember->getFillable());
    }

    public function testDatesAttribute()
    {
        $dates = ['deleted_at','created_at','updated_at'];
        foreach ($dates as $date){
            $this->assertContains($date,$this->castMember->getDates());
        }
        $this->assertCount(count($dates),$this->castMember->getDates());
    }

    public function testIfUseTraits()
    {
        $traits = [
            SoftDeletes::class, Uuid::class
        ];
        $castMemberTraits = array_keys(class_uses(CastMember::class));
        $this->assertEquals($traits,$castMemberTraits);
    }

    public function testCastsAttribute()
    {
        $casts = ['id'=>'string', 'type'=>'integer', 'is_active'=>'boolean'];
        $this->assertEquals($casts,$this->castMember->getCasts());
    }

    public function testIncrementingAttribute()
    {
        $this->assertFalse($this->castMember->incrementing);
    }
}
