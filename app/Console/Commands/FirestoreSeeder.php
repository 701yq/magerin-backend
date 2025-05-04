<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Kreait\Firebase\Factory;
use Faker\Factory as Faker;

class FirestoreSeeder extends Command
{
    protected $signature = 'firestore:seed';
    protected $description = 'Seed dummy data ke Firestore users dan reports';

    public function handle()
    {
        $firebase = (new Factory)->withServiceAccount(storage_path('app/firebase/firebase_credentials.json'));
        $firestore = $firebase->createFirestore()->database();
        $faker = Faker::create();

        // Insert dummy users
        $this->info('Membuat dummy users...');
        for ($i = 0; $i < 10; $i++) {
            $firestore->collection('users')->add([
                'name' => $faker->name,
                'phone' => $faker->phoneNumber,
                'created_at' => now()->toISOString(),
                'status' => $faker->randomElement(['Aktif', 'Diblokir']),
                'role' => 'user',
            ]);
        }

        // Insert dummy reports
        $this->info('Membuat dummy reports...');
        for ($i = 0; $i < 5; $i++) {
            $firestore->collection('reports')->add([
                'reporter_name' => $faker->name,
                'reported_name' => $faker->name,
                'category' => $faker->randomElement(['Penipuan', 'Konten tidak pantas']),
                'description' => $faker->sentence,
                'status' => $faker->randomElement(['Belum diproses', 'Selesai']),
            ]);
        }

        $this->info('✅ Selesai insert dummy data!');
    }
}
