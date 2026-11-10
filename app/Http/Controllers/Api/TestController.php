<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TestController extends Controller
{
    // GET API Test
    public function index()
    {
        return response()->json([
            'status' => true,
            'message' => 'API is working fine!',
        ]);
    }

    // POST API Test
    public function store(Request $request)
    {
        return response()->json([
            'status' => true,
            'message' => 'Data received successfully!',
            'data' => $request->all()
        ]);
    }

    // Dynamic Parameter Test
    public function show($id)
    {
        return response()->json([
            'status' => true,
            'message' => 'Data fetched successfully!',
            'id' => $id
        ]);
    }
}
