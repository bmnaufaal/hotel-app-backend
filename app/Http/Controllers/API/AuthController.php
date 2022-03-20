<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required|min:8',
        ]);

        if ($validator->fails()) {
            return $validator->messages();
        } else {
            $login = Auth::attempt(
                [
                    'email' => $request->email,
                    'password' => $request->password
                ]
            );
            if ($login) {
                $user = User::where('email', $request->email)->first();
                $tokenResult = $user->createToken('token-auth')->plainTextToken;
                return response()->json([
                    'status' => 'Success',
                    'message' => 'Berhasil login',
                    'content' => [
                        'status_code' => 200,
                        'access_token' => $tokenResult,
                        'token_type' => 'Bearer'
                    ]
                ]);
            } else {
                return response()->json([
                    'status' => 'Error',
                    'message' => 'Username atau password salah'
                ]);
            }
        }
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $user->currentAcccessToken()->delete();
        return response()->json([
            'status' => 'Successs',
            'message' => 'Berhasil logout'
        ], 200);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|unique:users',
            'password1' => 'required|min:8',
            'password2' => 'required|min:8|same:password1'
        ]);

        if ($validator->fails()) {
            return $validator->messages();
        } else {
            $data = array(
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password1)
            );

            $register = User::create($data);

            if ($register) {
                return response()->json([
                    'status' => 'Success',
                    'message' => 'Berhasil mendaftarkan pengguna'
                ]);
            } else {
                return response()->json([
                    'status' => 'Error',
                    'message' => 'Gagal mendaftarkan pengguna'
                ]);
            }
        }
    }
}
