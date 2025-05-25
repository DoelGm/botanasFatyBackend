<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(){
      $users = User::all();
      return response()->json($users);
    }
    public function show($id){
      $user = User::findOrFail($id);
      return response()->json($user);
    }
    public function store(Request $request){
      $validatedData = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8',
      ]);

      $user = new User();
      $user->name = $validatedData['name'];
      $user->email = $validatedData['email'];
      $user->password = bcrypt($validatedData['password']);
      $user->save();

      return response()->json($user, 201);
    }
    public function update(Request $request, $id){
      $user = User::findOrFail($id);

      $validatedData = $request->validate([
        'name' => 'sometimes|required|string|max:255',
        'email' => 'sometimes|required|string|email|max:255|unique:users,email,'.$user->id,
        'password' => 'sometimes|required|string|min:8',
      ]);

      if(isset($validatedData['name'])){
        $user->name = $validatedData['name'];
      }
      if(isset($validatedData['email'])){
        $user->email = $validatedData['email'];
      }
      if(isset($validatedData['password'])){
        $user->password = bcrypt($validatedData['password']);
      }
      $user->save();

      return response()->json($user);
    }
    public function destroy($id){
      $user = User::findOrFail($id);
      $user->delete();
      return response()->json(['message' => 'User deleted successfully']);
    }
}
