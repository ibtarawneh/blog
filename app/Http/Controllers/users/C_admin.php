<?php
namespace App\Http\Controllers\users;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Auth;


class C_admin extends Controller {

    public function all(){
        $admins = User::all();

        return response()->json($admins);
    }

    public function Credential(){
        $user = Auth::user();

        if ($user) {
            $data = [
                'auth' => true,
                'type' => 'User', 
                'Name' => $user->name,
                'ID' => $user->id,
            ];

            // Return JSON response with user data
            return  $data;
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }
    
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'type' => 'admin',
        ]);

        $token = $user->createToken('fffesbrjttrb54yyryrhru4646444wyr5u5y4h5u5he5')->accessToken;

        return response()->json(['token' => $token], 200);
    }
    
    public function add(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'type' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'name' => $request->name,
            'username' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'type' => $request->type,
        ]);

        $token = $user->createToken('fffesbrjttrb54yyryrhru4646444wyr5u5y4h5u5he5')->accessToken;

        return response()->json(['token' => $token], 200);
    }

    public function login(Request $request) {
        $data = $request->only('email', 'password');

        

        if (auth()->attempt($data)) {
            $user = [
                'auth' => true,
                'token' => auth()->user()->createToken('LaravelPassportAuth')->accessToken,
                'type' => 'User', 
                'name' => auth()->user()->name,
                'ID' => auth()->user()->id,
                'email' => auth()->user()->email,
            ];
            return response()->json($user, 200);
        } else {
            return response()->json(['error' => 'Unauthorised'], 401);
        }
    }
    
}
