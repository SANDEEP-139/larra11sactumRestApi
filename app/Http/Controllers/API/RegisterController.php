<?php
   
namespace App\Http\Controllers\API;
   
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Illuminate\Support\Facades\Hash;
   
class RegisterController extends BaseController
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */

     public function register(Request $request): JsonResponse
     {
         $validator = Validator::make($request->all(), [
             'name' => 'required',
             'email' => 'required|email|unique:users,email',
             'password' => 'required|min:6',
             'c_password' => 'required|same:password',
         ]);
     
         if ($validator->fails()) {
             return response()->json([
                 'success' => false,
                 'message' => 'Validation Error',
                 'errors' => $validator->errors(),
             ], 422);
         }
     
         $input = $request->all();
         $input['password'] = Hash::make($input['password']); // Using Hash facade instead of bcrypt
         $user = User::create($input);
         $token = $user->createToken('MyApp')->plainTextToken;
     
         return response()->json([
             'success' => true,
             'token' => $token,
             'name' => $user->name,
             'message' => 'User registered successfully.',
         ], 200);
     }
   
    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */

     public function login(Request $request): JsonResponse
     {
         $credentials = $request->only('email', 'password');
     
         if (Auth::attempt($credentials)) { 
             $user = Auth::user(); 
             $token = $user->createToken('MyApp')->plainTextToken;
             return response()->json([
                 'success' => true,
                 'token' => $token,
                 'name' => $user->name,
                 'message' => 'User logged in successfully.'
             ], 200);
         } else { 
             return response()->json([
                 'success' => false,
                 'message' => 'Unauthorized. Please check your credentials.'
             ], 401);
         } 
     }
}