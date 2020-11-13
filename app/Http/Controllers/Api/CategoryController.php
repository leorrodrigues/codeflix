<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends BasicCrudController
{
    private $rules = [
        'name' => 'required|max:255',
        'is_active' => 'boolean',
        'description' => 'nullable',
    ];

    public function index()
    {
        $collection = parent::index();
        return CategoryResource::collection($collection);
    }

    public function show($id)
    {
        $obj = parent::show($id);
        return new CategoryResource($obj);
    }

    protected function model()
    {
        return Category::class;
    }

    protected function rulesStore()
    {
        return $this->rules;
    }

    protected function rulesUpdate()
    {

        return $this->rules;
    }

    protected function resource()
    {
        return CategoryResource::class;
    }
}
