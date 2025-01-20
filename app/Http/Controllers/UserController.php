<?php

namespace App\Http\Controllers;

use App\Mail\PasswordResetEmail;
use App\Models\PasswordResetTokens;
use Exception;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
  /**
   * Summary of create
   * @param \Illuminate\Http\Request $request
   * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
   */
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
   * Summary of update
   * @param \Illuminate\Http\Request $request
   * @param string $id
   * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
   */
  public function update(Request $request, string $id)
  {
    $fields = $request->validate([
      'name' => 'required|string',
      'phoneNumber' => 'required|phone|string',
    ], [
      'phone' => 'Invalid number.'
    ]);

    $updateUser = User::where('id', $id)->update([
      'name' => $fields['name'],
      'phone_number' => $fields['phoneNumber'],
    ]);

    if (!$updateUser) {
      return response([
        'success' => false,
        'message' => 'Could not update account',
      ], 400);
    }

    return response([
      'success' => true,
      'message' => 'Successfully updated account',
      'data' => $updateUser,
    ], 201);
  }

  /**
   * Summary of delete
   * @param string $id
   * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
   */
  public function delete(string $id)
  {
    $user = User::where('id', $id)->update([
      'removed' => true,
    ]);

    if (!$user) {
      return response([
        'success' => false,
        'message' => 'User not found',
      ], 404);
    }

    return response([
      'success' => true,
      'message' => 'Successfully deleted user',
    ], 200);
  }

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
   * Summary of recoverPassword
   * @param \Illuminate\Http\Request $request
   * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
   */
  public function recoverPassword(Request $request)
  {
    $fields = $request->validate([
      'email' => 'required|email|string|max:100',
    ]);

    //check email
    $user = User::where('email', $fields['email'])->first();

    if (!$user) {
      return response(
        [
          'success' => false,
          'message' => "An error occured",
          'errors' => [
            'email' => 'That email cannot be found.'
          ]
        ],
        400
      );
    }

    $checkExistingResetRequest = PasswordResetTokens::where([['email', $fields['email']]]);

    //delete if token already exists
    if ($checkExistingResetRequest->exists()) {
      $checkExistingResetRequest->delete();
    }

    $token = rand(10000, 99999);

    try {
      Mail::mailer('smtp-no-reply')->to($fields['email'])
        ->send(new PasswordResetEmail($token));
    } catch (Exception $ex) {
      return response(
        [
          'success' => false,
          'message' => $ex->getMessage(),
        ],
        400
      );
    }

    $insertToken = PasswordResetTokens::insert(['email' => $fields['email'], 'token' => $token]);

    if (!$insertToken) {
      return response(
        [
          'success' => false,
          'message' => "Network error",
        ],
        400
      );
    }
    return response([
      'success' => true,
      'message' => "A reset token has been sent to your email address",
      'data' => $insertToken
    ], 201);
  }

  /**
   * Summary of confirmPasswordResetToken
   * @param \Illuminate\Http\Request $request
   * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
   */
  public function confirmPasswordResetToken(Request $request)
  {
    $fields = $request->validate([
      'email' => 'string|required|email|max:100',
      'token' => 'digits:5|required',
    ]);

    $checkForToken = PasswordResetTokens::where([
      ['email', $fields['email']],
      ['token', $fields['token']]
    ]);

    if (!$checkForToken) {
      return response([
        'success' => false,
        'message' => "Network error. Try again later",
      ], 400);
    }

    /** delete if token already exists */
    if ($checkForToken->exists()) {
      return response([
        'success' => true,
        'message' => "Token exists",
      ], 201);
    } else {
      return response([
        'success' => false,
        'message' => "Token does not exist",
        'errors' => [
          'token' => 'Token does not exist'
        ]
      ], 400);
    }
  }

  /**
   * Summary of resetPassword
   * @param \Illuminate\Http\Request $request
   * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
   */
  public function resetPassword(Request $request)
  {
    $fields = $request->validate([
      'email' => 'string|required|email|max:100',
      'token' => 'digits:5|required',
      'password' => 'required|string|min:8',
      'password_confirmation' => 'required|same:password|min:8',
    ]);

    $confirmToken = $this->confirmPasswordResetToken(new Request([
      'email' => $fields['email'],
      'token' => $fields['token'],
    ]));

    if ($confirmToken['success'] == false) {
      return $confirmToken;
    }
    //update user password
    $resetPassword = User::where([
      ['email', $fields['email']],
    ])->update([
          'password' => Hash::make($fields['password'])
        ]);

    if (!$resetPassword) {
      response([
        'success' => false,
        'message' => 'Could not reset password'
      ], 400);
    }

    $deleteToken = PasswordResetTokens::where([
      'email' => $fields['email'],
      'token' => $fields['token'],
    ])->delete();

    if (!$deleteToken) {
      response([
        'success' => false,
        'message' => 'Network error try again later'
      ], 400);
    }
    return response([
      'success' => true,
      'message' => 'Password Changed'
    ], 201);
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

  /**
   * Summary of uploadProfilePic
   * @param \Illuminate\Http\Request $request
   * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
   */
  public function uploadProfilePic(Request $request)
  {
    $fields = $request->validate([
      'id' => 'required|numeric',
      'profilePic' => 'required|image|mimes:jpeg,png,jpg|max:3072000',
    ]);

    $imageName = $fields['id'] . '.' . time() . '.' . $fields['profilePic']->extension();
    Storage::putFileAs(
      'public/uploads/userprofilepictures',
      $fields['profilePic'],
      $imageName
    );
    $uploadProfilePicQuery = User::where('id', $fields['id'])
      ->where('removed', 0)
      ->update([
        'profile_pic' => $imageName,
      ]);

    return response([
      'success' => true,
      'message' => 'Profile picture successfully uploaded',
      'data' => [
        'profilePicName' => $imageName,
        'query' => $uploadProfilePicQuery
      ]
    ], 201);
  }
}