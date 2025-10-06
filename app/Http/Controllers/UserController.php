<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function dashboard(): JsonResponse
    {
        $user = Auth::user();

        return response()->json([
            'status' => 'success',
            'message' => 'Welcome to User Dashboard',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
    }
}