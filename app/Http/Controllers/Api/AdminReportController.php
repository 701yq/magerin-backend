<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Kreait\Firebase\Factory;

class AdminReportController extends Controller
{
    protected $firestore;

    public function __construct()
    {
        $firebase = (new Factory)->withServiceAccount(storage_path('app/firebase/firebase_credentials.json'));
        $this->firestore = $firebase->createFirestore()->database();
    }

    // GET: /api/admin/reports
    public function index()
    {
        $documents = $this->firestore->collection('reports')->documents();
        $data = [];

        foreach ($documents as $doc) {
            $data[] = array_merge(['id' => $doc->id()], $doc->data());
        }

        return response()->json([
            'status' => true,
            'message' => 'Daftar laporan',
            'data' => $data
        ]);
    }

    // PUT: /api/admin/reports/{id}
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string'
        ]);

        $docRef = $this->firestore->collection('reports')->document($id);
        $snapshot = $docRef->snapshot();

        if (!$snapshot->exists()) {
            return response()->json([
                'status' => false,
                'message' => 'Laporan tidak ditemukan'
            ], 404);
        }

        $docRef->update([
            ['path' => 'status', 'value' => $request->status]
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Status laporan berhasil diperbarui'
        ]);
    }

    // DELETE: /api/admin/reports/{id}
    public function destroy($id)
    {
        $docRef = $this->firestore->collection('reports')->document($id);
        $snapshot = $docRef->snapshot();

        if (!$snapshot->exists()) {
            return response()->json([
                'status' => false,
                'message' => 'Laporan tidak ditemukan'
            ], 404);
        }

        $docRef->delete();

        return response()->json([
            'status' => true,
            'message' => 'Laporan berhasil dihapus'
        ]);
    }
}
