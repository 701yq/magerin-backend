<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Kreait\Firebase\Factory;

class AdminController extends Controller
{
    public function profile(Request $request)
    {
        $user = $request->user(); // Data user dari middleware

        if (!$user || !isset($user['email'])) {
            return response()->json([
                'status' => false,
                'message' => 'User tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Data profil admin',
            'data' => $user
        ]);
    }
}
