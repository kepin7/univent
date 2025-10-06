<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function dashboard(): JsonResponse
    {
        $admin = Auth::user();

        return response()->json([
            'status'  => 'success',
            'message' => 'Welcome to Admin Dashboard',
            'admin'   => [
                'id'    => $admin->id,
                'name'  => $admin->name,
                'email' => $admin->email,
            ]
        ]);
    }
}