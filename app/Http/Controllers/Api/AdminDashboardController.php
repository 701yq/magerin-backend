<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Kreait\Firebase\Factory;

class AdminDashboardController extends Controller
{
    protected $firestore;

    public function __construct()
    {
        $firebase = (new Factory)->withServiceAccount(storage_path('app/firebase/firebase_credentials.json'));
        $this->firestore = $firebase->createFirestore()->database();
    }

    public function index()
    {
        $users = $this->firestore->collection('users')->documents();
        $reports = $this->firestore->collection('reports')->documents();

        $totalUsers = 0;
        $activeUsers = 0;

        // Inisialisasi data per bulan (Jan–Des, index 0–11)
        $monthlyUsers = array_fill(0, 12, 0);

        foreach ($users as $user) {
            $data = $user->data();

            if (isset($data['role']) && $data['role'] === 'user') {
                $totalUsers++;

                if (isset($data['status']) && strtolower($data['status']) === 'aktif') {
                    $activeUsers++;
                }

                // Hitung berdasarkan created_at jika ada
                if (!empty($data['created_at'])) {
                    try {
                        $timestamp = strtotime($data['created_at']);
                        $monthIndex = (int)date('n', $timestamp) - 1; // Januari = 0
                        if ($monthIndex >= 0 && $monthIndex < 12) {
                            $monthlyUsers[$monthIndex]++;
                        }
                    } catch (\Exception $e) {
                        // Lewati jika format tanggal invalid
                    }
                }
            }
        }

        $totalReports = $reports->size();

        return response()->json([
            'status' => true,
            'message' => 'Data dashboard',
            'data' => [
                'total_users' => $totalUsers,
                'active_users' => $activeUsers,
                'total_reports' => $totalReports,
                'chart' => [
                    'labels' => [
                        'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                        'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
                    ],
                    'values' => $monthlyUsers
                ]
            ]
        ]);
    }
}
