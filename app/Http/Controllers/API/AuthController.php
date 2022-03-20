<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();
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
                'password' => bcrypt($request->password1)
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
