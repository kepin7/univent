<?php

namespace App\Http\Controllers;

use App\Mail\FeedbackNotification;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\PersonalAccessToken;

class FeedbackController extends Controller
{
    public function store(Request $request)
    {
        $token = $request->bearerToken();
        
        if (!$token) {
            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'You must login before sending feedback',
                'redirect' => url('http://127.0.0.1:8000/api/auth/login-email')
            ], 401);
        }

        $accessToken = PersonalAccessToken::findToken($token);
        
        if (!$accessToken) {
            return response()->json([
                'error' => 'Invalid or expired token. Please log in again.',
                'redirect' => url('http://127.0.0.1:8000/api/auth/login-email')
            ], 401);
        }

        $user = $accessToken->tokenable;

        if (!$user) {
            return response()->json([
                'error' => 'Invalid or expired token. Please log in again.',
                'redirect' => url('http://127.0.0.1:8000/api/auth/login-email')
            ], 401);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email',
            'message' => 'required|string|max:500',
        ]);

        $feedback = Feedback::create([
            'user_id' => $user->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'message' => $validated['message'],
        ]);

        try {
            Mail::to('hizkiakevin8@gmail.com')->send(new FeedbackNotification($feedback));
        } catch (\Throwable $e) {
            Log::error('Gagal mengirim email feedback: ' . $e->getMessage());
        }

        return response()->json([
            'message' => 'Feedback sent successfully!',
            'data' => $feedback
        ], 201);
    }
}