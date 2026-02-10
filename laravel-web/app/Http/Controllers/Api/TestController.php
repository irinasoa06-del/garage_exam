<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class TestController extends Controller
{
    public function test()
    {
        return response()->json([
            'status' => 'ok',
            'message' => 'API Laravel fonctionne'
        ]);
    }
}
