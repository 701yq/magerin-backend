<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Kreait\Firebase\Factory;

class AdminUserController extends Controller
{
    protected $firestore;

    public function __construct()
    {
        $firebase = (new Factory)->withServiceAccount(storage_path('app/firebase/firebase_credentials.json'));
        $this->firestore = $firebase->createFirestore()->database();
    }

    // PUT: /api/admin/users/{id}/status
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Aktif,Diblokir'
        ]);

        $docRef = $this->firestore->collection('users')->document($id);
        $snapshot = $docRef->snapshot();

        if (!$snapshot->exists()) {
            return response()->json([
                'status' => false,
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        $docRef->update([
            ['path' => 'status', 'value' => $request->status]
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Status user berhasil diperbarui'
        ]);
    }

    // GET: /api/admin/users
    public function index()
    {
        $documents = $this->firestore->collection('users')->documents();
        $data = [];

        foreach ($documents as $doc) {
            $userData = $doc->data();

            // Filter: hanya user biasa (bukan admin)
            if (isset($userData['role']) && strtolower($userData['role']) !== 'admin') {
                $data[] = [
                    'id' => $doc->id(),
                    'name' => $userData['name'] ?? '',
                    'phone' => $userData['phone'] ?? '',
                    'created_at' => $userData['created_at'] ?? '',
                    'status' => $userData['status'] ?? 'Aktif',
                ];
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Daftar semua user',
            'data' => $data,
        ]);
    }
}
