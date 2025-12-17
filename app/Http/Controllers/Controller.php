<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

abstract class Controller
{
    protected function get_user_id(Request $request)
    {
        return $request->attributes->get('user_id');
    }
}
