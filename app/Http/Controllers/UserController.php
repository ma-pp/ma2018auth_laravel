<?php

namespace App\Http\Controllers;

use JWTAuth;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;


class UserController extends Controller
{
    public function login(Request $request)
    {        
        $email = $request->get('email');
        $password = $request->get('password');
    
        if (JWTAuth::attempt(['email' => $email, 'password' => $password])) {
            $user = User::where('email', $email)->first();
            $token = JWTAuth::fromUser($user);
            $status = 200;
            $response = [
                "status" => 'success',
                "data" => [
                      "key" => "Bearer {$token}"   
                ],
            ];        
        } else {
            $status = 422;
            $response = [
                "status" => 'fail',
                "data" => [
                "password" => "password anda salah"                    
                ]
            ];
        }
        return response()->json(($response), $status);  
        
    }

    public function register(Request $request)
    {
            $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            // 'password' => 'required|string|min:6|confirmed',
            'password' => 'required|string|min:6',

        ]);

        if($validator->fails()){
                return response()->json($validator->errors()->toJson(), 400);
        }

        $user = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json(compact('user','token'),201);
    }
    public function getAuthenticatedUser()
    {
        try {

            if (! $user = JWTAuth::parseToken()->authenticate()) {
                    return response()->json(['user_not_found'], 404);
            }

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());

        }

        return response()->json(compact('user'));
    }
    
    public function open() 
    {
        $data = "This data is open and can be accessed without the client being authenticated";
        return response()->json(compact('data'),200);

    }

    public function logout() 
    {
        $data = "Hanya pengguna yang berwenang dapat melihat ini";
        return response()->json(compact('data'),200);
    }
}
