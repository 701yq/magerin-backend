<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Exception\Auth\InvalidToken;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        Log::info('🔥 AdminMiddleware dijalankan');

        // 🛡️ Ambil Authorization header dari request
        $authHeader = $request->header('Authorization');
        Log::info('🛡️ Authorization Header:', [$authHeader]);

        // ❌ Jika tidak ada header atau tidak menggunakan format Bearer
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            Log::warning('❌ Token tidak ditemukan di Authorization header');
            return response()->json([
                'message' => 'Token tidak ditemukan.'
            ], Response::HTTP_UNAUTHORIZED);
        }

        // 🧩 Ambil ID token dari header
        $idToken = str_replace('Bearer ', '', $authHeader);

        try {
            // 🔥 Inisialisasi Firebase
            $firebase = (new Factory)->withServiceAccount(storage_path('app/firebase/firebase_credentials.json'));
            $auth = $firebase->createAuth();

            // ✅ Verifikasi token
            $verifiedIdToken = $auth->verifyIdToken($idToken);
            $email = $verifiedIdToken->claims()->get('email');
            Log::info('✅ Token berhasil diverifikasi. Email:', [$email]);

            // ⚠️ Cek email di token
            if (!$email) {
                Log::warning('⚠️ Email tidak ditemukan dalam token');
                return response()->json([
                    'message' => 'Email tidak ditemukan dalam token.'
                ], Response::HTTP_UNAUTHORIZED);
            }

            // 🔍 Ambil data user dari Firestore berdasarkan email
            $firestore = $firebase->createFirestore()->database();
            $userDocs = $firestore->collection('users')->where('email', '=', $email)->documents();

            if ($userDocs->isEmpty()) {
                Log::warning('🚫 Tidak ditemukan user dengan email tersebut di Firestore');
                return response()->json([
                    'message' => 'User tidak ditemukan.'
                ], Response::HTTP_FORBIDDEN);
            }

            foreach ($userDocs as $doc) {
                $data = $doc->data();
                Log::info('👤 Data user ditemukan di Firestore:', $data);

                // 🔐 Cek apakah user memiliki role admin
                if (isset($data['role']) && $data['role'] === 'admin') {
                    Log::info('🔐 Akses diizinkan. User adalah admin');

                    // 🧾 Inject data user ke dalam request
                    $request->merge(['user' => $data]);

                    // 🚀 Lanjutkan request
                    return $next($request);
                }
            }

            // 🚫 Jika tidak ada yang ber-role admin
            Log::warning('🚫 Akses ditolak. Role bukan admin atau user tidak ditemukan');
            return response()->json([
                'message' => 'Akses ditolak. Bukan admin.'
            ], Response::HTTP_FORBIDDEN);

        } catch (InvalidToken $e) {
            // ❌ Token tidak valid
            Log::error('❌ Token tidak valid: ' . $e->getMessage());
            return response()->json([
                'message' => 'Token tidak valid: ' . $e->getMessage()
            ], Response::HTTP_UNAUTHORIZED);

        } catch (\Throwable $e) {
            // 💥 Error umum
            Log::error('💥 Terjadi error saat validasi token/admin:', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Terjadi error: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
