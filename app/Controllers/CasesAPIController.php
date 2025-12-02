<?php

namespace App\Http\Controllers;

use App\Http\Resources\CasesResource;
use App\sCase;
use Illuminate\Http\Request;

class CasesAPIController extends Controller
{
    public function index(){
        $cases = sCase::limit(10)->get();
        return CasesResource::collection($cases);
    } 
    //
}
