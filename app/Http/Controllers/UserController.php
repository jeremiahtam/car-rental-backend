<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

class UserController extends Controller
{
  /**
   * Summary of login
   * @param \Illuminate\Http\Request $request
   * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
   */
  public function login(Request $request)
  {
    $fields = $request->validate([
      'email' => 'required|email|string|max:100',
      'password' => 'required|string|min:8',
    ]);

    //check email
    $user = User::where('email', $fields['email'])->first();

    //check password
    if (!$user || !Hash::check($fields['password'], $user->password)) {
      return response([
        'success' => false,
        "message" => "Your username or password is incorrect.",
        "errors" => [
          "email" => "Your username or password is incorrect.",
          "password" => "Your username or password is incorrect.",
        ],
      ], 401);
    }

    $token = $user->createToken($fields['email'], ['user'])->plainTextToken;

    return response([
      'success' => true,
      'message' => 'Successfully logged in',
      'data' => [
        'token' => $token,
      ]
    ], 200);
  }

  /**
   * Summary of getUserInfoByToken
   * @param \Illuminate\Http\Request $request
   * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
   */
  public function getUserInfoByToken(Request $request)
  {
    $token = PersonalAccessToken::findToken($request->bearerToken());
    $user = $token->tokenable;

    $neededData = [
      'id' => $user->id,
      'name' => $user->name,
      'email' => $user->email,
      'profilePicture' => $user->profile_pic,
    ];

    return response([
      'success' => true,
      'message' => 'Successfully retrieved user info',
      'data' => $neededData,
    ], 200);
  }

  public function create(Request $request)
  {
    $fields = $request->validate([
      'name' => 'required|string',
      'email' => 'required|email|unique:users,email',
      'phoneNumber' => 'required|phone|string',
      'password' => 'required|string',
      'confirmPassword' => 'required|string|same:password',
    ], [
      'phone' => 'Invalid number.'
    ]);

    $createUser = User::create([
      'name' => $fields['name'],
      'email' => $fields['email'],
      'phone_number' => $fields['phoneNumber'],
      'password' => Hash::make($fields['password']),
      'removed' => 0,
    ]);

    if (!$createUser) {
      return response([
        'success' => false,
        'message' => 'Could not create an account',
      ], 400);
    }

    $token = $createUser->createToken($fields['email'], ['user'])->plainTextToken;

    return response([
      'success' => true,
      'message' => 'Successfully created an account',
      'data' => [
        'token' => $token,
      ],
    ], 201);
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    //
  }

  /**
   * Display the specified resource.
   */
  public function show(string $id)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(string $id)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, string $id)
  {
    //
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(string $id)
  {
    //
  }
}
