<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class PassportAuthController extends Controller
{
    private $keyToken = 'ZFmpGet65tCzLtjk';
    /**
     * Registration
     */
    public function register(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|min:4',
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        if(is_null(User::where('email', $request->email)->first())){
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password)
            ]);
           
            $token = $user->createToken($this->keyToken)->accessToken;
     
            return response()->json(['token' => $token], 200);
        }

        return response()->json(['message' => ''], 400);
    }
 
    /**
     * Login
     */
    public function login(Request $request)
    {
        $data = [
            'email' => $request->email,
            'password' => $request->password
        ];
        
        if (auth()->attempt($data)) {
            $token = auth()->user()->createToken($this->keyToken)->accessToken;
            return response()->json(['token' => $token, 'name' => auth()->user()->name], 200);
        } else {
            return response()->json(['error' => 'Unauthorised'], 401);
        }
    }  
}
