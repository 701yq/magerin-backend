<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Exception\Auth\InvalidPassword;
use Kreait\Firebase\Exception\Auth\UserNotFound;

class AuthApiController extends Controller
{
    protected $auth;
    protected $firestore;

    public function __construct()
    {
        $factory = (new Factory)->withServiceAccount(
            storage_path('app/firebase/firebase_credentials.json')
        );

        $this->auth = $factory->createAuth();
        $this->firestore = $factory->createFirestore()->database();
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        try {
            $signInResult = $this->auth->signInWithEmailAndPassword($request->email, $request->password);
            $user = $signInResult->data();

            $role = $this->getUserRoleByEmail($request->email);
            if ($role !== 'admin') {
                return response()->json([
                    'status' => false,
                    'message' => 'Akses hanya untuk admin.',
                ], 403);
            }

            return response()->json([
                'status' => true,
                'message' => 'Login berhasil',
                'data' => $user,
            ]);

        } catch (InvalidPassword $e) {
            return response()->json([
                'status' => false,
                'message' => 'Password salah',
            ], 401);
        } catch (UserNotFound $e) {
            return response()->json([
                'status' => false,
                'message' => 'Email tidak ditemukan',
            ], 404);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal login: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function me(Request $request)
{
    return response()->json([
        'status' => true,
        'message' => 'Data profil admin',
        'data' => $request->get('user'), // ambil manual dari request
    ]);
}


    private function getUserRoleByEmail($email)
    {
        $users = $this->firestore->collection('users')->where('email', '=', $email)->documents();
        foreach ($users as $doc) {
            $data = $doc->data();
            if (isset($data['role'])) {
                return $data['role'];
            }
        }
        return null;
    }
}
