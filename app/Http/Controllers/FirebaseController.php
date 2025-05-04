<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Factory;

class FirebaseController extends Controller
{
    public function getUsers()
    {
        $factory = (new Factory)->withServiceAccount(
            storage_path('app/firebase/firebase_credentials.json')
        );

        $firestore = $factory->createFirestore();
        $database = $firestore->database();

        $documents = $database->collection('users')->documents();

        $data = [];
        foreach ($documents as $doc) {
            $data[] = $doc->data();
        }

        return response()->json($data);
    }
}
