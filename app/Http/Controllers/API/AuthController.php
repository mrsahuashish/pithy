<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Support\Facades\Redis;

class AuthController extends Controller
{
    public function register(Request $request){
        
        $user = '';
        $token = '';

        if($request->provider_name=='google'){
           
            $user = User::where('provider_name', '=', 'google')
                        ->where('provider_id', '=', $request->provider_id)
                        ->first();

            if (!$user) {
                $user = User::create([
                            'provider_id' => $request->provider_id,
                            'provider_name' => 'google',
                            'login_oauth_uid' => $request->login_oauth_uid,
                            'name' => $request->name,
                            'email' => $request->email,
                            //'avatar' => $providerUser->picture, // in case you have an avatar and want to use google's
                        ]);
                $token = $user->createToken('Token')->accessToken;
            }else{
                return response()->json(['error'=>'Already registered!'],401);
            }
        }else if($request->provider_name=='facebook'){
            $user = User::where('provider_name', '=', 'facebook')
                        ->where('provider_id', '=', $request->provider_id)
                        ->first();

            if (!$user) {
                $user = User::create([
                            'provider_id' => $request->provider_id,
                            'provider_name' => 'facebook',
                            'login_oauth_uid' => $request->login_oauth_uid,
                            'name' => $request->name,
                            'email' => $request->email,
                        ]);
                $token = $user->createToken('Token')->accessToken;
            }else{
                return response()->json(['error'=>'Already registered!'],401);
            }
        }else{
            $user = User::where('email', '=', $request->email)
                        ->whereNull('deleted_at')
                        ->first();
            if(empty($user)){
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password)
                ]);
                $token = $user->createToken('Token')->accessToken;
            }else{
                return response()->json(['error'=>'Already registered!'],401);  
            }            
        }

        
        return response()->json(['token'=>$token,'user'=>$user],200);
    }

    public function login(Request $request){
        if($request->provider_name=='google'){
            $user = User::where('provider_name', '=', 'google')
                        ->where('provider_id', '=', $request->provider_id)
                        ->where('email', '=', $request->email)
                        ->whereNull('deleted_at')
                        ->first();

            if(!empty($user)){
                $token = $user->createToken('Token')->accessToken;
                return response()->json(['token'=>$token],200);
            }else{
                return response()->json(['error'=>'unauthorized'],401);
            }
        }else if($request->provider_name=='facebook'){
            $user = User::where('provider_name', '=', 'facebook')
                        ->where('provider_id', '=', $request->provider_id)
                        ->where('email', '=', $request->email)
                        ->whereNull('deleted_at')
                        ->first();
    
            if(auth()->attempt($data)){
                $token = $user->createToken('Token')->accessToken;
                return response()->json(['token'=>$token],200);
            }else{
                return response()->json(['error'=>'unauthorized'],401);
            }
        }else{
            $data = [
                'email' => $request->email,
                'password' => $request->password
            ];
    
            if(auth()->attempt($data)){
                $token = auth()->user()->createToken('Token')->accessToken;
                return response()->json(['token'=>$token],200);
            }else{
                return response()->json(['error'=>'unauthorized'],401);
            }
        }        
    }

    public function userInfo(){
        $user = auth()->user();
        return response()->json(['user'=>$user],200);
    }
}
