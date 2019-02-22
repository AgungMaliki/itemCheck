<?php

namespace App\Http\Controllers; 

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\User;

class AuthController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function register(Request $r){
        $name = $r->input('name');
        $email = $r->input('email');
        $password = Hash::make($r->input('password'));
        
        $register = User::create([
            'name' => $name,
            'email' => $email,
            'password' => $password
        ]);

        if($register){
            return response()->json([
                'success' => true,
                'message' => 'Register Success!',
                'data' => $register
            ], 201);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Register Fail!',
                'data' => ''
            ], 400);
        }
    }

    public function login(Request $r){
        $email = $r->input('email');
        $password = $r->input('password');

        $user = User::where('email', $email)->first();
        if(Hash::check($password, $user->password)){
            $apiToken = base64_encode(str_random(40));
            $user->update([
                'api_token' => $apiToken
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Berhasil Login',
                'data' => [
                    'user' => $user,
                    'api_token' => $apiToken
                ]
            ], 201);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Gagal Login',
                'data' => ''
            ]);
        }
    }

    //
}
