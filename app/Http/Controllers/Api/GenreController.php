<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Genre;
use Illuminate\Http\Request;

class GenreController extends BasicCrudController
{
    private $rules = [
        'name' => 'required|max:255',
        'is_active' => 'boolean',
    ];

    public function store(Request $request){
        $validatedData = $this->validate($request, $this->rulesStore());

        $self = $this;

        /** @var Genre $obj */
        $obj = \DB::transaction(function() use ($request, $validatedData, $self){
            $obj = $this->model()::create($validatedData);
            $self->handleRelations($obj, $request);
            return $obj;
        });

        $obj->refresh();

        return $obj;
    }

    public function update(Request $request, $id){
        $obj = $this->findOrFail($id);
        $validatedData = $this->validate($request, $this->rulesUpdate());

        $self = $this;

        $obj = \DB::transaction(function () use ($request, $validatedData, $self, $obj){
            $obj->update($validatedData);
            $self->handleRelations($obj, $request);
            return $obj;
        });

        return $obj;
    }

    protected function handleRelations($genre, Request $request) {
        $genre->categories()->sync($request->get('categories_id'));
    }

    protected function model()
    {
        return Genre::class;
    }

    protected function rulesUpdate()
    {
        return $this->rules;
    }

    protected function rulesStore()
    {
        return $this->rules;
    }
}