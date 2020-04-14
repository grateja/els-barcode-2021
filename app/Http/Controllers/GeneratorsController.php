<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class GeneratorsController extends Controller
{
    public function uniqueCode() {
        return auth('api')->user()->generateUniqueCode();
    }
}
