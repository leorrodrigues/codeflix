<?php

namespace App\Http\Controllers\Api;

use App\Models\CastMember;
use App\Http\Controllers\Controller;

class CastMemberController extends BasicCrudController
{
    private $rules;

    public function __construct()
    {
        $this->rules = [
            'name' => 'required|max:255',
            'type' => 'required|in:' . implode(',',CastMember::$types),
            // 'type' => 'required|integer|in:'. implode(',', [CastMember::TYPE_DIRECTOR, CastMember::TYPE_ACTOR]),
            'is_active' => 'boolean'
        ];
    }

    protected function model()
    {
        return CastMember::class;
    }

    protected function rulesStore()
    {
        return $this->rules;
    }

    protected function rulesUpdate()
    {
        return $this->rules;
    }
}
