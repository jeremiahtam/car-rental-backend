<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Http\Requests\StoreAdminRequest;
use App\Http\Requests\UpdateAdminRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class AdminController extends Controller
{
  public function login(Request $request)
  {
    $fields = $request->validate([
      'email' => 'required|email|string|max:100',
      'password' => 'required|string|min:8',
    ]);
    //check email
    $admin = Admin::where('email', $fields['email'])->first();

    //check password
    if (!$admin || !Hash::check($fields['_password'], $admin->password)) {
      return response([
        'success' => false,
        "message" => "Your username or password is incorrect.",
        'data' => $admin,
        "errors" => [
          "email" => "Your username or password is incorrect.",
          "password" => "Your username or password is incorrect.",
        ],
      ], 401);
    }

    $token = $admin->createToken($request['username'], ['admin'])->plainTextToken;

    $response = [
      'success' => true,
      'message' => 'Successfully logged in',
      'data' => [
        'token' => $token,
      ]
    ];

    return response($response, 201);
  }


  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    //
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(StoreAdminRequest $request)
  {
    //
  }

  /**
   * Display the specified resource.
   */
  public function show(Admin $admin)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(UpdateAdminRequest $request, Admin $admin)
  {
    //
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(Admin $admin)
  {
    //
  }
}
