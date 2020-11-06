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
        $this->CastMember = new CastMember();
    }

    public function testFillableAttribute()
    {
        $fillable = ['name','type','is_active'];
        $this->assertEquals($fillable,$this->CastMember->getFillable());
    }

    public function testDatesAttribute()
    {
        $dates = ['deleted_at','created_at','updated_at'];
        foreach ($dates as $date){
            $this->assertContains($date,$this->CastMember->getDates());
        }
        $this->assertCount(count($dates),$this->CastMember->getDates());
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
        $this->assertEquals($casts,$this->CastMember->getCasts());
    }

    public function testIncrementingAttribute()
    {
        $this->assertFalse($this->CastMember->incrementing);
    }
}
