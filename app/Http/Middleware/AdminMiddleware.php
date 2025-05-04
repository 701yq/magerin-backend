<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Exception\Auth\InvalidToken;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Ambil Authorization header
        $authHeader = $request->header('Authorization');

        // Cek apakah token tersedia dan dalam format Bearer
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return response()->json([
                'message' => 'Token tidak ditemukan.'
            ], Response::HTTP_UNAUTHORIZED);
        }

        // Ambil ID token dari header
        $idToken = str_replace('Bearer ', '', $authHeader);

        try {
            // Inisialisasi Firebase
            $firebase = (new Factory)->withServiceAccount(storage_path('app/firebase/firebase_credentials.json'));
            $auth = $firebase->createAuth();

            // Verifikasi token
            $verifiedIdToken = $auth->verifyIdToken($idToken);
            $email = $verifiedIdToken->claims()->get('email');

            // Pastikan token berisi email
            if (!$email) {
                return response()->json([
                    'message' => 'Email tidak ditemukan dalam token.'
                ], Response::HTTP_UNAUTHORIZED);
            }

            // Ambil data pengguna dari Firestore
            $firestore = $firebase->createFirestore()->database();
            $userDocs = $firestore->collection('users')
                ->where('email', '=', $email)
                ->documents();

            foreach ($userDocs as $doc) {
                $data = $doc->data();

                // Cek apakah user adalah admin
                if (isset($data['role']) && $data['role'] === 'admin') {
                    // Simpan data user ke dalam request
                    $request->merge(['user' => $data]);
                    return $next($request);
                }
            }

            // Jika user bukan admin
            return response()->json([
                'message' => 'Akses ditolak. Bukan admin.'
            ], Response::HTTP_FORBIDDEN);

        } catch (InvalidToken $e) {
            return response()->json([
                'message' => 'Token tidak valid: ' . $e->getMessage()
            ], Response::HTTP_UNAUTHORIZED);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Terjadi error: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
