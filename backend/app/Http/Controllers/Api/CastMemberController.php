<?php

namespace App\Http\Controllers\Api;

use App\Models\CastMember;
use App\Http\Controllers\Controller;
use App\Http\Resources\CastMemberResource;

class CastMemberController extends BasicCrudController
{
    private $rules;

    public function __construct()
    {
        $this->rules = [
            'name' => 'required|max:255',
            'type' => 'required|in:' . implode(',',CastMember::TYPES),
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

    protected function ResourceCollection()
    {
        return $this->resource();
    }

    protected function resource()
    {
        return CastMemberResource::class;
    }
}
