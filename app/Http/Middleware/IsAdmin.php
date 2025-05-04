<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Kreait\Firebase\Factory;

class IsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        $idToken = $request->bearerToken(); // Ambil token dari header Authorization

        if (!$idToken) {
            return response()->json(['message' => 'Token tidak ditemukan.'], 401);
        }

        try {
            $auth = (new Factory)->withServiceAccount(storage_path('app/firebase/firebase_credentials.json'))->createAuth();
            $verifiedIdToken = $auth->verifyIdToken($idToken);
            $email = $verifiedIdToken->claims()->get('email');

            // Ambil role user dari Firestore
            $firestore = (new Factory)->withServiceAccount(storage_path('app/firebase/firebase_credentials.json'))->createFirestore();
            $database = $firestore->database();
            $usersRef = $database->collection('users')->where('email', '=', $email);
            $documents = $usersRef->documents();

            foreach ($documents as $doc) {
                if (isset($doc['role']) && $doc['role'] === 'admin') {
                    return $next($request); // ✅ Lolos, lanjut ke controller
                }
            }

            return response()->json(['message' => 'Akses hanya untuk admin.'], 403);

        } catch (\Throwable $e) {
            return response()->json(['message' => 'Token tidak valid.'], 401);
        }
    }
}
