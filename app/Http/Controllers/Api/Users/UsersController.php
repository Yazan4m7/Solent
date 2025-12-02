<?php

namespace App\Http\Controllers\Api\Users;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    public function index(Request $request){
        $users = User::paginate(30);

        return response()->json(['data' => $users], 200);
    }

    public function create(Request $request){
        $this->validate($request, [

            'name'     => 'required',
            'password' => 'required'
        ]);

        try{
            $users = User::create(['name' => $request->name, 'email' => $request->email, 'password' => Hash::make($request->password)]);
        } catch (\Exception $e){
            return response()->json(['data' => 'Something went wrong'], 422);
        }

        return response()->json(['data' => $users], 200);
    }

    public function view($id){
        $user = User::where('id', $id)->first();
        if(!$user){
            return response()->json(['data' => 'User not found'], 404);
        }

        return response()->json(['data' => $user], 200);
    }

    public function update(Request $request){
        $this->validate($request, [
            'id'    => 'required',
            'name'     => 'required',
        ]);

        $user = User::where('id', $request->id)->first();
        if(!$user){
            return response()->json(['data' => 'User not found'], 404);
        }

        $user->name = $request->name;
        try{
            $user->save();
            return response()->json(['updated' => true], 200);
        } catch (\Exception $e){
            return response()->json(['updated' => false], 422);
        }
    }
}
