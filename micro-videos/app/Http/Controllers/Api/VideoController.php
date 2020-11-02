<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Video;
use Illuminate\Http\Request;

class VideoController extends BasicCrudController
{
   private $rules;

   protected function model()
   {
       return Video::class;
   }

   protected function rulesStore()
   {

   }

   protected function rulesUpdate()
   {

   }
}
